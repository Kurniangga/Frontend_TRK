<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class FasilitasController extends Controller
{
    /** /fasilitas atau /fasilitas?f=2 (nomor urut fasilitas yang tampil pertama, mulai 0). */
    public function index(Request $request)
    {
        $daftar = collect(config('fasilitas.daftar', []))
            ->map(fn ($f) => [
                'nama' => $f['nama'],
                'deskripsi' => $f['deskripsi'],
                'foto' => asset($f['foto']),
            ])
            ->values()
            ->all();

        $aktif = (int) $request->query('f', 0);
        if ($aktif < 0 || $aktif >= count($daftar)) {
            $aktif = 0;
        }

        return view('fasilitas', ['daftar' => $daftar, 'aktif' => $aktif]);
    }
}
