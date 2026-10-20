<?php

namespace App\Repositories;

/**
 * Kontrak sumber data mata kuliah.
 * Controller & view hanya bergantung pada interface ini, jadi saat pindah
 * dari data simulasi ke database cukup mengganti implementasinya
 * (lihat bind() di AppServiceProvider).
 */
interface MataKuliahRepository
{
    /** @return array<int, array{id: string, nama: string}> Daftar kurikulum untuk dropdown. */
    public function kurikulum(): array;

    /** @return int[] Nomor semester (1-8) yang punya mata kuliah pada kurikulum ini, urut naik. */
    public function semester(string $kurikulumId): array;

    /**
     * @return array<int, array{kode: string, nama: string, sks_teori: int, sks_praktek: int, jam_teori: int, jam_praktek: int}>
     */
    public function matkul(string $kurikulumId, int $semester): array;
}
