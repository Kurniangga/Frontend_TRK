{{--
    resources/views/admin/pengajuan.blade.php
    Daftar pengajuan kegiatan: tab status, cari, filter semester/jenis pengaju, paginasi.
    Tabel memakai DataTables 2.x (sorting & paginasi ditangani library). Tidak lagi memakai public/js/tabel-urut.js.
--}}
@extends('layouts.admin')

@section('title', 'Pengajuan Kegiatan')

@php
    $tabs = ['Semua', 'Diajukan', 'Direvisi', 'Ditolak'];
    $selectCls = 'h-10 w-full appearance-none rounded-lg border border-[#E8E8EC] bg-white px-4 pr-10 text-base text-[#2E2E2E] outline-none focus:border-[#2277FF] cursor-pointer';
    $chev = '<svg class="pointer-events-none absolute right-4 top-1/2 -translate-y-1/2 text-[#1C2024]" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 9l6 6l6 -6" /></svg>';
@endphp

@section('content')
{{-- CSS DataTables + penyesuaian tampilan (sama dengan halaman Kegiatan Mahasiswa / Dosen) --}}
<link rel="stylesheet" href="https://cdn.datatables.net/2.1.8/css/dataTables.dataTables.min.css">
<style>
    /* ---------- Kotak tabel ---------- */
    #tabel-wrap .dt-layout-row { margin: 0; }
    #tabel-wrap .dt-layout-row.dt-layout-table {
        border: 1px solid rgb(0 0 47 / .15);
        border-radius: 6px;
        overflow: hidden;                    /* latar header ikut terpotong sudut bulat */
        background: #fff;
    }
    #tabel-wrap .dt-layout-table .dt-layout-cell { width: 100%; padding: 0; overflow-x: auto; }

    #tabel { width: 100%; min-width: 960px; table-layout: fixed; border-collapse: collapse; margin: 0 !important; font-size: 14px; color: #1C2024; }

    /* Lebar kolom: Nama Kegiatan paling lebar, Aksi sempit (isinya hanya 1 tombol) */
    #tabel.dataTable > thead > tr > th:nth-child(1) { width: 28%; }
    #tabel.dataTable > thead > tr > th:nth-child(2) { width: 17%; }
    #tabel.dataTable > thead > tr > th:nth-child(3) { width: 17%; }
    #tabel.dataTable > thead > tr > th:nth-child(4) { width: 17%; }
    #tabel.dataTable > thead > tr > th:nth-child(5) { width: 12%; }
    #tabel.dataTable > thead > tr > th:nth-child(6) { width: 9%; }

    /* Kolom pertama: beri jarak dari tepi kiri kotak (header & isi sejajar) */
    #tabel.dataTable > thead > tr > th:first-child,
    #tabel.dataTable > tbody > tr > td:first-child:not(.dt-empty) { padding-left: 20px; }

    /* Header: latar abu penuh, semua kolom rata kiri */
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
    #tabel.dataTable > tbody > tr > td.dt-empty { padding: 24px; text-align: center; color: #A8A8A8; }

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
    /* Halaman aktif: border + angka biru */
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
</style>

