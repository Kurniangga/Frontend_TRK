<?php

use App\Http\Controllers\MataKuliahController;
use App\Http\Controllers\AkreditasiController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\KurikulumController;

Route::view('/', 'home')->name('home');
Route::get('/mata-kuliah', [MataKuliahController::class, 'index'])->name('mata-kuliah');
Route::get('/akreditasi', [AkreditasiController::class, 'index'])->name('akreditasi');
Route::get('/mata-kuliah/data', [MataKuliahController::class, 'data'])->name('mata-kuliah.data');
Route::get('/kurikulum', [KurikulumController::class, 'index'])->name('kurikulum');