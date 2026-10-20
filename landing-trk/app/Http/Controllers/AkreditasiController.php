<?php

namespace App\Http\Controllers;

use App\Models\Akreditasi;
use Illuminate\Http\Request;

class AkreditasiController extends Controller
{
    /** Halaman publik: menampilkan PDF akreditasi terbaru. */
    public function index()
    {
        $dokumen = Akreditasi::terbaru();

        return view('akreditasi', [
            'title' => 'Akreditasi',
            'subtitle' => 'Dokumen akreditasi Program Studi Teknologi Rekayasa Komputer Politeknik Negeri Semarang',
            'pdfUrl' => $dokumen?->url,
            'fileName' => $dokumen?->file_name ?? 'akreditasi-trk.pdf',
        ]);
    }

    /** Upload PDF baru (dipanggil dari dashboard admin). Yang terbaru otomatis tampil di halaman publik. */
    public function store(Request $request)
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:pdf', 'max:20480'], // maks 20 MB
        ]);

        $file = $request->file('file');

        Akreditasi::create([
            'file_path' => $file->store('akreditasi', 'public'),
            'file_name' => $file->getClientOriginalName(),
        ]);

        return back()->with('success', 'Dokumen akreditasi berhasil diunggah.');
    }
}
