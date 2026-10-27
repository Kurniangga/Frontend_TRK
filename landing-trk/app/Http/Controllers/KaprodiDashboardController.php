<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

class KaprodiDashboardController extends Controller
{
    public function index()
    {
        // TODO-DB: ganti data contoh ini dengan query database / repository.
        // 'route' = tujuan klik kartu. SELALU berawalan "kaprodi." supaya tidak pernah melintas ke dashboard peran lain.
        // Route::has: kartu tetap tampil (tanpa link) bila halaman tujuan belum ada.
        $url = fn (string $nama) => Route::has($nama) ? route($nama) : null;

        $stats = [
            ['label' => 'Dosen Aktif',       'value' => 24, 'url' => $url('kaprodi.dosen')],
            ['label' => 'Mahasiswa Aktif',   'value' => 67, 'url' => $url('kaprodi.mahasiswa')],
            ['label' => 'Jumlah Kegiatan',   'value' => 32, 'url' => $url('kaprodi.kegiatan.dosen')],
            ['label' => 'Menunggu Validasi', 'value' => 24, 'red' => true, 'url' => $url('kaprodi.pengajuan')],
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