<?php

namespace App\Repositories;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Akreditasi prodi (SIMULASI: JSON storage/app/private/admin/akreditasi.json).
 * TODO-DB: ganti isi method dengan Eloquent (nama method & bentuk array tetap).
 *
 * Satu baris: id, lembaga, peringkat, nomor_sk, year, berlaku_sampai (Y-m-d|''), status (aktif|nonaktif),
 *             file (path PDF disk "public" | null), created_at
 * ATURAN: selalu tepat SATU akreditasi berstatus aktif (selama data tidak kosong).
 */
class AkreditasiStore
{
    public const PERINGKAT = ['Unggul', 'Baik Sekali', 'Baik'];
    public const LEMBAGA = ['BAN-PT', 'LAM INFOKOM', 'LAMEMBA', 'LAM TEKNIK'];

    private const PATH = 'admin/akreditasi.json';

    /** @return array<int, array<string, mixed>> */
    public function all(): array
    {
        $disk = Storage::disk('local');

        if (! $disk->exists(self::PATH)) {
            $this->save($this->seed());
        }

        return json_decode($disk->get(self::PATH), true) ?: [];
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

    /** Tambah. Akreditasi pertama otomatis aktif; selain itu aktif hanya bila $aktifkan = true (yang lama dinonaktifkan). */
    public function create(array $data, bool $aktifkan, ?UploadedFile $pdf = null): array
    {
        $rows = $this->all();
        $jadiAktif = $aktifkan || count($rows) === 0;

        if ($jadiAktif) {
            foreach ($rows as &$r) {
                $r['status'] = 'nonaktif';
            }
            unset($r);
        }

        $row = [
            'id' => $this->uniqueId($data['lembaga'].' '.$data['peringkat'].' '.$data['year'], $rows),
            'lembaga' => $data['lembaga'],
            'peringkat' => $data['peringkat'],
            'nomor_sk' => $data['nomor_sk'] ?? '',
            'year' => $data['year'],
            'berlaku_sampai' => $data['berlaku_sampai'] ?? '',
            'status' => $jadiAktif ? 'aktif' : 'nonaktif',
            'file' => $pdf ? $pdf->store('akreditasi', 'public') : null,
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
            foreach (['lembaga', 'peringkat', 'year'] as $k) {
                $row[$k] = $data[$k];
            }
            $row['nomor_sk'] = $data['nomor_sk'] ?? '';
            $row['berlaku_sampai'] = $data['berlaku_sampai'] ?? '';
            if ($pdf) {
                $this->deleteFile($row['file']);
                $row['file'] = $pdf->store('akreditasi', 'public');
            }
            $found = $row;
        }
        unset($row);

        if ($found) {
            $this->save($rows);
        }

        return $found;
    }

    /** Jadikan $id satu-satunya yang aktif. */
    public function activate(string $id): ?array
    {
        $rows = $this->all();
        $found = null;

        foreach ($rows as &$row) {
            $row['status'] = $row['id'] === $id ? 'aktif' : 'nonaktif';
            if ($row['id'] === $id) {
                $found = $row;
            }
        }
        unset($row);

        if ($found) {
            $this->save($rows);
        }

        return $found;
    }

    /** Hapus. Yang berstatus aktif tidak boleh dihapus (aktifkan yang lain dulu) -> return 'aktif'. */
    public function delete(string $id): string
    {
        $rows = $this->all();
        $target = null;

        foreach ($rows as $r) {
            if ($r['id'] === $id) {
                $target = $r;
            }
        }

        if (! $target) {
            return 'tidak-ada';
        }
        if ($target['status'] === 'aktif') {
            return 'aktif';
        }

        $this->deleteFile($target['file']);
        $this->save(array_values(array_filter($rows, fn ($r) => $r['id'] !== $id)));

        return 'ok';
    }

    public function pdfUrl(array $row): ?string
    {
        return empty($row['file']) ? null : Storage::disk('public')->url($row['file']);
    }

    /** Bentuk yang dikirim ke halaman admin. */
    public function forAdmin(array $row): array
    {
        return [
            'id' => $row['id'],
            'lembaga' => $row['lembaga'],
            'peringkat' => $row['peringkat'],
            'nomor_sk' => $row['nomor_sk'],
            'year' => $row['year'],
            'berlaku_sampai' => $row['berlaku_sampai'],
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
        if ($file) {
            Storage::disk('public')->delete($file);
        }
    }

    private function uniqueId(string $name, array $rows): string
    {
        $base = Str::slug($name) ?: 'akreditasi';
        $ids = array_column($rows, 'id');
        $id = $base;
        for ($i = 2; in_array($id, $ids, true); $i++) {
            $id = $base.'-'.$i;
        }

        return $id;
    }

    /** Data awal CONTOH — ganti lewat halaman admin. */
    private function seed(): array
    {
        $mk = fn ($id, $l, $p, $sk, $y, $s, $b) => [
            'id' => $id, 'lembaga' => $l, 'peringkat' => $p, 'nomor_sk' => $sk, 'year' => $y,
            'berlaku_sampai' => $b, 'status' => $s, 'file' => null, 'created_at' => now()->toDateTimeString(),
        ];

        return [
            $mk('lam-infokom-unggul-2024', 'LAM INFOKOM', 'Unggul', '123/SK/LAM-INFOKOM/Ak/S/2024', '2024', 'aktif', '2029-12-31'),
            $mk('ban-pt-baik-sekali-2019', 'BAN-PT', 'Baik Sekali', '456/SK/BAN-PT/Akred/Dpl-III/2019', '2019', 'nonaktif', '2024-12-31'),
            $mk('ban-pt-baik-2014', 'BAN-PT', 'Baik', '789/SK/BAN-PT/Akred/Dpl-III/2014', '2014', 'nonaktif', '2019-12-31'),
        ];
    }
}
