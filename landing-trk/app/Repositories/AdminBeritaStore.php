<?php

namespace App\Repositories;

use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

/**
 * Data berita untuk admin & kaprodi (SIMULASI: file JSON storage/app/private/admin/berita.json).
 * TODO-DB: ganti isi method dengan query tabel berita (nama method & bentuk array tetap).
 *
 * Baris: id, judul, kegiatan (kategori), tanggal (Y-m-d|null), status (Publish|Draft),
 *        isi, foto (path disk 'public'|null), sumber (prev|none), kegiatan_id (bila sumber = prev)
 */
class AdminBeritaStore
{
    public const KEGIATAN = ['Prestasi Mahasiswa', 'Prestasi Dosen', 'Kunjungan Industri', 'Info Akademik', 'Lomba', 'Magang'];
    public const STATUS = ['Publish', 'Draft'];

    /** TODO-DB: ganti dengan query tabel kegiatan (mahasiswa/dosen yang sudah divalidasi). */
    public const KEGIATAN_SEBELUMNYA = [
        ['id' => 'k1', 'nama' => 'KMIPN VIII 2026', 'kategori' => 'Lomba'],
        ['id' => 'k2', 'nama' => 'Kunjungan Industri PT. Teknologi Maju', 'kategori' => 'Kunjungan Industri'],
        ['id' => 'k3', 'nama' => 'Magang Google Developer Student', 'kategori' => 'Magang'],
        ['id' => 'k4', 'nama' => 'Hibah Penelitian Dikti 2026', 'kategori' => 'Prestasi Dosen'],
    ];

    private const PATH = 'admin/berita.json';

    /** Untuk halaman daftar: tanggal sudah diformat "12 September 2026". */
    public function all(): array
    {
        return array_map(function ($r) {
            $r['tanggal'] = $r['tanggal'] ? Carbon::parse($r['tanggal'])->locale('id')->translatedFormat('j F Y') : '-';

            return $r;
        }, $this->raw());
    }

    /** Satu baris apa adanya (tanggal Y-m-d). */
    public function find(string $id): ?array
    {
        foreach ($this->raw() as $row) {
            if ($row['id'] === $id) {
                return $row;
            }
        }

        return null;
    }

    public function create(array $data): array
    {
        $rows = $this->raw();
        $row = ['id' => (string) (max(array_map('intval', array_column($rows, 'id')) ?: [0]) + 1)] + $this->bentuk($data, null);
        $rows[] = $row;
        $this->save($rows);

        return $row;
    }

    public function update(string $id, array $data): ?array
    {
        $rows = $this->raw();
        $found = null;

        foreach ($rows as &$row) {
            if ($row['id'] === $id) {
                $row = ['id' => $id] + $this->bentuk($data, $row);
                $found = $row;
            }
        }
        unset($row);

        if ($found) {
            $this->save($rows);
        }

        return $found;
    }

    /** Susun baris dari input form. Foto lama dipertahankan bila tidak ada foto baru. */
    private function bentuk(array $d, ?array $lama): array
    {
        $publish = $d['status'] === 'Publish';

        return [
            'judul' => $d['judul'],
            'kegiatan' => $d['kategori'],
            'tanggal' => $publish ? ($lama['tanggal'] ?? null ?: now()->toDateString()) : ($lama['tanggal'] ?? null),
            'status' => $d['status'],
            'isi' => $d['isi'],
            'foto' => $d['foto'] ?? ($lama['foto'] ?? null),
            'sumber' => $d['sumber'],
            'kegiatan_id' => $d['sumber'] === 'prev' ? ($d['kegiatan_id'] ?? null) : null,
        ];
    }

    /** @return array<int, array<string, mixed>> */
    private function raw(): array
    {
        $disk = Storage::disk('local');

        if (! $disk->exists(self::PATH)) {
            $this->save($this->seed());
        }

        return json_decode($disk->get(self::PATH), true) ?: [];
    }

    private function save(array $rows): void
    {
        Storage::disk('local')->put(self::PATH, json_encode(array_values($rows), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
    }

    private function seed(): array
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
                'tanggal' => Carbon::create(2026, 9 - ($i % 6), 1 + (($i * 7) % 28))->toDateString(),
                'status' => $i % 6 === 5 ? 'Draft' : 'Publish',
                'isi' => 'Isi berita contoh untuk "'.$j[0].'". Ganti dengan isi berita sebenarnya.',
                'foto' => null,
                'sumber' => 'none',
                'kegiatan_id' => null,
            ];
        }

        return $rows;
    }
}