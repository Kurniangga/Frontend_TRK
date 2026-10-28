<?php

namespace App\Http\Controllers;

use App\Repositories\KegiatanData;

class KaprodiKegiatanMahasiswaController extends Controller
{
    public function __construct(private KegiatanData $data) {}

    /** Daftar kegiatan mahasiswa (kaprodi): HANYA yang sudah disetujui. */
    public function index()
    {
        $rows = $this->data->mahasiswa();

        // Kaprodi hanya melihat kegiatan yang sudah disetujui (yang sudah/sedang berjalan).
        // Pengajuan baru, revisi, dan penolakan menjadi urusan admin.
        $rows = $this->data->disetujui($rows);

        return view('kaprodi.kegiatan-mahasiswa', [
            'rows' => $rows,
            'tahunList' => $this->data->tahun($rows),
        ]);
    }
}
