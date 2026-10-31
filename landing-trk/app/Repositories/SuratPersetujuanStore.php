<?php

namespace App\Repositories;

use Illuminate\Support\Facades\Storage;

/**
 * Penyimpanan sementara (simulasi) surat persetujuan yang sudah disetujui.
 * Disimpan di storage/app/private/admin/surat-persetujuan.json, kuncinya id pengajuan.
 *
 * TODO-DB: ganti dengan tabel surat_persetujuan saat database siap.
 */
class SuratPersetujuanStore
{
    private const FILE = 'admin/surat-persetujuan.json';

    public function find(string $id): ?array
    {
        return $this->all()[$id] ?? null;
    }

    public function save(string $id, array $data): array
    {
        $rows = $this->all();
        $rows[$id] = $data;
        $this->write($rows);

        return $data;
    }

    public function delete(string $id): void
    {
        $rows = $this->all();
        if (isset($rows[$id])) {
            unset($rows[$id]);
            $this->write($rows);
        }
    }

    private function all(): array
    {
        $disk = Storage::disk('local');
        if (! $disk->exists(self::FILE)) {
            return [];
        }

        return json_decode($disk->get(self::FILE), true) ?: [];
    }

    private function write(array $rows): void
    {
        Storage::disk('local')->put(
            self::FILE,
            json_encode((object) $rows, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)
        );
    }
}