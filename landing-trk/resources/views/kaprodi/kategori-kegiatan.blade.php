{{--
    resources/views/kaprodi/kategori-kegiatan.blade.php
    Versi kaprodi (BACA SAJA) dari admin Kategori Kegiatan. Data $rows: [id, name, jenis, description].
    Tabel memakai DataTables 2.x (sorting & paginasi ditangani library), tampilan sama dengan halaman Dosen, Mahasiswa, Berita, Akreditasi & Kurikulum.
    Tidak ada aksi tambah / ubah / hapus di halaman ini.
--}}
@extends('layouts.kaprodi')

@section('title', 'Kategori Kegiatan')

@section('content')
{{-- CSS DataTables + penyesuaian tampilan (sama dengan halaman Dosen, Mahasiswa, Berita, Akreditasi & Kurikulum) --}}
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

    #tabel { width: 100%; min-width: 640px; table-layout: fixed; border-collapse: collapse; margin: 0 !important; font-size: 14px; color: #1C2024; }

    /* Lebar kolom (total harus 100%): No 10, Nama Kategori 32, Keterangan 58 */
    #tabel.dataTable > thead > tr > th:nth-child(1) { width: 10%; }
    #tabel.dataTable > thead > tr > th:nth-child(2) { width: 32%; }
    #tabel.dataTable > thead > tr > th:nth-child(3) { width: 58%; }

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
        height: 56px; padding: 8px; text-align: left;
        border-top: 1px solid rgb(0 0 47 / .15);
        box-shadow: none;
    }
    #tabel.dataTable > tbody > tr:first-child > td { border-top: 0; }
    #tabel.dataTable > tbody > tr > td.truncate { overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
    #tabel.dataTable > tbody > tr > td.dt-empty { padding: 24px; text-align: center; color: #A8A8A8; }

    /* Kolom pertama: beri jarak dari tepi kiri kotak (header & isi sejajar) */
    #tabel.dataTable > thead > tr > th:first-child,
    #tabel.dataTable > tbody > tr > td:first-child:not(.dt-empty) { padding-left: 20px; }

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

<div class="px-6 pb-8 pt-4 md:px-8">
    <h1 class="text-[32px] font-bold leading-[38px] tracking-[-0.2px] text-[#111]">Kategori Kegiatan</h1>

    <x-admin.breadcrumb :items="[
        ['Dashboard', 'kaprodi.dashboard'],
        ['Kegiatan', 'kaprodi.kegiatan.dosen'],
        ['Kategori Kegiatan'],
    ]" />

    <section class="mt-6 rounded-lg bg-white p-6 shadow-[0_1px_4px_rgba(0,0,0,0.06)]">
        <div>
            <h2 class="text-lg font-medium text-[#111]">Daftar Kategori Kegiatan</h2>
            <p class="text-sm text-[#8A8A90]">Kategori kegiatan yang tersedia untuk dosen dan mahasiswa</p>
        </div>

        {{-- Cari --}}
        <div class="relative mt-5">
            <svg class="absolute left-3.5 top-1/2 -translate-y-1/2 text-[#8A8A90]" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="10" cy="10" r="7" /><path d="M21 21l-6-6" /></svg>
            <input id="kk-cari" type="text" placeholder="Cari kategori kegiatan..."
                   class="h-10 w-full rounded-md border border-[#E4E4E7] bg-white pl-11 pr-3 text-base outline-none placeholder:text-[#B3B3B8] focus:border-[#2277FF]">
        </div>

        {{-- Tabel (isi <tbody> dan paginasi dibuat otomatis oleh DataTables) --}}
        <div id="tabel-wrap" class="mt-5">
            <table id="tabel">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Kategori</th>
                        <th>Keterangan</th>
                    </tr>
                </thead>
            </table>
        </div>
    </section>
</div>
@endsection

