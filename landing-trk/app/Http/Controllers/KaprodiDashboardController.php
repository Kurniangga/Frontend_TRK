<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class KaprodiDashboardController extends Controller
{
    public function index()
    {
        // TODO-DB: ganti data contoh ini dengan query database / repository.
        $stats = [
            ['label' => 'Dosen Aktif',       'value' => 24],
            ['label' => 'Mahasiswa Aktif',   'value' => 67],
            ['label' => 'Jumlah Kegiatan',   'value' => 32],
            ['label' => 'Menunggu Validasi', 'value' => 24, 'red' => true],
        ];

        $kegiatanPerTahun = [
            '2022' => 7, '2023' => 17, '2024' => 10, '2025' => 20,
            '2026' => 14, '2027' => 16, '2028' => 9,
        ];

        $rows = [
            ['kegiatan' => 'Seminar Nasional AI', 'dosen' => 'Angga Wahyu Wibowo', 'tanggal' => '2 hari yang lalu'],
            ['kegiatan' => 'Pelatihan Industri',  'dosen' => 'Kuwat Santoso',       'tanggal' => '2 hari yang lalu'],
            ['kegiatan' => 'Kunjungan Industri',  'dosen' => 'Wiktasari',           'tanggal' => '3 hari yang lalu'],
            ['kegiatan' => 'Pelatihan Industri',  'dosen' => 'Tahan Prahara',       'tanggal' => '3 hari yang lalu'],
        ];

        return view('kaprodi.dashboard', compact('stats', 'kegiatanPerTahun', 'rows'));
    }

    /** Halaman menu sidebar kaprodi yang isinya belum dibuat. */
    public function placeholder(Request $request)
    {
        $nama = $request->route()->getName();
        $judul = 'Halaman';

        foreach (config('kaprodi.menu') as $m) {
            foreach ($m['children'] ?? [$m] as $item) {
                if (($item['route'] ?? null) === $nama) {
                    $judul = $item['label'];
                }
            }
        }

        return view('kaprodi.placeholder', compact('judul'));
    }
}
