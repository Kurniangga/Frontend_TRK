<?php

namespace App\Http\Controllers;

class DosenController extends Controller
{
    /** Halaman daftar dosen (admin). Aksi lihat/ubah/hapus/tambah menyusul di tahap berikutnya. */
    public function index()
    {
        // TODO-DB: ganti seluruh data contoh ini dengan query tabel dosen.
        $jabatan = ['Asisten Ahli', 'Lektor', 'Lektor Kepala', 'Guru Besar'];

        $contoh = [
            ['198501012010', 'Angga Wahyu Wibowo', 'Lektor Kepala', 'IV/a'],
            ['199003152015', 'Kuwat Santoso',      'Lektor',        'III/c'],
            ['197812202005', 'Tahan Prahara',      'Guru Besar',    'IV/d'],
            ['198501012010', 'Wiktasari',          'Lektor Kepala', 'IV/a'],
            ['199003152015', 'Filda Huwani Dewi',  'Lektor',        'III/c'],
            ['197812202005', 'Angga Wahyu Wibowo', 'Guru Besar',    'IV/d'],
            ['198501012010', 'Kuwat Santoso',      'Lektor Kepala', 'IV/a'],
            ['199003152015', 'Tahan Prahara',      'Lektor',        'III/c'],
            ['197812202005', 'Wiktasari',          'Guru Besar',    'IV/d'],
            ['198501012010', 'Filda Huwani Dewi',  'Lektor Kepala', 'IV/a'],
        ];

        // 100 baris contoh supaya paginasi (1 2 ... 9 10) terlihat seperti di desain
        $dosen = [];
        for ($i = 0; $i < 100; $i++) {
            $b = $contoh[$i % 10];
            $dosen[] = [
                'id' => (string) ($i + 1),
                'nip' => $b[0],
                'nama' => $b[1],
                'jabatan' => $b[2],
                'golongan' => $b[3],
                'status' => ($i % 13 === 12) ? 'nonaktif' : 'aktif',
            ];
        }

        return view('admin.dosen', compact('dosen', 'jabatan'));
    }
}
