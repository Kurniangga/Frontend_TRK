<?php

namespace App\Http\Controllers;

use App\Repositories\MataKuliahRepository;
use Illuminate\Http\Request;

class AdminMataKuliahController extends Controller
{
    private const ROMAWI = [1 => 'I', 2 => 'II', 3 => 'III', 4 => 'IV', 5 => 'V', 6 => 'VI', 7 => 'VII', 8 => 'VIII'];

    /** Dipakai bersama halaman publik: data lewat MataKuliahRepository (binding di AppServiceProvider). */
    public function __construct(private MataKuliahRepository $repo) {}

    /** Daftar mata kuliah per kurikulum & semester (?kurikulum=..&semester=..&q=..). */
    public function index(Request $request)
    {
        $daftar = $this->repo->kurikulum();
        $ids = array_column($daftar, 'id');

        $kurikulumId = in_array($request->query('kurikulum'), $ids, true) ? $request->query('kurikulum') : ($ids[0] ?? null);
        $semesterList = $kurikulumId ? $this->repo->semester($kurikulumId) : [];

        $semester = (int) $request->query('semester');
        if (! in_array($semester, $semesterList, true)) {
            $semester = $semesterList[0] ?? 1;
        }

        $semua = $kurikulumId ? $this->repo->matkul($kurikulumId, $semester) : [];

        // Pencarian kode / nama (server-side supaya rowspan semester & sub total tetap benar).
        $q = trim((string) $request->query('q'));
        $tampil = $q === '' ? $semua : array_values(array_filter($semua, fn ($r) =>
            stripos($r['kode'], $q) !== false || stripos($r['nama'], $q) !== false
        ));

        // Sub total selalu dihitung dari SEMUA mata kuliah semester ini, bukan hasil cari.
        $total = [
            'sks_teori' => array_sum(array_column($semua, 'sks_teori')),
            'sks_praktek' => array_sum(array_column($semua, 'sks_praktek')),
            'jam_teori' => array_sum(array_column($semua, 'jam_teori')),
            'jam_praktek' => array_sum(array_column($semua, 'jam_praktek')),
        ];

        return view('admin.mata-kuliah', [
            'kurikulumList' => $daftar,
            'kurikulumId' => $kurikulumId,
            'semesterList' => $semesterList,
            'semester' => $semester,
            'romawi' => self::ROMAWI[$semester] ?? (string) $semester,
            'q' => $q,
            'courses' => $tampil,
            'jumlahSemua' => count($semua),
            'total' => $total,
        ]);
    }
}
