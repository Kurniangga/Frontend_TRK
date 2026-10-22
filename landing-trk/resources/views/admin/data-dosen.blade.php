{{--
    resources/views/data-dosen.blade.php
    Satu file, berdiri sendiri (data + tampilan + JavaScript). Route cukup:
        Route::view('/data-dosen', 'data-dosen')->name('data-dosen');
    Cari, filter pangkat & status, halaman (paginasi), tambah, ubah, dan lihat detail sudah
    berfungsi di halaman. Datanya masih sementara (hilang saat di-refresh). Lihat TODO di
    bagian script untuk menyambungkannya ke database / controller Laravel.
--}}
@php
    /* ==================== DATA CONTOH (ganti dengan data dari database) ==================== */
    $jabatan  = ['Asisten Ahli', 'Lektor', 'Lektor Kepala', 'Guru Besar'];
    $golongan = ['III/a', 'III/b', 'III/c', 'III/d', 'IV/a', 'IV/b', 'IV/c', 'IV/d', 'IV/e'];

    $contoh = [
        ['198501012010', 'Angga Wahyu Wibowo',  'Lektor Kepala', 'IV/a'],
        ['199003152015', 'Kuwat Santoso',       'Lektor',        'III/c'],
        ['197812202005', 'Tahan Prahara',       'Guru Besar',    'IV/d'],
        ['198501012010', 'Wiktasari',           'Lektor Kepala', 'IV/a'],
        ['199003152015', 'Filda Huwani Dewi',   'Lektor',        'III/c'],
        ['197812202005', 'Angga Wahyu Wibowo',  'Guru Besar',    'IV/d'],
        ['198501012010', 'Kuwat Santoso',       'Lektor Kepala', 'IV/a'],
        ['199003152015', 'Tahan Prahara',       'Lektor',        'III/c'],
        ['197812202005', 'Wiktasari',           'Guru Besar',    'IV/d'],
        ['198501012010', 'Filda Huwani Dewi',   'Lektor Kepala', 'IV/a'],
    ];

    // 100 baris contoh supaya paginasi (1 2 ... 9 10) terlihat seperti di desain
    $dosen = [];
    for ($i = 0; $i < 100; $i++) {
        $b = $contoh[$i % 10];
        $dosen[] = [
            'id'       => (string) ($i + 1),
            'nip'      => $b[0],
            'nama'     => $b[1],
            'jabatan'  => $b[2],
            'golongan' => $b[3],
            'status'   => ($i % 13 === 12) ? 'nonaktif' : 'aktif',
        ];
    }
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Data Dosen · {{ config('app.name') }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        .font-jakarta { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body>
<div class="font-jakarta flex min-h-screen bg-[#F5F5F5] text-[#111]">

    {{-- ===================== SIDEBAR ===================== --}}
    <aside class="sticky top-0 hidden h-screen w-60 shrink-0 flex-col overflow-y-auto bg-white lg:flex">

        {{-- Logo (ganti dengan <img src="{{ asset('images/logo-trk.png') }}"> bila sudah punya file logonya) --}}
        <a href="{{ url('/') }}" class="flex items-center gap-2 px-5 pb-5 pt-6" aria-label="Teknologi Rekayasa Komputer">
            <span class="text-[34px] font-extrabold leading-none tracking-[-3px] text-[#B3262E]">TI</span>
            <span class="text-[11px] font-extrabold leading-[13px] text-[#111]">TEKNOLOGI<br>REKAYASA KOMPUTER</span>
        </a>

        <nav class="flex flex-1 flex-col gap-3 px-3 pt-4">
            <a href="{{ url('/dashboard') }}" class="flex h-10 items-center gap-3 rounded-lg px-3 text-base text-[#111] hover:bg-[#F3F7FF]">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h6v6h-6z" /><path d="M14 4h6v6h-6z" /><path d="M4 14h6v6h-6z" /><path d="M14 14h6v6h-6z" /></svg>
                <span class="flex-1">Dashboard</span>
            </a>

            {{-- Kurikulum (submenu) --}}
            <div>
                <button type="button" data-sub="sub-kurikulum" class="flex h-10 w-full items-center gap-3 rounded-lg px-3 text-base text-[#111] hover:bg-[#F3F7FF]">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M3 12m0 1a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v6a1 1 0 0 1 -1 1h-4a1 1 0 0 1 -1 -1z" /><path d="M9 8m0 1a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v10a1 1 0 0 1 -1 1h-4a1 1 0 0 1 -1 -1z" /><path d="M15 4m0 1a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v14a1 1 0 0 1 -1 1h-4a1 1 0 0 1 -1 -1z" /><path d="M4 20l14 0" /></svg>
                    <span class="flex-1 text-left">Kurikulum</span>
                    <svg data-chev class="transition-transform" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M6 9l6 6l6 -6" /></svg>
                </button>
                <div id="sub-kurikulum" class="mt-1 hidden">
                    <a href="{{ url('/kurikulum') }}" class="flex h-9 items-center rounded-md pl-11 pr-3 text-[15px] text-[#555] hover:text-[#1F73FF]">Kurikulum TRK</a>
                    <a href="{{ url('/mata-kuliah') }}" class="flex h-9 items-center rounded-md pl-11 pr-3 text-[15px] text-[#555] hover:text-[#1F73FF]">Mata Kuliah</a>
                </div>
            </div>

            {{-- Data Dosen (halaman aktif) --}}
            <a href="{{ url('/data-dosen') }}" class="flex h-10 items-center gap-3 rounded-lg bg-[#EAF1FF] px-3 text-base text-[#1F73FF]">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M8 7a4 4 0 1 0 8 0a4 4 0 0 0 -8 0" /><path d="M6 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2" /></svg>
                <span class="flex-1">Data Dosen</span>
            </a>

            <a href="{{ url('/data-mahasiswa') }}" class="flex h-10 items-center gap-3 rounded-lg px-3 text-base text-[#111] hover:bg-[#F3F7FF]">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M5 7a4 4 0 1 0 8 0a4 4 0 0 0 -8 0" /><path d="M3 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2" /><path d="M16 3.13a4 4 0 0 1 0 7.75" /><path d="M21 21v-2a4 4 0 0 0 -3 -3.85" /></svg>
                <span class="flex-1">Data Mahasiswa</span>
            </a>

            {{-- Kegiatan (submenu) --}}
            <div>
                <button type="button" data-sub="sub-kegiatan" class="flex h-10 w-full items-center gap-3 rounded-lg px-3 text-base text-[#111] hover:bg-[#F3F7FF]">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M4 13a8 8 0 0 1 7 7a6 6 0 0 0 3 -5a9 9 0 0 0 6 -8a3 3 0 0 0 -3 -3a9 9 0 0 0 -8 6a6 6 0 0 0 -5 3" /><path d="M7 14a6 6 0 0 0 -3 6a6 6 0 0 0 6 -3" /><path d="M15 9m-1 0a1 1 0 1 0 2 0a1 1 0 1 0 -2 0" /></svg>
                    <span class="flex-1 text-left">Kegiatan</span>
                    <svg data-chev class="transition-transform" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M6 9l6 6l6 -6" /></svg>
                </button>
                <div id="sub-kegiatan" class="mt-1 hidden">
                    <a href="{{ url('/kegiatan-dosen') }}" class="flex h-9 items-center rounded-md pl-11 pr-3 text-[15px] text-[#555] hover:text-[#1F73FF]">Kegiatan Dosen</a>
                    <a href="{{ url('/kegiatan-mahasiswa') }}" class="flex h-9 items-center rounded-md pl-11 pr-3 text-[15px] text-[#555] hover:text-[#1F73FF]">Kegiatan Mahasiswa</a>
                    <a href="{{ url('/kategori-kegiatan') }}" class="flex h-9 items-center rounded-md pl-11 pr-3 text-[15px] text-[#555] hover:text-[#1F73FF]">Kategori Kegiatan</a>
                    <a href="{{ url('/jenis-dokumen') }}" class="flex h-9 items-center rounded-md pl-11 pr-3 text-[15px] text-[#555] hover:text-[#1F73FF]">Jenis Dokumen</a>
                </div>
            </div>

            <a href="{{ url('/pengajuan') }}" class="flex h-10 items-center gap-3 rounded-lg px-3 text-base text-[#111] hover:bg-[#F3F7FF]">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4m0 2a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v12a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2z" /><path d="M4 13h3l3 3h4l3 -3h3" /></svg>
                <span class="flex-1">Pengajuan</span>
            </a>

            <a href="{{ url('/pengujian') }}" class="flex h-10 items-center gap-3 rounded-lg px-3 text-base text-[#111] hover:bg-[#F3F7FF]">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M9 3l6 0" /><path d="M10 9l4 0" /><path d="M10 3v6l-4 11a.7 .7 0 0 0 .5 1h11a.7 .7 0 0 0 .5 -1l-4 -11v-6" /></svg>
                <span class="flex-1">Pengujian</span>
            </a>
        </nav>

        <div class="px-3 pb-[17px] pt-4">
            <a href="{{ url('/logout') }}" class="flex h-10 items-center gap-3 rounded-lg px-3 text-base text-[#111] hover:bg-[#F3F7FF]">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M14 8v-2a2 2 0 0 0 -2 -2h-7a2 2 0 0 0 -2 2v12a2 2 0 0 0 2 2h7a2 2 0 0 0 2 -2v-2" /><path d="M9 12h12l-3 -3" /><path d="M18 15l3 -3" /></svg>
                <span class="flex-1">Keluar</span>
            </a>
        </div>
    </aside>

    <div class="min-w-0 flex-1">

        {{-- ===================== HEADER ===================== --}}
        <header class="flex h-[70px] items-center gap-4 bg-white px-3">
            <div class="relative min-w-0 flex-1">
                <svg class="absolute left-3.5 top-2 text-[#777]" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M10 10m-7 0a7 7 0 1 0 14 0a7 7 0 1 0 -14 0" /><path d="M21 21l-6 -6" /></svg>
                <input placeholder="Cari" class="h-9 w-full rounded-md border border-[#8A8A8A] bg-white pl-[45px] pr-3 text-base outline-none placeholder:text-[#B3B3B8]">
            </div>
            <svg class="shrink-0" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.65" stroke-linecap="round" stroke-linejoin="round"><path d="M10 5a2 2 0 1 1 4 0a7 7 0 0 1 4 6v3a4 4 0 0 0 2 3h-16a4 4 0 0 0 2 -3v-3a7 7 0 0 1 4 -6" /><path d="M9 17v1a3 3 0 0 0 6 0v-1" /></svg>
            <div class="flex shrink-0 items-center gap-2 pr-4">
                <div class="flex size-8 items-center justify-center rounded-full bg-[#E3C9B2] text-sm font-semibold text-[#6B4423]">M</div>
                <div class="hidden leading-tight sm:block">
                    <div class="text-sm font-semibold">Mukhlis</div>
                    <div class="text-xs text-[#444]">Admin</div>
                </div>
            </div>
        </header>

        {{-- ===================== ISI HALAMAN ===================== --}}
        <main class="p-4 pb-10 sm:p-6">
            <div class="flex flex-wrap items-start justify-between gap-4">
                <div>
                    <h1 class="mt-[6px] text-[32px] font-bold leading-10 tracking-[-0.3px]">Data Dosen</h1>
                    <p class="mt-0.5 text-2xl leading-8 text-[#A8A8A8]">Dashboard / Data Dosen</p>
                </div>
                <button id="btnAdd" type="button" class="mt-2 flex h-10 items-center gap-2 rounded-md bg-[#1F73FF] px-4 text-base font-medium text-white">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 5l0 14" /><path d="M5 12l14 0" /></svg>
                    Tambah Dosen
                </button>
            </div>

            <section class="mt-6 rounded-lg bg-white p-4 shadow-[0_1px_4px_rgba(0,0,0,0.06)]">

                {{-- Pencarian + filter --}}
                <div class="flex flex-wrap gap-4">
                    <div class="relative min-w-[220px] flex-1">
                        <svg class="absolute left-3.5 top-2.5 text-[#8A8A90]" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M10 10m-7 0a7 7 0 1 0 14 0a7 7 0 1 0 -14 0" /><path d="M21 21l-6 -6" /></svg>
                        <input id="q" placeholder="Cari nama atau NIP..." class="h-10 w-full rounded-md border border-[#E4E4E7] bg-white pl-[45px] pr-3 text-base outline-none placeholder:text-[#B3B3B8] focus:border-[#1F73FF]">
                    </div>
                    <div class="relative w-full sm:w-[190px]">
                        <select id="fPangkat" class="h-10 w-full appearance-none rounded-md border border-[#E4E4E7] bg-white px-4 pr-9 text-sm outline-none focus:border-[#1F73FF]">
                            <option value="all">Semua Pangkat</option>
                            @foreach ($jabatan as $j)
                                <option value="{{ $j }}">{{ $j }}</option>
                            @endforeach
                        </select>
                        <svg class="pointer-events-none absolute right-3 top-[11px]" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M6 9l6 6l6 -6" /></svg>
                    </div>
                    <div class="relative w-full sm:w-[190px]">
                        <select id="fStatus" class="h-10 w-full appearance-none rounded-md border border-[#E4E4E7] bg-white px-4 pr-9 text-sm outline-none focus:border-[#1F73FF]">
                            <option value="all">Semua Status</option>
                            <option value="aktif">Aktif</option>
                            <option value="nonaktif">Nonaktif</option>
                        </select>
                        <svg class="pointer-events-none absolute right-3 top-[11px]" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M6 9l6 6l6 -6" /></svg>
                    </div>
                </div>

                {{-- Tabel --}}
                <div class="mt-5 overflow-x-auto rounded-md border border-[#E4E4E7]">
                    <table class="w-full min-w-[820px] table-fixed border-collapse text-[13px]">
                        <thead>
                            <tr class="h-10 bg-[#F2F2F7] text-left font-medium text-[#222]">
                                <th class="px-3 font-medium">NIP</th>
                                <th class="px-3 font-medium">Nama</th>
                                <th class="px-3 font-medium">Pangkat / Golongan</th>
                                <th class="px-3 font-medium">Status</th>
                                <th class="px-3 font-medium">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="tbody" class="text-[#222]"></tbody>
                    </table>
                </div>

                {{-- Paginasi --}}
                <nav id="pager" class="mt-6 flex items-center justify-center gap-2 pb-2" aria-label="Halaman"></nav>
            </section>
        </main>
    </div>

    {{-- ===================== MODAL (tambah / ubah / lihat) ===================== --}}
    <div id="modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 p-4" role="dialog" aria-modal="true" aria-labelledby="modalTitle">
        <div class="w-full max-w-[460px] rounded-lg bg-white p-6 shadow-lg">
            <h3 id="modalTitle" class="text-lg font-semibold">Tambah Dosen</h3>
            <div class="mt-4 flex flex-col gap-3">
                <div>
                    <label for="fNip" class="mb-1 block text-xs font-medium text-[#55555C]">NIP</label>
                    <input id="fNip" inputmode="numeric" placeholder="Contoh: 198501012010" class="h-9 w-full rounded-md border border-[#E4E4E7] bg-white px-3 text-sm outline-none focus:border-[#1F73FF] disabled:bg-[#F7F7F8]">
                </div>
                <div>
                    <label for="fNama" class="mb-1 block text-xs font-medium text-[#55555C]">Nama</label>
                    <input id="fNama" placeholder="Nama lengkap beserta gelar" class="h-9 w-full rounded-md border border-[#E4E4E7] bg-white px-3 text-sm outline-none focus:border-[#1F73FF] disabled:bg-[#F7F7F8]">
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label for="fJabatan" class="mb-1 block text-xs font-medium text-[#55555C]">Jabatan</label>
                        <select id="fJabatan" class="h-9 w-full rounded-md border border-[#E4E4E7] bg-white px-2 text-sm outline-none focus:border-[#1F73FF] disabled:bg-[#F7F7F8]">
                            @foreach ($jabatan as $j)
                                <option value="{{ $j }}">{{ $j }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="fGolongan" class="mb-1 block text-xs font-medium text-[#55555C]">Golongan</label>
                        <select id="fGolongan" class="h-9 w-full rounded-md border border-[#E4E4E7] bg-white px-2 text-sm outline-none focus:border-[#1F73FF] disabled:bg-[#F7F7F8]">
                            @foreach ($golongan as $g)
                                <option value="{{ $g }}">{{ $g }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div>
                    <label for="fStatusForm" class="mb-1 block text-xs font-medium text-[#55555C]">Status</label>
                    <select id="fStatusForm" class="h-9 w-full rounded-md border border-[#E4E4E7] bg-white px-2 text-sm outline-none focus:border-[#1F73FF] disabled:bg-[#F7F7F8]">
                        <option value="aktif">Aktif</option>
                        <option value="nonaktif">Nonaktif</option>
                    </select>
                </div>
                <p id="fError" class="hidden text-xs text-[#E5484D]"></p>
            </div>
            <div class="mt-5 flex justify-end gap-2">
                <button id="btnCancel" type="button" class="h-9 rounded-md border border-[#E4E4E7] bg-white px-4 text-sm font-medium">Batal</button>
                <button id="btnSave" type="button" class="h-9 rounded-md bg-[#1F73FF] px-4 text-sm font-medium text-white">Simpan</button>
            </div>
        </div>
    </div>
</div>

<script>
(function () {
    var data = @json($dosen);
    var PER_PAGE = 10;
    var q = '', fPangkat = 'all', fStatus = 'all', page = 1;
    var mode = 'add', editId = null;

    var $ = function (id) { return document.getElementById(id); };
    var tbody = $('tbody'), pager = $('pager'), modal = $('modal');

    function esc(s) {
        return String(s).replace(/[&<>"']/g, function (c) {
            return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c];
        });
    }
    function svg(w, sw, paths) {
        return '<svg width="' + w + '" height="' + w + '" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="' + sw + '" stroke-linecap="round" stroke-linejoin="round">' + paths + '</svg>';
    }
    var I_EYE  = svg(16, 1.5, '<path d="M10 12a2 2 0 1 0 4 0a2 2 0 0 0 -4 0" /><path d="M21 12c-2.4 4 -5.4 6 -9 6c-3.6 0 -6.6 -2 -9 -6c2.4 -4 5.4 -6 9 -6c3.6 0 6.6 2 9 6" />');
    var I_EDIT = svg(16, 1.5, '<path d="M4 20h4l10.5 -10.5a2.828 2.828 0 1 0 -4 -4l-10.5 10.5v4" /><path d="M13.5 6.5l4 4" />');
    var I_LEFT = svg(16, 2, '<path d="M15 6l-6 6l6 6" />');
    var I_RIGHT = svg(16, 2, '<path d="M9 6l6 6l-6 6" />');

    /* ---------- data terfilter ---------- */
    function filtered() {
        var s = q.toLowerCase();
        return data.filter(function (r) {
            return (r.nama.toLowerCase().indexOf(s) !== -1 || r.nip.indexOf(s) !== -1) &&
                   (fPangkat === 'all' || r.jabatan === fPangkat) &&
                   (fStatus === 'all' || r.status === fStatus);
        });
    }

    /* ---------- tabel ---------- */
    function renderTable(list) {
        var rows = list.slice((page - 1) * PER_PAGE, page * PER_PAGE);
        tbody.innerHTML = rows.length ? rows.map(function (r) {
            var on = r.status === 'aktif';
            return '<tr class="h-12 border-t border-[#E4E4E7]">' +
                '<td class="px-3">' + esc(r.nip) + '</td>' +
                '<td class="px-3">' + esc(r.nama) + '</td>' +
                '<td class="px-3">' + esc(r.jabatan) + ', ' + esc(r.golongan) + '</td>' +
                '<td class="px-3"><span class="inline-flex items-center rounded px-2 py-0.5 text-[11px] font-medium ' +
                    (on ? 'bg-[#DDF3D0] text-[#3A8A1E]' : 'bg-[#EDEDF0] text-[#55555C]') + '">' + (on ? 'Aktif' : 'Nonaktif') + '</span></td>' +
                '<td class="px-3"><div class="flex gap-2">' +
                    '<button type="button" title="Lihat detail" aria-label="Lihat detail" data-act="view" data-id="' + esc(r.id) + '" class="flex size-8 items-center justify-center rounded-md bg-[#D4E3FF] text-[#1F73FF] hover:bg-[#C2D8FF]">' + I_EYE + '</button>' +
                    '<button type="button" title="Ubah" aria-label="Ubah" data-act="edit" data-id="' + esc(r.id) + '" class="flex size-8 items-center justify-center rounded-md bg-[#FDE7CE] text-[#F0A35E] hover:bg-[#FBDDB9]">' + I_EDIT + '</button>' +
                '</div></td></tr>';
        }).join('') :
            '<tr class="h-12 border-t border-[#E4E4E7]"><td colspan="5" class="px-3 text-center text-[#8A8A90]">Tidak ada data dosen.</td></tr>';
    }

    /* ---------- paginasi: 1 2 ... 9 10 ---------- */
    function renderPager(total) {
        var pages = [];
        [1, 2, total - 1, total, page - 1, page, page + 1].forEach(function (p) {
            if (p >= 1 && p <= total && pages.indexOf(p) === -1) pages.push(p);
        });
        pages.sort(function (a, b) { return a - b; });

        var out = '', last = 0;
        var arrow = 'flex size-8 items-center justify-center rounded-md ';
        out += '<button type="button" data-p="' + (page - 1) + '" aria-label="Sebelumnya" ' + (page <= 1 ? 'disabled' : '') +
               ' class="' + arrow + 'bg-[#1F73FF] text-white disabled:bg-[#CFE0FF] disabled:text-[#1F73FF] disabled:cursor-not-allowed">' + I_LEFT + '</button>';
        pages.forEach(function (p) {
            if (last && p - last > 1) out += '<span class="flex size-8 items-center justify-center text-[13px] text-[#55555C]">\u2026</span>';
            out += '<button type="button" data-p="' + p + '" class="size-8 rounded-md border text-[13px] ' +
                   (p === page ? 'border-[#1F73FF] text-[#1F73FF] font-medium' : 'border-[#E4E4E7] bg-white text-[#222] hover:border-[#1F73FF]') + '">' + p + '</button>';
            last = p;
        });
        out += '<button type="button" data-p="' + (page + 1) + '" aria-label="Berikutnya" ' + (page >= total ? 'disabled' : '') +
               ' class="' + arrow + 'bg-[#1F73FF] text-white disabled:bg-[#CFE0FF] disabled:text-[#1F73FF] disabled:cursor-not-allowed">' + I_RIGHT + '</button>';
        pager.innerHTML = total > 1 ? out : '';
    }

    function render() {
        var list = filtered();
        var total = Math.max(1, Math.ceil(list.length / PER_PAGE));
        if (page > total) page = total;
        renderTable(list);
        renderPager(total);
    }

    /* ---------- modal ---------- */
    function setDisabled(d) {
        ['fNip', 'fNama', 'fJabatan', 'fGolongan', 'fStatusForm'].forEach(function (id) { $(id).disabled = d; });
        $('btnSave').classList.toggle('hidden', d);
        $('btnCancel').textContent = d ? 'Tutup' : 'Batal';
    }
    function openModal(m, row) {
        mode = m; editId = row ? row.id : null;
        $('modalTitle').textContent = m === 'add' ? 'Tambah Dosen' : (m === 'edit' ? 'Ubah Data Dosen' : 'Detail Dosen');
        $('fNip').value = row ? row.nip : '';
        $('fNama').value = row ? row.nama : '';
        $('fJabatan').value = row ? row.jabatan : 'Lektor';
        $('fGolongan').value = row ? row.golongan : 'III/c';
        $('fStatusForm').value = row ? row.status : 'aktif';
        $('fError').classList.add('hidden');
        setDisabled(m === 'view');
        modal.classList.remove('hidden'); modal.classList.add('flex');
        if (m !== 'view') $('fNip').focus();
    }
    function closeModal() { modal.classList.add('hidden'); modal.classList.remove('flex'); }

    function save() {
        var nip = $('fNip').value.trim(), nama = $('fNama').value.trim();
        var err = $('fError');
        if (!nip || !nama) { err.textContent = 'NIP dan nama wajib diisi.'; err.classList.remove('hidden'); return; }
        if (!/^\d+$/.test(nip)) { err.textContent = 'NIP hanya boleh berisi angka.'; err.classList.remove('hidden'); return; }

        var item = { nip: nip, nama: nama, jabatan: $('fJabatan').value, golongan: $('fGolongan').value, status: $('fStatusForm').value };
        if (mode === 'edit') {
            data.forEach(function (r) { if (r.id === editId) { Object.assign(r, item); } });
            // TODO: kirim ke server, mis. PUT /dosen/{id}
        } else {
            item.id = String(Date.now());
            data.unshift(item); page = 1;
            // TODO: kirim ke server, mis. POST /dosen
        }
        closeModal(); render();
    }

    /* ---------- event ---------- */
    $('btnAdd').addEventListener('click', function () { openModal('add', null); });
    $('btnCancel').addEventListener('click', closeModal);
    $('btnSave').addEventListener('click', save);
    modal.addEventListener('click', function (e) { if (e.target === modal) closeModal(); });
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') closeModal();
        if (e.key === 'Enter' && !modal.classList.contains('hidden') && mode !== 'view') save();
    });
    $('q').addEventListener('input', function (e) { q = e.target.value.trim(); page = 1; render(); });
    $('fPangkat').addEventListener('change', function (e) { fPangkat = e.target.value; page = 1; render(); });
    $('fStatus').addEventListener('change', function (e) { fStatus = e.target.value; page = 1; render(); });

    pager.addEventListener('click', function (e) {
        var b = e.target.closest('button[data-p]'); if (!b || b.disabled) return;
        page = parseInt(b.dataset.p, 10); render();
    });
    tbody.addEventListener('click', function (e) {
        var b = e.target.closest('button[data-act]'); if (!b) return;
        var row = data.filter(function (r) { return r.id === b.dataset.id; })[0]; if (!row) return;
        openModal(b.dataset.act === 'edit' ? 'edit' : 'view', row);
    });

    /* Submenu sidebar (Kurikulum, Kegiatan) */
    document.querySelectorAll('[data-sub]').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var el = document.getElementById(btn.dataset.sub);
            el.classList.toggle('hidden');
            btn.querySelector('[data-chev]').classList.toggle('rotate-180', !el.classList.contains('hidden'));
        });
    });

    /* Contoh pengiriman ke Laravel (aktifkan bila route + controller sudah ada):
       fetch('/dosen', {
           method: 'POST',
           headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content },
           body: JSON.stringify(item)
       });
    */

    render();
})();
</script>
</body>
</html>
