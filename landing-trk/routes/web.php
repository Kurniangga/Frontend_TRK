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

Route::view('/', 'home')->name('home');
Route::get('/mata-kuliah', [MataKuliahController::class, 'index'])->name('mata-kuliah');
Route::get('/akreditasi', [AkreditasiController::class, 'index'])->name('akreditasi');
Route::get('/mata-kuliah/data', [MataKuliahController::class, 'data'])->name('mata-kuliah.data');
Route::get('/kurikulum', [KurikulumController::class, 'index'])->name('kurikulum');
Route::get('/fasilitas', [FasilitasController::class, 'index'])->name('fasilitas');
Route::view('/login', 'login')->name('login');
Route::post('/login', [AdminAuthController::class, 'login'])->name('login.attempt');

Route::prefix('admin')->middleware(AdminOnly::class)->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');
    Route::post('/keluar', [AdminAuthController::class, 'logout'])->name('admin.logout');

    // Halaman sidebar yang isinya belum dibuat (otomatis dari config/admin.php)
    foreach (config('admin.menu') as $m) {
        foreach ($m['children'] ?? [$m] as $c) {
            if (($c['route'] ?? null) && $c['route'] !== 'admin.dashboard') {
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