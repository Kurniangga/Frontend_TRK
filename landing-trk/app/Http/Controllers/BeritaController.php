<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class BeritaController extends Controller
{
    // Halaman daftar berita: /kabar (tujuan tombol "Muat lebih banyak")
    public function index(Request $request)
    {
        // TODO-DB: ganti dengan Berita::latest()->paginate(4)
        $semua = collect(config('berita', []))->values();

        $perHalaman = 4;
        $halaman    = LengthAwarePaginator::resolveCurrentPage();

        $berita = new LengthAwarePaginator(
            $semua->forPage($halaman, $perHalaman)->values(),
            $semua->count(),
            $perHalaman,
            $halaman,
            ['path' => $request->url()]
        );

        $terbaru  = $semua->take(3);
        $prestasi = $semua->where('kategori', 'Prestasi')->take(3);

        return view('berita-index', compact('berita', 'terbaru', 'prestasi'));
    }

    public function show(string $slug)
    {
        // TODO-DB: ganti config() dengan Berita::where('slug', $slug)->firstOrFail()
        $semua = config('berita', []);

        abort_unless(isset($semua[$slug]), 404);

        $berita = $semua[$slug];

        // Berita lain (selain yang sedang dibuka), maksimal 4
        $terkait = collect($semua)
            ->except($slug)
            ->take(4)
            ->all();

        return view('berita-show', compact('berita', 'terkait'));
    }
}