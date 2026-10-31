<?php

namespace App\Repositories;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

/**
 * Penyimpanan tanda tangan kaprodi (PNG transparan), unggah SEKALI dipakai ulang.
 *
 * - Daftar: storage/app/private/kaprodi/tanda-tangan.json  (kunci = email kaprodi)
 * - Gambar: storage/app/private/kaprodi/tanda-tangan/*.png (disk local, TIDAK publik)
 * - Saat surat ditandatangani, gambar DISALIN ke storage/app/private/surat/tanda-tangan/
 *   (snapshot), jadi mengganti tanda tangan tidak mengubah surat lama.
 *
 * TODO-DB: tabel `tanda_tangan` (id_user, path, aktif, created_at) dan kolom salinan
 *          path di tabel surat persetujuan.
 */
class TandaTanganStore
{
    private const FILE = 'kaprodi/tanda-tangan.json';
    private const DIR  = 'kaprodi/tanda-tangan';

    /** Kunci kaprodi yang sedang login (TODO-DB: ganti dengan id_user). */
    public static function kunci(): string
    {
        $s = session('kaprodi');

        return is_array($s) && ! empty($s['email']) ? (string) $s['email'] : 'kaprodi';
    }

    /** Tanda tangan aktif milik $kunci, atau null bila belum ada / file hilang. */
    public function aktif(string $kunci): ?array
    {
        $row = $this->semua()[$kunci] ?? null;

        if (! $row || empty($row['path']) || ! Storage::disk('local')->exists($row['path'])) {
            return null;
        }

        return $row;
    }

    /** Simpan (atau ganti) tanda tangan. PNG di-encode ulang lewat GD bila tersedia. */
    public function simpan(string $kunci, UploadedFile $file): array
    {
        $isi  = $this->bersihkan($file);
        $disk = Storage::disk('local');
        $path = self::DIR . '/' . Str::uuid() . '.png';

        $disk->put($path, $isi);

        $semua = $this->semua();
        $lama  = $semua[$kunci]['path'] ?? null;

        $semua[$kunci] = ['path' => $path, 'diunggah_pada' => now()->toIso8601String()];
        $this->tulis($semua);

        if ($lama && $lama !== $path) {
            $disk->delete($lama);
        }

        return $semua[$kunci];
    }

    /** Salin tanda tangan aktif ke folder surat. Mengembalikan path salinan, atau null. */
    public function salinKeSurat(string $kunci, string $idPengajuan): ?string
    {
        $row = $this->aktif($kunci);
        if (! $row) {
            return null;
        }

        $tujuan = 'surat/tanda-tangan/' . $idPengajuan . '-' . Str::random(8) . '.png';
        Storage::disk('local')->copy($row['path'], $tujuan);

        return $tujuan;
    }

    /* ---------- internal ---------- */

    private function bersihkan(UploadedFile $file): string
    {
        $mentah = (string) file_get_contents($file->getRealPath());
        $info   = @getimagesizefromstring($mentah);

        if (! $info || $info[2] !== IMAGETYPE_PNG) {
            throw ValidationException::withMessages(['tanda_tangan' => 'File harus berupa gambar PNG yang valid.']);
        }

        if (! function_exists('imagecreatefromstring')) {
            return $mentah; // GD tidak aktif: simpan apa adanya (sudah lolos cek PNG)
        }

        $img = @imagecreatefromstring($mentah);
        if (! $img) {
            throw ValidationException::withMessages(['tanda_tangan' => 'Gambar tidak dapat dibaca.']);
        }

        imagealphablending($img, false);
        imagesavealpha($img, true);

        ob_start();
        imagepng($img);
        $out = (string) ob_get_clean();
        imagedestroy($img);

        return $out;
    }

    private function semua(): array
    {
        $disk = Storage::disk('local');

        if (! $disk->exists(self::FILE)) {
            return [];
        }

        return json_decode($disk->get(self::FILE), true) ?: [];
    }

    private function tulis(array $semua): void
    {
        Storage::disk('local')->put(
            self::FILE,
            json_encode((object) $semua, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)
        );
    }
}