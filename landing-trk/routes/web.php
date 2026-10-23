<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MataKuliahController;
use App\Http\Controllers\AkreditasiController;
use App\Http\Controllers\KurikulumController;
use App\Http\Controllers\FasilitasController;
use App\Http\Controllers\PemulihanController;
use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AdminKurikulumController;
use App\Http\Controllers\DosenController;
use App\Http\Controllers\MahasiswaController;
use App\Http\Controllers\BeritaController;
use App\Http\Controllers\PrestasiController;
use App\Http\Controllers\KegiatanMahasiswaController;
use App\Http\Controllers\KegiatanDosenController;
use App\Http\Controllers\JenisDokumenController;
use App\Http\Middleware\AdminOnly;
use App\Http\Controllers\KategoriKegiatanController;
use App\Http\Controllers\AdminMataKuliahController;

// ---------- Publik ----------
Route::view('/', 'home')->name('home');
Route::get('/mata-kuliah', [MataKuliahController::class, 'index'])->name('mata-kuliah');
Route::get('/mata-kuliah/data', [MataKuliahController::class, 'data'])->name('mata-kuliah.data');
Route::get('/akreditasi', [AkreditasiController::class, 'index'])->name('akreditasi');
Route::get('/kurikulum', [KurikulumController::class, 'index'])->name('kurikulum');
Route::get('/fasilitas', [FasilitasController::class, 'index'])->name('fasilitas');
Route::get('/kabar', [BeritaController::class, 'index'])->name('berita.index');
Route::get('/kabar/{slug}', [BeritaController::class, 'show'])->name('berita.show');
Route::get('/prestasi', [PrestasiController::class, 'index'])->name('prestasi.index');

// ---------- Login & pemulihan ----------
Route::view('/login', 'login')->name('login');
Route::post('/login', [AdminAuthController::class, 'login'])->name('login.attempt');
Route::get('/lupa-sandi', [PemulihanController::class, 'show'])->name('password.request');
Route::post('/lupa-sandi/email', [PemulihanController::class, 'kirimOtp'])->name('password.email');
Route::post('/lupa-sandi/otp', [PemulihanController::class, 'verifikasi'])->name('password.otp');
Route::post('/lupa-sandi/kirim-ulang', [PemulihanController::class, 'kirimUlang'])->name('password.kirim-ulang');
Route::post('/lupa-sandi/ubah-email', [PemulihanController::class, 'ubahEmail'])->name('password.ubah-email');
Route::post('/lupa-sandi/sandi', [PemulihanController::class, 'simpan'])->name('password.simpan');

// ---------- Admin (dijaga AdminOnly) ----------
Route::prefix('admin')->middleware(AdminOnly::class)->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');
    Route::post('/keluar', [AdminAuthController::class, 'logout'])->name('admin.logout');

    // Kurikulum TRK
    Route::get('/kurikulum/trk', [AdminKurikulumController::class, 'index'])->name('admin.kurikulum.trk');
    Route::post('/kurikulum/trk', [AdminKurikulumController::class, 'store'])->name('admin.kurikulum.store');
    Route::put('/kurikulum/trk/{id}', [AdminKurikulumController::class, 'update'])->name('admin.kurikulum.update');
    Route::patch('/kurikulum/trk/{id}/status', [AdminKurikulumController::class, 'status'])->name('admin.kurikulum.status');
    Route::delete('/kurikulum/trk/{id}', [AdminKurikulumController::class, 'destroy'])->name('admin.kurikulum.destroy');

    

    // Data Dosen
    Route::get('/dosen', [DosenController::class, 'index'])->name('admin.dosen');
    Route::get('/dosen/{id}', [DosenController::class, 'show'])->name('admin.dosen.show');
    Route::get('/dosen/{id}/edit', [DosenController::class, 'edit'])->name('admin.dosen.edit');
    Route::put('/dosen/{id}', [DosenController::class, 'update'])->name('admin.dosen.update');

    // Data Mahasiswa
    Route::get('/mahasiswa', [MahasiswaController::class, 'index'])->name('admin.mahasiswa');
    Route::get('/mahasiswa/{id}', [MahasiswaController::class, 'show'])->name('admin.mahasiswa.show');
    Route::get('/mahasiswa/{id}/edit', [MahasiswaController::class, 'edit'])->name('admin.mahasiswa.edit');
    Route::put('/mahasiswa/{id}', [MahasiswaController::class, 'update'])->name('admin.mahasiswa.update');

    // Kegiatan
    Route::get('/kegiatan/mahasiswa', [KegiatanMahasiswaController::class, 'index'])->name('admin.kegiatan.mahasiswa');
    Route::get('/kegiatan/dosen', [KegiatanDosenController::class, 'index'])->name('admin.kegiatan.dosen');

    // Jenis Dokumen
    Route::get('/kegiatan/jenis-dokumen', [JenisDokumenController::class, 'index'])->name('admin.kegiatan.jenis-dokumen');
    Route::post('/kegiatan/jenis-dokumen', [JenisDokumenController::class, 'store'])->name('admin.kegiatan.jenis-dokumen.store');
    Route::put('/kegiatan/jenis-dokumen/{id}', [JenisDokumenController::class, 'update'])->name('admin.kegiatan.jenis-dokumen.update');
    Route::delete('/kegiatan/jenis-dokumen/{id}', [JenisDokumenController::class, 'destroy'])->name('admin.kegiatan.jenis-dokumen.destroy');

    // Kategori Kegiatan
    Route::get('/kegiatan/kategori-kegiatan', [KategoriKegiatanController::class, 'index'])->name('admin.kegiatan.kategori-kegiatan');
    Route::post('/kegiatan/kategori-kegiatan', [KategoriKegiatanController::class, 'store'])->name('admin.kegiatan.kategori-kegiatan.store');
    Route::put('/kegiatan/kategori-kegiatan/{id}', [KategoriKegiatanController::class, 'update'])->name('admin.kegiatan.kategori-kegiatan.update');
    Route::delete('/kegiatan/kategori-kegiatan/{id}', [KategoriKegiatanController::class, 'destroy'])->name('admin.kegiatan.kategori-kegiatan.destroy');

    // Placeholder otomatis dari config/admin.php untuk menu yang halamannya BELUM dibuat.
    // Setiap halaman admin baru WAJIB ditambahkan ke daftar $sudahAda ini.
    $sudahAda = [
        'admin.dashboard',
        'admin.kurikulum.trk',
        'admin.dosen',
        'admin.mahasiswa',
        'admin.kegiatan.mahasiswa',
        'admin.kegiatan.dosen',
        'admin.kegiatan.jenis-dokumen',
        'admin.kegiatan.kategori-kegiatan',
    ];

    foreach (config('admin.menu') as $m) {
        foreach ($m['children'] ?? [$m] as $c) {
            if (($c['route'] ?? null) && ! in_array($c['route'], $sudahAda)) {
                Route::get($c['url'], [DashboardController::class, 'placeholder'])->name($c['route']);
            }
        }
    }
});