<?php

namespace App\Repositories;

use Illuminate\Support\Facades\Storage;

/**
 * Penyimpanan mata kuliah SIMULASI berbasis file JSON (storage/app/private/admin/mata-kuliah.json).
 * Dipakai admin, kaprodi (CRUD) dan halaman publik (lewat JsonMataKuliahRepository).
 * Data awal diambil dari config/matkul.php saat file JSON belum ada.
 *
 * Satu baris: id (int), kurikulum (id kurikulum, string), semester (1-8), kode, nama,
 *             sks_teori, sks_praktek, jam_teori, jam_praktek, created_at
 *
 * TODO-DB: ganti isi method dengan Eloquent (tabel mata_kuliah). Nama method & bentuk array dijaga tetap.
 */
class MataKuliahStore
{
    public const ROMAWI = [1 => 'I', 2 => 'II', 3 => 'III', 4 => 'IV', 5 => 'V', 6 => 'VI', 7 => 'VII', 8 => 'VIII'];

    private const PATH = 'admin/mata-kuliah.json';

    /** @return array<int, array<string, mixed>> */
    public function all(): array
    {
        $disk = Storage::disk('local');

        if (! $disk->exists(self::PATH)) {
            $this->save($this->seed());
        }

        return json_decode($disk->get(self::PATH), true) ?: [];
    }

    public function find(int $id): ?array
    {
        foreach ($this->all() as $row) {
            if ((int) $row['id'] === $id) {
                return $row;
            }
        }

        return null;
    }

    /** Semua mata kuliah milik satu kurikulum. */
    public function forKurikulum(string $kurikulumId): array
    {
        return array_values(array_filter($this->all(), fn ($r) => (string) $r['kurikulum'] === $kurikulumId));
    }

    public function count(string $kurikulumId): int
    {
        return count($this->forKurikulum($kurikulumId));
    }

    /** @return int[] Semester yang sudah punya mata kuliah, urut naik. */
    public function semesters(string $kurikulumId): array
    {
        $semesters = array_unique(array_map(fn ($r) => (int) $r['semester'], $this->forKurikulum($kurikulumId)));
        sort($semesters);

        return array_values($semesters);
    }

    /** Semester pertama yang berisi data (atau 1 bila kurikulum masih kosong). */
    public function firstSemester(string $kurikulumId): int
    {
        return $this->semesters($kurikulumId)[0] ?? 1;
    }

    /** Mata kuliah pada satu semester, urut kode. */
    public function bySemester(string $kurikulumId, int $semester): array
    {
        $rows = array_values(array_filter(
            $this->forKurikulum($kurikulumId),
            fn ($r) => (int) $r['semester'] === $semester
        ));

        usort($rows, fn ($a, $b) => strnatcasecmp($a['kode'], $b['kode']));

        return $rows;
    }

    /**
     * Data untuk halaman daftar: baris yang tampil (hasil cari), jumlah semua, dan sub total
     * (sub total selalu dari SEMUA mata kuliah semester itu, bukan hasil cari).
     */
    public function listing(string $kurikulumId, int $semester, string $q = ''): array
    {
        $semua = $this->bySemester($kurikulumId, $semester);

        $tampil = $q === '' ? $semua : array_values(array_filter(
            $semua,
            fn ($r) => stripos($r['kode'], $q) !== false || stripos($r['nama'], $q) !== false
        ));

        return [
            'courses' => $tampil,
            'jumlahSemua' => count($semua),
            'romawi' => self::ROMAWI[$semester] ?? (string) $semester,
            'total' => [
                'sks_teori' => array_sum(array_column($semua, 'sks_teori')),
                'sks_praktek' => array_sum(array_column($semua, 'sks_praktek')),
                'jam_teori' => array_sum(array_column($semua, 'jam_teori')),
                'jam_praktek' => array_sum(array_column($semua, 'jam_praktek')),
            ],
        ];
    }

    /** Apakah kode sudah dipakai di kurikulum ini (abaikan baris $exceptId saat edit). */
    public function kodeExists(string $kurikulumId, string $kode, ?int $exceptId = null): bool
    {
        foreach ($this->forKurikulum($kurikulumId) as $row) {
            if (strcasecmp($row['kode'], $kode) === 0 && (int) $row['id'] !== $exceptId) {
                return true;
            }
        }

        return false;
    }

    public function create(array $data): array
    {
        $rows = $this->all();

        $row = ['id' => $this->nextId($rows)] + $this->normalize($data) + ['created_at' => now()->toDateTimeString()];

        $rows[] = $row;
        $this->save($rows);

        return $row;
    }

    public function update(int $id, array $data): ?array
    {
        $rows = $this->all();
        $found = null;

        foreach ($rows as &$row) {
            if ((int) $row['id'] === $id) {
                $row = array_merge($row, $this->normalize($data));
                $found = $row;
            }
        }
        unset($row);

        if ($found) {
            $this->save($rows);
        }

        return $found;
    }

    public function delete(int $id): bool
    {
        $rows = $this->all();
        $keep = array_values(array_filter($rows, fn ($r) => (int) $r['id'] !== $id));

        $this->save($keep);

        return count($keep) < count($rows);
    }

    /** Dipanggil saat sebuah kurikulum dihapus (di database = cascadeOnDelete). */
    public function deleteByKurikulum(string $kurikulumId): void
    {
        $this->save(array_values(array_filter(
            $this->all(),
            fn ($r) => (string) $r['kurikulum'] !== $kurikulumId
        )));
    }

    private function normalize(array $d): array
    {
        return [
            'kurikulum' => (string) $d['kurikulum'],
            'semester' => (int) $d['semester'],
            'kode' => trim($d['kode']),
            'nama' => trim($d['nama']),
            'sks_teori' => (int) $d['sks_teori'],
            'sks_praktek' => (int) $d['sks_praktek'],
            'jam_teori' => (int) $d['jam_teori'],
            'jam_praktek' => (int) $d['jam_praktek'],
        ];
    }

    private function nextId(array $rows): int
    {
        return $rows ? max(array_map('intval', array_column($rows, 'id'))) + 1 : 1;
    }

    private function save(array $rows): void
    {
        Storage::disk('local')->put(self::PATH, json_encode(array_values($rows), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
    }

    /** Data awal dari config/matkul.php: [id kurikulum][semester] => [kode, nama, sks_teori, sks_praktek, jam_teori, jam_praktek]. */
    private function seed(): array
    {
        $rows = [];
        $id = 1;

        foreach (config('matkul.data', []) as $kurikulumId => $semesters) {
            foreach ($semesters as $semester => $list) {
                foreach ($list as $r) {
                    $rows[] = [
                        'id' => $id++,
                        'kurikulum' => (string) $kurikulumId,   // kunci angka di PHP jadi int, paksa string
                        'semester' => (int) $semester,
                        'kode' => $r[0],
                        'nama' => $r[1],
                        'sks_teori' => $r[2],
                        'sks_praktek' => $r[3],
                        'jam_teori' => $r[4],
                        'jam_praktek' => $r[5],
                        'created_at' => now()->toDateTimeString(),
                    ];
                }
            }
        }

        return $rows;
    }
}
