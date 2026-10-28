<?php

namespace App\Repositories;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Penyimpanan dosen SIMULASI berbasis file JSON (storage/app/private/admin/dosen.json).
 * Data awal dibuat otomatis (100 dosen contoh) saat file belum ada.
 *
 * Saat database siap: ganti isi method di class ini dengan Eloquent (nama method & bentuk
 * array tetap), controller dan view tidak perlu diubah.
 *
 * Satu baris dosen:
 *   id, nip, nama, jabatan, golongan (= pangkat saat ini), status (aktif|nonaktif),
 *   email, role (dosen|kaprodi), status_akun (aktif|nonaktif), password_hash (null|string),
 *   riwayat[] = { pangkat, golongan, sk, tanggal (Y-m-d) }  -> urutan pertama = terbaru / saat ini
 */
class DosenStore
{
    public const JABATAN = ['Asisten Ahli', 'Lektor', 'Lektor Kepala', 'Guru Besar'];
    public const GOLONGAN = ['III/a', 'III/b', 'III/c', 'III/d', 'IV/a', 'IV/b', 'IV/c', 'IV/d', 'IV/e'];
    public const ROLE = ['dosen' => 'Dosen', 'kaprodi' => 'Kaprodi'];

    private const PATH = 'admin/dosen.json';

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

    /** Apakah nilai $field sudah dipakai dosen LAIN (untuk cek unik nip / email). */
    public function usedByOther(string $field, string $value, string $exceptId): bool
    {
        foreach ($this->all() as $row) {
            if ($row['id'] !== $exceptId && strcasecmp((string) $row[$field], $value) === 0) {
                return true;
            }
        }

        return false;
    }

    /**
     * Ubah satu dosen. $riwayatBaru (opsional) ditambahkan di urutan teratas dan
     * menjadi pangkat/golongan saat ini.
     */
    public function update(string $id, array $data, ?array $riwayatBaru = null): ?array
    {
        $rows = $this->all();
        $found = null;

        foreach ($rows as &$row) {
            if ($row['id'] !== $id) {
                continue;
            }

            $row['nama'] = $data['nama'];
            $row['nip'] = $data['nip'];
            $row['status'] = $data['status'];
            $row['email'] = $data['email'];
            $row['role'] = $data['role'];
            $row['status_akun'] = $data['status_akun'];

            if (! empty($data['foto'])) {
                $row['foto'] = $data['foto'];   // path foto di disk 'public'
            }

            if (! empty($data['password'])) {
                $row['password_hash'] = password_hash($data['password'], PASSWORD_DEFAULT);
            }

            if ($riwayatBaru) {
                array_unshift($row['riwayat'], $riwayatBaru);
                $row['jabatan'] = $riwayatBaru['pangkat'];
                $row['golongan'] = $riwayatBaru['golongan'];
            }

            $found = $row;
        }
        unset($row);

        if ($found) {
            $this->save($rows);
        }

        return $found;
    }

    /** Tambah dosen baru. $data: nama, nip, status, email, role, password, pangkat, golongan, sk, tanggal. */
    public function create(array $data): array
    {
        $rows = $this->all();
        $id = (string) (max(array_map('intval', array_column($rows, 'id')) ?: [0]) + 1);

        $row = [
            'id' => $id,
            'nip' => $data['nip'],
            'nama' => $data['nama'],
            'jabatan' => $data['pangkat'],
            'golongan' => $data['golongan'],
            'status' => $data['status'],
            'email' => $data['email'],
            'role' => $data['role'],
            'status_akun' => 'aktif',   // akun baru otomatis aktif
            'password_hash' => password_hash($data['password'], PASSWORD_DEFAULT),
            'riwayat' => [['pangkat' => $data['pangkat'], 'golongan' => $data['golongan'], 'sk' => $data['sk'], 'tanggal' => $data['tanggal']]],
        ];

        $rows[] = $row;
        $this->save($rows);

        return $row;
    }

    /** Apakah nilai $field sudah dipakai dosen mana pun (cek unik saat tambah). */
    public function exists(string $field, string $value): bool
    {
        return $this->usedByOther($field, $value, '');
    }

    private function save(array $rows): void
    {
        Storage::disk('local')->put(self::PATH, json_encode(array_values($rows), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
    }

    /** Data awal contoh. */
    private function seed(): array
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

        $rows = [];
        for ($i = 0; $i < 100; $i++) {
            $b = $contoh[$i % 10];
            $slug = str_replace('-', '', Str::slug($b[1]));
            $status = ($i % 13 === 12) ? 'nonaktif' : 'aktif';

            $rows[] = [
                'id' => (string) ($i + 1),
                'nip' => (string) ((int) $b[0] + $i * 7),            // dibuat unik
                'nama' => $b[1],
                'jabatan' => $b[2],
                'golongan' => $b[3],
                'status' => $status,
                'email' => $slug.($i >= 10 ? $i : '').'@polines.ac.id',
                'role' => 'dosen',
                'status_akun' => $status,
                'password_hash' => null,
                'riwayat' => [
                    ['pangkat' => $b[2], 'golongan' => $b[3], 'sk' => '1042/UN/KP/2022', 'tanggal' => '2022-01-12'],
                    ['pangkat' => 'Lektor', 'golongan' => 'III/d', 'sk' => '0788/UN/KP/2018', 'tanggal' => '2018-10-01'],
                    ['pangkat' => 'Asisten Ahli', 'golongan' => 'III/b', 'sk' => '0120/UN/KP/2012', 'tanggal' => '2012-03-01'],
                ],
            ];
        }

        return $rows;
    }
}