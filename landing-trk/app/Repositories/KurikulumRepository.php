<?php

namespace App\Repositories;

/**
 * Kontrak sumber data dokumen kurikulum (tab di halaman Kurikulum + menu dropdown navbar).
 * Ganti implementasinya lewat bind() di AppServiceProvider saat pindah ke database.
 */
interface KurikulumRepository
{
    /**
     * Semua kurikulum yang ditampilkan publik, sudah terurut sesuai urutan tab.
     *
     * @return array<int, array{id: string, nama: string, pdf_url: string, file_name: string}>
     */
    public function all(): array;
}
