<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; // Tambahkan ini wajib untuk pakai Query Builder

class HomeController extends Controller
{
    public function index()
    {
        // 1. Ambil data gabungan dari tabel users dan dosen
        $dosenDb = DB::table('users')
            ->join('dosen', 'users.id_user', '=', 'dosen.id_user')
            ->where('users.id_role', 3) // Asumsi 3 adalah ID untuk Dosen. Sesuaikan jika salah!
            ->where('users.status', 1)  // Mengambil yang statusnya aktif saja (opsional tapi bagus)
            ->select('dosen.nama', 'users.foto_profil')
            ->get();

        // 2. Format ulang ke bentuk array agar sesuai dengan frontend
        $lecturers = $dosenDb->map(function ($d) {
            return [
                $d->nama ?? 'Nama Dosen',
                $d->foto_profil ?? 'images/logo_trk remove.png'
            ];
        })->toArray();

        // 3. Kirim ke tampilan Blade
        return view('home', compact('lecturers'));
    }
}