<?php

namespace App\Http\Controllers;

use Carbon\Carbon;

class KegiatanDosenController extends Controller
{
    /** Daftar kegiatan dosen (pengajuan dari dosen untuk divalidasi admin). */
    public function index()
    {
        return view('admin.kegiatan-dosen', ['rows' => $this->daftar()]);
    }

    /** TODO-DB: ganti dengan query tabel kegiatan dosen. */
    private function daftar(): array
    {
        $kegiatan = [
            ['Seminar Nasional Kecerdasan Buatan', 'Seminar'],
            ['Penelitian Internet of Things',      'Penelitian'],
            ['Pengabdian Pelatihan Web Desa',      'Pengabdian'],
            ['Pelatihan Sertifikasi Jaringan',     'Pelatihan'],
            ['Penelitian Keamanan Siber',          'Penelitian'],
            ['Seminar Pendidikan Vokasi',          'Seminar'],
            ['Pengabdian Literasi Digital',        'Pengabdian'],
            ['Pelatihan Pengajaran Berbasis Proyek', 'Pelatihan'],
        ];
        $dosen  = ['Wiktasari', 'Kuwat Santoso', 'Tahan Prahara', 'Filda Huwani Dewi', 'Angga Wahyu Wibowo', 'Mukhlis'];
        $status = ['Diajukan', 'Direvisi', 'Disetujui', 'Ditolak', 'Disetujui', 'Diajukan'];

        $rows = [];
        for ($i = 0; $i < 45; $i++) {
            $k = $kegiatan[$i % 8];
            $rows[] = [
                'id' => (string) ($i + 1),
                'nama' => $k[0],
                'kategori' => $k[1],
                'dosen' => $dosen[$i % 6],
                'tanggal' => Carbon::create(2026, 1 + ($i % 12), 1 + (($i * 5) % 28))->locale('id')->translatedFormat('j F Y'),
                'status' => $status[$i % 6],
                'semester' => $i % 2 === 0 ? 'Ganjil' : 'Genap',
            ];
        }

        return $rows;
    }
}
