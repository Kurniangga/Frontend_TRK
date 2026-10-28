<?php

namespace App\Http\Controllers;

use App\Repositories\KegiatanData;

class KegiatanMahasiswaController extends Controller
{
    public function __construct(private KegiatanData $data) {}

    /** Daftar kegiatan mahasiswa (admin): semua status, untuk divalidasi. */
    public function index()
    {
        $rows = $this->data->mahasiswa();


       return view('admin.kegiatan-mahasiswa', [
            'rows' => $rows,
            'tahunList' => $this->data->tahun($rows),
        ]);
    } // <-- Pastikan ada kurung kurawal tutup di sini untuk mengakhiri function index()

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

        $rows = [];
        for ($i = 0; $i < 45; $i++) {
            $k = $kegiatan[$i % 8];
            $dt = \Carbon\Carbon::create(2026, 1 + ($i % 12), 1 + (($i * 5) % 28));
            $rows[] = [
                'id' => (string) ($i + 1),
                'nama' => $k[0],
                'kategori' => $k[1],
                'pengaju' => $pengaju[$i % 6],
                'tanggal' => $dt->clone()->locale('id')->translatedFormat('j F Y'),
                'raw_date' => $dt->toDateString(), // Ditambahkan untuk acuan sorting JavaScript
                'status' => 'Disetujui',
                'semester' => $i % 2 === 0 ? 'Ganjil' : 'Genap',
                'tingkat' => (string) (1 + ($i % 3)),
                'kelas' => $i % 4 < 2 ? 'A' : 'B',
            ];
        }

        return $rows;

    }
}