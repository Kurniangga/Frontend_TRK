<?php

namespace App\Http\Controllers;

use Carbon\Carbon;

class AdminPengajuanController extends Controller
{
    public function index()
    {
        return view('admin.pengajuan', ['rows' => $this->daftarPengajuan()]);
    }

    /** TODO-DB: Nanti diganti dengan query database: WHERE status IN ('Diajukan', 'Direvisi') */
    private function daftarPengajuan(): array
    {
        $kegiatan = [
            ['Seminar Nasional Kecerdasan Buatan', 'Seminar'],
            ['Penelitian Internet of Things',      'Penelitian'],
            ['Pengabdian Pelatihan Web Desa',      'Pengabdian'],
            ['Pelatihan Sertifikasi Jaringan',     'Pelatihan'],
            ['Kompetisi Pengembangan Web',         'Lomba'],
            ['Magang Industri Semester 6',         'Magang'],
        ];
        
        $pengaju = ['Dea Cantika', 'Kuwat Santoso', 'Rizky Pratama', 'Aulia Rahmawati', 'Angga Wahyu Wibowo'];
        
        $status = ['Diajukan', 'Direvisi', 'Diajukan', 'Direvisi']; 

        $rows = [];
        for ($i = 0; $i < 25; $i++) { // Sengaja dibuat lebih sedikit (25) agar terlihat seperti antrean
            $k = $kegiatan[$i % 6];
            
            $rows[] = [
                'id' => (string) ($i + 1),
                'nama' => $k[0],
                'kategori' => $k[1],
                'pengaju' => $pengaju[$i % 5],
                'tanggal' => Carbon::create(2026, 1 + ($i % 10), 1 + (($i * 3) % 28))->locale('id')->translatedFormat('j F Y'),
                'status' => $status[$i % 4],
                'semester' => $i % 2 === 0 ? 'Ganjil' : 'Genap',
                
                'jenis_pengaju' => $i % 2 === 0 ? 'Dosen' : 'Mahasiswa', 
            ];
        }

        return $rows;
    }
}