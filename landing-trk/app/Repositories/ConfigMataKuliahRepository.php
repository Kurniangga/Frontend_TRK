<?php

namespace App\Repositories;

/** Implementasi SIMULASI: membaca config/matkul.php. */
class ConfigMataKuliahRepository implements MataKuliahRepository
{
    public function kurikulum(): array
    {
        return config('matkul.kurikulum', []);
    }

    public function semester(string $kurikulumId): array
    {
        $semesters = array_keys(config("matkul.data.$kurikulumId", []));
        $semesters = array_filter(array_map('intval', $semesters), fn ($s) => $s >= 1 && $s <= 8);
        sort($semesters);

        return array_values($semesters);
    }

    public function matkul(string $kurikulumId, int $semester): array
    {
        $rows = config("matkul.data.$kurikulumId.$semester", []);

        return array_map(fn ($r) => [
            'kode' => $r[0],
            'nama' => $r[1],
            'sks_teori' => $r[2],
            'sks_praktek' => $r[3],
            'jam_teori' => $r[4],
            'jam_praktek' => $r[5],
        ], $rows);
    }
}
