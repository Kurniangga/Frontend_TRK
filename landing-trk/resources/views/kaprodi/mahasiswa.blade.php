{{--
    resources/views/kaprodi/mahasiswa.blade.php
    Daftar mahasiswa (kaprodi) - HANYA BACA: cari, filter tingkat & status, paginasi. Tanpa tambah / hapus.
    Ikon mata menuju halaman detail kaprodi (route kaprodi.mahasiswa.show) bila route itu sudah dibuat.
    Tabel memakai DataTables 2.x (sorting & paginasi ditangani library). Tidak lagi memakai public/js/tabel-urut.js.
--}}
@extends('layouts.kaprodi')

@section('title', 'Mahasiswa')

@section('content')
{{-- CSS DataTables + penyesuaian tampilan (sama dengan halaman admin & Data Dosen kaprodi) --}}
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

    /* Lebar kolom (total harus 100%): NIM 20, Nama 26, Tingkat 14, Kelas 14, Status 16, Aksi 10 */
    #tabel.dataTable > thead > tr > th:nth-child(1) { width: 20%; }
    #tabel.dataTable > thead > tr > th:nth-child(2) { width: 26%; }
    #tabel.dataTable > thead > tr > th:nth-child(3) { width: 14%; }
    #tabel.dataTable > thead > tr > th:nth-child(4) { width: 14%; }
    #tabel.dataTable > thead > tr > th:nth-child(5) { width: 16%; }
    #tabel.dataTable > thead > tr > th:nth-child(6) { width: 10%; }

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

<main class="flex flex-col gap-10 px-4 py-8 sm:px-6">
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div class="flex flex-col gap-2">
            <h1 class="text-[32px] font-bold leading-[38px] tracking-[-0.2px]">Mahasiswa</h1>
            <x-admin.breadcrumb :items="[['Dashboard', 'kaprodi.dashboard'], ['Data Mahasiswa']]" />
        </div>
    </div>

    <section class="flex flex-col gap-8 rounded-lg bg-white px-4 pb-6 pt-4 shadow-[1px_0_3px_rgba(0,0,0,0.02),5px_2px_6px_rgba(0,0,0,0.02)]">

        {{-- Pencarian + filter --}}
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:gap-6">
            <div class="flex flex-1 items-center gap-2 rounded-lg border border-[#E8E8EC] bg-white px-3 py-1.5 focus-within:border-[#2277FF]">
                <svg class="text-[#A8A8A8]" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M10 10m-7 0a7 7 0 1 0 14 0a7 7 0 1 0 -14 0" /><path d="M21 21l-6 -6" /></svg>
                <input id="q" placeholder="Cari nama atau NIM..." class="min-w-0 flex-1 bg-transparent text-base tracking-[-0.18px] outline-none placeholder:text-[#A8A8A8]">
            </div>
            <div class="relative w-full sm:w-[187px]">
                <select id="fLevel" class="h-10 w-full cursor-pointer appearance-none rounded-lg border border-[#E8E8EC] bg-white px-4 pr-10 text-base text-[#2E2E2E] shadow-[inset_0_0_0_1px_rgba(0,0,45,0.09)] outline-none focus:ring-2 focus:ring-[#2277FF]/40">
                    <option value="all">Semua Tingkat</option>
                    @foreach ($levels as $l)
                        <option value="{{ $l }}">{{ $l }}</option>
                    @endforeach
                </select>
                <svg class="pointer-events-none absolute right-4 top-1/2 -translate-y-1/2 text-[#1C2024]" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 9l6 6l6 -6" /></svg>
            </div>
            <div class="relative w-full sm:w-[187px]">
                <select id="fStatus" class="h-10 w-full cursor-pointer appearance-none rounded-lg border border-[#E8E8EC] bg-white px-4 pr-10 text-base text-[#2E2E2E] shadow-[inset_0_0_0_1px_rgba(0,0,45,0.09)] outline-none focus:ring-2 focus:ring-[#2277FF]/40">
                    <option value="all">Semua Status</option>
                    <option value="Aktif">Aktif</option>
                    <option value="Cuti">Cuti</option>
                    <option value="Lulus">Lulus</option>
                    <option value="Non-aktif">Non-aktif</option>
                </select>
                <svg class="pointer-events-none absolute right-4 top-1/2 -translate-y-1/2 text-[#1C2024]" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 9l6 6l6 -6" /></svg>
            </div>
        </div>

        {{-- Tabel (isi <tbody> dan paginasi dibuat otomatis oleh DataTables) --}}
        <div id="tabel-wrap">
            <table id="tabel">
                <thead>
                    <tr>
                        <th>NIM</th>
                        <th>Nama</th>
                        <th>Tingkat</th>
                        <th>Kelas</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
            </table>
        </div>
    </section>
</main>
@endsection

