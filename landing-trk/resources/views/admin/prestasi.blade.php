{{--
    resources/views/admin/prestasi.blade.php
    Daftar prestasi dosen & mahasiswa: tab tipe, cari, filter semester & kategori, paginasi (berfungsi).
    Aksi: lihat, edit (link), hapus (modal konfirmasi -> DELETE lewat form + CSRF).
    Tabel memakai DataTables 2.x (sorting & paginasi ditangani library). Tidak lagi memakai public/js/tabel-urut.js.
--}}
@extends('layouts.admin')

@section('title', 'Prestasi')

@php
    $tabs = ['Semua', 'Dosen', 'Mahasiswa'];
    $kategoriList = ['Lomba', 'Penghargaan'];
    $selectCls = 'h-10 w-full appearance-none rounded-lg border border-[#E8E8EC] bg-white px-4 pr-10 text-base text-[#2E2E2E] outline-none focus:border-[#2277FF] cursor-pointer';
    $chev = '<svg class="pointer-events-none absolute right-4 top-1/2 -translate-y-1/2 text-[#1C2024]" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 9l6 6l6 -6" /></svg>';
@endphp

@section('content')
{{-- CSS DataTables + penyesuaian tampilan (sama dengan halaman Kegiatan & Berita) --}}
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

    #tabel { width: 100%; min-width: 1040px; table-layout: fixed; border-collapse: collapse; margin: 0 !important; font-size: 14px; color: #1C2024; }

    /* Lebar kolom: Nama Prestasi paling lebar, Aksi pas untuk 3 tombol */
    #tabel.dataTable > thead > tr > th:nth-child(1) { width: 27%; }
    #tabel.dataTable > thead > tr > th:nth-child(2) { width: 15%; }
    #tabel.dataTable > thead > tr > th:nth-child(3) { width: 17%; }
    #tabel.dataTable > thead > tr > th:nth-child(4) { width: 12%; }
    #tabel.dataTable > thead > tr > th:nth-child(5) { width: 16%; }
    #tabel.dataTable > thead > tr > th:nth-child(6) { width: 13%; }

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
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div class="flex flex-col gap-2">
            <h1 class="text-[32px] font-bold leading-[38px] tracking-[-0.2px]">Prestasi</h1>
            <p class="text-xl font-medium leading-[30px] tracking-[-0.15px] text-[#A8A8A8] sm:text-2xl">Dashboard / Prestasi</p>
        </div>

        {{-- Tombol Tambah Prestasi --}}
        <a href="{{ Route::has('admin.prestasi.create') ? route('admin.prestasi.create') : '#' }}"
           class="inline-flex cursor-pointer items-center justify-center gap-2 rounded-lg bg-[#2277FF] px-4 py-2.5 text-sm font-semibold text-white transition-colors hover:bg-[#1b5fcc] focus:outline-none focus:ring-2 focus:ring-[#2277FF] focus:ring-offset-2">
            <svg class="size-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
            </svg>
            Prestasi
        </a>
    </div>

    <div class="flex flex-col gap-8 rounded-lg bg-white p-4 pb-6 shadow-sm">

        {{-- Tab tipe --}}
        <div id="tabs" role="tablist" class="flex gap-6 overflow-x-auto border-b border-[#0A0A0A]/[0.06]">
            @foreach ($tabs as $t)
                <button type="button" role="tab" data-tab="{{ $t }}" aria-selected="{{ $loop->first ? 'true' : 'false' }}"
                        class="group relative flex h-8 shrink-0 cursor-pointer items-center gap-2 px-1 text-xs text-[#0A0A0A]/30 transition-colors hover:text-[#0A0A0A]/60 aria-selected:font-medium aria-selected:text-[#0A0A0A]">
                    <span>{{ $t }}</span>
                    @if ($loop->first)
                        <span class="rounded-lg bg-[#2277FF] px-2 py-0.5 font-medium text-[#FEFEFE]">{{ count($rows) }}</span>
                    @endif
                    <span class="absolute inset-x-0 bottom-0 hidden h-0.5 bg-[#2277FF] group-aria-selected:block"></span>
                </button>
            @endforeach
        </div>

        {{-- Filter (pencarian + 2 dropdown tetap dalam 1 baris di layar lebar) --}}
        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:gap-6">
            <div class="flex flex-1 items-center gap-2 rounded-lg border border-[#E8E8EC] bg-white px-3 py-1.5 focus-within:border-[#2277FF]">
                <svg class="size-6 text-[#A8A8A8]" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="10" cy="10" r="7"/><path d="M21 21l-6-6"/></svg>
                <input id="q" type="text" placeholder="Cari nama prestasi..." class="min-w-0 flex-1 bg-transparent text-base tracking-[-0.18px] outline-none placeholder:text-[#A8A8A8]">
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
                <select id="fKategori" class="{{ $selectCls }}">
                    <option value="">Semua Kategori</option>
                    @foreach ($kategoriList as $k)
                        <option value="{{ $k }}">{{ $k }}</option>
                    @endforeach
                </select>
                {!! $chev !!}
            </div>
        </div>

        {{-- Tabel (isi <tbody> dan paginasi dibuat otomatis oleh DataTables) --}}
        <div id="tabel-wrap">
            <table id="tabel">
                <thead>
                    <tr>
                        <th>Nama Prestasi</th>
                        <th>Kategori</th>
                        <th>Diunggah Oleh</th>
                        <th>Tipe</th>
                        <th>Tanggal</th>
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
        <h2 id="modalHapusJudul" class="modal-judul">Hapus prestasi?</h2>
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
    var CSRF = @json(csrf_token());

    var TIPE = {
        'Dosen':     'border-[#D8C8F5] bg-[#EADFFB] text-[#6A3FC7]',
        'Mahasiswa': 'border-[#B7E0E0] bg-[#D9F1F1] text-[#0E7C7B]'
    };

    var $ = function (id) { return document.getElementById(id); };
    var tabs = Array.prototype.slice.call(document.querySelectorAll('[data-tab]'));

    // Nilai filter (tab tipe + 2 dropdown), dicek dari data asli baris
    var filter = { tab: 'Semua', semester: '', kategori: '' };

    function esc(s) {
        return String(s == null ? '' : s).replace(/[&<>"']/g, function (c) {
            return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c];
        });
    }
    function svg(paths, w, sw) {
        return '<svg width="' + w + '" height="' + w + '" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="' + sw + '" stroke-linecap="round" stroke-linejoin="round">' + paths + '</svg>';
    }
    var I_EYE  = svg('<circle cx="12" cy="12" r="2"/><path d="M22 12c-2.667 4.667-6 7-10 7s-7.333-2.333-10-7c2.667-4.667 6-7 10-7s7.333 2.333 10 7"/>', 20, 1.5);
    var I_EDIT = svg('<path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"/>', 20, 1.5);
    var I_DEL  = svg('<path d="M3 6h18M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2M10 11v6M14 11v6"/>', 20, 1.5);

    var TXT = DataTable.render.text(); // otomatis escape HTML

    // Tanggal -> angka untuk pengurutan. Pakai raw_date kalau ada; kalau tidak, baca teks "21 Mei 2024"
    var BULAN = { jan: 1, feb: 2, mar: 3, apr: 4, mei: 5, may: 5, jun: 6, jul: 7, agu: 8, aug: 8, ags: 8, sep: 9, okt: 10, oct: 10, nov: 11, des: 12, dec: 12 };
    function tglKeAngka(r) {
        if (r.raw_date) { var t = new Date(r.raw_date).getTime(); if (!isNaN(t)) return t; }
        var m = String(r.tanggal || '').toLowerCase().match(/(\d{1,2})\s+([a-z]+)\.?\s+(\d{4})/);
        if (m && BULAN[m[2].slice(0, 3)]) return Date.UTC(+m[3], BULAN[m[2].slice(0, 3)] - 1, +m[1]);
        return 0;
    }

    /* ---------- Filter tab tipe + semester + kategori ---------- */
    DataTable.ext.search.push(function (settings, searchData, dataIndex, r) {
        if (settings.nTable.id !== 'tabel') return true;
        return (filter.tab === 'Semua' || r.tipe === filter.tab) &&
               (!filter.semester || r.semester === filter.semester) &&
               (!filter.kategori || r.kategori === filter.kategori);
    });

    var table = new DataTable('#tabel', {
        data: data,
        autoWidth: false,
        pageLength: 10,
        order: [],                       // tanpa urutan awal = urutan asli dari server
        columns: [
            { data: 'nama',       render: TXT, className: 'truncate' },
            { data: 'kategori',   render: TXT, className: 'truncate' },
            { data: 'pengunggah', render: TXT, className: 'truncate' },
            {
                data: 'tipe',
                render: function (d, type) {
                    if (type !== 'display') return d;
                    return '<span class="inline-flex rounded-lg border px-2 py-0.5 text-xs font-medium ' + (TIPE[d] || '') + '">' + esc(d) + '</span>';
                }
            },
            {   // tampil teks tanggal, diurutkan memakai tanggal asli (angka timestamp)
                data: 'tanggal', className: 'truncate',
                render: function (d, type, row) {
                    if (type === 'sort' || type === 'type') return tglKeAngka(row);
                    return esc(d);
                }
            },
            {
                data: null, orderable: false, searchable: false,
                render: function (d, type, r) {
                    var id = esc(r.id);
                    return '<div class="flex items-center gap-2">' +
                        '<a href="/admin/prestasi/' + id + '" title="Lihat" aria-label="Lihat" class="rounded-md bg-[#CDDEF9] p-1.5 text-[#2277FF] hover:bg-[#BBD2F6] cursor-pointer">' + I_EYE + '</a>' +
                        '<a href="/admin/prestasi/' + id + '/edit" title="Edit" aria-label="Edit" class="rounded-md bg-[#FFF5EB] p-1.5 text-[#F59E0B] hover:bg-[#FDE68A] cursor-pointer">' + I_EDIT + '</a>' +
                        '<button type="button" title="Hapus" aria-label="Hapus" data-act="hapus" data-id="' + id + '" class="rounded-md bg-[#FCE8E8] p-1.5 text-[#EF4444] hover:bg-[#F8D0D0] cursor-pointer">' + I_DEL + '</button>' +
                    '</div>';
                }
            }
        ],
        // Tooltip: arahkan kursor ke teks yang terpotong "..." -> muncul teks lengkapnya
        createdRow: function (tr, r) {
            var td = tr.children;
            td[0].title = r.nama || '';
            td[1].title = r.kategori || '';
            td[2].title = r.pengunggah || '';
            td[4].title = r.tanggal || '';
        },
        // Hanya paginasi (tengah bawah). Kotak cari, "show entries", dan info bawaan dimatikan
        layout: { topStart: null, topEnd: null, bottomStart: null, bottomEnd: null, bottom: 'paging' },
        pagingType: 'simple_numbers',
        language: {
            emptyTable: 'Tidak ada prestasi.',
            zeroRecords: 'Tidak ada prestasi.',
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

    /* ---------- Event filter ---------- */
    tabs.forEach(function (t) {
        t.addEventListener('click', function () {
            filter.tab = t.dataset.tab;
            tabs.forEach(function (x) { x.setAttribute('aria-selected', x === t ? 'true' : 'false'); });
            table.draw();
        });
    });
    $('q').addEventListener('input', function (e) {
        table.column(0).search(e.target.value.trim()).draw();   // cari di nama prestasi saja
    });
    $('fSemester').addEventListener('change', function (e) { filter.semester = e.target.value; table.draw(); });
    $('fKategori').addEventListener('change', function (e) { filter.kategori = e.target.value; table.draw(); });

    /* ---------- Hapus: modal konfirmasi, lalu DELETE lewat form + CSRF (halaman dimuat ulang -> kembali ke halaman 1) ---------- */
    var modal = $('modalHapus'), modalNama = $('modalHapusNama'), btnBatal = $('modalHapusBatal'), btnYa = $('modalHapusYa');
    var idHapus = null;

    function kirimHapus(id) {
        var f = document.createElement('form');
        f.method = 'POST';
        f.action = '/admin/prestasi/' + encodeURIComponent(id);
        f.innerHTML = '<input type="hidden" name="_token" value="' + CSRF + '"><input type="hidden" name="_method" value="DELETE">';
        document.body.appendChild(f);
        f.submit();
    }

    $('tabel').addEventListener('click', function (e) {
        var b = e.target.closest('button[data-act="hapus"]'); if (!b) return;
        var r = data.filter(function (x) { return String(x.id) === b.dataset.id; })[0];
        idHapus = b.dataset.id;
        if (typeof modal.showModal !== 'function') {            // cadangan untuk browser lama
            if (confirm('Hapus prestasi "' + (r ? r.nama : '') + '"?')) kirimHapus(idHapus);
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