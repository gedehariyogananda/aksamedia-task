<?php

namespace App\Http\Controllers;

use App\Models\Nilai;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class NilaiController extends Controller
{
    // ---------------------- TASK TAMBAHAN NILAI (NILAI RT) ------------------------//

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

    // ---------------------- TASK TAMBAHAN NILAI (NILAI ST) ------------------------//

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
}
