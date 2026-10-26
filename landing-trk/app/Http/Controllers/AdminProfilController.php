<?php

namespace App\Http\Controllers;

/**
 * Halaman "Profil Saya" khusus Admin.
 * Data diambil otomatis dari session('admin') akun yang sedang login.
 * TODO-DB: nanti ganti sumbernya ke tabel users (NIP, jabatan, dll).
 */
class AdminProfilController extends Controller
{
    public function index()
    {
        $s = session('admin');
        $s = is_array($s) ? $s : [];

        $p = [
            'nama'   => $s['nama']  ?? 'Admin TRK',
            'email'  => $s['email'] ?? 'trk@admin.polines.ac.id',
            'nip'    => $s['nip']   ?? '-',
            'peran'  => 'Admin',
            'unit'   => $s['unit']  ?? 'Prodi Teknologi Rekayasa Komputer',
            'kampus' => 'Politeknik Negeri Semarang',
            'status' => 'Aktif',
        ];

        return view('admin.admin-profil', compact('p'));
    }
}