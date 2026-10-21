<?php

use App\Http\Controllers\MataKuliahController;
use App\Http\Controllers\AkreditasiController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\KurikulumController;
use App\Http\Controllers\FasilitasController;

Route::view('/', 'home')->name('home');
Route::get('/mata-kuliah', [MataKuliahController::class, 'index'])->name('mata-kuliah');
Route::get('/akreditasi', [AkreditasiController::class, 'index'])->name('akreditasi');
Route::get('/mata-kuliah/data', [MataKuliahController::class, 'data'])->name('mata-kuliah.data');
Route::get('/kurikulum', [KurikulumController::class, 'index'])->name('kurikulum');
Route::get('/fasilitas', [FasilitasController::class, 'index'])->name('fasilitas');
Route::view('/login', 'login')->name('login');
Route::post('/login', function () {
    // SEMENTARA: nanti diganti proses login sungguhan
    return back()->withInput()->withErrors(['email' => 'Login belum terhubung ke database.']);
})->name('login.attempt');
Route::view('/lupa-sandi', 'lupa-sandi')->name('password.request');