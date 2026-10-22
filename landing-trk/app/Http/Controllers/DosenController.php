<?php

namespace App\Http\Controllers;

use Illuminate\Support\Str;

class DosenController extends Controller
{
    private const JABATAN = ['Asisten Ahli', 'Lektor', 'Lektor Kepala', 'Guru Besar'];

    /** Halaman daftar dosen (admin). */
    public function index()
    {
        return view('admin.dosen', ['dosen' => $this->daftar(), 'jabatan' => self::JABATAN]);
    }

    /** Halaman detail satu dosen: profil, riwayat pangkat, dan aktivitas (4 tab). */
    public function show(string $id)
    {
        $row = collect($this->daftar())->firstWhere('id', $id);
        abort_unless($row, 404);

        // TODO-DB: semua data di bawah ini (selain $row) diganti query database berdasarkan $id.
        $dosen = $row + [
            'foto' => null,   // URL foto dosen; null = tampil inisial
        ];

        $info = [
            ['Email akun', Str::of($row['nama'])->lower()->replaceMatches('/[^a-z]/', '').'@polines.ac.id'],
            ['Role', 'Dosen'],
            ['Pangkat / golongan saat ini', $row['jabatan'].', '.$row['golongan']],
            ['Akun login', $row['status'] === 'aktif' ? 'Aktif' : 'Nonaktif'],
        ];

        $riwayat = [
            ['pangkat' => 'Ketua Prodi',  'golongan' => 'IV/a',  'sk' => '1042/UN/KP/2022', 'tanggal' => '12 Januari 2022', 'now' => true],
            ['pangkat' => 'Lektor',       'golongan' => 'III/d', 'sk' => '0788/UN/KP/2018', 'tanggal' => '1 Oktober 2018'],
            ['pangkat' => 'Lektor',       'golongan' => 'III/c', 'sk' => '0415/UN/KP/2015', 'tanggal' => '1 April 2015'],
            ['pangkat' => 'Asisten Ahli', 'golongan' => 'III/b', 'sk' => '0120/UN/KP/2012', 'tanggal' => '1 Maret 2012'],
        ];

        // Urutan tab = urutan di array ini. Tiap item: title, year, type, role
        $aktivitas = [
            'penelitian' => ['label' => 'Penelitian', 'items' => [
                ['title' => 'Deteksi hoaks dengan machine learning', 'year' => '2026', 'type' => 'Penelitian Terapan',      'role' => 'Ketua'],
                ['title' => 'Analisis sentimen ulasan aplikasi',     'year' => '2025', 'type' => 'Penelitian Dasar',        'role' => 'Anggota'],
                ['title' => 'Sistem rekomendasi mata kuliah',        'year' => '2025', 'type' => 'Penelitian Pengembangan', 'role' => 'Anggota'],
            ]],
            'pengabdian' => ['label' => 'Pengabdian', 'items' => [
                ['title' => 'Pelatihan sensor sederhana pada sistem terbenam', 'year' => '2025', 'type' => 'Pengabdian kepada Masyarakat', 'role' => 'Ketua'],
                ['title' => 'Pendampingan digitalisasi UMKM',                  'year' => '2024', 'type' => 'Pengabdian kepada Masyarakat', 'role' => 'Anggota'],
            ]],
            'pengajaran' => ['label' => 'Pengajaran', 'items' => [
                ['title' => 'Pemrograman Web Lanjut', 'year' => '2026', 'type' => 'Semester Ganjil · 3 SKS', 'role' => 'Pengampu'],
                ['title' => 'Basis Data II',          'year' => '2026', 'type' => 'Semester Ganjil · 3 SKS', 'role' => 'Pengampu'],
                ['title' => 'Jaringan Komputer',      'year' => '2025', 'type' => 'Semester Genap · 3 SKS',  'role' => 'Koordinator'],
            ]],
            'kegiatan' => ['label' => 'Kegiatan', 'items' => [
                ['title' => 'Seminar Nasional AI',   'year' => '2026', 'type' => 'Seminar',            'role' => 'Pemateri'],
                ['title' => 'Pelatihan Industri',    'year' => '2025', 'type' => 'Pelatihan',          'role' => 'Peserta'],
                ['title' => 'Kunjungan Industri',    'year' => '2025', 'type' => 'Kunjungan Industri', 'role' => 'Pendamping'],
            ]],
        ];

        return view('admin.dosen-detail', compact('dosen', 'info', 'riwayat', 'aktivitas'));
    }

    /** TODO-DB: ganti dengan query tabel dosen. */
    private function daftar(): array
    {
        $contoh = [
            ['198501012010', 'Angga Wahyu Wibowo', 'Lektor Kepala', 'IV/a'],
            ['199003152015', 'Kuwat Santoso',      'Lektor',        'III/c'],
            ['197812202005', 'Tahan Prahara',      'Guru Besar',    'IV/d'],
            ['198501012010', 'Wiktasari',          'Lektor Kepala', 'IV/a'],
            ['199003152015', 'Filda Huwani Dewi',  'Lektor',        'III/c'],
            ['197812202005', 'Angga Wahyu Wibowo', 'Guru Besar',    'IV/d'],
            ['198501012010', 'Kuwat Santoso',      'Lektor Kepala', 'IV/a'],
            ['199003152015', 'Tahan Prahara',      'Lektor',        'III/c'],
            ['197812202005', 'Wiktasari',          'Guru Besar',    'IV/d'],
            ['198501012010', 'Filda Huwani Dewi',  'Lektor Kepala', 'IV/a'],
        ];

        $dosen = [];
        for ($i = 0; $i < 100; $i++) {
            $b = $contoh[$i % 10];
            $dosen[] = [
                'id' => (string) ($i + 1),
                'nip' => $b[0],
                'nama' => $b[1],
                'jabatan' => $b[2],
                'golongan' => $b[3],
                'status' => ($i % 13 === 12) ? 'nonaktif' : 'aktif',
            ];
        }

        return $dosen;
    }
}