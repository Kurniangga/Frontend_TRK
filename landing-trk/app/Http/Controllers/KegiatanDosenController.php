<?php

namespace App\Http\Controllers;

use App\Repositories\KegiatanData;

class KegiatanDosenController extends Controller
{
    public function __construct(private KegiatanData $data) {}

    /** Daftar kegiatan dosen (admin): HANYA yang sudah disetujui. Aksi: lihat & hapus. */
    public function index()
    {
        $rows = $this->data->disetujui($this->data->dosen());

        return view('admin.kegiatan-dosen', [
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
        return $this->data->dosen();
    }
}
