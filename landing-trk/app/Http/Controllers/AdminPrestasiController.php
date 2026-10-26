<?php

namespace App\Http\Controllers;

/**
 * Halaman "Prestasi" (admin): prestasi dosen & mahasiswa dalam satu tabel.
 * Prestasi langsung diunggah (tanpa pengajuan/status). Satu orang mengunggah,
 * peraihnya bisa individu atau tim (daftar 'anggota').
 * Data masih simulasi. TODO-DB: ganti isi daftar() dengan data asli.
 */
class AdminPrestasiController extends Controller
{
    public function index()
    {
        return view('admin.prestasi', ['rows' => $this->daftar()]);
    }

    // --- METHOD BARU UNTUK MENAMPILKAN DETAIL PRESTASI ---
    public function show($id)
    {
        $rows = $this->daftar();
        
        // Mencari 1 data prestasi yang id-nya sesuai dengan parameter $id dari URL
        $prestasi = collect($rows)->firstWhere('id', (int) $id);

        // Jika data tidak ditemukan, tampilkan error 404
        if (!$prestasi) {
            abort(404);
        }

        // Tampilkan halaman detail dengan membawa data prestasi yang ditemukan
        return view('admin.prestasi-detail', compact('prestasi'));
    }

    // (Simpan metode index, show, dan daftar yang sudah ada sebelumnya)

    public function create()
    {
        return view('admin.prestasi-tambah');
    }

    public function store()
    {
        // TODO-DB: Logika simpan data ke database
        return redirect()->route('admin.prestasi')->with('success', 'Prestasi berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $rows = $this->daftar();
        $prestasi = collect($rows)->firstWhere('id', (int) $id);

        if (!$prestasi) {
            abort(404);
        }

        return view('admin.prestasi-edit', compact('prestasi'));
    }

    public function update($id)
    {
        // TODO-DB: Logika pembaruan data ke database
        return redirect()->route('admin.prestasi')->with('success', 'Prestasi berhasil diperbarui.');
    }

    public function destroy($id)
    {
        // TODO-DB: Logika hapus data dari database
        return redirect()->route('admin.prestasi')->with('success', 'Prestasi berhasil dihapus.');
    }

    private function daftar(): array
    {
        $judul = [
            ['Juara 1 Lomba Inovasi Teknologi Nasional', 'Lomba'],
            ['Juara 2 Kompetisi Karya Tulis Ilmiah', 'Lomba'],
            ['Medali Perak Kejuaraan Robotik Nasional', 'Lomba'],
            ['Juara Harapan 1 Hackathon Nasional', 'Lomba'],
            ['Dosen Berprestasi Tingkat Politeknik', 'Penghargaan'],
            ['Penghargaan Mahasiswa Berprestasi', 'Penghargaan'],
            ['Piagam Penghargaan Karya Inovatif', 'Penghargaan'],
        ];
        // [pengunggah, tipe pengunggah, anggota tim (termasuk pengunggah)]
        $unggah = [
            ['Dea Cantika', 'Dosen', ['Dea Cantika']],
            ['Rizky Pratama', 'Mahasiswa', ['Rizky Pratama', 'Citra Lestari', 'Bagas Setiawan']],
            ['Kuwat Santoso', 'Dosen', ['Kuwat Santoso', 'Angga Wahyu Wibowo']],
            ['Citra Lestari', 'Mahasiswa', ['Citra Lestari']],
            ['Angga Wahyu Wibowo', 'Dosen', ['Angga Wahyu Wibowo']],
            ['Aulia Rahmawati', 'Mahasiswa', ['Aulia Rahmawati', 'Rizky Pratama']],
        ];
        $bulan = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober'];

        $rows = [];
        for ($i = 0; $i < 28; $i++) {
            $j = $judul[$i % count($judul)];
            $u = $unggah[$i % count($unggah)];
            $rows[] = [
                'id'         => $i + 1,
                'nama'       => $j[0],
                'kategori'   => $j[1],
                'pengunggah' => $u[0],
                'tipe'       => $u[1],
                'anggota'    => $u[2],
                'tanggal'    => (($i % 27) + 1).' '.$bulan[$i % 10].' 2026',
                'semester'   => $i % 2 === 0 ? 'Ganjil' : 'Genap',
            ];
        }

        return $rows;
    }
}