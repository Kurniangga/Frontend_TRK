<?php

namespace App\Repositories;

use Illuminate\Support\Facades\Storage;

/**
 * Penyimpanan Kategori Kegiatan ke file JSON (simulasi, sebelum ada database).
 * File: storage/app/private/admin/kategori-kegiatan.json (dibuat otomatis).
 * TODO-DB: ganti dengan model Eloquent + tabel `kategori_kegiatan`.
 */
class KategoriKegiatanStore
{
    private const FILE = 'admin/kategori-kegiatan.json';

    /** Pilihan pada dropdown "Jenis Kegiatan" di pop-up. */
    public const JENIS = ['Kegiatan Dosen', 'Kegiatan Mahasiswa'];

    public function all(): array
    {
        $disk = Storage::disk('local');

        if (! $disk->exists(self::FILE)) {
            $this->write($this->seed());
        }

        $data = json_decode($disk->get(self::FILE), true);

        return is_array($data) ? array_values($data) : [];
    }

    /** Nama yang sama (tanpa membedakan huruf besar/kecil) tidak boleh ada dalam satu jenis. */
    public function exists(string $name, string $jenis, ?string $exceptId = null): bool
    {
        foreach ($this->all() as $r) {
            if ($exceptId !== null && (string) $r['id'] === $exceptId) {
                continue;
            }
            if ($r['jenis'] === $jenis && mb_strtolower($r['name']) === mb_strtolower($name)) {
                return true;
            }
        }

        return false;
    }

    public function create(string $name, string $jenis, ?string $description, ?string $template_surat = null): array
    {
        $rows = $this->all();
        $id   = $rows ? max(array_column($rows, 'id')) + 1 : 1;

        $row = [
            'id'             => $id, 
            'name'           => $name, 
            'jenis'          => $jenis, 
            'description'    => $description ?? '',
            'template_surat' => $template_surat ?? ''
        ];
        
        $rows[] = $row;
        $this->write($rows);

        return $row;
    }

    public function update(string $id, string $name, string $jenis, ?string $description, ?string $template_surat = null): ?array
    {
        $rows = $this->all();

        foreach ($rows as &$r) {
            if ((string) $r['id'] === $id) {
                $r['name']           = $name;
                $r['jenis']          = $jenis;
                $r['description']    = $description ?? '';
                $r['template_surat'] = $template_surat ?? '';
                
                $this->write($rows);

                return $r;
            }
        }

        return null;
    }

    public function delete(string $id): bool
    {
        $rows = $this->all();
        $baru = array_values(array_filter($rows, fn ($r) => (string) $r['id'] !== $id));

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
            ['id' => 1, 'name' => 'Penelitian',        'jenis' => 'Kegiatan Dosen',     'description' => 'Kegiatan penelitian dan publikasi ilmiah dosen', 'template_surat' => ''],
            ['id' => 2, 'name' => 'Pengabdian',        'jenis' => 'Kegiatan Dosen',     'description' => 'Pengabdian kepada masyarakat oleh dosen', 'template_surat' => ''],
            ['id' => 3, 'name' => 'Pelatihan',         'jenis' => 'Kegiatan Dosen',     'description' => 'Pelatihan dan peningkatan kompetensi dosen', 'template_surat' => ''],
            ['id' => 4, 'name' => 'Seminar',           'jenis' => 'Kegiatan Dosen',     'description' => 'Seminar, konferensi, dan workshop', 'template_surat' => ''],
            ['id' => 5, 'name' => 'Kunjungan Industri', 'jenis' => 'Kegiatan Mahasiswa', 'description' => 'Kunjungan ke perusahaan atau industri mitra', 'template_surat' => 'Yang bertanda tangan di bawah ini menyetujui mahasiswa:\nNama: [NAMA_PENGAJU]\nNIM: [IDENTITAS]\n\nUntuk melaksanakan kegiatan [NAMA_KEGIATAN] di [LOKASI] pada tanggal [TANGGAL].'],
            ['id' => 6, 'name' => 'Lomba',             'jenis' => 'Kegiatan Mahasiswa', 'description' => 'Kompetisi tingkat regional, nasional, dan internasional', 'template_surat' => 'Telah disetujui pengajuan [NAMA_KEGIATAN] untuk [NAMA_PENGAJU] di [LOKASI].'],
            ['id' => 7, 'name' => 'Magang',            'jenis' => 'Kegiatan Mahasiswa', 'description' => 'Magang atau praktik kerja di dunia industri', 'template_surat' => ''],
        ];
    }
}