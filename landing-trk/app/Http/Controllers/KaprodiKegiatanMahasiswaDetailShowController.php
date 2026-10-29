<?php

namespace App\Http\Controllers;

use App\Repositories\KegiatanData;

class KaprodiKegiatanMahasiswaDetailShowController extends Controller
{
    public function __construct(private KegiatanData $data) {}

    /** Detail satu kegiatan mahasiswa (baca saja), dari sumber data yang sama dengan daftar. */
    public function show(string $id)
    {
        $row = $this->data->cari('mahasiswa', $id);

        abort_if($row === null, 404, 'Kegiatan tidak ditemukan.');
        abort_if($row['status'] !== KegiatanData::DISETUJUI, 404, 'Kegiatan tidak ditemukan.');

        $d = $this->susunDetail($row);

        return view()->first([
            'kaprodi.kaprodi-kegiatan-mahasiswa-detail-show',
            'kaprodi.kegiatan-mahasiswa-detail',
        ], compact('d'));
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
