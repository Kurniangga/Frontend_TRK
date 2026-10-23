<?php

namespace App\Repositories;

use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Penyimpanan mahasiswa SIMULASI berbasis file JSON (storage/app/private/admin/mahasiswa.json).
 * Data awal (23 mahasiswa contoh) dibuat otomatis saat file belum ada.
 *
 * Saat database siap: ganti isi method di class ini dengan Eloquent (nama method & bentuk
 * array tetap), controller dan view tidak perlu diubah.
 *
 * Satu baris mahasiswa:
 *   id, nim, nama, level (Tingkat 1-4), kelas (mis. TI-2A), status (Aktif|Cuti|Lulus|Non-aktif),
 *   angkatan, email, tanggal_lahir (Y-m-d), telepon, password_hash (null|string)
 */
class MahasiswaStore
{
    public const TINGKAT = ['Tingkat 1', 'Tingkat 2', 'Tingkat 3', 'Tingkat 4'];
    public const STATUS = ['Aktif', 'Cuti', 'Lulus', 'Non-aktif'];

    private const PATH = 'admin/mahasiswa.json';

    /** @return array<int, array<string, mixed>> */
    public function all(): array
    {
        $disk = Storage::disk('local');

        if (! $disk->exists(self::PATH)) {
            $this->save($this->seed());
        }

        return json_decode($disk->get(self::PATH), true) ?: [];
    }

    public function find(string $id): ?array
    {
        foreach ($this->all() as $row) {
            if ($row['id'] === $id) {
                return $row;
            }
        }

        return null;
    }

    /** Apakah nilai $field sudah dipakai mahasiswa LAIN (untuk cek unik nim / email). */
    public function usedByOther(string $field, string $value, string $exceptId): bool
    {
        foreach ($this->all() as $row) {
            if ($row['id'] !== $exceptId && strcasecmp((string) $row[$field], $value) === 0) {
                return true;
            }
        }

        return false;
    }

    /** Ubah satu mahasiswa. Password hanya diganti bila $data['password'] terisi. */
    public function update(string $id, array $data): ?array
    {
        $rows = $this->all();
        $found = null;

        foreach ($rows as &$row) {
            if ($row['id'] !== $id) {
                continue;
            }

            foreach (['nama', 'nim', 'tanggal_lahir', 'telepon', 'angkatan', 'status', 'email'] as $k) {
                $row[$k] = $data[$k] ?? null;
            }

            if (! empty($data['password'])) {
                $row['password_hash'] = password_hash($data['password'], PASSWORD_DEFAULT);
            }

            $found = $row;
        }
        unset($row);

        if ($found) {
            $this->save($rows);
        }

        return $found;
    }

    private function save(array $rows): void
    {
        Storage::disk('local')->put(self::PATH, json_encode(array_values($rows), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
    }

    /** Data awal contoh. */
    private function seed(): array
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
                'tanggal_lahir' => Carbon::create(2003 + ($i % 4), 1 + ($i % 12), 1 + (($i * 3) % 28))->format('Y-m-d'),
                'telepon' => '0856-0037-'.sprintf('%04d', 2000 + $i),
                'password_hash' => null,
            ];
        }

        return $rows;
    }
}
