<?php

namespace App\Http\Controllers;

use App\Repositories\PengajuanStore;

/** Daftar pengajuan sisi admin: hanya yang masih perlu ditinjau (Diajukan / Direvisi). */
class AdminPengajuanController extends Controller
{
    public function __construct(private PengajuanStore $pengajuan)
    {
    }

    public function index()
    {
        // TODO-DB: query database: WHERE status IN ('Diajukan', 'Direvisi')
        return view('admin.pengajuan', ['rows' => $this->pengajuan->daftar()]);
    }
}