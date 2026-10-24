<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class KaprodiJenisDokumenController extends Controller
{
    /** Baca saja: pakai data yang sama dengan halaman admin. */
    public function index(Request $request)
    {
        $data = app(JenisDokumenController::class)->index($request)->getData();

        return view('kaprodi.jenis-dokumen', [
            'rows' => $data['rows'] ?? [],
        ]);
    }
}