@push('scripts')
{{-- Hapus baris jQuery ini kalau layout kaprodi kamu sudah memuat jQuery 3.x --}}
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/2.1.8/js/dataTables.min.js"></script>
<script>
(function () {
    var rows = @json($rows);

    var WARNA_JENIS = {
        'Kegiatan Dosen': 'bg-[#E3EDFF] text-[#2277FF]',
        'Kegiatan Mahasiswa': 'bg-[#FFF1CC] text-[#A86A00]'
    };

    var $ = function (id) { return document.getElementById(id); };
    var q = '';

    function esc(s) {
        return String(s == null ? '' : s).replace(/[&<>"']/g, function (c) {
            return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c];
        });
    }
    var TXT = DataTable.render.text(); // otomatis escape HTML

    /* ---------- Pencarian: nama, keterangan, dan jenis kategori ---------- */
    DataTable.ext.search.push(function (settings, searchData, dataIndex, r) {
        if (settings.nTable.id !== 'tabel') return true;
        if (!q) return true;
        return String(r.name == null ? '' : r.name).toLowerCase().indexOf(q) !== -1 ||
               String(r.description == null ? '' : r.description).toLowerCase().indexOf(q) !== -1 ||
               String(r.jenis == null ? '' : r.jenis).toLowerCase().indexOf(q) !== -1;
    });

    var table = new DataTable('#tabel', {
        data: rows,
        autoWidth: false,
        pageLength: 10,
        order: [],                       // tanpa urutan awal = urutan asli dari server
        columns: [
            { data: null, orderable: false, searchable: false, defaultContent: '' },   // nomor urut: diisi ulang setiap tabel digambar
            {   // nama kategori (satu baris) + label jenis di bawahnya
                data: 'name',
                render: function (d, type, r) {
                    if (type !== 'display') return d == null ? '' : d;
                    return '<div class="truncate font-medium text-[#111]">' + esc(d) + '</div>' +
                           '<span class="mt-1 inline-block max-w-full truncate rounded-full px-2 py-0.5 align-top text-[11px] font-medium ' + (WARNA_JENIS[r.jenis] || 'bg-[#F2F2F7] text-[#55555C]') + '">' + esc(r.jenis) + '</span>';
                }
            },
            { data: 'description', render: TXT, className: 'truncate' }
        ],
        // Tooltip: arahkan kursor ke teks yang terpotong "..." -> muncul teks lengkapnya
        createdRow: function (tr, r) {
            var td = tr.children;
            td[1].title = (r.name || '') + (r.jenis ? ' (' + r.jenis + ')' : '');
            td[2].title = r.description || '';
        },
        // Hanya paginasi (tengah bawah). Kotak cari, "show entries", dan info bawaan dimatikan
        layout: { topStart: null, topEnd: null, bottomStart: null, bottomEnd: null, bottom: 'paging' },
        pagingType: 'simple_numbers',
        language: {
            emptyTable: 'Tidak ada kategori.',
            zeroRecords: 'Tidak ada kategori.',
            paginate: { previous: 'Sebelumnya', next: 'Berikutnya' }
        }
    });

    // Nomor urut mengikuti hasil pencarian & halaman (halaman 2 mulai dari 11, dst.)
    function isiNomor() {
        var mulai = table.page.info().start;
        table.column(0, { page: 'current' }).nodes().each(function (cell, i) { cell.textContent = mulai + i + 1; });
    }

    // Sembunyikan paginasi kalau hanya ada 1 halaman
    function togglePager() {
        var p = document.querySelector('#tabel-wrap .dt-paging');
        if (p) p.style.display = table.page.info().pages > 1 ? '' : 'none';
    }
    table.on('draw.dt', function () { isiNomor(); togglePager(); });
    isiNomor();
    togglePager();

    /* ---------- Event pencarian ---------- */
    $('kk-cari').addEventListener('input', function (e) { q = e.target.value.trim().toLowerCase(); table.draw(); });
})();
</script>
@endpush