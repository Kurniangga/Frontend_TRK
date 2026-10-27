<?php

namespace App\Http\Controllers;

use App\Repositories\AdminBeritaStore;

/** Daftar berita (kaprodi, baca saja). Beda dengan BeritaController (halaman publik /kabar). */
class KaprodiBeritaController extends Controller
{
    public function __construct(private AdminBeritaStore $store) {}

    public function index()
    {
        return view('kaprodi.berita', ['rows' => $this->store->all(), 'kegiatan' => AdminBeritaStore::KEGIATAN]);
    }

    /** Detail berita. */
    public function show(string $id)
    {
        $berita = $this->store->find($id);
        abort_unless($berita, 404);

        $tanggal = $berita['tanggal'] ? \Carbon\Carbon::parse($berita['tanggal'])->locale('id') : null;
        $kata = str_word_count(strip_tags($berita['isi']));

        return view('kaprodi.berita-detail', [
            'berita' => $berita,
            'paragraf' => array_values(array_filter(preg_split('/\R{1,}/', trim($berita['isi'])))),
            'foto' => $this->store->fotoUrl($berita),
            'hariTanggal' => $tanggal ? $tanggal->translatedFormat('l, j F Y') : '-',
            'tanggalPendek' => $tanggal ? $tanggal->format('d/m/Y') : '-',
            'waktuBaca' => max(1, (int) ceil($kata / 200)).' menit',
            'terkait' => $this->store->related($berita),
        ]);
    }
}