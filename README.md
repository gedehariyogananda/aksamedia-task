## AKSAMEDIA TEST BACKEND DOCUMENT

1. Task Overview
   ```bash
   
   - api testing collection reposiory (postman) : https://www.postman.com/crimson-eclipse-393334/workspace/aksamedia-api/collection/29489672-80729349-b7c7-4e6c-8d23-2f9e5fce1cd6
   
   - addres website : https://aksa.ajakjago.com/api/{uri} -> to akses api
     for example : https://aksa.ajakjago.com/api/nilai/st (to akses api bonus nilai)

   ```
2. Route Overview
   ![image](https://github.com/user-attachments/assets/c8580e76-fc62-4b24-a953-d3e8ff93f1d0)

    
3. Tugas 1 (Code and Result Body JSON)
   ```bash
   public function login(Request $request)
    {
        $validates = Validator::make($request->all(), [
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        if ($validates->fails()) {
            return response()->json($validates->errors(), 400);
        }

        try {
            $userCheck = User::where('username', $request->username)->first();
            if (!$userCheck) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not found',
                ], 400);
            }

            if (!$token = Auth::guard('api')->attempt($validates->validated())) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized',
                ], 401);
            }

            if ($userCheck->roles == 'atasan') {
                return response()->json([
                    'success' => 'success',
                    'message' => 'Authentication as Atasan successfully',
                    'data' => [
                        'token' => $token,
                        'atasan' => [
                            'id' => $userCheck->id,
                            'name' => $userCheck->name,
                            'username' => $userCheck->username,
                            'phone' => $userCheck->phone,
                            'email' => $userCheck->email
                        ]
                    ]
                ], 200);
            }

            if ($userCheck->roles == 'admin') {
                return response()->json([
                    'success' => 'success',
                    'message' => 'Authentication as Admin successfully',
                    'data' => [
                        'token' => $token,
                        'admin' => [
                            'id' => $userCheck->id,
                            'name' => $userCheck->name,
                            'username' => $userCheck->username,
                            'phone' => $userCheck->phone,
                            'email' => $userCheck->email
                        ]
                    ]

                ]);
            }
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }

   ```

   result hasil if roles admin :
   ![image](https://github.com/user-attachments/assets/eccc16c5-5d17-4e2f-9ea7-45ba1e4c96a4)

   result hasil if roles atasan :
   ![image](https://github.com/user-attachments/assets/e4f9cf47-79dc-43fb-af89-415b6d41f496)

4. Tugas 2 (Code and Result Body JSON)
   ```bash
     private $division;
    private $employee;

    public function __construct(Division $division, Employee $employee)
    {
        $this->division = $division;
        $this->employee = $employee;
    }
    public function divisionData()
    {
        if (!auth()->check()) {
            return response()->json([
                "status" => "error",
                "message" => "Unauthorized",
            ], 401);
        }

        $divisions = $this->division->select('id', 'name')->paginate(4);

        return response()->json([
            "status" => "success",
            "message" => "Division data loaded successfully",
            "data" => [
                "divisions" => $divisions->items(),
            ],
            "pagination" => [
                "current_page" => $divisions->currentPage(),
                "last_page" => $divisions->lastPage(),
                "per_page" => $divisions->perPage(),
                "total" => $divisions->total(),
                "next_page_url" => $divisions->nextPageUrl(),
                "prev_page_url" => $divisions->previousPageUrl(),
            ]
        ], 200);
    }
   ```

   result if not authenticate :
   ![image](https://github.com/user-attachments/assets/086368a5-651f-48bf-977f-edebb1ecd4a2)
   result if authenticate && success response :
   ![image](https://github.com/user-attachments/assets/9b91fc3c-017e-446e-a705-358763c95643)
   ![image](https://github.com/user-attachments/assets/e3400f5f-8e6f-417b-9644-aa2309d0c88d)

5. Tugas 3 (Code and Result Body JSON)
   ```bash
   public function index()
    {

        $employees = $this->employee->with('division')->paginate(4);
        $mappedDataEmployees = $employees->map(function ($employee) {
            return [
                'id' => $employee->id,
                'image' => $employee->image,
                'name' => $employee->name,
                'phone' =>  $employee->phone,
                'division' => [
                    'id' => $employee->division->id,
                    'name' => $employee->division->name,
                ],
                'position' => $employee->position,
            ];
        });


        return response()->json([
            "status" => "success",
            "message" => "Employee data loaded successfully",
            "data" => [
                "employees" => $mappedDataEmployees,
            ],
            "pagination" => [
                "current_page" => $employees->currentPage(),
                "last_page" => $employees->lastPage(),
                "per_page" => $employees->perPage(),
                "total" => $employees->total(),
                "next_page_url" => $employees->nextPageUrl(),
                "prev_page_url" => $employees->previousPageUrl(),
            ]
        ], 200);
    }

   ```
   
   result if not authenticate :
   ![image](https://github.com/user-attachments/assets/179dfc5a-ad99-4ab4-97d8-d3cae8fa27d2)

   result if authenticate && success response :
   ![image](https://github.com/user-attachments/assets/d17de274-6788-42b1-b36a-54ac8dc8fd05)
   ![image](https://github.com/user-attachments/assets/10ffa104-f606-45db-b706-beb89c361887)

6. Tugas 4 (Code and Result Body JSON)
   ```bash
    public function store(Request $request)
    {
        $validates = Validator::make($request->all(), [
            'name' => 'required|string',
            'position' => 'required|string',
            'division' => 'required|uuid|exists:divisions,id',
            'image' => 'required|string',
            'phone' => 'required|string',
        ]);

        if ($validates->fails()) {
            return response()->json([
                "status" => "error",
                "message" => $validates->errors(),
            ], 400);
        }

        try {
            $dataRequest = [
                'name' => $request->name,
                'position' => $request->position,
                'division_id' => $request->division,
                'image' => $request->image,
                'phone' => $request->phone
            ];

            $employee = $this->employee->create($dataRequest);

            if (!$employee) {
                return response()->json([
                    "status" => "error",
                    "message" => "Failed to create employee data",
                ], 400);
            }

            return response()->json([
                "status" => "success",
                "message" => "Employee data created successfully",
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                "status" => "error",
                "message" => $e->getMessage(),
            ], 500);
        }
    }
   ```

   
   result if not authenticate :
   ![image](https://github.com/user-attachments/assets/98f7ceb3-43d7-4edb-8c11-163f587f5ba0)

   result if authenticate && success response :
   ![image](https://github.com/user-attachments/assets/885febc2-fba9-45f5-8f8b-924d3460b896)

7. Tugas 5 (Code and Result Body JSON)
   ```bash
    public function update(Request $request, $uuid)
    {
        $validates = Validator::make($request->all(), [
            'name' => 'required|string',
            'position' => 'required|string',
            'division' => 'required|uuid|exists:divisions,id',
            'image' => 'required|string',
            'phone' => 'required|string',
        ]);

        if ($validates->fails()) {
            return response()->json([
                "status" => "error",
                "message" => $validates->errors(),
            ], 400);
        }

        try {
            $dataRequest = [
                'name' => $request->name,
                'position' => $request->position,
                'division_id' => $request->division,
                'image' => $request->image,
                'phone' => $request->phone
            ];

            $employeeInit = $this->employee->findByUuid($uuid);

            if (!$employeeInit) {
                return response()->json([
                    "status" => "error",
                    "message" => "Data not found"
                ]);
            }

            $dataUpdated = $employeeInit->update($dataRequest);

            if (!$dataUpdated) {
                return response()->json([
                    "status" => "error",
                    "message" => "Data failed updated"
                ]);
            }

            return response()->json([
                "status" => "success",
                "message" => "Data succesfuly updated"
            ]);
        } catch (Exception $e) {
            return response()->json([
                "status" => "error",
                "message" => $e->getMessage(),
            ], 500);
        }
    }

   ```
   result if not authenticate :
   ![image](https://github.com/user-attachments/assets/958d40a2-1bc3-4572-b7cd-0493fee2376c)

   result if authenticate && success response :
   ![image](https://github.com/user-attachments/assets/6c055413-950e-4732-b83c-22b05d3da855)


8. Tugas 6 (Code and Result Body JSON)
   ```bash
     public function destroy($uuid)
    {
        $dataEmployees = $this->employee->findByUuid($uuid);
        $deleteData = $dataEmployees->delete();

        if (!$deleteData) {
            return response()->json([
                "status" => "error",
                "message" => "Data failed deleted"
            ]);
        }

        return response()->json([
            "status" => "success",
            "message" => "Data succesfuly deleted"
        ]);
    }

   ```


   result if not authenticate :
   ![image](https://github.com/user-attachments/assets/d8951621-4ea8-4d74-b41f-fc18ae067907)

   result if authenticate && success response :
   ![image](https://github.com/user-attachments/assets/96bc1919-21ad-4483-ae9d-d295472e996e)


9. Tugas 7 (Code and Result Body JSON)
   ```bash
       public function logout()
    {
        Auth::guard('api')->logout();
        return response()->json([
            'success' => "success",
            'message' => 'Successfully logged out',
        ]);
    }

   ```

   result if not authenticate :
   ![image](https://github.com/user-attachments/assets/eb74d7cd-19d0-4189-abf7-988780ea288f)

   result if authenticate && success response :
   ![image](https://github.com/user-attachments/assets/02feaaf3-d85d-4987-9283-c6dfb31a03cb)


10. Tugas Bonus Point 1 (Code and Result Body JSON)
   ```bash
    public function index()
    {
        try {
            //  -- start kalau menggunakan db raw query manual sql -- //
            $sql = "
                SELECT 
                    nisn,
                    nama,
                    LOWER(nama_pelajaran) AS nama_pelajaran,
                    skor
                FROM nilais
                WHERE materi_uji_id = 7
                    AND nama_pelajaran != 'Pelajaran Khusus'
                ORDER BY nisn, LOWER(nama_pelajaran)
            ";

            // -- kalau pakai eloquent query builder -- //
            $datas = Nilai::where('materi_uji_id', '7')
                ->where('nama_pelajaran', '!=', 'Pelajaran Khusus')
                ->get();

            $mappedData = $datas->groupBy('nisn')->map(function ($items) {
                $firstItem = $items->first();
                $nilaiRt = [];

                foreach ($items as $item) {
                    $nilaiRt[strtolower($item->nama_pelajaran)] = (int)$item->skor;
                }

                // urutkan dari abjad agar sesuai
                ksort($nilaiRt);

                return [
                    'nama' => $firstItem->nama,
                    'nilaiRt' => $nilaiRt,
                    'nisn' => $firstItem->nisn,
                ];
            })->values(); //reset key values

            return response()->json([
                'message' => 'Data berhasil diambil',
                'data' => $mappedData
            ], 200);


            // -- proses menggunakan sql query biasa (raw query manual) tanpa eloquent laravel -- //
            // $datas = DB::select($sql);
            // $mappedData = collect($datas)->groupBy('nisn')->map(function ($items) {
            //     $firstItem = $items->first();
            //     $nilaiRt = [];
            //     foreach ($items as $item) {
            //         $nilaiRt[strtolower($item->nama_pelajaran)] = (int)$item->skor;
            //     }

            //     // urutkan dari abjad agar sesuai
            //     ksort($nilaiRt);

            //     return [
            //         'nama' => $firstItem->nama,
            //         'nilaiRt' => $nilaiRt,
            //         'nisn' => $firstItem->nisn,
            //     ];
            // })->values(); //reset key values

            // return response()->json([
            //     'message' => 'Data berhasil diambil',
            //     'data' => $mappedData
            // ], 200);

            // -- end proses menggunakan sql query biasa (raw query manual) tanpa eloquent laravel -- //

        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }
    }

   ```

   result if success response :
   ![image](https://github.com/user-attachments/assets/4ad6dc7f-2f3f-47be-88d8-4319d9c52e64)
   ![image](https://github.com/user-attachments/assets/42d75c87-8836-4e01-803f-ee1a27667316)

   all response body : 
   ```bash
    {
    "message": "Data berhasil diambil",
    "data": [
        {
            "nama": "Ahmad Fadlan",
            "nilaiRt": {
                "artistic": 2,
                "conventional": 2,
                "enterprising": 4,
                "investigative": 2,
                "realistic": 4,
                "social": 2
            },
            "nisn": "3097012709"
        },
        {
            "nama": "Alpianor",
            "nilaiRt": {
                "artistic": 4,
                "conventional": 4,
                "enterprising": 2,
                "investigative": 4,
                "realistic": 5,
                "social": 9
            },
            "nisn": "3088979054"
        },
        {
            "nama": "Aspianor",
            "nilaiRt": {
                "artistic": 4,
                "conventional": 4,
                "enterprising": 4,
                "investigative": 4,
                "realistic": 7,
                "social": 7
            },
            "nisn": "0084867232"
        },
        {
            "nama": "Delly Marselina",
            "nilaiRt": {
                "artistic": 5,
                "conventional": 8,
                "enterprising": 4,
                "investigative": 6,
                "realistic": 8,
                "social": 9
            },
            "nisn": "0087420239"
        },
        {
            "nama": "Fiola",
            "nilaiRt": {
                "artistic": 2,
                "conventional": 3,
                "enterprising": 4,
                "investigative": 1,
                "realistic": 5,
                "social": 7
            },
            "nisn": "00825052525"
        },
        {
            "nama": "Lestari",
            "nilaiRt": {
                "artistic": 4,
                "conventional": 8,
                "enterprising": 2,
                "investigative": 4,
                "realistic": 7,
                "social": 9
            },
            "nisn": "3097259402"
        },
        {
            "nama": "Luna Maya",
            "nilaiRt": {
                "artistic": 6,
                "conventional": 4,
                "enterprising": 3,
                "investigative": 4,
                "realistic": 3,
                "social": 8
            },
            "nisn": "0075745967"
        },
        {
            "nama": "M.Arifin Ilham",
            "nilaiRt": {
                "artistic": 6,
                "conventional": 10,
                "enterprising": 6,
                "investigative": 10,
                "realistic": 8,
                "social": 11
            },
            "nisn": "0059052491"
        },
        {
            "nama": "M.Panki Erisman",
            "nilaiRt": {
                "artistic": 2,
                "conventional": 6,
                "enterprising": 3,
                "investigative": 4,
                "realistic": 6,
                "social": 6
            },
            "nisn": "0084112788"
        },
        {
            "nama": "Muhamad Rengki",
            "nilaiRt": {
                "artistic": 6,
                "conventional": 4,
                "enterprising": 6,
                "investigative": 1,
                "realistic": 5,
                "social": 4
            },
            "nisn": "0085305124"
        },
        {
            "nama": "Muhammad Rizal Fatoni",
            "nilaiRt": {
                "artistic": 2,
                "conventional": 6,
                "enterprising": 2,
                "investigative": 5,
                "realistic": 3,
                "social": 10
            },
            "nisn": "0078019829"
        },
        {
            "nama": "Muhammad Sanusi",
            "nilaiRt": {
                "artistic": 2,
                "conventional": 7,
                "enterprising": 6,
                "investigative": 6,
                "realistic": 4,
                "social": 10
            },
            "nisn": "0094494403"
        },
        {
            "nama": "Noni",
            "nilaiRt": {
                "artistic": 6,
                "conventional": 6,
                "enterprising": 8,
                "investigative": 7,
                "realistic": 8,
                "social": 8
            },
            "nisn": "0085692202"
        },
        {
            "nama": "Nor Fitriyani",
            "nilaiRt": {
                "artistic": 5,
                "conventional": 9,
                "enterprising": 6,
                "investigative": 9,
                "realistic": 4,
                "social": 9
            },
            "nisn": "0081726779"
        },
        {
            "nama": "Noraini",
            "nilaiRt": {
                "artistic": 5,
                "conventional": 5,
                "enterprising": 1,
                "investigative": 3,
                "realistic": 4,
                "social": 7
            },
            "nisn": "3083779436"
        },
        {
            "nama": "Norman",
            "nilaiRt": {
                "artistic": 3,
                "conventional": 8,
                "enterprising": 4,
                "investigative": 4,
                "realistic": 4,
                "social": 7
            },
            "nisn": "0073754988"
        },
        {
            "nama": "Nuraini A",
            "nilaiRt": {
                "artistic": 4,
                "conventional": 7,
                "enterprising": 3,
                "investigative": 5,
                "realistic": 3,
                "social": 8
            },
            "nisn": "3071512187"
        },
        {
            "nama": "Putri Faza Najwa",
            "nilaiRt": {
                "artistic": 5,
                "conventional": 8,
                "enterprising": 9,
                "investigative": 9,
                "realistic": 4,
                "social": 6
            },
            "nisn": "3084548483"
        },
        {
            "nama": "Roynaldo C.W",
            "nilaiRt": {
                "artistic": 2,
                "conventional": 5,
                "enterprising": 5,
                "investigative": 8,
                "realistic": 5,
                "social": 9
            },
            "nisn": "0089988234"
        },
        {
            "nama": "Saddam Sugianto",
            "nilaiRt": {
                "artistic": 8,
                "conventional": 8,
                "enterprising": 8,
                "investigative": 9,
                "realistic": 7,
                "social": 7
            },
            "nisn": "0053213163"
        },
        {
            "nama": "Selia",
            "nilaiRt": {
                "artistic": 2,
                "conventional": 6,
                "enterprising": 2,
                "investigative": 3,
                "realistic": 6,
                "social": 9
            },
            "nisn": "0062838429"
        },
        {
            "nama": "Siti Aisah",
            "nilaiRt": {
                "artistic": 4,
                "conventional": 5,
                "enterprising": 5,
                "investigative": 2,
                "realistic": 5,
                "social": 8
            },
            "nisn": "0084759546"
        },
        {
            "nama": "Siti Hadizah",
            "nilaiRt": {
                "artistic": 9,
                "conventional": 8,
                "enterprising": 4,
                "investigative": 6,
                "realistic": 6,
                "social": 8
            },
            "nisn": "0052418071"
        }
    ]
}
   ```

11. Tugas Bonus Point 2 (Code and Result Body JSON)
   ```bash
    public function nilaiST()
    {
        try {
            // jika menggunakan sql query biasa (raw query manual) tanpa eloquent laravel
            $sql = "
                SELECT 
                    n.nisn,
                    n.nama,
                    LOWER(n.nama_pelajaran) AS nama_pelajaran,
                    CASE 
                        WHEN n.pelajaran_id = 44 THEN ROUND(n.skor * 41.67, 2)
                        WHEN n.pelajaran_id = 45 THEN ROUND(n.skor * 29.67, 2)
                        WHEN n.pelajaran_id = 46 THEN ROUND(n.skor * 100, 2)
                        WHEN n.pelajaran_id = 47 THEN ROUND(n.skor * 23.81, 2)
                        ELSE 0
                    END AS skor
                FROM nilais n
                WHERE n.materi_uji_id = 4
                ORDER BY n.nisn, LOWER(n.nama_pelajaran)
            ";


            // -- jika menggunakan eloquent query builder -- //
            $datas = Nilai::where('materi_uji_id', '4')->get();

            $mappedData = $datas->groupBy('nisn')->map(function ($items) {
                $firstItem = $items->first();
                $nilaiST = [];
                $totalSkor = 0;

                foreach ($items as $item) {
                    $skor = 0;

                    switch ($item->pelajaran_id) {
                        case 44:
                            $skor = round((int)$item->skor * 41.67, 2);
                            break;
                        case 45:
                            $skor = round((int)$item->skor * 29.67, 2);
                            break;
                        case 46:
                            $skor = round((int)$item->skor * 100, 2);
                            break;
                        case 47:
                            $skor = round((int)$item->skor * 23.81, 2);
                            break;
                    }

                    $nilaiST[strtolower($item->nama_pelajaran)] = $skor;
                    $totalSkor += $skor;
                }

                // urutkan dari abjad agar sesuai
                ksort($nilaiST);

                return [
                    'listNilai' => $nilaiST,
                    'nama' => $firstItem->nama,
                    'nisn' => $firstItem->nisn,
                    'total' => round($totalSkor, 2)
                ];
            })->values(); // Reset key values

            // sort data dari skor tinggi
            $sortedData = $mappedData->sortByDesc('total')->values();

            return response()->json([
                'message' => 'Data berhasil diambil',
                'data' => $sortedData
            ], 200);

            // -- proses menggunakan sql query biasa (raw query manual) tanpa eloquent laravel -- //
            // $datas = DB::select($sql);

            // $groupedData = collect($datas)->groupBy('nisn')->map(function ($items) {
            //     $firstItem = $items->first();
            //     $nilaiST = [];
            //     $totalSkor = 0;

            //     foreach ($items as $item) {
            //         $nilaiST[$item->nama_pelajaran] = $item->skor;
            //         $totalSkor += $item->skor;
            //     }

            //     ksort($nilaiST);

            //     return [
            //         'listNilai' => $nilaiST,
            //         'nama' => $firstItem->nama,
            //         'nisn' => $firstItem->nisn,
            //         'total' => round($totalSkor, 2)
            //     ];
            // })->values();

            // $sortedData = $groupedData->sortByDesc('total')->values();

            // return response()->json([
            //     'message' => 'Data berhasil diambil',
            //     'data' => $sortedData
            // ], 200);

            // -- end proses menggunakan sql query biasa (raw query manual) tanpa eloquent laravel -- //

        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }
   ```

   result if success response :
   ![image](https://github.com/user-attachments/assets/5fd1f201-7353-4cc3-849f-72647f205d2d)
   ![image](https://github.com/user-attachments/assets/70f064b2-5afd-4a81-bcb7-a8941251c7c5)
   ![image](https://github.com/user-attachments/assets/6cf949a6-ccbd-49e7-899a-e23d62b7a6df)

   all response body : 
```bash
        {
        "message": "Data berhasil diambil",
        "data": [
            {
                "listNilai": {
                    "figural": 142.86,
                    "kuantitatif": 89.01,
                    "penalaran": 200,
                    "verbal": 208.35
                },
                "nama": "Muhammad Sanusi",
                "nisn": "0094494403",
                "total": 640.22
            },
            {
                "listNilai": {
                    "figural": 190.48,
                    "kuantitatif": 59.34,
                    "penalaran": 200,
                    "verbal": 166.68
                },
                "nama": "Aspianor",
                "nisn": "0084867232",
                "total": 616.5
            },
            {
                "listNilai": {
                    "figural": 190.48,
                    "kuantitatif": 118.68,
                    "penalaran": 100,
                    "verbal": 166.68
                },
                "nama": "Delly Marselina",
                "nisn": "0087420239",
                "total": 575.84
            },
            {
                "listNilai": {
                    "figural": 142.86,
                    "kuantitatif": 148.35,
                    "penalaran": 200,
                    "verbal": 83.34
                },
                "nama": "Lestari",
                "nisn": "3097259402",
                "total": 574.55
            },
            {
                "listNilai": {
                    "figural": 119.05,
                    "kuantitatif": 59.34,
                    "penalaran": 200,
                    "verbal": 166.68
                },
                "nama": "Nor Fitriyani",
                "nisn": "0081726779",
                "total": 545.07
            },
            {
                "listNilai": {
                    "figural": 142.86,
                    "kuantitatif": 118.68,
                    "penalaran": 100,
                    "verbal": 166.68
                },
                "nama": "Saddam Sugianto",
                "nisn": "0053213163",
                "total": 528.22
            },
            {
                "listNilai": {
                    "figural": 166.67,
                    "kuantitatif": 148.35,
                    "penalaran": 0,
                    "verbal": 208.35
                },
                "nama": "Nuraini A",
                "nisn": "3071512187",
                "total": 523.37
            },
            {
                "listNilai": {
                    "figural": 166.67,
                    "kuantitatif": 89.01,
                    "penalaran": 100,
                    "verbal": 166.68
                },
                "nama": "Siti Hadizah",
                "nisn": "0052418071",
                "total": 522.36
            },
            {
                "listNilai": {
                    "figural": 142.86,
                    "kuantitatif": 29.67,
                    "penalaran": 100,
                    "verbal": 208.35
                },
                "nama": "Ahmad Fadlan",
                "nisn": "3097012709",
                "total": 480.88
            },
            {
                "listNilai": {
                    "figural": 47.62,
                    "kuantitatif": 89.01,
                    "penalaran": 200,
                    "verbal": 125.01
                },
                "nama": "Muhammad Rizal Fatoni",
                "nisn": "0078019829",
                "total": 461.64
            },
            {
                "listNilai": {
                    "figural": 142.86,
                    "kuantitatif": 118.68,
                    "penalaran": 100,
                    "verbal": 83.34
                },
                "nama": "Noraini",
                "nisn": "3083779436",
                "total": 444.88
            },
            {
                "listNilai": {
                    "figural": 142.86,
                    "kuantitatif": 29.67,
                    "penalaran": 200,
                    "verbal": 41.67
                },
                "nama": "Norman",
                "nisn": "0073754988",
                "total": 414.2
            },
            {
                "listNilai": {
                    "figural": 119.05,
                    "kuantitatif": 148.35,
                    "penalaran": 100,
                    "verbal": 41.67
                },
                "nama": "Selia",
                "nisn": "0062838429",
                "total": 409.07
            },
            {
                "listNilai": {
                    "figural": 166.67,
                    "kuantitatif": 59.34,
                    "penalaran": 0,
                    "verbal": 166.68
                },
                "nama": "Luna Maya",
                "nisn": "0075745967",
                "total": 392.69
            },
            {
                "listNilai": {
                    "figural": 166.67,
                    "kuantitatif": 59.34,
                    "penalaran": 0,
                    "verbal": 166.68
                },
                "nama": "Siti Aisah",
                "nisn": "0084759546",
                "total": 392.69
            },
            {
                "listNilai": {
                    "figural": 214.29,
                    "kuantitatif": 59.34,
                    "penalaran": 0,
                    "verbal": 83.34
                },
                "nama": "Roynaldo C.W",
                "nisn": "0089988234",
                "total": 356.97
            },
            {
                "listNilai": {
                    "figural": 166.67,
                    "kuantitatif": 59.34,
                    "penalaran": 0,
                    "verbal": 125.01
                },
                "nama": "M.Arifin Ilham",
                "nisn": "0059052491",
                "total": 351.02
            },
            {
                "listNilai": {
                    "figural": 71.43,
                    "kuantitatif": 59.34,
                    "penalaran": 200,
                    "verbal": 0
                },
                "nama": "Noni",
                "nisn": "0085692202",
                "total": 330.77
            },
            {
                "listNilai": {
                    "figural": 119.05,
                    "kuantitatif": 118.68,
                    "penalaran": 0,
                    "verbal": 83.34
                },
                "nama": "M.Panki Erisman",
                "nisn": "0084112788",
                "total": 321.07
            },
            {
                "listNilai": {
                    "figural": 71.43,
                    "kuantitatif": 59.34,
                    "penalaran": 100,
                    "verbal": 83.34
                },
                "nama": "Muhamad Rengki",
                "nisn": "0085305124",
                "total": 314.11
            },
            {
                "listNilai": {
                    "figural": 119.05,
                    "kuantitatif": 89.01,
                    "penalaran": 0,
                    "verbal": 83.34
                },
                "nama": "Alpianor",
                "nisn": "3088979054",
                "total": 291.4
            },
            {
                "listNilai": {
                    "figural": 119.05,
                    "kuantitatif": 59.34,
                    "penalaran": 0,
                    "verbal": 83.34
                },
                "nama": "Fiola",
                "nisn": "00825052525",
                "total": 261.73
            },
            {
                "listNilai": {
                    "figural": 71.43,
                    "kuantitatif": 59.34,
                    "penalaran": 0,
                    "verbal": 125.01
                },
                "nama": "Putri Faza Najwa",
                "nisn": "3084548483",
                "total": 255.78
            }
        ]
    }
    
```


## AKSAMEDIA TEST BACKEND DOCUMENT - THANKFULL