@push('scripts')
{{-- Hapus baris jQuery ini kalau layout kaprodi kamu sudah memuat jQuery 3.x --}}
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/2.1.8/js/dataTables.min.js"></script>
<script>
(function () {
    var data = @json($students);
    var PAGE_SIZE = 10;   // jumlah baris per halaman

    // Halaman detail kaprodi belum dibuat: tombol aktif otomatis begitu route kaprodi.mahasiswa.show terdaftar.
    var URL_DETAIL = @js(\Illuminate\Support\Facades\Route::has('kaprodi.mahasiswa.show') ? route('kaprodi.mahasiswa.show', ['id' => '__ID__']) : null);

    var $ = function (id) { return document.getElementById(id); };

    // Nilai filter dropdown (dicek dari data asli baris)
    var filter = { level: 'all', status: 'all' };

    function esc(s) {
        return String(s == null ? '' : s).replace(/[&<>"']/g, function (c) {
            return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c];
        });
    }
    function svg(w, sw, paths) {
        return '<svg width="' + w + '" height="' + w + '" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="' + sw + '" stroke-linecap="round" stroke-linejoin="round">' + paths + '</svg>';
    }
    var I_EYE = svg(20, 1.5, '<path d="M10 12a2 2 0 1 0 4 0a2 2 0 0 0 -4 0" /><path d="M21 12c-2.4 4 -5.4 6 -9 6c-3.6 0 -6.6 -2 -9 -6c2.4 -4 5.4 -6 9 -6c3.6 0 6.6 2 9 6" />');

    var TXT = DataTable.render.text(); // otomatis escape HTML

    /* ---------- Filter tingkat + status ---------- */
    DataTable.ext.search.push(function (settings, searchData, dataIndex, r) {
        if (settings.nTable.id !== 'tabel') return true;
        return (filter.level === 'all' || r.level === filter.level) &&
               (filter.status === 'all' || r.status === filter.status);
    });

    var table = new DataTable('#tabel', {
        data: data,
        autoWidth: false,
        pageLength: PAGE_SIZE,
        order: [],                       // tanpa urutan awal = urutan asli dari server
        columns: [
            { data: 'nim',       render: TXT, className: 'truncate', orderable: false },   // NIM tidak bisa diurutkan (sama seperti sebelumnya)
            { data: 'name',      render: TXT, className: 'truncate' },
            { data: 'level',     render: TXT, className: 'truncate', searchable: false },
            { data: 'className', render: TXT, className: 'truncate', searchable: false },
            {
                data: 'status', searchable: false,
                render: function (d, type) {
                    if (type !== 'display') return d;
                    var warna = d === 'Aktif' ? 'border-[#D6EDB5] bg-[#E6F3D3] text-[#4C7D08]'
                              : d === 'Cuti'  ? 'border-[#FAD4A8] bg-[#FBE5CB] text-[#854700]'
                              :                 'border-[#E4E4E7] bg-[#EDEDF0] text-[#55555C]';
                    return '<span class="inline-flex rounded-lg border px-2 py-0.5 text-xs font-medium leading-4 ' + warna + '">' + esc(d) + '</span>';
                }
            },
            {
                data: null, orderable: false, searchable: false,
                render: function (d, type, r) {
                    return '<div class="flex items-center gap-3">' +
                        '<button type="button" title="Lihat detail" aria-label="Lihat detail" data-act="view" data-id="' + esc(r.id) + '" class="rounded-md bg-[#CDDEF9] p-1.5 text-[#2277FF] hover:bg-[#BBD2F6] cursor-pointer">' + I_EYE + '</button>' +
                    '</div>';
                }
            }
        ],
        // Tooltip: arahkan kursor ke teks yang terpotong "..." -> muncul teks lengkapnya
        createdRow: function (tr, r) {
            var td = tr.children;
            td[0].title = r.nim || '';
            td[1].title = r.name || '';
        },
        // Hanya paginasi (tengah bawah). Kotak cari, "show entries", dan info bawaan dimatikan
        layout: { topStart: null, topEnd: null, bottomStart: null, bottomEnd: null, bottom: 'paging' },
        pagingType: 'simple_numbers',
        language: {
            emptyTable: 'Tidak ada data',
            zeroRecords: 'Tidak ada data',
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
    // Cari di NIM dan nama (kolom lain tidak ikut dicari)
    $('q').addEventListener('input', function (e) { table.search(e.target.value.trim()).draw(); });
    $('fLevel').addEventListener('change', function (e) { filter.level = e.target.value; table.draw(); });
    $('fStatus').addEventListener('change', function (e) { filter.status = e.target.value; table.draw(); });

    /* ---------- Aksi: lihat detail (aktif bila route kaprodi.mahasiswa.show ada) ---------- */
    $('tabel').addEventListener('click', function (e) {
        var b = e.target.closest('button[data-act]'); if (!b) return;
        if (b.dataset.act === 'view' && URL_DETAIL) {
            window.location.href = URL_DETAIL.replace('__ID__', encodeURIComponent(b.dataset.id));
        }
    });
})();
</script>
@endpush