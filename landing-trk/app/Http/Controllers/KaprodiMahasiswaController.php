<?php

namespace App\Http\Controllers;

use App\Repositories\MahasiswaStore;
use Carbon\Carbon;
use Illuminate\Support\Str;

/** Data mahasiswa untuk kaprodi: HANYA BACA (tanpa tambah / ubah / hapus). Data sama dengan admin. */
class KaprodiMahasiswaController extends Controller
{
    public function __construct(private MahasiswaStore $store) {}

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
        ], $this->store->all());

        return view('kaprodi.mahasiswa', ['students' => $students, 'levels' => MahasiswaStore::TINGKAT]);
    }

    /** Detail satu mahasiswa. */
    public function show(string $id)
    {
        $row = $this->store->find($id);
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
            ['Tanggal lahir', $row['tanggal_lahir'] ? Carbon::parse($row['tanggal_lahir'])->locale('id')->translatedFormat('j F Y') : '-'],
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

        return view('kaprodi.mahasiswa-detail', compact('mahasiswa', 'academic', 'identity', 'history', 'kegiatan'));
    }
}