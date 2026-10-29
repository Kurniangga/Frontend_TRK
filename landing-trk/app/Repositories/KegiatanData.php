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

        return $this->tanpaDihapus('dosen', $this->susun($kegiatan, 'dosen', $dosen));
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

        return $this->tanpaDihapus('mahasiswa', $this->susun($kegiatan, 'pengaju', $pengaju, true));
    }

    /** Cari satu kegiatan menurut id; null bila tidak ada / sudah dihapus. */
    public function cari(string $jenis, string $id): ?array
    {
        $rows = $jenis === 'dosen' ? $this->dosen() : $this->mahasiswa();
        foreach ($rows as $r) {
            if ($r['id'] === $id) {
                return $r;
            }
        }

        return null;
    }

    /**
     * Hapus kegiatan (simulasi): id dicatat di storage/app/private/admin/kegiatan-dihapus.json.
     * Admin & kaprodi memakai catatan yang sama, jadi hasilnya konsisten di daftar dan laporan.
     * TODO-DB: ganti dengan DELETE pada tabel kegiatan.
     */
    public function hapus(string $jenis, string $id): bool
    {
        if ($this->cari($jenis, $id) === null) {
            return false;
        }
        $semua = $this->dihapus();
        $semua[$jenis][] = $id;
        $semua[$jenis] = array_values(array_unique($semua[$jenis]));

        $file = $this->fileDihapus();
        if (! is_dir(dirname($file))) {
            mkdir(dirname($file), 0775, true);
        }
        file_put_contents($file, json_encode($semua, JSON_PRETTY_PRINT), LOCK_EX);

        return true;
    }

    private function fileDihapus(): string
    {
        return storage_path('app/private/admin/kegiatan-dihapus.json');
    }

    private function dihapus(): array
    {
        $file = $this->fileDihapus();
        $data = is_file($file) ? json_decode((string) file_get_contents($file), true) : [];
        $data = is_array($data) ? $data : [];

        return ['dosen' => $data['dosen'] ?? [], 'mahasiswa' => $data['mahasiswa'] ?? []];
    }

    private function tanpaDihapus(string $jenis, array $rows): array
    {
        $hapus = $this->dihapus()[$jenis];

        return array_values(array_filter($rows, fn ($r) => ! in_array($r['id'], $hapus, true)));
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
                'raw_date' => $tgl->format('Y-m-d'), // dipakai pengurutan Terbaru/Terlama di daftar
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
