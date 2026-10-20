<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MataKuliahController extends Controller
{
    public function index(Request $request)
    {
        $daftarKurikulum = config('matkul.kurikulum');
        $data = config('matkul.data');

        // ?kurikulum=2026&semester=1 -> divalidasi, kalau tidak valid pakai default
        $kurikulum = in_array($request->query('kurikulum'), $daftarKurikulum, true)
            ? $request->query('kurikulum')
            : $daftarKurikulum[0];

        $semesterTersedia = array_keys($data[$kurikulum] ?? []) ?: [1];
        $semester = (int) $request->query('semester', $semesterTersedia[0]);
        if (! in_array($semester, $semesterTersedia, true)) {
            $semester = $semesterTersedia[0]; // mis. ganti kurikulum yang tidak punya semester itu
        }

        // Baris config: [kode, nama, sks_teori, sks_praktek, jam_teori, jam_praktek]
        $courses = collect($data[$kurikulum][$semester] ?? [])->map(fn ($r) => [
            'kode' => $r[0],
            'nama' => $r[1],
            'sks_teori' => $r[2],
            'sks_praktek' => $r[3],
            'jam_teori' => $r[4],
            'jam_praktek' => $r[5],
        ])->all();

        $total = [
            'sks_teori' => array_sum(array_column($courses, 'sks_teori')),
            'sks_praktek' => array_sum(array_column($courses, 'sks_praktek')),
            'jam_teori' => array_sum(array_column($courses, 'jam_teori')),
            'jam_praktek' => array_sum(array_column($courses, 'jam_praktek')),
        ];

        // Dropdown untuk view: @foreach ($filters as $f)
        $filters = [
            [
                'name' => 'kurikulum',
                'label' => 'Pilih Kurikulum',
                'selected' => $kurikulum,
                'options' => collect($daftarKurikulum)->mapWithKeys(fn ($k) => [$k => "Kurikulum $k"])->all(),
            ],
            [
                'name' => 'semester',
                'label' => 'Pilih Semester',
                'selected' => $semester,
                'options' => collect($semesterTersedia)->mapWithKeys(fn ($s) => [$s => "Semester $s"])->all(),
            ],
        ];

        $stats = [
            ['value' => count($courses), 'label' => 'Mata Kuliah'],
            ['value' => $total['sks_teori'] + $total['sks_praktek'], 'label' => 'Total SKS'],
            ['value' => $total['jam_teori'] + $total['jam_praktek'], 'label' => 'Total Jam'],
        ];

        return view('mata-kuliah', compact('semester', 'filters', 'stats', 'courses', 'total'));
    }
}