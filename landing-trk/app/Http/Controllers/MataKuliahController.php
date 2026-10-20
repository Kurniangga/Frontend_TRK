<?php

namespace App\Http\Controllers;

use App\Repositories\MataKuliahRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MataKuliahController extends Controller
{
    public function __construct(private MataKuliahRepository $repo) {}

    /** Halaman penuh (render pertama / tanpa JavaScript). */
    public function index(Request $request)
    {
        return view('mata-kuliah', $this->state($request));
    }

    /** Dipanggil JavaScript saat dropdown diganti: mengembalikan potongan HTML + opsi semester. */
    public function data(Request $request): JsonResponse
    {
        $s = $this->state($request);

        return response()->json([
            'kurikulum' => $s['kurikulumId'],
            'semester' => $s['semester'],
            'semesterOptions' => collect($s['semesterList'])
                ->map(fn ($n) => ['value' => $n, 'label' => "Semester $n"])->values(),
            'statsHtml' => view('mata-kuliah._stats', $s)->render(),
            'tableHtml' => view('mata-kuliah._tabel', $s)->render(),
        ]);
    }

    /** Menyusun semua data yang dibutuhkan view dari ?kurikulum=..&semester=.. */
    private function state(Request $request): array
    {
        $daftar = $this->repo->kurikulum();
        $ids = array_column($daftar, 'id');

        $kurikulumId = in_array($request->query('kurikulum'), $ids, true)
            ? $request->query('kurikulum')
            : ($ids[0] ?? null);

        $semesterList = $kurikulumId ? $this->repo->semester($kurikulumId) : [];

        // semester yang diminta tidak ada di kurikulum ini -> pakai semester pertama yang ada
        $semester = (int) $request->query('semester');
        if (! in_array($semester, $semesterList, true)) {
            $semester = $semesterList[0] ?? 1;
        }

        $courses = $kurikulumId ? $this->repo->matkul($kurikulumId, $semester) : [];

        $total = [
            'sks_teori' => array_sum(array_column($courses, 'sks_teori')),
            'sks_praktek' => array_sum(array_column($courses, 'sks_praktek')),
            'jam_teori' => array_sum(array_column($courses, 'jam_teori')),
            'jam_praktek' => array_sum(array_column($courses, 'jam_praktek')),
        ];

        return [
            'kurikulumId' => $kurikulumId,
            'semester' => $semester,
            'semesterList' => $semesterList,
            'filters' => [
                [
                    'name' => 'kurikulum',
                    'label' => 'Pilih Kurikulum',
                    'selected' => $kurikulumId,
                    'options' => collect($daftar)->pluck('nama', 'id')->all(),
                ],
                [
                    'name' => 'semester',
                    'label' => 'Pilih Semester',
                    'selected' => $semester,
                    'options' => collect($semesterList)->mapWithKeys(fn ($n) => [$n => "Semester $n"])->all(),
                ],
            ],
            'stats' => [
                ['value' => count($courses), 'label' => 'Mata Kuliah'],
                ['value' => $total['sks_teori'] + $total['sks_praktek'], 'label' => 'Total SKS'],
                ['value' => $total['jam_teori'] + $total['jam_praktek'], 'label' => 'Total Jam'],
            ],
            'courses' => $courses,
            'total' => $total,
        ];
    }
}