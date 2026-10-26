<?php

namespace App\Http\Controllers;

/**
 * Halaman "Profil Saya" khusus Kaprodi.
 * Data diambil otomatis dari session('kaprodi') akun yang sedang login.
 * TODO-DB: nanti ganti sumbernya ke tabel users (NIP, jabatan, dll).
 */
class KaprodiProfilController extends Controller
{
    public function index()
    {
        $s = session('kaprodi');
        $s = is_array($s) ? $s : [];

        $p = [
            'nama'   => $s['nama']  ?? 'Kaprodi TRK',
            'email'  => $s['email'] ?? 'trk@kaprodi.polines.ac.id',
            'nip'    => $s['nip']   ?? '-',
            'peran'  => 'Kaprodi',
            'unit'   => $s['unit']  ?? 'Prodi Teknologi Rekayasa Komputer',
            'kampus' => 'Politeknik Negeri Semarang',
            'status' => 'Aktif',
        ];

        return view('kaprodi.kaprodi-profil', compact('p'));
    }
}