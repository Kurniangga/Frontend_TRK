<?php

namespace App\Repositories;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Penyimpanan kurikulum SIMULASI berbasis file JSON (storage/app/private/admin/kurikulum.json).
 * Dipakai dashboard admin (CRUD) dan halaman publik (lewat JsonKurikulumRepository).
 *
 * Saat database siap: ganti isi method di class ini dengan Eloquent (nama method & bentuk
 * array tetap), halaman admin dan publik tidak perlu diubah.
 *
 * Satu baris: id, name, description, year, status (aktif|nonaktif), file (path PDF | null), created_at
 *   - file berawalan "dokumen/" = PDF di folder public/ (data awal)
 *   - selain itu             = PDF hasil upload di disk "public" (storage/app/public)
 */
class KurikulumStore
{
    private const PATH = 'admin/kurikulum.json';

    /** @return array<int, array<string, mixed>> */
    public function all(): array
    {
        $disk = Storage::disk('local');

        if (! $disk->exists(self::PATH)) {
            $this->save($this->seed());
        }

        return json_decode($disk->get(self::PATH), true) ?: [];
    }

    /** Daftar ringkas untuk dropdown (semua status), tahun terbaru dulu. @return array<int, array{id: string, nama: string}> */
    public function options(): array
    {
        $rows = $this->all();
        usort($rows, fn ($a, $b) => strcmp((string) $b['year'], (string) $a['year']) ?: strcmp($a['name'], $b['name']));

        return array_map(fn ($r) => ['id' => (string) $r['id'], 'nama' => $r['name']], $rows);
    }

    public function find(string $id): ?array
    {
        foreach ($this->all() as $row) {
            if ($row['id'] === $id) {
                return $row;
            }
        }

        return null;
    }

    public function create(array $data, ?UploadedFile $pdf = null): array
    {
        $rows = $this->all();

        $row = [
            'id' => $this->uniqueId($data['name'], $rows),
            'name' => $data['name'],
            'description' => $data['description'] ?? '',
            'year' => $data['year'],
            'status' => 'aktif',
            'file' => $pdf ? $pdf->store('kurikulum', 'public') : null,
            'created_at' => now()->toDateTimeString(),
        ];

        array_unshift($rows, $row);
        $this->save($rows);

        return $row;
    }

    public function update(string $id, array $data, ?UploadedFile $pdf = null): ?array
    {
        $rows = $this->all();
        $found = null;

        foreach ($rows as &$row) {
            if ($row['id'] !== $id) {
                continue;
            }
            $row['name'] = $data['name'];
            $row['description'] = $data['description'] ?? '';
            $row['year'] = $data['year'];
            if ($pdf) {
                $this->deleteFile($row['file']);
                $row['file'] = $pdf->store('kurikulum', 'public');
            }
            $found = $row;
        }
        unset($row);

        if ($found) {
            $this->save($rows);
        }

        return $found;
    }

    public function toggleStatus(string $id): ?array
    {
        $rows = $this->all();
        $found = null;

        foreach ($rows as &$row) {
            if ($row['id'] === $id) {
                $row['status'] = $row['status'] === 'aktif' ? 'nonaktif' : 'aktif';
                $found = $row;
            }
        }
        unset($row);

        if ($found) {
            $this->save($rows);
        }

        return $found;
    }

    public function delete(string $id): bool
    {
        $rows = $this->all();
        $keep = [];

        foreach ($rows as $row) {
            if ($row['id'] === $id) {
                $this->deleteFile($row['file']);
                // Mata kuliah milik kurikulum ini ikut terhapus (di database = cascadeOnDelete).
                app(MataKuliahStore::class)->deleteByKurikulum($id);
            } else {
                $keep[] = $row;
            }
        }

        $this->save($keep);

        return count($keep) < count($rows);
    }

    /** URL PDF untuk ditampilkan (atau null bila belum diupload). */
    public function pdfUrl(array $row): ?string
    {
        if (empty($row['file'])) {
            return null;
        }

        return str_starts_with($row['file'], 'dokumen/')
            ? asset($row['file'])
            : Storage::disk('public')->url($row['file']);
    }

    /** Bentuk yang dikirim ke halaman admin. */
    public function forAdmin(array $row): array
    {
        return [
            'id' => $row['id'],
            'name' => $row['name'],
            'description' => $row['description'],
            'year' => $row['year'],
            'status' => $row['status'],
            'pdf_url' => $this->pdfUrl($row),
        ];
    }

    private function save(array $rows): void
    {
        Storage::disk('local')->put(self::PATH, json_encode(array_values($rows), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
    }

    private function deleteFile(?string $file): void
    {
        if ($file && ! str_starts_with($file, 'dokumen/')) {
            Storage::disk('public')->delete($file);
        }
    }

    private function uniqueId(string $name, array $rows): string
    {
        $base = Str::slug($name) ?: 'kurikulum';
        $ids = array_column($rows, 'id');
        $id = $base;
        for ($i = 2; in_array($id, $ids, true); $i++) {
            $id = $base.'-'.$i;
        }

        return $id;
    }

    /** Data awal: diambil dari config/kurikulum.php (PDF contoh di public/dokumen/kurikulum/). */
    private function seed(): array
    {
        return array_map(fn ($k) => [
            'id' => $k['id'],
            'name' => $k['nama'],
            'description' => '',
            'year' => preg_match('/\d{4}/', $k['nama'], $m) ? $m[0] : '',
            'status' => 'aktif',
            'file' => $k['file'],
            'created_at' => now()->toDateTimeString(),
        ], config('kurikulum.daftar', []));
    }
}
