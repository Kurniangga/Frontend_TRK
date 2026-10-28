<?php

namespace App\Http\Controllers;

use App\Repositories\KegiatanData;

class KegiatanDosenController extends Controller
{
    public function __construct(private KegiatanData $data) {}

    /** Daftar kegiatan dosen (admin): semua status, untuk divalidasi. */
    public function index()
    {
        $rows = $this->data->dosen();

        return view('admin.kegiatan-dosen', [
            'rows' => $rows,
            'tahunList' => $this->data->tahun($rows),
        ]);
    }
}
