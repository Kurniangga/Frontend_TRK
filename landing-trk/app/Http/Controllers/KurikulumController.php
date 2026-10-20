<?php

namespace App\Http\Controllers;

use App\Repositories\KurikulumRepository;
use Illuminate\Http\Request;

class KurikulumController extends Controller
{
    public function __construct(private KurikulumRepository $repo) {}

    /** /kurikulum atau /kurikulum?kurikulum=2026 (tab aktif awal). */
    public function index(Request $request)
    {
        $daftar = $this->repo->all();
        $ids = array_column($daftar, 'id');

        $aktifId = in_array($request->query('kurikulum'), $ids, true)
            ? $request->query('kurikulum')
            : ($ids[0] ?? null);

        return view('kurikulum', [
            'subtitle' => 'Dokumen kurikulum Teknologi Rekayasa Komputer · Politeknik Negeri Semarang',
            'daftar' => $daftar,
            'aktifId' => $aktifId,
        ]);
    }
}
