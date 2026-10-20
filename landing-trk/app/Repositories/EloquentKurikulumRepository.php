<?php

namespace App\Repositories;

use App\Models\Kurikulum;
use Illuminate\Support\Facades\Storage;

/**
 * Implementasi DATABASE (belum dipakai).
 * Tabel `kurikulum` (sama dengan yang dipakai dashboard admin "Kurikulum TRK"):
 *   id, nama, deskripsi, tahun_terbit, status ('aktif'/'nonaktif'), pdf_path, urutan, timestamps
 *
 * Admin tambah  -> baris baru muncul sebagai tab + menu navbar.
 * Admin hapus   -> baris hilang dari tab + menu (hapus juga file PDF-nya di storage).
 */
class EloquentKurikulumRepository implements KurikulumRepository
{
    public function all(): array
    {
        return Kurikulum::query()
            ->where('status', 'aktif')
            ->whereNotNull('pdf_path')
            ->orderBy('urutan')
            ->orderBy('tahun_terbit')
            ->get()
            ->map(fn ($k) => [
                'id' => (string) $k->id,
                'nama' => $k->nama,
                'pdf_url' => Storage::disk('public')->url($k->pdf_path),
                'file_name' => str($k->nama)->slug() . '.pdf',
            ])
            ->all();
    }
}
