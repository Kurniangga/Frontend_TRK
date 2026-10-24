<?php

namespace App\Http\Controllers;

use App\Repositories\MahasiswaStore;

/** Data mahasiswa untuk kaprodi: HANYA BACA (tanpa tambah / ubah / hapus). Data sama dengan admin. */
class KaprodiMahasiswaController extends Controller
{
    public function __construct(private MahasiswaStore $store) {}

    /** Daftar mahasiswa. */
    public function index()
    {
        $students = array_map(fn ($r) => [
            'id' => $r['id'],
            'nim' => $r['nim'],
            'name' => $r['nama'],
            'level' => $r['level'],
            'className' => $r['kelas'],
            'status' => $r['status'],
        ], $this->store->all());

        return view('kaprodi.mahasiswa', ['students' => $students, 'levels' => MahasiswaStore::TINGKAT]);
    }
}
