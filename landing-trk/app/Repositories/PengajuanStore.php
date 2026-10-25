<?php

namespace App\Repositories;

use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

/**
 * Satu-satunya pintu data Pengajuan Kegiatan, dipakai admin DAN kaprodi
 * (daftar + detail + simpan keputusan).
 *
 * Sumber data: storage/app/private/admin/pengajuan.json
 * File dibuat otomatis dari contoh() bila belum ada (atau kosong), seperti DosenStore.
 * Mau mengubah data awal? Edit contoh() lalu hapus pengajuan.json.
 *
 * TODO-DB: ganti isi class ini dengan query tabel pengajuan saat database siap
 * (nama method dan bentuk data yang dikembalikan tidak perlu berubah).
 */
class PengajuanStore
{
    private const FILE = 'admin/pengajuan-keputusan.json';

    /** Kolom standar satu pengajuan (supaya semua properti selalu ada). */
    private const KOSONG = [
        'id' => null, 'nama' => '', 'kategori' => '', 'jenis' => '', 'jenis_pengaju' => '',
        'pengaju' => '', 'penyelenggara' => '', 'tanggal' => null, 'semester' => '',
        'anggota' => [], 'dokumen' => [], 'riwayat' => [],
        'status' => 'Diajukan', 'nomor_surat' => null, 'catatan' => null, 'catatan_kaprodi' => null,
        'surat_pengajuan_nama' => null, 'surat_pengajuan_path' => null,
    ];

    /** Baris untuk halaman daftar: hanya pengajuan yang masih perlu ditinjau. */
    public function daftar(): array
    {
        $out = [];
        foreach ($this->semua() as $r) {
            if (! in_array($r['status'], ['Diajukan', 'Direvisi'], true)) {
                continue;
            }
            $out[] = [
                'id'            => $r['id'],
                'nama'          => $r['nama'],
                'kategori'      => $r['kategori'],
                'pengaju'       => $r['pengaju'],
                'tanggal'       => $this->tanggalIndo($r['tanggal']),
                'status'        => $r['status'],
                'semester'      => $r['semester'],
                'jenis_pengaju' => $r['jenis_pengaju'],
            ];
        }
        return $out;
    }

    /** Satu pengajuan sebagai objek ($p->nama, $p->status, ...). 404 bila id tidak ada. */
    public function cari(string|int $id): object
    {
        foreach ($this->semua() as $r) {
            if ((string) $r['id'] === (string) $id) {
                return (object) $r;
            }
        }
        abort(404);
    }

    /**
     * Gabungkan $field ke pengajuan lalu tambahkan $baris ke riwayat.
     * Baris "Menunggu ..." dihapus begitu ada keputusan.
     */
    public function simpanKeputusan(object $p, array $field, string $baris): void
    {
        $semua = $this->semua();

        foreach ($semua as $i => $r) {
            if ((string) $r['id'] !== (string) $p->id) {
                continue;
            }

            $riwayat = array_values(array_filter(
                $r['riwayat'] ?? [],
                fn ($x) => ! str_starts_with($x, 'Menunggu')
            ));
            $riwayat[] = $baris;

            $semua[$i] = array_merge($r, $field, ['riwayat' => $riwayat]);
            break;
        }

        $this->tulis($semua);
    }

    /* ---------- bentuk data untuk view ---------- */

    public function info(object $p): array
    {
        return [
            ['Pengaju', $p->pengaju],
            ['Kategori', $p->kategori],
            ['Jenis', $p->jenis],
            ['Penyelenggara', $p->penyelenggara],
            ['Tanggal', $this->tanggalIndo($p->tanggal)],
        ];
    }

    public function anggota(object $p): array
    {
        return array_map(fn ($a) => [$a['nama'], $a['status']], $p->anggota ?? []);
    }

    public function dokumen(object $p): array
    {
        return array_map(fn ($d) => [
            $d['nama'],
            $d['kategori'],
            ! empty($d['path']) ? Storage::disk('public')->url($d['path']) : '#',
        ], $p->dokumen ?? []);
    }

    /** Surat pengajuan yang diunggah kaprodi, atau null bila belum ada. */
    public function surat(object $p): ?array
    {
        if (! $p->surat_pengajuan_path) {
            return null;
        }
        return [
            'nama' => $p->surat_pengajuan_nama ?: basename($p->surat_pengajuan_path),
            'url'  => Storage::disk('public')->url($p->surat_pengajuan_path),
        ];
    }

    /* ---------- file JSON ---------- */

    private function semua(): array
    {
        $disk = Storage::disk('local');

        $data = $disk->exists(self::FILE) ? json_decode($disk->get(self::FILE), true) : null;

        // File belum ada / kosong / rusak -> isi dengan data contoh.
        if (! is_array($data) || $data === []) {
            $data = $this->contoh();
            $this->tulis($data);
        }

        return array_map(fn ($r) => array_merge(self::KOSONG, $r), $data);
    }

