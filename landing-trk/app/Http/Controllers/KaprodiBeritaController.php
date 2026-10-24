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
}
