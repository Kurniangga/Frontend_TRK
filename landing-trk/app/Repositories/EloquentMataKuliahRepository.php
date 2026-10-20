<?php

namespace App\Repositories;

use App\Models\Kurikulum;
use App\Models\MataKuliah;

/**
 * Implementasi DATABASE (belum dipakai).
 * Aktifkan setelah model Kurikulum & MataKuliah + migrasinya dibuat, dengan mengganti
 * binding di AppServiceProvider menjadi EloquentMataKuliahRepository::class.
 *
 * Tabel yang diharapkan:
 *   kurikulum   : id, nama, tahun_terbit, status ('aktif'/'nonaktif'), ...
 *   mata_kuliah : id, kurikulum_id, semester (1-8), kode, nama,
 *                 sks_teori, sks_praktek, jam_teori, jam_praktek, urutan
 */
class EloquentMataKuliahRepository implements MataKuliahRepository
{
    public function kurikulum(): array
    {
        return Kurikulum::query()
            ->where('status', 'aktif')
            ->whereHas('mataKuliah')            // kurikulum kosong tidak tampil
            ->orderByDesc('tahun_terbit')
            ->get(['id', 'nama'])
            ->map(fn ($k) => ['id' => (string) $k->id, 'nama' => $k->nama])
            ->all();
    }

    public function semester(string $kurikulumId): array
    {
        return MataKuliah::query()
            ->where('kurikulum_id', $kurikulumId)
            ->distinct()
            ->orderBy('semester')
            ->pluck('semester')
            ->map(fn ($s) => (int) $s)
            ->all();
    }

    public function matkul(string $kurikulumId, int $semester): array
    {
        return MataKuliah::query()
            ->where('kurikulum_id', $kurikulumId)
            ->where('semester', $semester)
            ->orderBy('urutan')
            ->orderBy('kode')
            ->get()
            ->map(fn ($m) => [
                'kode' => $m->kode,
                'nama' => $m->nama,
                'sks_teori' => (int) $m->sks_teori,
                'sks_praktek' => (int) $m->sks_praktek,
                'jam_teori' => (int) $m->jam_teori,
                'jam_praktek' => (int) $m->jam_praktek,
            ])
            ->all();
    }
}
