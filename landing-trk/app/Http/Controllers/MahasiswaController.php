<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Support\Str;

class MahasiswaController extends Controller
{
    private const TINGKAT = ['Tingkat 1', 'Tingkat 2', 'Tingkat 3', 'Tingkat 4'];

    /** Daftar mahasiswa. */
    public function index()
    {
        $students = array_map(fn ($r) => [
            'id' => $r['id'],
            'nim' => $r['nim'],
            'name' => $r['nama'],
            'level' => $r['level'],
            'className' => $r['kelas'],
            'status' => $r['status'],
        ], $this->daftar());

        return view('admin.mahasiswa', ['students' => $students, 'levels' => self::TINGKAT]);
    }

    /** Detail satu mahasiswa. */
    public function show(string $id)
    {
        $row = collect($this->daftar())->firstWhere('id', $id);
        abort_unless($row, 404);

        // TODO-DB: riwayat & kegiatan diganti query database berdasarkan $id.
        $mahasiswa = ['id' => $row['id'], 'nama' => $row['nama'], 'nim' => $row['nim'], 'status' => $row['status']];

        $academic = [
            ['Angkatan', $row['angkatan']],
            ['Program studi', 'Teknik Informatika, D3'],
            ['Tingkat saat ini', $row['level']],
            ['Kelas saat ini', Str::after($row['kelas'], 'TI-')],
        ];

        $identity = [
            ['Email akun', $row['email']],
            ['Tanggal lahir', $row['tanggal_lahir']],
            ['No. telepon', $row['telepon']],
            ['Akun login', 'Aktif'],
        ];

        $history = [
            ['year' => '2025/2026', 'current' => true, 'sem' => 'Ganjil', 'prodi' => 'Teknik Informatika', 'level' => $row['level'], 'cls' => Str::after($row['kelas'], 'TI-')],
            ['year' => '2024/2025', 'sem' => 'Genap',  'prodi' => 'Teknik Informatika', 'level' => 'Tingkat 1', 'cls' => '2A'],
            ['year' => '2024/2025', 'sem' => 'Ganjil', 'prodi' => 'Teknik Informatika', 'level' => 'Tingkat 1', 'cls' => '2A'],
        ];

        $kegiatan = [
            ['title' => 'Kompetisi Pengembangan Aplikasi Web', 'year' => '2026', 'type' => 'Lomba',    'role' => 'Ketua Tim'],
            ['title' => 'Pelatihan Dasar Jaringan Komputer',   'year' => '2025', 'type' => 'Pelatihan', 'role' => 'Peserta'],
            ['title' => 'Seminar Nasional Teknologi Informasi', 'year' => '2025', 'type' => 'Seminar',  'role' => 'Panitia'],
        ];

        return view('admin.mahasiswa-detail', compact('mahasiswa', 'academic', 'identity', 'history', 'kegiatan'));
    }

    /** TODO-DB: ganti dengan query tabel mahasiswa. */
    private function daftar(): array
    {
        $nama = [
            'Rizky Pratama', 'Aulia Rahmawati', 'Bagas Setiawan', 'Citra Lestari', 'Fajar Nugroho',
            'Indah Permata', 'Galih Prakoso', 'Nadia Safitri', 'Yoga Maulana', 'Putri Ayu Ningrum',
        ];

        $rows = [];
        for ($i = 0; $i < 23; $i++) {
            $n = $nama[$i % 10];
            $rows[] = [
                'id' => (string) ($i + 1),
                'nim' => (string) (3322401001 + $i),
                'nama' => $n,
                'level' => self::TINGKAT[$i % 4],
                'kelas' => 'TI-'.(($i % 4) + 1).['A', 'B', 'C'][$i % 3],
                'status' => ($i % 7 === 6) ? 'Cuti' : 'Aktif',
                'angkatan' => (string) (2022 + ($i % 4)),
                'email' => str_replace('-', '', Str::slug($n)).($i >= 10 ? $i : '').'@mhs.polines.ac.id',
                'tanggal_lahir' => Carbon::create(2003 + ($i % 4), 1 + ($i % 12), 1 + (($i * 3) % 28))->locale('id')->translatedFormat('j F Y'),
                'telepon' => '0856-0037-'.sprintf('%04d', 2000 + $i),
            ];
        }

        return $rows;
    }
}
