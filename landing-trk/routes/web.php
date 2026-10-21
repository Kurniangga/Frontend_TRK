<?php

use App\Http\Controllers\MataKuliahController;
use App\Http\Controllers\AkreditasiController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\KurikulumController;
use App\Http\Controllers\FasilitasController;
use App\Http\Controllers\PemulihanController;

Route::view('/', 'home')->name('home');
Route::get('/mata-kuliah', [MataKuliahController::class, 'index'])->name('mata-kuliah');
Route::get('/akreditasi', [AkreditasiController::class, 'index'])->name('akreditasi');
Route::get('/mata-kuliah/data', [MataKuliahController::class, 'data'])->name('mata-kuliah.data');
Route::get('/kurikulum', [KurikulumController::class, 'index'])->name('kurikulum');
Route::get('/fasilitas', [FasilitasController::class, 'index'])->name('fasilitas');
Route::view('/login', 'login')->name('login');
Route::post('/login', [PemulihanController::class, 'login'])->name('login.attempt');
Route::get('/lupa-sandi', [PemulihanController::class, 'show'])->name('password.request');
Route::post('/lupa-sandi/email', [PemulihanController::class, 'kirimOtp'])->name('password.email');
Route::post('/lupa-sandi/otp', [PemulihanController::class, 'verifikasi'])->name('password.otp');
Route::post('/lupa-sandi/kirim-ulang', [PemulihanController::class, 'kirimUlang'])->name('password.kirim-ulang');
Route::post('/lupa-sandi/ubah-email', [PemulihanController::class, 'ubahEmail'])->name('password.ubah-email');
Route::post('/lupa-sandi/sandi', [PemulihanController::class, 'simpan'])->name('password.simpan');