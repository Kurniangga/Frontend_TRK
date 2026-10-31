<?php

namespace App\Http\Controllers;

use App\Repositories\TandaTanganStore;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

/**
 * Unggah dan tampilkan tanda tangan kaprodi.
 * Background sudah dihapus di browser (canvas); server menerima PNG transparan.
 * TODO-DB: simpan ke tabel tanda_tangan.
 */
class KaprodiTandaTanganController extends Controller
{
    /** POST /kaprodi/tanda-tangan (dipanggil lewat fetch, balasan JSON) */
    public function store(Request $request, TandaTanganStore $store)
    {
        $request->validate([
            'tanda_tangan' => ['required', 'file', 'mimetypes:image/png', 'max:1024', 'dimensions:max_width=2000,max_height=2000'],
        ], [
            'tanda_tangan.required'   => 'Pilih gambar tanda tangan terlebih dahulu.',
            'tanda_tangan.mimetypes'  => 'Tanda tangan harus berupa PNG.',
            'tanda_tangan.max'        => 'Ukuran tanda tangan maksimal 1 MB.',
            'tanda_tangan.dimensions' => 'Dimensi gambar maksimal 2000 x 2000 piksel.',
        ]);

        $store->simpan(TandaTanganStore::kunci(), $request->file('tanda_tangan'));

        return response()->json(['url' => route('kaprodi.tanda-tangan.gambar') . '?v=' . time()]);
    }

    /** GET /kaprodi/tanda-tangan/gambar : tanda tangan aktif kaprodi yang sedang login */
    public function gambar(TandaTanganStore $store)
    {
        $row = $store->aktif(TandaTanganStore::kunci());
        abort_unless($row, 404);

        return Storage::disk('local')->response($row['path'], null, [
            'Cache-Control'          => 'private, no-cache',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }
}