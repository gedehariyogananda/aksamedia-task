<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Division;
use App\Models\Employee;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class EmployeeController extends Controller
{
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
}
