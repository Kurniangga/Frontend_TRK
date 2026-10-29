<?php

namespace App\Http\Controllers;

use App\Repositories\KegiatanData;

class KegiatanMahasiswaController extends Controller
{
    public function __construct(private KegiatanData $data) {}

    /** Daftar kegiatan mahasiswa (admin): HANYA yang sudah disetujui. Aksi: lihat & hapus. */
    public function index()
    {
        $rows = $this->data->disetujui($this->data->mahasiswa());

        return view('admin.kegiatan-mahasiswa', [
            'rows' => $rows,
            'tahunList' => $this->data->tahun($rows),
        ]);
    }

    /**
     * Semua baris (semua status), dari sumber data yang sama dengan daftar & laporan.
     * Dipertahankan supaya kode lain yang memanggil daftar() lewat Reflection tidak error.
     */
    private function daftar(): array
    {
        return $this->data->mahasiswa();
    }
}
