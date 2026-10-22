<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class PrestasiController extends Controller
{
    // Halaman daftar prestasi: /prestasi (tujuan tombol "Muat lebih banyak" di landing)
    public function index(Request $request)
    {
        // TODO-DB: ganti dengan Prestasi::orderBy('juara')->paginate(8)
        $semua = collect(config('prestasi', []))->values();

        $perHalaman = 8;
        $halaman    = LengthAwarePaginator::resolveCurrentPage();

        $prestasi = new LengthAwarePaginator(
            $semua->forPage($halaman, $perHalaman)->values(),
            $semua->count(),
            $perHalaman,
            $halaman,
            ['path' => $request->url()]
        );

        return view('prestasi-index', compact('prestasi'));
    }
}