<main class="flex flex-col gap-10 px-4 py-8 sm:px-6">
    {{-- Header --}}
    <div class="flex flex-col gap-2">
        <h1 class="text-[32px] font-bold leading-[38px] tracking-[-0.2px]">Pengajuan Kegiatan</h1>
        <x-admin.breadcrumb :items="[['Dashboard', 'admin.dashboard'], ['Pengajuan Kegiatan']]" />
    </div>

    @if (session('success'))
        <div class="rounded-md border border-[#D6EDB5] bg-[#E6F3D3] px-4 py-3 text-sm text-[#4C7D08]" role="status">{{ session('success') }}</div>
    @endif

    <div class="flex flex-col gap-8 rounded-lg bg-white p-4 pb-6 shadow-sm">

        {{-- Tab status --}}
        <div id="tabs" role="tablist" class="flex gap-6 overflow-x-auto border-b border-[#0A0A0A]/[0.06]">
            @foreach ($tabs as $t)
                <button type="button" role="tab" data-tab="{{ $t }}" aria-selected="{{ $loop->first ? 'true' : 'false' }}"
                        class="group relative flex h-8 shrink-0 cursor-pointer items-center gap-2 px-1 text-xs text-[#0A0A0A]/30 transition-colors hover:text-[#0A0A0A]/60 aria-selected:font-medium aria-selected:text-[#0A0A0A]">
                    <span>{{ $t }}</span>
                    @if ($loop->first)
                        <span id="totalBadge" class="rounded-lg bg-[#2277FF] px-2 py-0.5 font-medium text-[#FEFEFE]">{{ count($rows) }}</span>
                    @endif
                    <span class="absolute inset-x-0 bottom-0 hidden h-0.5 bg-[#2277FF] group-aria-selected:block"></span>
                </button>
            @endforeach
        </div>

        {{-- Filter (pencarian + 2 dropdown tetap dalam 1 baris di layar lebar) --}}
        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:gap-6">
            <div class="flex flex-1 items-center gap-2 rounded-lg border border-[#E8E8EC] bg-white px-3 py-1.5 focus-within:border-[#2277FF]">
                <svg class="size-6 text-[#A8A8A8]" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="10" cy="10" r="7"/><path d="M21 21l-6-6"/></svg>
                <input id="q" type="text" placeholder="Cari nama kegiatan..." class="min-w-0 flex-1 bg-transparent text-base tracking-[-0.18px] outline-none placeholder:text-[#A8A8A8]">
            </div>
            <div class="relative w-full lg:w-[187px]">
                <select id="fSemester" class="{{ $selectCls }}">
                    <option value="">Semua Semester</option>
                    <option value="Ganjil">Ganjil</option>
                    <option value="Genap">Genap</option>
                </select>
                {!! $chev !!}
            </div>
            <div class="relative w-full lg:w-[187px]">
                <select id="fJenis" class="{{ $selectCls }}">
                    <option value="">Semua Pengaju</option>
                    <option value="Dosen">Dosen</option>
                    <option value="Mahasiswa">Mahasiswa</option>
                </select>
                {!! $chev !!}
            </div>
        </div>

        {{-- Tabel (isi <tbody> dan paginasi dibuat otomatis oleh DataTables) --}}
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
@endsection

