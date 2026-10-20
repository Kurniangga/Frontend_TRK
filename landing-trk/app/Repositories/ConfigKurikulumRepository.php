<?php

namespace App\Repositories;

/** Implementasi SIMULASI: membaca config/kurikulum.php, PDF ada di public/dokumen/kurikulum/. */
class ConfigKurikulumRepository implements KurikulumRepository
{
    public function all(): array
    {
        return array_map(fn ($k) => [
            'id' => $k['id'],
            'nama' => $k['nama'],
            'pdf_url' => asset($k['file']),
            'file_name' => basename($k['file']),
        ], config('kurikulum.daftar', []));
    }
}
