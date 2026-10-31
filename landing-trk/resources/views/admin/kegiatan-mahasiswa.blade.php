{{--
    resources/views/admin/kegiatan-mahasiswa.blade.php
    Daftar kegiatan mahasiswa (admin): HANYA yang sudah disetujui. Aksi: lihat (link ke detail) & hapus. TANPA tambah / ubah.
    Laporan PDF/Excel per tahun tetap ada.
    Tabel memakai DataTables 2.x (sorting, paginasi, pencarian ditangani library). Tidak lagi memakai public/js/tabel-urut.js.
--}}
@extends('layouts.admin')

@section('title', 'Kegiatan Mahasiswa')

@php
    $selectCls = 'h-10 w-full appearance-none rounded-lg border border-[#E8E8EC] bg-white px-4 pr-10 text-base text-[#2E2E2E] outline-none focus:border-[#2277FF] cursor-pointer';
    $chev = '<svg class="pointer-events-none absolute right-4 top-1/2 -translate-y-1/2 text-[#1C2024]" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 9l6 6l6 -6" /></svg>';
@endphp

@section('content')
{{-- CSS DataTables + penyesuaian agar mirip tampilan lama --}}
<link rel="stylesheet" href="https://cdn.datatables.net/2.1.8/css/dataTables.dataTables.min.css">
<style>
    /* ---------- Filter: pencarian 1 baris penuh, 5 dropdown sejajar & sama lebar ---------- */
    .filter-wrap { display: flex; flex-direction: column; gap: 12px; }
    .filter-grid { display: grid; gap: 12px; grid-template-columns: repeat(2, minmax(0, 1fr)); }
    @media (min-width: 640px)  { .filter-grid { grid-template-columns: repeat(3, minmax(0, 1fr)); } }
    @media (min-width: 1024px) { .filter-grid { grid-template-columns: repeat(5, minmax(0, 1fr)); } }

    /* ---------- Kotak tabel: border + sudut bulat HANYA membungkus tabel (paginasi di luar kotak) ---------- */
    #tabel-wrap .dt-layout-row { margin: 0; }
    #tabel-wrap .dt-layout-row.dt-layout-table {
        border: 1px solid rgb(0 0 47 / .15);
        border-radius: 6px;
        overflow: hidden;                    /* latar header ikut terpotong sudut bulat */
        background: #fff;
    }
    #tabel-wrap .dt-layout-table .dt-layout-cell { width: 100%; padding: 0; overflow-x: auto; }

    #tabel { width: 100%; min-width: 960px; table-layout: fixed; border-collapse: collapse; margin: 0 !important; font-size: 14px; color: #1C2024; }

    /* Header: latar abu penuh, teks + ikon sort rapat di kiri, semua kolom rata kiri (termasuk Tanggal) */
    #tabel.dataTable > thead > tr > th {
        padding: 8px; font-weight: 510; text-align: left;
        background: #F9F9FC;
        border-bottom: 1px solid rgb(0 0 47 / .15);
    }
    #tabel.dataTable > thead > tr > th .dt-column-header {
        display: flex; flex-direction: row !important; justify-content: flex-start !important; align-items: center; gap: 6px;
    }
    #tabel.dataTable > thead > tr > th .dt-column-title { flex-grow: 0 !important; }

    #tabel.dataTable > tbody > tr > td {
        padding: 8px; text-align: left;
        border-top: 1px solid rgb(0 0 47 / .15);
        box-shadow: none;
    }
    #tabel.dataTable > tbody > tr:first-child > td { border-top: 0; }
    #tabel.dataTable > tbody > tr > td.truncate { overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }

    /* ---------- Paginasi: di tengah bawah, tombol kotak 32px ---------- */
    #tabel-wrap .dt-layout-row:not(.dt-layout-table) { padding: 24px 0 0; justify-content: center; }
    #tabel-wrap .dt-layout-row:not(.dt-layout-table) .dt-layout-cell { width: 100%; padding: 0; display: flex; justify-content: center; }
    #tabel-wrap .dt-paging nav { display: flex; align-items: center; justify-content: center; gap: 8px; }

    #tabel-wrap .dt-paging .dt-paging-button {
        display: flex !important; align-items: center; justify-content: center;
        width: 32px; height: 32px; min-width: 32px; padding: 0 !important; margin: 0 !important;
        border: 1px solid #DFE3E8; border-radius: 4px !important;
        font-size: 14px; font-weight: 700; letter-spacing: -.16px; line-height: 1;
        box-shadow: none !important; cursor: pointer;
    }
    #tabel-wrap .dt-paging .dt-paging-button.disabled { cursor: not-allowed; opacity: .6; }

    /* Tombol angka */
    #tabel-wrap .dt-paging .dt-paging-button:not(.previous):not(.next):not(.ellipsis),
    #tabel-wrap .dt-paging .dt-paging-button:not(.previous):not(.next):not(.ellipsis):hover {
        background: #fff !important; color: #0A0A0A !important; border-color: #DFE3E8 !important;
    }
    #tabel-wrap .dt-paging .dt-paging-button:not(.previous):not(.next):not(.ellipsis):hover { border-color: #2277FF !important; }
    /* Halaman aktif: border + angka biru (selector sengaja dibuat lebih spesifik dari aturan tombol angka di atas) */
    #tabel-wrap .dt-paging .dt-paging-button.current:not(.previous):not(.next):not(.ellipsis),
    #tabel-wrap .dt-paging .dt-paging-button.current:not(.previous):not(.next):not(.ellipsis):hover,
    #tabel-wrap .dt-paging .dt-paging-button[aria-current="page"]:not(.previous):not(.next):not(.ellipsis),
    #tabel-wrap .dt-paging .dt-paging-button[aria-current="page"]:not(.previous):not(.next):not(.ellipsis):hover {
        color: #2277FF !important; border-color: #2277FF !important; background: #fff !important;
    }
    #tabel-wrap .dt-paging .dt-paging-button.ellipsis { border-color: transparent !important; background: none !important; color: #55555C !important; opacity: 1; cursor: default; }

    /* Tombol sebelumnya (biru penuh, panah putih) & berikutnya (biru muda, panah biru) */
    #tabel-wrap .dt-paging .dt-paging-button.previous,
    #tabel-wrap .dt-paging .dt-paging-button.next {
        font-size: 0 !important; background-repeat: no-repeat !important; background-position: center !important; background-size: 20px 20px !important;
    }
    #tabel-wrap .dt-paging .dt-paging-button.previous,
    #tabel-wrap .dt-paging .dt-paging-button.previous.disabled:hover {
        background-color: #2277FF !important; border-color: #2277FF !important;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%23fff' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='M15 6l-6 6l6 6'/%3E%3C/svg%3E") !important;
    }
    #tabel-wrap .dt-paging .dt-paging-button.next,
    #tabel-wrap .dt-paging .dt-paging-button.next.disabled:hover {
        background-color: #CDDEF9 !important; border-color: #2277FF !important;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%232277FF' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='M9 6l6 6l-6 6'/%3E%3C/svg%3E") !important;
    }
    #tabel-wrap .dt-paging .dt-paging-button.previous:hover:not(.disabled) { background-color: #1A66E0 !important; border-color: #1A66E0 !important; }
    #tabel-wrap .dt-paging .dt-paging-button.next:hover:not(.disabled) { background-color: #BBD2F6 !important; }

    /* ---------- Modal konfirmasi hapus ---------- */
    #modalHapus { position: fixed; inset: 0; margin: auto; height: fit-content; width: 380px; max-width: calc(100vw - 32px); padding: 0; border: 0; background: transparent; overflow: visible; }  /* margin:auto + inset:0 = tepat di tengah (Tailwind reset menimpa margin bawaan dialog) */
    #modalHapus::backdrop { background: rgb(10 10 10 / .45); }
    #modalHapus[open] { animation: modalMasuk .15s ease-out; }
    @keyframes modalMasuk { from { opacity: 0; transform: translateY(8px) scale(.98); } to { opacity: 1; transform: none; } }
    .modal-kartu { padding: 24px; border-radius: 12px; background: #fff; text-align: center; box-shadow: 0 20px 50px rgb(0 0 0 / .2); }
    .modal-ikon { display: flex; align-items: center; justify-content: center; width: 48px; height: 48px; margin: 0 auto 16px; border-radius: 9999px; background: #FFD9D9; color: #E5484D; }
    .modal-judul { font-size: 18px; font-weight: 700; line-height: 24px; color: #0A0A0A; }
    .modal-teks { margin-top: 8px; font-size: 14px; line-height: 20px; color: #55555C; }
    .modal-teks strong { font-weight: 600; color: #0A0A0A; word-break: break-word; }
    .modal-aksi { display: flex; gap: 12px; margin-top: 24px; }
    .modal-btn { flex: 1; height: 40px; border: 1px solid transparent; border-radius: 8px; font-size: 14px; font-weight: 500; cursor: pointer; }
    .modal-btn-batal { background: #fff; border-color: #E8E8EC; color: #2E2E2E; }
    .modal-btn-batal:hover { background: #F9F9FB; }
    .modal-btn-hapus { background: #E5484D; color: #fff; }
    .modal-btn-hapus:hover { background: #D13A3F; }
    .modal-btn-hapus:disabled { opacity: .7; cursor: not-allowed; }
</style>

<main class="flex flex-col gap-10 px-4 py-8 sm:px-6">
    {{-- Header --}}
    <div class="flex flex-col gap-2">
        <h1 class="text-[32px] font-bold leading-[38px] tracking-[-0.2px]">Kegiatan Mahasiswa</h1>
        <x-admin.breadcrumb :items="[['Dashboard', 'admin.dashboard'], ['Kegiatan'], ['Kegiatan Mahasiswa']]" />
    </div>

    @if (session('success'))
        <div class="rounded-md border border-[#D6EDB5] bg-[#E6F3D3] px-4 py-3 text-sm text-[#4C7D08]" role="status">{{ session('success') }}</div>
    @endif

    <div class="flex flex-col gap-8 rounded-lg bg-white p-4 pb-6 shadow-sm">

        {{-- Laporan data kegiatan: unduh PDF / Excel --}}
        <form method="GET" action="{{ route('admin.kegiatan.mahasiswa.laporan') }}" class="flex flex-col gap-4 rounded-md border border-[#E8E8EC] bg-[#F9F9FB] p-4 lg:flex-row lg:items-end">
            <div class="lg:mr-auto">
                <h2 class="text-sm font-semibold text-[#0A0A0A]">Laporan Data Kegiatan Mahasiswa</h2>
                <p class="mt-0.5 text-xs text-[#6B6B72]">Unduh rekap kegiatan berdasarkan kurun waktu (tahun).</p>
            </div>
            <label class="flex w-full flex-col gap-1.5 lg:w-36">
                <span class="text-xs font-medium text-[#0A0A0A]">Dari tahun</span>
                <div class="relative">
                    <select name="dari" class="{{ $selectCls }}">
                        @foreach ($tahunList as $t)
                            <option value="{{ $t }}" @selected($loop->last)>{{ $t }}</option>
                        @endforeach
                    </select>
                    {!! $chev !!}
                </div>
            </label>
            <label class="flex w-full flex-col gap-1.5 lg:w-36">
                <span class="text-xs font-medium text-[#0A0A0A]">Sampai tahun</span>
                <div class="relative">
                    <select name="sampai" class="{{ $selectCls }}">
                        @foreach ($tahunList as $t)
                            <option value="{{ $t }}" @selected($loop->last)>{{ $t }}</option>
                        @endforeach
                    </select>
                    {!! $chev !!}
                </div>
            </label>
            <label class="flex w-full flex-col gap-1.5 lg:w-44">
                <span class="text-xs font-medium text-[#0A0A0A]">Status kegiatan</span>
                <div class="relative">
                    <select name="status" class="{{ $selectCls }}">
                        <option value="Disetujui">Disetujui</option>
                        <option value="Semua">Semua status</option>
                        <option value="Diajukan">Diajukan</option>
                        <option value="Direvisi">Direvisi</option>
                        <option value="Ditolak">Ditolak</option>
                    </select>
                    {!! $chev !!}
                </div>
            </label>
            <div class="flex gap-2">
                <button type="submit" name="format" value="pdf" class="flex h-10 items-center gap-1.5 rounded-lg border border-[#E5484D] bg-white px-4 text-sm font-medium text-[#E5484D] hover:bg-[#FFF1F1] cursor-pointer">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M12 4v11" /><path d="M8 11l4 4l4 -4" /><path d="M5 20h14" /></svg>
                    PDF
                </button>
                <button type="submit" name="format" value="xlsx" class="flex h-10 items-center gap-1.5 rounded-lg border border-[#17803D] bg-white px-4 text-sm font-medium text-[#17803D] hover:bg-[#F0FAF3] cursor-pointer">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M12 4v11" /><path d="M8 11l4 4l4 -4" /><path d="M5 20h14" /></svg>
                    Excel
                </button>
            </div>
        </form>

        {{-- Hanya kegiatan yang sudah disetujui (sudah / sedang berjalan) --}}
        <div class="flex flex-wrap items-center gap-2 border-b border-[#0A0A0A]/[0.06] pb-3">
            <span class="rounded-lg border border-[#D6EDB5] bg-[#E6F3D3] px-2 py-0.5 text-xs font-medium text-[#4C7D08]">Disetujui</span>
            <span class="text-xs text-[#0A0A0A]/60">Menampilkan kegiatan yang sudah disetujui ({{ count($rows) }} kegiatan).</span>
        </div>

        {{-- Filter: pencarian 1 baris penuh, 5 dropdown sejajar & sama lebar --}}
        <div class="filter-wrap">
            <div class="flex h-10 items-center gap-2 rounded-lg border border-[#E8E8EC] bg-white px-3 focus-within:border-[#2277FF]">
                <svg class="size-6 shrink-0 text-[#A8A8A8]" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="10" cy="10" r="7"/><path d="M21 21l-6-6"/></svg>
                <input id="q" type="text" placeholder="Cari nama kegiatan..." class="min-w-0 flex-1 bg-transparent text-base tracking-[-0.18px] outline-none placeholder:text-[#A8A8A8]">
            </div>

            <div class="filter-grid">
                <div class="relative">
                    <select id="fTahun" class="{{ $selectCls }}">
                        <option value="">Semua Tahun</option>
                        @foreach (array_reverse($tahunList) as $t)
                            <option value="{{ $t }}">{{ $t }}</option>
                        @endforeach
                    </select>
                    {!! $chev !!}
                </div>

                <div class="relative">
                    <select id="fSemester" class="{{ $selectCls }}">
                        <option value="">Semua Semester</option>
                        <option value="Ganjil">Ganjil</option>
                        <option value="Genap">Genap</option>
                    </select>
                    {!! $chev !!}
                </div>

                <div class="relative">
                    <select id="fTingkat" class="{{ $selectCls }}">
                        <option value="">Semua Tingkat</option>
                        <option value="1">Tingkat 1</option>
                        <option value="2">Tingkat 2</option>
                        <option value="3">Tingkat 3</option>
                    </select>
                    {!! $chev !!}
                </div>

                <div class="relative">
                    <select id="fKelas" class="{{ $selectCls }}">
                        <option value="">Semua Kelas</option>
                        <option value="A">A</option>
                        <option value="B">B</option>
                    </select>
                    {!! $chev !!}
                </div>

                {{-- Urutan waktu (mengatur urutan kolom Tanggal) --}}
                <div class="relative">
                    <select id="fUrut" class="{{ $selectCls }}">
                        <option value="">Semua Waktu</option>
                        <option value="terbaru">Terbaru</option>
                        <option value="terlama">Terlama</option>
                    </select>
                    {!! $chev !!}
                </div>
            </div>
        </div>

        {{-- Tabel (isi <tbody>, info, dan paginasi dibuat otomatis oleh DataTables) --}}
        <div id="tabel-wrap">
            <table id="tabel">
                <thead>
                    <tr>
                        <th>Nama Kegiatan</th>
                        <th>Kategori</th>
                        <th>Pengaju</th>
                        <th>Tanggal</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</main>

{{-- Modal konfirmasi hapus (dibuka lewat JS: tombol Hapus di tabel) --}}
<dialog id="modalHapus" aria-labelledby="modalHapusJudul" aria-describedby="modalHapusTeks">
    <div class="modal-kartu">
        <div class="modal-ikon">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M4 7l16 0" /><path d="M10 11l0 6" /><path d="M14 11l0 6" /><path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" /><path d="M9 7v-3a1 1 0 0 1 1 -1h6a1 1 0 0 1 1 1v3" /></svg>
        </div>
        <h2 id="modalHapusJudul" class="modal-judul">Hapus kegiatan?</h2>
        <p id="modalHapusTeks" class="modal-teks"><strong id="modalHapusNama"></strong> akan dihapus permanen.</p>
        <div class="modal-aksi">
            <button type="button" id="modalHapusBatal" class="modal-btn modal-btn-batal">Batal</button>
            <button type="button" id="modalHapusYa" class="modal-btn modal-btn-hapus">Hapus</button>
        </div>
    </div>
</dialog>
@endsection

@push('scripts')
{{-- Hapus baris jQuery ini kalau layout admin kamu sudah memuat jQuery 3.x --}}
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/2.1.8/js/dataTables.min.js"></script>
<script>
(function () {
    var data = @json($rows);
    var URL_VIEW   = @json(route('admin.kegiatan.mahasiswa.detail.show', ['id' => '__ID__']));
    var URL_DELETE = @json(route('admin.kegiatan.mahasiswa.destroy', ['id' => '__ID__']));
    var CSRF       = @json(csrf_token());

    var STATUS = {
        'Diajukan':  'border-[#AAC8F8] bg-[#CDDEF9] text-[#2277FF]',
        'Direvisi':  'border-[#FAD4A8] bg-[#FBE5CB] text-[#854700]',
        'Disetujui': 'border-[#D6EDB5] bg-[#E6F3D3] text-[#4C7D08]',
        'Ditolak':   'border-[#FAA8A8] bg-[#FBCBCB] text-[#FF3F3F]'
    };

    var $ = function (id) { return document.getElementById(id); };

    // Nilai filter dropdown (tahun/semester/tingkat/kelas tidak tampil sebagai kolom, jadi dicek dari data asli baris)
    var filter = { tahun: '', semester: '', tingkat: '', kelas: '' };

    function esc(s) {
        return String(s == null ? '' : s).replace(/[&<>"']/g, function (c) {
            return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c];
        });
    }
    function svg(paths, w, sw) {
        return '<svg width="' + w + '" height="' + w + '" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="' + sw + '" stroke-linecap="round" stroke-linejoin="round">' + paths + '</svg>';
    }
    var I_EYE   = svg('<circle cx="12" cy="12" r="2"/><path d="M22 12c-2.667 4.667-6 7-10 7s-7.333-2.333-10-7c2.667-4.667 6-7 10-7s7.333 2.333 10 7"/>', 20, 1.5);
    var I_TRASH = svg('<path d="M4 7l16 0" /><path d="M10 11l0 6" /><path d="M14 11l0 6" /><path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" /><path d="M9 7v-3a1 1 0 0 1 1 -1h6a1 1 0 0 1 1 1v3" />', 20, 1.5);

    var TXT = DataTable.render.text(); // otomatis escape HTML

    var table = new DataTable('#tabel', {
        data: data,
        autoWidth: false,
        pageLength: 10,
        order: [],                       // tanpa urutan awal = urutan asli dari server
        columns: [
            { data: 'nama',     render: TXT, className: 'truncate' },
            { data: 'kategori', render: TXT, className: 'truncate' },
            { data: 'pengaju',  render: TXT, className: 'truncate' },
            {   // tampil "21 Mei 2024", tapi diurutkan memakai raw_date (angka timestamp)
                data: 'tanggal', className: 'truncate',
                render: function (d, type, row) {
                    if (type === 'sort' || type === 'type') {
                        var t = new Date(row.raw_date).getTime();
                        return isNaN(t) ? 0 : t;
                    }
                    return esc(d);
                }
            },
            {
                data: 'status',
                render: function (d, type) {
                    if (type !== 'display') return d;
                    return '<span class="inline-flex rounded-lg border px-2 py-0.5 text-xs font-medium ' + (STATUS[d] || '') + '">' + esc(d) + '</span>';
                }
            },
            {
                data: null, orderable: false, searchable: false,
                render: function (d, type, r) {
                    var id = esc(r.id);
                    return '<div class="flex items-center gap-3">' +
                        '<a href="' + URL_VIEW.replace('__ID__', encodeURIComponent(r.id)) + '" title="Lihat" aria-label="Lihat" class="rounded-md bg-[#CDDEF9] p-1.5 text-[#2277FF] hover:bg-[#BBD2F6] cursor-pointer">' + I_EYE + '</a>' +
                        '<button type="button" title="Hapus" aria-label="Hapus" data-act="delete" data-id="' + id + '" class="rounded-md bg-[#FFD9D9] p-1.5 text-[#E5484D] hover:bg-[#FFC9C9] cursor-pointer">' + I_TRASH + '</button>' +
                    '</div>';
                }
            }
        ],
        // Hanya paginasi (tengah bawah). Kotak cari, "show entries", dan info bawaan dimatikan
        layout: { topStart: null, topEnd: null, bottomStart: null, bottomEnd: null, bottom: 'paging' },
        pagingType: 'simple_numbers',
        language: {
            emptyTable: 'Tidak ada kegiatan.',
            zeroRecords: 'Tidak ada kegiatan.',
            paginate: { previous: 'Sebelumnya', next: 'Berikutnya' }
        }
    });

    // Sembunyikan paginasi kalau hanya ada 1 halaman
    function togglePager() {
        var p = document.querySelector('#tabel-wrap .dt-paging');
        if (p) p.style.display = table.page.info().pages > 1 ? '' : 'none';
    }
    table.on('draw.dt', togglePager);
    togglePager();

    /* ---------- Filter tahun / semester / tingkat / kelas ---------- */
    DataTable.ext.search.push(function (settings, searchData, dataIndex, r) {
        if (settings.nTable.id !== 'tabel') return true;
        return (!filter.tahun    || String(r.tahun) === filter.tahun) &&
               (!filter.semester || r.semester === filter.semester) &&
               (!filter.tingkat  || r.tingkat === filter.tingkat) &&
               (!filter.kelas    || r.kelas === filter.kelas);
    });

    /* ---------- Event filter ---------- */
    $('q').addEventListener('input', function (e) {
        table.column(0).search(e.target.value.trim()).draw();   // cari hanya di nama kegiatan
    });
    [['fTahun', 'tahun'], ['fSemester', 'semester'], ['fTingkat', 'tingkat'], ['fKelas', 'kelas']].forEach(function (p) {
        $(p[0]).addEventListener('change', function (e) {
            filter[p[1]] = e.target.value;
            table.draw();
        });
    });

    /* ---------- Dropdown Terbaru / Terlama = urutan kolom Tanggal (kolom ke-4, indeks 3) ---------- */
    $('fUrut').addEventListener('change', function (e) {
        var v = e.target.value;
        table.order(v === 'terbaru' ? [[3, 'desc']] : v === 'terlama' ? [[3, 'asc']] : []).draw();
    });

    // Kalau urutan diubah lewat klik header, dropdown ikut menyesuaikan
    table.on('order.dt', function () {
        var o = table.order();
        var v = '';
        if (o.length === 1 && o[0][0] === 3) v = o[0][1] === 'desc' ? 'terbaru' : 'terlama';
        $('fUrut').value = v;
    });

    /* ---------- Hapus: modal konfirmasi, lalu DELETE lewat form + CSRF ---------- */
    var modal = $('modalHapus'), modalNama = $('modalHapusNama'), btnBatal = $('modalHapusBatal'), btnYa = $('modalHapusYa');
    var idHapus = null;

    function kirimHapus(id) {
        var f = document.createElement('form');
        f.method = 'POST';
        f.action = URL_DELETE.replace('__ID__', encodeURIComponent(id));
        f.innerHTML = '<input type="hidden" name="_token" value="' + CSRF + '"><input type="hidden" name="_method" value="DELETE">';
        document.body.appendChild(f);
        f.submit();
    }

    $('tabel').addEventListener('click', function (e) {
        var b = e.target.closest('button[data-act="delete"]'); if (!b) return;
        var r = data.filter(function (x) { return String(x.id) === b.dataset.id; })[0];
        idHapus = b.dataset.id;
        if (typeof modal.showModal !== 'function') {            // cadangan untuk browser lama
            if (confirm('Hapus kegiatan "' + (r ? r.nama : '') + '"? Tindakan ini tidak dapat dibatalkan.')) kirimHapus(idHapus);
            return;
        }
        modalNama.textContent = '"' + (r ? r.nama : '') + '"';
        modal.showModal();                                      // fokus awal di tombol Batal; Esc menutup modal
    });

    btnBatal.addEventListener('click', function () { modal.close(); });
    modal.addEventListener('click', function (e) { if (e.target === modal) modal.close(); });   // klik area gelap = batal
    modal.addEventListener('close', function () { idHapus = null; btnYa.disabled = false; btnYa.textContent = 'Hapus'; });
    btnYa.addEventListener('click', function () {
        if (!idHapus) return;
        btnYa.disabled = true;                                  // cegah klik ganda
        btnYa.textContent = 'Menghapus...';
        kirimHapus(idHapus);
    });
})();
</script>
@endpush