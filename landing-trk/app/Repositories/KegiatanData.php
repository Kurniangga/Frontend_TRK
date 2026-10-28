<?php

namespace App\Repositories;

use Carbon\Carbon;

/**
 * Sumber data daftar kegiatan dosen & mahasiswa (SIMULASI, 45 baris per jenis, tahun 2024-2026).
 * Dipakai bersama oleh halaman daftar (admin & kaprodi) dan laporan PDF/Excel,
 * jadi angka di daftar dan di laporan selalu sama.
 *
 * Satu baris: id, nama, kategori, (dosen | pengaju), tanggal (tampilan), tanggal_iso (Y-m-d), tahun (int),
 *             status (Diajukan|Direvisi|Disetujui|Ditolak), semester (Ganjil|Genap), [tingkat, kelas] khusus mahasiswa.
 *
 * TODO-DB: ganti isi method dengan query tabel kegiatan (nama method & bentuk array dijaga tetap).
 */
class KegiatanData
{
    public const STATUS = ['Diajukan', 'Direvisi', 'Disetujui', 'Ditolak'];

    /** Hanya kegiatan yang sudah disetujui (dipakai dashboard kaprodi). */
    public const DISETUJUI = 'Disetujui';

    public function dosen(): array
    {
        $kegiatan = [
            ['Seminar Nasional Kecerdasan Buatan', 'Seminar'],
            ['Penelitian Internet of Things', 'Penelitian'],
            ['Pengabdian Pelatihan Web Desa', 'Pengabdian'],
            ['Pelatihan Sertifikasi Jaringan', 'Pelatihan'],
            ['Penelitian Keamanan Siber', 'Penelitian'],
            ['Seminar Pendidikan Vokasi', 'Seminar'],
            ['Pengabdian Literasi Digital', 'Pengabdian'],
            ['Pelatihan Pengajaran Berbasis Proyek', 'Pelatihan'],
        ];
        $dosen = ['Wiktasari', 'Kuwat Santoso', 'Tahan Prahara', 'Filda Huwani Dewi', 'Angga Wahyu Wibowo', 'Mukhlis'];

        return $this->susun($kegiatan, 'dosen', $dosen);
    }

    public function mahasiswa(): array
    {
        $kegiatan = [
            ['Kompetisi Pengembangan Aplikasi Web', 'Lomba'],
            ['Magang Industri Semester Genap', 'Magang'],
            ['Kunjungan Industri ke Semarang', 'Kunjungan Industri'],
            ['Hackathon Nasional Polines', 'Lomba'],
            ['Magang Developer Backend', 'Magang'],
            ['Studi Banding Pusat Data', 'Kunjungan Industri'],
            ['Lomba Desain Antarmuka Pengguna', 'Lomba'],
            ['Magang Jaringan dan Keamanan', 'Magang'],
        ];
        $pengaju = ['Rizky Pratama', 'Aulia Rahmawati', 'Bagas Setiawan', 'Citra Lestari', 'Fajar Nugroho', 'Indah Permata'];

        return $this->susun($kegiatan, 'pengaju', $pengaju, true);
    }

    /** Hanya baris berstatus Disetujui. */
    public function disetujui(array $rows): array
    {
        return array_values(array_filter($rows, fn ($r) => $r['status'] === self::DISETUJUI));
    }

    /** Tahun yang ada di data, urut naik. @return int[] */
    public function tahun(array $rows): array
    {
        $tahun = array_values(array_unique(array_column($rows, 'tahun')));
        sort($tahun);

        return $tahun;
    }

    private function susun(array $kegiatan, string $kolomOrang, array $orang, bool $mahasiswa = false): array
    {
        $status = ['Diajukan', 'Direvisi', 'Disetujui', 'Ditolak', 'Disetujui', 'Diajukan'];

        $rows = [];
        for ($i = 0; $i < 45; $i++) {
            $k = $kegiatan[$i % 8];
            // 15 kegiatan per tahun (2024, 2025, 2026); status berputar sehingga tiap tahun punya semua status.
            $tgl = Carbon::create(2024 + intdiv($i, 15), 1 + ($i % 12), 1 + (($i * 5) % 28));

            $row = [
                'id' => (string) ($i + 1),
                'nama' => $k[0],
                'kategori' => $k[1],
                $kolomOrang => $orang[$i % 6],
                'tanggal' => $tgl->copy()->locale('id')->translatedFormat('j F Y'),
                'tanggal_iso' => $tgl->format('Y-m-d'),
                'tahun' => (int) $tgl->format('Y'),
                'status' => $status[$i % 6],
                'semester' => $i % 2 === 0 ? 'Ganjil' : 'Genap',
            ];

            if ($mahasiswa) {
                $row['tingkat'] = (string) (1 + ($i % 3));
                $row['kelas'] = $i % 4 < 2 ? 'A' : 'B';
            }

            $rows[] = $row;
        }

        return $rows;
    }
}
