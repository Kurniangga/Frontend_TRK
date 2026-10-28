<?php

namespace App\Repositories;

/**
 * Sumber data halaman PUBLIK Mata Kuliah: kurikulum berstatus aktif yang punya mata kuliah,
 * tahun terbaru dulu. Data dari MataKuliahStore, jadi perubahan admin/kaprodi langsung tampil.
 */
class JsonMataKuliahRepository implements MataKuliahRepository
{
    public function __construct(private KurikulumStore $kurikulum, private MataKuliahStore $matkul) {}

    public function kurikulum(): array
    {
        $rows = array_filter(
            $this->kurikulum->all(),
            fn ($r) => $r['status'] === 'aktif' && $this->matkul->count((string) $r['id']) > 0
        );

        usort($rows, fn ($a, $b) => strcmp((string) $b['year'], (string) $a['year']));

        return array_map(fn ($r) => ['id' => (string) $r['id'], 'nama' => $r['name']], array_values($rows));
    }

    public function semester(string $kurikulumId): array
    {
        return $this->matkul->semesters($kurikulumId);
    }

    public function matkul(string $kurikulumId, int $semester): array
    {
        return array_map(fn ($r) => [
            'kode' => $r['kode'],
            'nama' => $r['nama'],
            'sks_teori' => (int) $r['sks_teori'],
            'sks_praktek' => (int) $r['sks_praktek'],
            'jam_teori' => (int) $r['jam_teori'],
            'jam_praktek' => (int) $r['jam_praktek'],
        ], $this->matkul->bySemester($kurikulumId, $semester));
    }
}
