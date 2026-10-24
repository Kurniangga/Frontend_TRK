<?php

namespace App\Http\Controllers;

use App\Repositories\DosenStore;

/** Data dosen untuk kaprodi: HANYA BACA (tanpa tambah / ubah / hapus). Data sama dengan admin. */
class KaprodiDosenController extends Controller
{
    public function __construct(private DosenStore $store) {}

    /** Daftar dosen. (Hanya kolom yang dibutuhkan tabel, tidak mengirim hash sandi dll.) */
    public function index()
    {
        // TODO-DB: ganti dengan query tabel dosen (read only).
        $dosen = array_map(fn ($r) => [
            'id' => $r['id'],
            'nip' => $r['nip'],
            'nama' => $r['nama'],
            'jabatan' => $r['jabatan'],
            'golongan' => $r['golongan'],
            'status' => $r['status'],
        ], $this->store->all());

        return view('kaprodi.dosen', ['dosen' => $dosen, 'jabatan' => DosenStore::JABATAN]);
    }
}
