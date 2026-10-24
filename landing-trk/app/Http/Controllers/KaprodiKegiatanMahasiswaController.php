<?php

namespace App\Http\Controllers;

use Carbon\Carbon;

class KaprodiKegiatanMahasiswaController extends Controller
{
    /** (Kaprodi) Daftar kegiatan mahasiswa (pengajuan dari mahasiswa untuk divalidasi admin). */
    public function index()
    {
        return view('kaprodi.kegiatan-mahasiswa', ['rows' => $this->daftar()]);
    }

    /** TODO-DB: ganti dengan query tabel kegiatan mahasiswa. */
    private function daftar(): array
    {
        $kegiatan = [
            ['Kompetisi Pengembangan Aplikasi Web', 'Lomba'],
            ['Magang Industri Semester Genap',      'Magang'],
            ['Kunjungan Industri ke Semarang',      'Kunjungan Industri'],
            ['Hackathon Nasional Polines',          'Lomba'],
            ['Magang Developer Backend',            'Magang'],
            ['Studi Banding Pusat Data',            'Kunjungan Industri'],
            ['Lomba Desain Antarmuka Pengguna',     'Lomba'],
            ['Magang Jaringan dan Keamanan',        'Magang'],
        ];
        $pengaju = ['Rizky Pratama', 'Aulia Rahmawati', 'Bagas Setiawan', 'Citra Lestari', 'Fajar Nugroho', 'Indah Permata'];
        $status = ['Diajukan', 'Direvisi', 'Disetujui', 'Ditolak', 'Disetujui', 'Diajukan'];

        $rows = [];
        for ($i = 0; $i < 45; $i++) {
            $k = $kegiatan[$i % 8];
            $rows[] = [
                'id' => (string) ($i + 1),
                'nama' => $k[0],
                'kategori' => $k[1],
                'pengaju' => $pengaju[$i % 6],
                'tanggal' => Carbon::create(2026, 1 + ($i % 12), 1 + (($i * 5) % 28))->locale('id')->translatedFormat('j F Y'),
                'status' => $status[$i % 6],
                'semester' => $i % 2 === 0 ? 'Ganjil' : 'Genap',
                'tingkat' => (string) (1 + ($i % 3)),
                'kelas' => $i % 4 < 2 ? 'A' : 'B',
            ];
        }

        return $rows;
    }
}
