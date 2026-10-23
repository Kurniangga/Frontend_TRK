<?php

namespace App\Http\Controllers;

use App\Repositories\KurikulumStore;

/** Kurikulum TRK untuk kaprodi: HANYA BACA (tanpa tambah / ubah / hapus). Data sama dengan admin. */
class KaprodiKurikulumController extends Controller
{
    public function __construct(private KurikulumStore $store) {}

    public function index()
    {
        // TODO-DB: ganti dengan query tabel kurikulum (read only).
        $kurikulum = array_map(fn ($r) => $this->store->forAdmin($r), $this->store->all());

        return view('kaprodi.kurikulum', compact('kurikulum'));
    }
}
