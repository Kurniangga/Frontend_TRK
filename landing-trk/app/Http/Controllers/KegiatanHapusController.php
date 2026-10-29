<?php

namespace App\Http\Controllers;

use App\Repositories\KegiatanData;
use Illuminate\Http\Request;

/**
 * Hapus kegiatan dosen / mahasiswa. Dipakai admin DAN kaprodi (dibedakan dari nama route).
 * Kaprodi hanya boleh menghapus kegiatan yang sudah disetujui (yang tampil di daftarnya).
 */
class KegiatanHapusController extends Controller
{
    public function __construct(private KegiatanData $data) {}

    public function dosen(Request $request, string $id)
    {
        return $this->hapus($request, 'dosen', $id);
    }

    public function mahasiswa(Request $request, string $id)
    {
        return $this->hapus($request, 'mahasiswa', $id);
    }

    private function hapus(Request $request, string $jenis, string $id)
    {
        $kaprodi = $request->routeIs('kaprodi.*');
        $row = $this->data->cari($jenis, $id);

        abort_if($row === null, 404, 'Kegiatan tidak ditemukan.');
        abort_if($kaprodi && $row['status'] !== KegiatanData::DISETUJUI, 404, 'Kegiatan tidak ditemukan.');

        $this->data->hapus($jenis, $id);

        return redirect()
            ->route(($kaprodi ? 'kaprodi' : 'admin').'.kegiatan.'.$jenis)
            ->with('success', 'Kegiatan "'.$row['nama'].'" berhasil dihapus.');
    }
}
