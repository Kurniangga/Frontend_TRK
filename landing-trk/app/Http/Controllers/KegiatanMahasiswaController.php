<?php

namespace App\Http\Controllers;

use App\Repositories\KegiatanData;

class KegiatanMahasiswaController extends Controller
{
    public function __construct(private KegiatanData $data) {}

    /** Daftar kegiatan mahasiswa (admin): semua status, untuk divalidasi. */
    public function index()
    {
        $rows = $this->data->mahasiswa();

        return view('admin.kegiatan-mahasiswa', [
            'rows' => $rows,
            'tahunList' => $this->data->tahun($rows),
        ]);
    }
}
