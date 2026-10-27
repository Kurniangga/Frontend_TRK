<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Route;
class DashboardController extends Controller
{
    public function index()
    {
        // TODO-DB: ganti data contoh ini dengan query database / repository.
        // 'route' = tujuan klik kartu. SELALU berawalan "admin." supaya tidak pernah melintas ke dashboard peran lain.
        // Route::has: kartu tetap tampil (tanpa link) bila halaman tujuan belum ada.
        $url = fn (string $nama) => Route::has($nama) ? route($nama) : null;

        $stats = [
            ['label' => 'Dosen Aktif',       'value' => 24, 'url' => $url('admin.dosen')],
            ['label' => 'Mahasiswa Aktif',   'value' => 67, 'url' => $url('admin.mahasiswa')],
            ['label' => 'Jumlah Kegiatan',   'value' => 32, 'url' => $url('admin.kegiatan.dosen')],
            ['label' => 'Menunggu Validasi', 'value' => 24, 'red' => true, 'url' => $url('admin.pengajuan')],
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
            ['kegiatan' => 'Kuliah Tamu',         'dosen' => 'Kuwat Santoso',       'tanggal' => '3 hari yang lalu'],
        ];

        return view('admin.dashboard', compact('stats', 'kegiatanPerTahun', 'rows'));
    }

    /** Halaman menu sidebar yang isinya belum dibuat. */
    public function placeholder(\Illuminate\Http\Request $request)
    {
        $nama = $request->route()->getName();
        $judul = 'Halaman';

        foreach (config('admin.menu') as $m) {
            foreach (isset($m['children']) ? $m['children'] : [$m] as $item) {
                if (($item['route'] ?? null) === $nama) {
                    $judul = $item['label'];
                }
            }
        }

        return view('admin.placeholder', compact('judul'));
    }
}