@push('scripts')
{{-- Hapus baris jQuery ini kalau layout admin kamu sudah memuat jQuery 3.x --}}
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/2.1.8/js/dataTables.min.js"></script>
<script>
(function () {
    var data = @json($rows);

    var STATUS = {
        'Diajukan':  'border-[#AAC8F8] bg-[#CDDEF9] text-[#2277FF]',
        'Direvisi':  'border-[#FAD4A8] bg-[#FBE5CB] text-[#854700]',
        'Disetujui': 'border-[#D6EDB5] bg-[#E6F3D3] text-[#4C7D08]',
        'Ditolak':   'border-[#FAA8A8] bg-[#FBCBCB] text-[#FF3F3F]'
    };

    var $ = function (id) { return document.getElementById(id); };
    var tabs = Array.prototype.slice.call(document.querySelectorAll('[data-tab]'));

    // Nilai filter (tab status + 2 dropdown), dicek dari data asli baris
    var filter = { tab: 'Semua', semester: '', jenis: '' };

    function esc(s) {
        return String(s == null ? '' : s).replace(/[&<>"']/g, function (c) {
            return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c];
        });
    }
    function svg(paths, w, sw) {
        return '<svg width="' + w + '" height="' + w + '" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="' + sw + '" stroke-linecap="round" stroke-linejoin="round">' + paths + '</svg>';
    }
    var I_EYE = svg('<circle cx="12" cy="12" r="2"/><path d="M22 12c-2.667 4.667-6 7-10 7s-7.333-2.333-10-7c2.667-4.667 6-7 10-7s7.333 2.333 10 7"/>', 20, 1.5);

    var TXT = DataTable.render.text(); // otomatis escape HTML

    // Tanggal -> angka untuk pengurutan. Pakai raw_date kalau ada; kalau tidak, baca teks "21 Mei 2024"
    var BULAN = { jan: 1, feb: 2, mar: 3, apr: 4, mei: 5, may: 5, jun: 6, jul: 7, agu: 8, aug: 8, ags: 8, sep: 9, okt: 10, oct: 10, nov: 11, des: 12, dec: 12 };
    function tglKeAngka(r) {
        if (r.raw_date) { var t = new Date(r.raw_date).getTime(); if (!isNaN(t)) return t; }
        var m = String(r.tanggal || '').toLowerCase().match(/(\d{1,2})\s+([a-z]+)\.?\s+(\d{4})/);
        if (m && BULAN[m[2].slice(0, 3)]) return Date.UTC(+m[3], BULAN[m[2].slice(0, 3)] - 1, +m[1]);
        return 0;
    }

    var table = new DataTable('#tabel', {
        data: data,
        autoWidth: false,
        pageLength: 10,
        order: [],                       // tanpa urutan awal = urutan asli dari server
        columns: [
            { data: 'nama',     render: TXT, className: 'truncate' },
            { data: 'kategori', render: TXT, className: 'truncate' },
            { data: 'pengaju',  render: TXT, className: 'truncate' },
            {   // tampil "1 Januari 2026", tapi diurutkan memakai tanggal asli (angka timestamp)
                data: 'tanggal', className: 'truncate',
                render: function (d, type, row) {
                    if (type === 'sort' || type === 'type') return tglKeAngka(row);
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
                    return '<div class="flex items-center gap-3">' +
                        '<button type="button" title="Tinjau" aria-label="Tinjau" data-act="view" data-id="' + esc(r.id) + '" class="rounded-md bg-[#CDDEF9] p-1.5 text-[#2277FF] hover:bg-[#BBD2F6] cursor-pointer">' + I_EYE + '</button>' +
                    '</div>';
                }
            }
        ],
        // Tooltip: arahkan kursor ke teks yang terpotong "..." -> muncul teks lengkapnya
        createdRow: function (tr, r) {
            var td = tr.children;
            td[0].title = r.nama || '';
            td[1].title = r.kategori || '';
            td[2].title = r.pengaju || '';
            td[3].title = r.tanggal || '';
        },
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

    /* ---------- Filter tab status + semester + jenis pengaju ---------- */
    DataTable.ext.search.push(function (settings, searchData, dataIndex, r) {
        if (settings.nTable.id !== 'tabel') return true;
        return (filter.tab === 'Semua' || r.status === filter.tab) &&
               (!filter.semester || r.semester === filter.semester) &&
               (!filter.jenis || r.jenis_pengaju === filter.jenis);
    });

    /* ---------- Event ---------- */
    tabs.forEach(function (t) {
        t.addEventListener('click', function () {
            filter.tab = t.dataset.tab;
            tabs.forEach(function (x) { x.setAttribute('aria-selected', x === t ? 'true' : 'false'); });
            table.draw();
        });
    });
    $('q').addEventListener('input', function (e) {
        table.column(0).search(e.target.value.trim()).draw();   // cari di nama kegiatan saja
    });
    $('fSemester').addEventListener('change', function (e) { filter.semester = e.target.value; table.draw(); });
    $('fJenis').addEventListener('change', function (e) { filter.jenis = e.target.value; table.draw(); });

    /* ---------- Aksi: tinjau -> halaman detail ---------- */
    $('tabel').addEventListener('click', function (e) {
        var btn = e.target.closest('button[data-act="view"]');
        if (!btn) return;
        window.location.href = '/admin/pengajuan/' + btn.dataset.id;
    });
})();
</script>
@endpush