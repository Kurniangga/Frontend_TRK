<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class KaprodiKategoriKegiatanController extends Controller
{
    /** Baca saja: pakai data yang sama dengan halaman admin. */
    public function index(Request $request)
    {
        $data = app(KategoriKegiatanController::class)->index($request)->getData();

        return view('kaprodi.kategori-kegiatan', [
            'rows' => $data['rows'] ?? [],
        ]);
    }
}