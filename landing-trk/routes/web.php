<?php

use App\Http\Controllers\MataKuliahController;
use App\Http\Controllers\AkreditasiController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\KurikulumController;
use App\Http\Controllers\FasilitasController;
use App\Http\Controllers\PemulihanController;
use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Middleware\AdminOnly;
use App\Http\Controllers\AdminKurikulumController;
use App\Http\Controllers\DosenController;
use App\Http\Controllers\MahasiswaController;
use App\Http\Controllers\BeritaController;
use App\Http\Controllers\PrestasiController;
use App\Http\Controllers\KegiatanMahasiswaController;


Route::view('/', 'home')->name('home');
Route::get('/mata-kuliah', [MataKuliahController::class, 'index'])->name('mata-kuliah');
Route::get('/akreditasi', [AkreditasiController::class, 'index'])->name('akreditasi');
Route::get('/mata-kuliah/data', [MataKuliahController::class, 'data'])->name('mata-kuliah.data');
Route::get('/kurikulum', [KurikulumController::class, 'index'])->name('kurikulum');
Route::get('/fasilitas', [FasilitasController::class, 'index'])->name('fasilitas');
Route::view('/login', 'login')->name('login');
Route::post('/login', [AdminAuthController::class, 'login'])->name('login.attempt');
Route::get('/kabar', [BeritaController::class, 'index'])->name('berita.index');
Route::get('/kabar/{slug}', [BeritaController::class, 'show'])->name('berita.show');
Route::get('/prestasi', [PrestasiController::class, 'index'])->name('prestasi.index');

Route::prefix('admin')->middleware(AdminOnly::class)->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');
    Route::post('/keluar', [AdminAuthController::class, 'logout'])->name('admin.logout');

    Route::get('/kurikulum/trk', [AdminKurikulumController::class, 'index'])->name('admin.kurikulum.trk');
    Route::post('/kurikulum/trk', [AdminKurikulumController::class, 'store'])->name('admin.kurikulum.store');
    Route::put('/kurikulum/trk/{id}', [AdminKurikulumController::class, 'update'])->name('admin.kurikulum.update');
    Route::patch('/kurikulum/trk/{id}/status', [AdminKurikulumController::class, 'status'])->name('admin.kurikulum.status');
    Route::delete('/kurikulum/trk/{id}', [AdminKurikulumController::class, 'destroy'])->name('admin.kurikulum.destroy');

    Route::get('/dosen', [DosenController::class, 'index'])->name('admin.dosen');
        Route::get('/dosen/{id}', [DosenController::class, 'show'])->name('admin.dosen.show');
        Route::get('/dosen/{id}/edit', [DosenController::class, 'edit'])->name('admin.dosen.edit');
        Route::put('/dosen/{id}', [DosenController::class, 'update'])->name('admin.dosen.update');   // ← BARU

    Route::get('/mahasiswa', [MahasiswaController::class, 'index'])->name('admin.mahasiswa');
        Route::get('/mahasiswa/{id}', [MahasiswaController::class, 'show'])->name('admin.mahasiswa.show');
    Route::get('/kegiatan/mahasiswa', [KegiatanMahasiswaController::class, 'index'])->name('admin.kegiatan.mahasiswa'); 

    // Halaman sidebar yang isinya belum dibuat
    foreach (config('admin.menu') as $m) {
        foreach ($m['children'] ?? [$m] as $c) {
            if (($c['route'] ?? null) && ! in_array($c['route'], ['admin.dashboard', 'admin.kurikulum.trk', 'admin.dosen', 'admin.mahasiswa', 'admin.kegiatan.mahasiswa'])) {   // ← 'admin.dosen' ditambahkan
                Route::get($c['url'], [DashboardController::class, 'placeholder'])->name($c['route']);
            }
        }
    }
});

Route::get('/lupa-sandi', [PemulihanController::class, 'show'])->name('password.request');
Route::post('/lupa-sandi/email', [PemulihanController::class, 'kirimOtp'])->name('password.email');
Route::post('/lupa-sandi/otp', [PemulihanController::class, 'verifikasi'])->name('password.otp');
Route::post('/lupa-sandi/kirim-ulang', [PemulihanController::class, 'kirimUlang'])->name('password.kirim-ulang');
Route::post('/lupa-sandi/ubah-email', [PemulihanController::class, 'ubahEmail'])->name('password.ubah-email');
Route::post('/lupa-sandi/sandi', [PemulihanController::class, 'simpan'])->name('password.simpan');