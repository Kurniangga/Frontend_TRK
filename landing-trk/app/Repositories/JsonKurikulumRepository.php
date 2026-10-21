<?php

namespace App\Repositories;

use Illuminate\Support\Str;

/**
 * Sumber data halaman PUBLIK (tab Kurikulum + menu navbar): hanya kurikulum
 * berstatus aktif yang sudah punya PDF, diurutkan tahun terbaru dulu.
 */
class JsonKurikulumRepository implements KurikulumRepository
{
    public function __construct(private KurikulumStore $store) {}

    public function all(): array
    {
        $rows = array_filter($this->store->all(), fn ($r) => $r['status'] === 'aktif' && ! empty($r['file']));

        usort($rows, fn ($a, $b) => strcmp((string) $b['year'], (string) $a['year']));

        return array_map(fn ($r) => [
            'id' => $r['id'],
            'nama' => $r['name'],
            'pdf_url' => $this->store->pdfUrl($r),
            'file_name' => Str::slug($r['name']).'.pdf',
        ], array_values($rows));
    }
}
