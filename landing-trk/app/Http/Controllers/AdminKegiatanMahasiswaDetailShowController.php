<?php

namespace App\Http\Controllers;

use ReflectionMethod;

class AdminKegiatanMahasiswaDetailShowController extends Controller
{
    /** Detail satu pengajuan kegiatan mahasiswa (baca saja), diambil dari data daftar yang sama. */
    public function show(string $id)
    {
        $row = collect($this->semuaBaris())
            ->map(fn ($r) => (array) $r)
            ->first(fn ($r) => (string) ($r['id'] ?? '') === (string) $id);

        abort_if($row === null, 404, 'Kegiatan tidak ditemukan.');

        $d = $this->susunDetail($row);

        return view()->first([
            'admin.kegiatan-mahasiswa-detail-show',
            'admin.kegiatan-mahasiswa-detail',
        ], compact('d'));
    }

    /** Ambil seluruh baris dari KegiatanMahasiswaController::daftar() (public/private/static semuanya bisa). */
    private function semuaBaris(): array
    {
        $ctrl   = app(KegiatanMahasiswaController::class);
        $method = new ReflectionMethod($ctrl, 'daftar');
        $method->setAccessible(true);

        return collect($method->invoke($ctrl))->all();
    }

    /** Nilai pertama yang ada dari daftar kemungkinan nama kolom. */
    private function ambil(array $row, array $keys, $default = '-')
    {
        foreach ($keys as $k) {
            if (isset($row[$k]) && $row[$k] !== '' && $row[$k] !== null) {
                return $row[$k];
            }
        }

        return $default;
    }

    private function susunDetail(array $row): array
    {
        $judul   = $this->ambil($row, ['kegiatan', 'nama_kegiatan', 'nama', 'judul', 'name', 'title']);
        $pengaju = $this->ambil($row, ['pengaju', 'mahasiswa', 'nama_mahasiswa', 'dosen']);
        $tanggal = $this->ambil($row, ['tanggal', 'tgl', 'date']);
        $status  = $this->ambil($row, ['status'], 'Diajukan');

        // Riwayat: pakai bawaan data bila ada, kalau tidak disusun dari data yang tersedia.
        $riwayat = $row['riwayat'] ?? [
            "Diajukan oleh {$pengaju}, {$tanggal}",
            $status === 'Diajukan' ? 'Menunggu verifikasi admin' : "Status saat ini: {$status}",
        ];

        return [
            'judul'         => $judul,
            'status'        => $status,
            'pengaju'       => $pengaju,
            'kategori'      => $this->ambil($row, ['kategori', 'category']),
            'jenis'         => 'Kegiatan mahasiswa',
            'penyelenggara' => $this->ambil($row, ['penyelenggara', 'organizer']),
            'tanggal'       => $tanggal,
            'anggota'       => $row['anggota'] ?? [['nama' => $pengaju, 'status' => 'Pengaju']],
            'dokumen'       => $row['dokumen'] ?? [],
            'riwayat'       => $riwayat,
        ];
    }
}