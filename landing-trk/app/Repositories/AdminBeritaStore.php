<?php

namespace App\Repositories;

use Carbon\Carbon;

/**
 * Data berita untuk halaman admin & kaprodi (SIMULASI: array contoh).
 * TODO-DB: ganti isi method dengan query tabel berita (nama method & bentuk array tetap).
 *
 * Satu baris: id, judul, kegiatan (kategori), tanggal (j F Y), status (Publish|Draft)
 */
class AdminBeritaStore
{
    public const KEGIATAN = ['Prestasi Mahasiswa', 'Prestasi Dosen', 'Kunjungan Industri', 'Info Akademik', 'Lomba', 'Magang'];
    public const STATUS = ['Publish', 'Draft'];

    /** @return array<int, array<string, string>> */
    public function all(): array
    {
        $judul = [
            ['Mahasiswa TRK Raih Juara 1 Nasional Web Design', 'Prestasi Mahasiswa'],
            ['Kunjungan Industri ke PT. Teknologi Maju 2026', 'Kunjungan Industri'],
            ['Pengumuman Libur Semester Ganjil 2026/2027', 'Info Akademik'],
            ['Dosen TRK Memenangkan Hibah Penelitian Dikti', 'Prestasi Dosen'],
            ['Dosen TRK menemukan inovasi', 'Lomba'],
            ['Kunjungan Industri ke Shanghai China', 'Kunjungan Industri'],
            ['Mahasiswa TRK raih medali emas lomba Cipta Karya Inovasi', 'Lomba'],
            ['Mahasiswa TRK berhasil membawa pulang piala dari ITS', 'Lomba'],
            ['Dosen TRK bersama Mahasiswa TRK berkunjung di kawasan Industri Denpasar', 'Kunjungan Industri'],
            ['Mahasiswa TRK tembus magang di Google dan menciptakan inovasi sistem pengamanan yang efisien', 'Magang'],
        ];

        $rows = [];
        for ($i = 0; $i < 48; $i++) {
            $j = $judul[$i % 10];
            $rows[] = [
                'id' => (string) ($i + 1),
                'judul' => $j[0],
                'kegiatan' => $j[1],
                'tanggal' => Carbon::create(2026, 9 - ($i % 6), 1 + (($i * 7) % 28))->locale('id')->translatedFormat('j F Y'),
                'status' => $i % 6 === 5 ? 'Draft' : 'Publish',   // 40 Publish, 8 Draft
            ];
        }

        return $rows;
    }
}