    private function tulis(array $semua): void
    {
        Storage::disk('local')->put(
            self::FILE,
            json_encode(array_values($semua), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
        );
    }

    private function tanggalIndo(?string $tanggal): string
    {
        return $tanggal ? Carbon::parse($tanggal)->locale('id')->translatedFormat('j F Y') : '-';
    }

    /** Data awal. Ubah sesuka Anda, lalu hapus pengajuan.json agar dibuat ulang. */
    private function contoh(): array
    {
        $anggota = fn (string $kegiatan) => [
            ['nama' => $kegiatan, 'status' => 'Dosen'],
            ['nama' => 'Pelatihan Industri', 'status' => 'Mahasiswa'],
        ];

        $dokumen = [
            ['nama' => 'proposal_kegiatan.pdf', 'kategori' => 'Proposal', 'path' => null],
            ['nama' => 'surat_undangan.pdf', 'kategori' => 'Surat Undangan', 'path' => null],
        ];

        $baris = [
            [
                'nama' => 'Seminar Nasional Kecerdasan Buatan', 'kategori' => 'Seminar', 'jenis' => 'Kegiatan Akademik',
                'jenis_pengaju' => 'Mahasiswa', 'pengaju' => 'Dea Cantika', 'tanggal' => '2026-01-01', 'semester' => 'Ganjil',
                'status' => 'Diajukan',
                'riwayat' => ['Diajukan oleh Dea Cantika, 1 Januari 2026', 'Menunggu verifikasi admin/kaprodi'],
            ],
            [
                'nama' => 'Workshop Penulisan Jurnal Internasional', 'kategori' => 'Pelatihan', 'jenis' => 'Kegiatan dosen',
                'jenis_pengaju' => 'Dosen', 'pengaju' => 'Kuwat Santoso', 'tanggal' => '2026-09-14', 'semester' => 'Ganjil',
                'status' => 'Diajukan',
                'riwayat' => ['Diajukan oleh Kuwat Santoso, 12 September 2026', 'Menunggu verifikasi admin/kaprodi'],
            ],
            [
                'nama' => 'Lomba Cerdas Cermat Informatika', 'kategori' => 'Lomba', 'jenis' => 'Kegiatan Akademik',
                'jenis_pengaju' => 'Mahasiswa', 'pengaju' => 'Rizky Pratama', 'tanggal' => '2026-03-20', 'semester' => 'Genap',
                'status' => 'Diajukan',
                'riwayat' => ['Diajukan oleh Rizky Pratama, 15 Maret 2026', 'Menunggu verifikasi admin/kaprodi'],
            ],
            [
                'nama' => 'Pelatihan Sertifikasi Jaringan', 'kategori' => 'Pelatihan', 'jenis' => 'Kegiatan Akademik',
                'jenis_pengaju' => 'Mahasiswa', 'pengaju' => 'Aulia Rahmawati', 'tanggal' => '2026-04-10', 'semester' => 'Genap',
                'status' => 'Direvisi', 'catatan' => 'Lengkapi rincian anggaran pada proposal.',
                'riwayat' => ['Diajukan oleh Aulia Rahmawati, 1 April 2026', 'Direvisi oleh Admin, 3 April 2026: Lengkapi rincian anggaran pada proposal.'],
            ],
            [
                'nama' => 'Seminar Nasional AI', 'kategori' => 'Lomba', 'jenis' => 'Kegiatan dosen',
                'jenis_pengaju' => 'Dosen', 'pengaju' => 'Kuwat Santoso', 'tanggal' => '2026-09-14', 'semester' => 'Ganjil',
                'status' => 'Diajukan',
                'riwayat' => ['Diajukan oleh Kuwat Santoso, 12 September 2026', 'Menunggu verifikasi admin/kaprodi'],
            ],
            [
                'nama' => 'Kunjungan Industri ke Jakarta', 'kategori' => 'Kunjungan', 'jenis' => 'Kegiatan Akademik',
                'jenis_pengaju' => 'Mahasiswa', 'pengaju' => 'Dimas Saputra', 'tanggal' => '2026-05-12', 'semester' => 'Genap',
                'status' => 'Diajukan',
                'riwayat' => ['Diajukan oleh Dimas Saputra, 2 Mei 2026', 'Menunggu verifikasi admin/kaprodi'],
            ],
        ];

        $out = [];
        foreach ($baris as $i => $b) {
            $out[] = array_merge(self::KOSONG, $b, [
                'id'            => $i + 1,
                'penyelenggara' => 'Politeknik Negeri Semarang',
                'anggota'       => $anggota($b['nama']),
                'dokumen'       => $dokumen,
            ]);
        }
        return $out;
    }
}