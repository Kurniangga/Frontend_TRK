<?php

namespace App\Http\Controllers;

use App\Repositories\AdminBeritaStore;

/** Daftar berita (admin). Beda dengan BeritaController (halaman publik /kabar). */
class AdminBeritaController extends Controller
{
    public function __construct(private AdminBeritaStore $store) {}

    public function index()
    {
        return view('admin.berita', ['rows' => $this->store->all(), 'kegiatan' => AdminBeritaStore::KEGIATAN]);
    }
}
