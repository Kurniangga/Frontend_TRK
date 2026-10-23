<?php

namespace App\Repositories;

use Illuminate\Support\Facades\Storage;

/**
 * Penyimpanan Jenis Dokumen ke file JSON (simulasi, sebelum ada database).
 * File: storage/app/private/admin/jenis-dokumen.json (dibuat otomatis).
 * TODO-DB: ganti dengan model Eloquent + tabel `jenis_dokumen`.
 */
class JenisDokumenStore
{
    private const FILE = 'admin/jenis-dokumen.json';

    public function all(): array
    {
        $disk = Storage::disk('local');

        if (! $disk->exists(self::FILE)) {
            $this->write($this->seed());
        }

        $data = json_decode($disk->get(self::FILE), true);

        return is_array($data) ? array_values($data) : [];
    }

    public function exists(string $name, ?string $exceptId = null): bool
    {
        foreach ($this->all() as $r) {
            if ($exceptId !== null && (string) $r['id'] === $exceptId) {
                continue;
            }
            if (mb_strtolower($r['name']) === mb_strtolower($name)) {
                return true;
            }
        }

        return false;
    }

    public function create(string $name, ?string $description): array
    {
        $rows = $this->all();
        $id   = $rows ? max(array_column($rows, 'id')) + 1 : 1;

        $row = ['id' => $id, 'name' => $name, 'description' => $description ?? ''];
        $rows[] = $row;
        $this->write($rows);

        return $row;
    }

    public function update(string $id, string $name, ?string $description): ?array
    {
        $rows = $this->all();

        foreach ($rows as &$r) {
            if ((string) $r['id'] === $id) {
                $r['name']        = $name;
                $r['description'] = $description ?? '';
                $this->write($rows);

                return $r;
            }
        }

        return null;
    }

    public function delete(string $id): bool
    {
        $rows  = $this->all();
        $baru  = array_values(array_filter($rows, fn ($r) => (string) $r['id'] !== $id));

        if (count($baru) === count($rows)) {
            return false;
        }

        $this->write($baru);

        return true;
    }

    private function write(array $rows): void
    {
        Storage::disk('local')->put(
            self::FILE,
            json_encode(array_values($rows), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)
        );
    }

    private function seed(): array
    {
        return [
            ['id' => 1, 'name' => 'Sertifikat',        'description' => 'Sertifikat keikutsertaan atau penghargaan kegiatan'],
            ['id' => 2, 'name' => 'Surat Tugas',       'description' => 'Surat tugas resmi dari institusi'],
            ['id' => 3, 'name' => 'Laporan Kegiatan',  'description' => 'Laporan hasil pelaksanaan kegiatan'],
            ['id' => 4, 'name' => 'Foto Kegiatan',     'description' => 'Dokumentasi foto pelaksanaan kegiatan'],
            ['id' => 5, 'name' => 'Surat Keterangan',  'description' => 'Surat keterangan pendukung kegiatan'],
        ];
    }
}