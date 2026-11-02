# Struktur Folder `landing_page`

Daftar berikut mencantumkan file dan folder yang ada di workspace. Direktori dependency dan hasil/runtime (`.git`, `vendor`, `node_modules`, `storage`, `cache`, dan `build`) sengaja tidak dirinci.

```text
landing_page/
├── landing-trk/
│   ├── .agents/
│   │   └── skills/
│   │       ├── deploying-to-cloud/
│   │       │   ├── reference/
│   │       │   │   └── checklists.md
│   │       │   └── SKILL.md
│   │       ├── infer-conventions/
│   │       │   ├── references/
│   │       │   │   └── checklist.md
│   │       │   └── SKILL.md
│   │       ├── laravel-best-practices/
│   │       │   ├── rules/
│   │       │   │   ├── advanced-queries.md
│   │       │   │   ├── architecture.md
│   │       │   │   ├── blade-views.md
│   │       │   │   ├── caching.md
│   │       │   │   ├── collections.md
│   │       │   │   ├── config.md
│   │       │   │   ├── db-performance.md
│   │       │   │   ├── eloquent.md
│   │       │   │   ├── error-handling.md
│   │       │   │   ├── events-notifications.md
│   │       │   │   ├── http-client.md
│   │       │   │   ├── mail.md
│   │       │   │   ├── migrations.md
│   │       │   │   ├── queue-jobs.md
│   │       │   │   ├── routing.md
│   │       │   │   ├── scheduling.md
│   │       │   │   ├── security.md
│   │       │   │   ├── style.md
│   │       │   │   └── validation.md
│   │       │   └── SKILL.md
│   │       ├── tailwindcss-development/
│   │       │   └── SKILL.md
│   │       └── testing-best-practices/
│   │           ├── rules/
│   │           │   ├── assertions.md
│   │           │   ├── endpoint-tests.md
│   │           │   ├── finding-features.md
│   │           │   ├── isolation.md
│   │           │   ├── naming.md
│   │           │   ├── performance.md
│   │           │   ├── review.md
│   │           │   ├── security.md
│   │           │   └── test-data.md
│   │           └── SKILL.md
│   ├── app/
│   │   ├── Http/
│   │   │   ├── Controllers/
│   │   │   │   ├── AdminAkreditasiController.php
│   │   │   │   ├── AdminAuthController.php
│   │   │   │   ├── AdminBeritaController.php
│   │   │   │   ├── AdminKegiatanDosenDetailShowController.php
│   │   │   │   ├── AdminKegiatanMahasiswaDetailShowController.php
│   │   │   │   ├── AdminKurikulumController.php
│   │   │   │   ├── AdminMataKuliahController.php
│   │   │   │   ├── AdminPengajuanController.php
│   │   │   │   ├── AdminPrestasiController.php
│   │   │   │   ├── AdminProfilController.php
│   │   │   │   ├── AdminTinjauPengajuanController.php
│   │   │   │   ├── AkreditasiController.php
│   │   │   │   ├── BeritaController.php
│   │   │   │   ├── Controller.php
│   │   │   │   ├── DashboardController.php
│   │   │   │   ├── DosenController.php
│   │   │   │   ├── FasilitasController.php
│   │   │   │   ├── JenisDokumenController.php
│   │   │   │   ├── KaprodiBeritaController.php
│   │   │   │   ├── KaprodiDashboardController.php
│   │   │   │   ├── KaprodiDosenController.php
│   │   │   │   ├── KaprodiJenisDokumenController.php
│   │   │   │   ├── KaprodiKategoriKegiatanController.php
│   │   │   │   ├── KaprodiKegiatanDosenController.php
│   │   │   │   ├── KaprodiKegiatanDosenDetailShowController.php
│   │   │   │   ├── KaprodiKegiatanMahasiswaController.php
│   │   │   │   ├── KaprodiKegiatanMahasiswaDetailShowController.php
│   │   │   │   ├── KaprodiKurikulumController.php
│   │   │   │   ├── KaprodiMahasiswaController.php
│   │   │   │   ├── KaprodiMataKuliahController.php
│   │   │   │   ├── KaprodiPengajuanController.php
│   │   │   │   ├── KaprodiPrestasiController.php
│   │   │   │   ├── KaprodiProfilController.php
│   │   │   │   ├── KaprodiTandaTanganController.php
│   │   │   │   ├── KategoriKegiatanController.php
│   │   │   │   ├── KegiatanDosenController.php
│   │   │   │   ├── KegiatanHapusController.php
│   │   │   │   ├── KegiatanLaporanController.php
│   │   │   │   ├── KegiatanMahasiswaController.php
│   │   │   │   ├── KurikulumController.php
│   │   │   │   ├── MahasiswaController.php
│   │   │   │   ├── MataKuliahController.php
│   │   │   │   ├── PemulihanController.php
│   │   │   │   └── PrestasiController.php
│   │   │   └── Middleware/
│   │   │       ├── AdminOnly.php
│   │   │       └── KaprodiOnly.php
│   │   ├── Models/
│   │   │   ├── Akreditasi.php
│   │   │   └── User.php
│   │   ├── Providers/
│   │   │   ├── Laporan/
│   │   │   └── AppServiceProvider.php
│   │   ├── Repositories/
│   │   │   ├── AdminBeritaStore.php
│   │   │   ├── AkreditasiStore.php
│   │   │   ├── ConfigKurikulumRepository.php
│   │   │   ├── ConfigMataKuliahRepository.php
│   │   │   ├── DosenStore.php
│   │   │   ├── EloquentKurikulumRepository.php
│   │   │   ├── EloquentMataKuliahRepository.php
│   │   │   ├── JenisDokumenStore.php
│   │   │   ├── JsonKurikulumRepository.php
│   │   │   ├── JsonMataKuliahRepository.php
│   │   │   ├── KategoriKegiatanStore.php
│   │   │   ├── KegiatanData.php
│   │   │   ├── KurikulumRepository.php
│   │   │   ├── KurikulumStore.php
│   │   │   ├── MahasiswaStore.php
│   │   │   ├── MataKuliahRepository.php
│   │   │   ├── MataKuliahStore.php
│   │   │   ├── PengajuanStore.php
│   │   │   ├── SuratPersetujuanStore.php
│   │   │   └── TandaTanganStore.php
│   │   └── Support/
│   │       └── Laporan/
│   │           ├── LaporanKegiatan.php
│   │           ├── PdfTabel.php
│   │           └── XlsxTabel.php
│   ├── bootstrap/
│   │   ├── app.php
│   │   └── providers.php
│   ├── config/
│   │   ├── admin.php
│   │   ├── app.php
│   │   ├── auth.php
│   │   ├── berita.php
│   │   ├── cache.php
│   │   ├── database.php
│   │   ├── fasilitas.php
│   │   ├── filesystems.php
│   │   ├── kaprodi.php
│   │   ├── kurikulum.php
│   │   ├── logging.php
│   │   ├── mail.php
│   │   ├── matkul.php
│   │   ├── prestasi.php
│   │   ├── queue.php
│   │   ├── services.php
│   │   └── session.php
│   ├── database/
│   │   ├── factories/
│   │   │   └── UserFactory.php
│   │   ├── migrations/
│   │   │   ├── 0001_01_01_000000_create_users_table.php
│   │   │   ├── 0001_01_01_000001_create_cache_table.php
│   │   │   ├── 0001_01_01_000002_create_jobs_table.php
│   │   │   └── 2026_10_20_000000_create_akreditasi_table.php
│   │   ├── seeders/
│   │   │   └── DatabaseSeeder.php
│   │   ├── .gitignore
│   │   └── database.sqlite
│   ├── public/
│   │   ├── images/
│   │   │   ├── login/
│   │   │   │   ├── gedung-polines.png
│   │   │   │   └── logo-polines-login.png
│   │   │   ├── slides/
│   │   │   │   ├── KKL.jpg
│   │   │   │   ├── magang.jpg
│   │   │   │   ├── PBL.jpeg
│   │   │   │   └── tefa.jpg
│   │   │   ├── logo.png
│   │   │   ├── logo_trk remove.png
│   │   │   ├── logo_trk.jpeg
│   │   │   ├── logo-ti.png
│   │   │   ├── logo-trk new.png
│   │   │   ├── mahasiswa trk.jpeg
│   │   │   ├── mahasiswa.jpg
│   │   │   └── mhs.png.jpeg
│   │   ├── js/
│   │   │   └── tabel-urut.js
│   │   ├── .htaccess
│   │   ├── favicon.ico
│   │   ├── hot
│   │   ├── index.php
│   │   └── robots.txt
│   ├── resources/
│   │   ├── css/
│   │   │   └── app.css
│   │   ├── js/
│   │   │   ├── app.js
│   │   │   └── bootstrap.js
│   │   └── views/
│   │       ├── admin/
│   │       │   ├── partials/
│   │       │   │   ├── berita-detail.blade.php
│   │       │   │   ├── berita-form.blade.php
│   │       │   │   └── prestasi-form.blade.php
│   │       │   ├── admin-profil.blade.php
│   │       │   ├── akreditasi.blade.php
│   │       │   ├── berita.blade.php
│   │       │   ├── berita-detail.blade.php
│   │       │   ├── berita-edit.blade.php
│   │       │   ├── berita-tambah.blade.php
│   │       │   ├── dashboard.blade.php
│   │       │   ├── data-dosen.blade.php
│   │       │   ├── dosen.blade.php
│   │       │   ├── dosen-detail.blade.php
│   │       │   ├── dosen-edit.blade.php
│   │       │   ├── dosen-tambah.blade.php
│   │       │   ├── jenis-dokumen.blade.php
│   │       │   ├── kategori-kegiatan.blade.php
│   │       │   ├── kategori-kegiatan-form.blade.php
│   │       │   ├── kegiatan-dosen.blade.php
│   │       │   ├── kegiatan-dosen-detail-show.blade.php
│   │       │   ├── kegiatan-mahasiswa.blade.php
│   │       │   ├── kegiatan-mahasiswa-detail-show.blade.php
│   │       │   ├── kurikulum.blade.php
│   │       │   ├── mahasiswa.blade.php
│   │       │   ├── mahasiswa-detail.blade.php
│   │       │   ├── mahasiswa-edit.blade.php
│   │       │   ├── mahasiswa-tambah.blade.php
│   │       │   ├── mata-kuliah.blade.php
│   │       │   ├── pengajuan.blade.php
│   │       │   ├── placeholder.blade.php
│   │       │   ├── prestasi.blade.php
│   │       │   ├── prestasi-detail.blade.php
│   │       │   ├── prestasi-edit.blade.php
│   │       │   ├── prestasi-tambah.blade.php
│   │       │   ├── tambah-mata-kuliah.blade.php
│   │       │   └── tinjau-pengajuan.blade.php
│   │       ├── components/
│   │       │   ├── admin/
│   │       │   │   ├── breadcrumb.blade.php
│   │       │   │   └── sidebar.blade.php
│   │       │   ├── kaprodi/
│   │       │   │   └── sidebar.blade.php
│   │       │   ├── auth-layout.blade.php
│   │       │   ├── footer.blade.php
│   │       │   ├── icon.blade.php
│   │       │   ├── navbar.blade.php
│   │       │   ├── paginasi.blade.php
│   │       │   ├── section-header.blade.php
│   │       │   ├── tool-button.blade.php
│   │       │   └── user-menu.blade.php
│   │       ├── kaprodi/
│   │       │   ├── partials/
│   │       │   │   └── mata-kuliah-form.blade.php
│   │       │   ├── berita.blade.php
│   │       │   ├── berita-detail.blade.php
│   │       │   ├── berita-edit.blade.php
│   │       │   ├── berita-tambah.blade.php
│   │       │   ├── dashboard.blade.php
│   │       │   ├── dosen.blade.php
│   │       │   ├── dosen-detail.blade.php
│   │       │   ├── jenis-dokumen.blade.php
│   │       │   ├── kaprodi-kegiatan-mahasiswa-detail-show.blade.php
│   │       │   ├── kaprodi-profil.blade.php
│   │       │   ├── kategori-kegiatan.blade.php
│   │       │   ├── kegiatan-dosen.blade.php
│   │       │   ├── kegiatan-dosen-detail-show.blade.php
│   │       │   ├── kegiatan-mahasiswa.blade.php
│   │       │   ├── kurikulum.blade.php
│   │       │   ├── mahasiswa.blade.php
│   │       │   ├── mahasiswa-detail.blade.php
│   │       │   ├── mata-kuliah.blade.php
│   │       │   ├── mata-kuliah-edit.blade.php
│   │       │   ├── mata-kuliah-tambah.blade.php
│   │       │   ├── pengajuan.blade.php
│   │       │   ├── placeholder.blade.php
│   │       │   ├── prestasi.blade.php
│   │       │   ├── prestasi-detail.blade.php
│   │       │   └── tinjau-pengajuan.blade.php
│   │       ├── layouts/
│   │       │   ├── admin.blade.php
│   │       │   ├── app.blade.php
│   │       │   └── kaprodi.blade.php
│   │       ├── mata-kuliah/
│   │       │   ├── _stats.blade.php
│   │       │   └── _tabel.blade.php
│   │       ├── akreditasi.blade.php
│   │       ├── berita-index.blade.php
│   │       ├── berita-show.blade.php
│   │       ├── fasilitas.blade.php
│   │       ├── home.blade.php
│   │       ├── kurikulum.blade.php
│   │       ├── login.blade.php
│   │       ├── mata-kuliah.blade.php
│   │       ├── pemulihan.blade.php
│   │       ├── prestasi-index.blade.php
│   │       └── welcome.blade.php
│   ├── routes/
│   │   ├── console.php
│   │   └── web.php
│   ├── tests/
│   │   ├── Feature/
│   │   │   └── ExampleTest.php
│   │   ├── Unit/
│   │   │   └── ExampleTest.php
│   │   ├── Pest.php
│   │   └── TestCase.php
│   ├── .editorconfig
│   ├── .env
│   ├── .env.example
│   ├── .gitattributes
│   ├── .gitignore
│   ├── AGENTS.md
│   ├── artisan
│   ├── boost.json
│   ├── composer.json
│   ├── composer.lock
│   ├── opencode.json
│   ├── package.json
│   ├── package-lock.json
│   ├── phpunit.xml
│   ├── README.md
│   ├── views.txt
│   └── vite.config.js
├── App.blade
├── Home.blade
├── Section header.blade
├── STRUKTUR_FOLDER.md
└── views.txt
```
