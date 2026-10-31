{{--
    resources/views/kaprodi/kurikulum.blade.php
    Kurikulum TRK (kaprodi): tambah / ubah / ubah status / hapus langsung tersimpan lewat
    KaprodiKurikulumController -> KurikulumStore (JSON). Data sama dengan admin; halaman publik ikut berubah.
    Tabel memakai DataTables 2.x (sorting & paginasi ditangani library), tampilan sama dengan halaman Dosen, Mahasiswa, Berita & Akreditasi.
    Hapus memakai modal konfirmasi (bukan confirm bawaan browser).
--}}
@extends('layouts.kaprodi')

@section('title', 'Kurikulum TRK')

@section('content')
{{-- CSS DataTables + penyesuaian tampilan (sama dengan halaman Dosen, Mahasiswa, Berita & Akreditasi) --}}
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

    #tabel { width: 100%; min-width: 940px; table-layout: fixed; border-collapse: collapse; margin: 0 !important; font-size: 14px; color: #1C2024; }

    /* Lebar kolom (total harus 100%): Nama 32, Tahun 15, Status 14, Aksi 18, Dokumen 21 */
    #tabel.dataTable > thead > tr > th:nth-child(1) { width: 20%; }
    #tabel.dataTable > thead > tr > th:nth-child(2) { width: 20%; }
    #tabel.dataTable > thead > tr > th:nth-child(3) { width: 14%; }
    #tabel.dataTable > thead > tr > th:nth-child(4) { width: 18%; }
    #tabel.dataTable > thead > tr > th:nth-child(5) { width: 21%; }

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
    .modal-error { margin-top: 12px; font-size: 13px; color: #E5484D; }
    .modal-aksi { display: flex; gap: 12px; margin-top: 24px; }
    .modal-btn { flex: 1; height: 40px; border: 1px solid transparent; border-radius: 8px; font-size: 14px; font-weight: 500; cursor: pointer; }
    .modal-btn-batal { background: #fff; border-color: #E8E8EC; color: #2E2E2E; }
    .modal-btn-batal:hover { background: #F9F9FB; }
    .modal-btn-hapus { background: #E5484D; color: #fff; }
    .modal-btn-hapus:hover { background: #D13A3F; }
    .modal-btn-hapus:disabled { opacity: .7; cursor: not-allowed; }
</style>

<main class="p-4 pb-10 sm:p-6">
    <h1 class="mt-[6px] text-[32px] font-bold leading-10 tracking-[-0.3px]">Kurikulum TRK</h1>
    <x-admin.breadcrumb :items="[['Dashboard', 'kaprodi.dashboard'], ['Kurikulum'], ['Kurikulum TRK']]" />

    <section class="mt-6 rounded-lg bg-white p-6 shadow-[0_1px_4px_rgba(0,0,0,0.06)]">
        <div class="flex flex-wrap items-start justify-between gap-4">
            <div>
                <h2 class="text-lg font-medium">Daftar Kurikulum</h2>
                <p class="text-sm text-[#8A8A90]">Kurikulum yang digunakan oleh program studi</p>
            </div>
            <button id="btnAdd" type="button" class="flex h-10 cursor-pointer items-center gap-2 rounded-md bg-[#1F73FF] px-4 text-base font-medium text-white">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 5l0 14" /><path d="M5 12l14 0" /></svg>
                Kurikulum
            </button>
        </div>

        {{-- Pencarian + filter status --}}
        <div class="mt-5 flex flex-wrap gap-4">
            <div class="relative min-w-[220px] flex-1">
                <svg class="absolute left-3.5 top-2.5 text-[#8A8A90]" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M10 10m-7 0a7 7 0 1 0 14 0a7 7 0 1 0 -14 0" /><path d="M21 21l-6 -6" /></svg>
                <input id="q" placeholder="Cari nama kurikulum..." class="h-10 w-full rounded-md border border-[#E4E4E7] bg-white pl-[45px] pr-3 text-base outline-none placeholder:text-[#B3B3B8] focus:border-[#1F73FF]">
            </div>
            <div class="relative w-full sm:w-[190px]">
                <select id="statusFilter" class="h-10 w-full cursor-pointer appearance-none rounded-md border border-[#E4E4E7] bg-white px-4 pr-9 text-sm outline-none focus:border-[#1F73FF]">
                    <option value="all">Semua Status</option>
                    <option value="aktif">Aktif</option>
                    <option value="nonaktif">Nonaktif</option>
                </select>
                <svg class="pointer-events-none absolute right-3 top-[11px]" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M6 9l6 6l6 -6" /></svg>
            </div>
        </div>

        {{-- Tabel (isi <tbody> dan paginasi dibuat otomatis oleh DataTables) --}}
        <div id="tabel-wrap" class="mt-5">
            <table id="tabel">
                <thead>
                    <tr>
                        <th>Nama Kurikulum</th>
                        <th>Tahun Terbit</th>
                        <th>Status</th>
                        <th>Aksi</th>
                        <th>Dokumen</th>
                    </tr>
                </thead>
            </table>
        </div>
    </section>
</main>

{{-- ===================== POP-UP TAMBAH / EDIT ===================== --}}
<div id="modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 p-4" role="dialog" aria-modal="true" aria-labelledby="modalTitle">
    <div class="w-full max-w-[425px] rounded-lg bg-white p-6 shadow-lg">
        <h3 id="modalTitle" class="text-lg font-semibold">Tambah Kurikulum</h3>
        <div class="mt-4 flex flex-col gap-3">
            <input id="fName" placeholder="Nama kurikulum" maxlength="120" class="h-9 w-full rounded-md border border-[#E4E4E7] bg-white px-3 text-sm outline-none focus:border-[#1F73FF]">
            <input id="fDesc" placeholder="Keterangan" maxlength="255" class="h-9 w-full rounded-md border border-[#E4E4E7] bg-white px-3 text-sm outline-none focus:border-[#1F73FF]">
            <input id="fYear" placeholder="Tahun terbit" inputmode="numeric" maxlength="4" class="h-9 w-full rounded-md border border-[#E4E4E7] bg-white px-3 text-sm outline-none focus:border-[#1F73FF]">

            <label class="flex flex-col gap-1">
                <span id="fPdfLabel" class="text-xs font-medium text-[#55555C]">Dokumen PDF (opsional, maks. 20 MB)</span>
                <input id="fPdf" type="file" accept="application/pdf"
                       class="w-full cursor-pointer rounded-md border border-dashed border-[#BDBDBD] bg-[#FAFAFA] p-2 text-xs text-[#55555C] file:mr-3 file:cursor-pointer file:rounded-md file:border-0 file:bg-[#E3EDFF] file:px-3 file:py-1.5 file:text-xs file:font-medium file:text-[#1F73FF]">
            </label>

            <p id="fError" class="hidden text-xs text-[#E5484D]"></p>
        </div>
        <div class="mt-5 flex justify-end gap-2">
            <button id="btnCancel" type="button" class="h-9 rounded-md border border-[#E4E4E7] bg-white px-4 text-sm font-medium">Batal</button>
            <button id="btnSave" type="button" class="h-9 rounded-md bg-[#1F73FF] px-4 text-sm font-medium text-white disabled:opacity-60">Simpan</button>
        </div>
    </div>
</div>

{{-- Modal konfirmasi hapus (dibuka lewat JS: tombol Hapus di tabel) --}}
<dialog id="modalHapus" aria-labelledby="modalHapusJudul" aria-describedby="modalHapusTeks">
    <div class="modal-kartu">
        <div class="modal-ikon">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M4 7l16 0" /><path d="M10 11l0 6" /><path d="M14 11l0 6" /><path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" /><path d="M9 7v-3a1 1 0 0 1 1 -1h6a1 1 0 0 1 1 1v3" /></svg>
        </div>
        <h2 id="modalHapusJudul" class="modal-judul">Hapus kurikulum?</h2>
        <p id="modalHapusTeks" class="modal-teks"><strong id="modalHapusNama"></strong> akan dihapus permanen. Semua mata kuliah di kurikulum ini juga ikut terhapus.</p>
        <p id="modalHapusError" class="modal-error" hidden></p>
        <div class="modal-aksi">
            <button type="button" id="modalHapusBatal" class="modal-btn modal-btn-batal">Batal</button>
            <button type="button" id="modalHapusYa" class="modal-btn modal-btn-hapus">Hapus</button>
        </div>
    </div>
</dialog>
@endsection

@push('scripts')
{{-- Hapus baris jQuery ini kalau layout kaprodi kamu sudah memuat jQuery 3.x --}}
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/2.1.8/js/dataTables.min.js"></script>
<script>
(function () {
    var data = @json($kurikulum);
    var URL_BASE = @js(route('kaprodi.kurikulum.trk'));   // /kaprodi/kurikulum/trk
    var CSRF = document.querySelector('meta[name=csrf-token]').content;

    var $ = function (id) { return document.getElementById(id); };
    var modal = $('modal');

    // Nilai filter (cari nama + status)
    var filter = { q: '', status: 'all' };
    var editId = null, busyForm = false;

    function esc(s) {
        return String(s == null ? '' : s).replace(/[&<>"']/g, function (c) {
            return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c];
        });
    }
    function icon(paths) {
        return '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">' + paths + '</svg>';
    }
    var I_REFRESH = icon('<path d="M20 11a8.1 8.1 0 0 0 -15.5 -2m-.5 -4v4h4" /><path d="M4 13a8.1 8.1 0 0 0 15.5 2m.5 4v-4h-4" />');
    var I_EDIT    = icon('<path d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1" /><path d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415z" /><path d="M16 5l3 3" />');
    var I_TRASH   = icon('<path d="M4 7l16 0" /><path d="M10 11l0 6" /><path d="M14 11l0 6" /><path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" /><path d="M9 7v-3a1 1 0 0 1 1 -1h6a1 1 0 0 1 1 1v3" />');
    var I_FILE    = icon('<path d="M14 3v4a1 1 0 0 0 1 1h4" /><path d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z" /><path d="M9 9l1 0" /><path d="M9 13l6 0" /><path d="M9 17l6 0" />');

    var TXT = DataTable.render.text(); // otomatis escape HTML

    function cariBaris(id) { return data.filter(function (r) { return String(r.id) === String(id); })[0]; }

    /* ---------- kirim ke server ---------- */
    function request(method, url, body) {
        var opts = { method: method, headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' } };
        if (body) opts.body = body;
        return fetch(url, opts).then(function (res) {
            return res.json().catch(function () { return {}; }).then(function (json) {
                if (!res.ok) {
                    var msg = json.errors ? Object.values(json.errors)[0][0] : (json.message || 'Terjadi kesalahan, coba lagi.');
                    throw new Error(msg);
                }
                return json;
            });
        });
    }

    /* ---------- Filter cari + status ---------- */
    DataTable.ext.search.push(function (settings, searchData, dataIndex, r) {
        if (settings.nTable.id !== 'tabel') return true;
        return String(r.name || '').toLowerCase().indexOf(filter.q) !== -1 &&
               (filter.status === 'all' || r.status === filter.status);
    });

    var table = new DataTable('#tabel', {
        data: data,
        autoWidth: false,
        pageLength: 10,
        order: [],                       // tanpa urutan awal = urutan asli dari server
        columns: [
            {   // nama + keterangan di baris kedua (masing-masing satu baris, selebihnya "...")
                data: 'name',
                render: function (d, type, r) {
                    if (type !== 'display') return d == null ? '' : d;
                    return '<div class="truncate">' + esc(d) + '</div>' +
                           (r.description ? '<div class="truncate text-xs text-[#8A8A90]">' + esc(r.description) + '</div>' : '');
                }
            },
            { data: 'year', render: TXT, className: 'truncate' },
            {
                data: 'status',
                render: function (d, type) {
                    if (type !== 'display') return d;
                    var on = d === 'aktif';
                    return '<span class="inline-flex items-center rounded px-2 py-0.5 text-[11px] font-medium ' +
                        (on ? 'bg-[#DDF3D0] text-[#3A8A1E]' : 'bg-[#EDEDF0] text-[#55555C]') + '">' + (on ? 'Aktif' : 'Nonaktif') + '</span>';
                }
            },
            {
                data: null, orderable: false, searchable: false,
                render: function (d, type, r) {
                    var id = esc(r.id);
                    return '<div class="flex gap-2">' +
                        '<button type="button" title="Ubah status" aria-label="Ubah status" data-act="toggle" data-id="' + id + '" class="flex size-8 cursor-pointer items-center justify-center rounded-md bg-[#FFF1D6] text-[#C77700] hover:bg-[#FCE7BD]">' + I_REFRESH + '</button>' +
                        '<button type="button" title="Edit" aria-label="Edit" data-act="edit" data-id="' + id + '" class="flex size-8 cursor-pointer items-center justify-center rounded-md bg-[#D4E3FF] text-[#1F73FF] hover:bg-[#C2D8FF]">' + I_EDIT + '</button>' +
                        '<button type="button" title="Hapus" aria-label="Hapus" data-act="delete" data-id="' + id + '" class="flex size-8 cursor-pointer items-center justify-center rounded-md bg-[#FFD9D9] text-[#E5484D] hover:bg-[#FFC9C9]">' + I_TRASH + '</button>' +
                    '</div>';
                }
            },
            {
                data: null, orderable: false, searchable: false,
                render: function (d, type, r) {
                    var ada = !!r.pdf_url;
                    return '<button type="button" title="' + (ada ? 'Buka dokumen PDF' : 'Belum ada dokumen. Klik Edit untuk mengunggahnya.') + '" data-act="pdf" data-id="' + esc(r.id) + '"' + (ada ? '' : ' aria-disabled="true"') +
                        ' class="flex h-7 items-center gap-1 rounded-md border border-[#1F73FF] bg-white px-2.5 text-xs font-medium text-[#1F73FF]' + (ada ? ' cursor-pointer hover:bg-[#F2F7FF]' : ' cursor-not-allowed opacity-40') + '">' + I_FILE + 'Lihat PDF</button>';
                }
            }
        ],
        // Tooltip: arahkan kursor ke teks yang terpotong "..." -> muncul teks lengkapnya
        createdRow: function (tr, r) {
            var td = tr.children;
            td[0].title = (r.name || '') + (r.description ? '\n' + r.description : '');
            td[1].title = r.year || '';
        },
        // Hanya paginasi (tengah bawah). Kotak cari, "show entries", dan info bawaan dimatikan
        layout: { topStart: null, topEnd: null, bottomStart: null, bottomEnd: null, bottom: 'paging' },
        pagingType: 'simple_numbers',
        language: {
            emptyTable: 'Tidak ada kurikulum.',
            zeroRecords: 'Tidak ada kurikulum.',
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

    // Muat ulang isi tabel dari array `data`. tetapDiHalaman=true -> tetap di halaman sekarang; false -> kembali ke halaman 1
    function muatUlang(tetapDiHalaman) {
        table.clear().rows.add(data).draw(!tetapDiHalaman);
    }

    /* ---------- Event filter ---------- */
    $('q').addEventListener('input', function (e) { filter.q = e.target.value.trim().toLowerCase(); table.draw(); });
    $('statusFilter').addEventListener('change', function (e) { filter.status = e.target.value; table.draw(); });

    /* ---------- Pop-up tambah / edit ---------- */
    function showError(msg) { var e = $('fError'); e.textContent = msg; e.classList.remove('hidden'); }

    function openForm(row) {
        editId = row ? row.id : null;
        $('modalTitle').textContent = row ? 'Edit Kurikulum' : 'Tambah Kurikulum';
        $('fName').value = row ? row.name : '';
        $('fDesc').value = row ? row.description : '';
        $('fYear').value = row ? row.year : '';
        $('fPdf').value = '';
        $('fPdfLabel').textContent = row && row.pdf_url
            ? 'Ganti dokumen PDF (kosongkan jika tidak diganti)'
            : 'Dokumen PDF (opsional, maks. 20 MB)';
        $('fError').classList.add('hidden');
        modal.classList.remove('hidden'); modal.classList.add('flex');
        $('fName').focus();
    }
    function closeForm() { modal.classList.add('hidden'); modal.classList.remove('flex'); }
    function formTerbuka() { return !modal.classList.contains('hidden'); }

    function save() {
        if (busyForm) return;
        var name = $('fName').value.trim(), desc = $('fDesc').value.trim(), year = $('fYear').value.trim();
        if (!name || !year) { showError('Nama kurikulum dan tahun terbit wajib diisi.'); return; }
        if (!/^\d{4}$/.test(year)) { showError('Tahun terbit harus 4 angka, mis. 2026.'); return; }

        var fd = new FormData();
        fd.append('name', name); fd.append('description', desc); fd.append('year', year);
        var file = $('fPdf').files[0];
        if (file) fd.append('pdf', file);
        if (editId) fd.append('_method', 'PUT');

        var idEdit = editId;
        busyForm = true; $('btnSave').disabled = true;
        request('POST', idEdit ? URL_BASE + '/' + encodeURIComponent(idEdit) : URL_BASE, fd)
            .then(function (json) {
                if (idEdit) {
                    data = data.map(function (r) { return String(r.id) === String(idEdit) ? json.data : r; });
                } else {
                    data.unshift(json.data);
                }
                closeForm();
                muatUlang(!!idEdit);          // edit: tetap di halaman; tambah: ke halaman 1 (data baru ada di paling atas)
            })
            .catch(function (err) { showError(err.message); })
            .then(function () { busyForm = false; $('btnSave').disabled = false; });
    }

    $('btnAdd').addEventListener('click', function () { openForm(null); });
    $('btnCancel').addEventListener('click', closeForm);
    $('btnSave').addEventListener('click', save);
    modal.addEventListener('click', function (e) { if (e.target === modal) closeForm(); });
    document.addEventListener('keydown', function (e) {
        if (!formTerbuka()) return;
        if (e.key === 'Escape') closeForm();
        if (e.key === 'Enter' && e.target.tagName === 'INPUT' && e.target.type !== 'file') save();
    });

    /* ---------- Hapus: modal konfirmasi, lalu DELETE lewat fetch; tabel dimuat ulang & kembali ke halaman 1 ---------- */
    var hd = $('modalHapus'), btnYa = $('modalHapusYa'), pesanError = $('modalHapusError');
    var idHapus = null, busyHapus = false;

    function hapusBaris(id) {
        return request('DELETE', URL_BASE + '/' + encodeURIComponent(id)).then(function () {
            data = data.filter(function (r) { return String(r.id) !== String(id); });
            muatUlang(false);                 // kembali ke halaman 1
        });
    }

    function bukaHapus(row) {
        idHapus = row.id;
        if (typeof hd.showModal !== 'function') {                      // cadangan untuk browser lama
            if (confirm('Hapus kurikulum "' + row.name + '"? Semua mata kuliah di kurikulum ini juga ikut terhapus.')) {
                hapusBaris(idHapus).catch(function (err) { alert(err.message); });
            }
            return;
        }
        $('modalHapusNama').textContent = '"' + row.name + '"';
        pesanError.hidden = true;
        hd.showModal();                                                // fokus awal di tombol Batal; Esc menutup modal
    }

    $('modalHapusBatal').addEventListener('click', function () { hd.close(); });
    hd.addEventListener('click', function (e) { if (e.target === hd) hd.close(); });   // klik area gelap = batal
    hd.addEventListener('close', function () { idHapus = null; busyHapus = false; btnYa.disabled = false; btnYa.textContent = 'Hapus'; });
    btnYa.addEventListener('click', function () {
        if (idHapus == null || busyHapus) return;
        busyHapus = true; btnYa.disabled = true; btnYa.textContent = 'Menghapus...';   // cegah klik ganda
        hapusBaris(idHapus)
            .then(function () { hd.close(); })
            .catch(function (err) {
                pesanError.textContent = err.message; pesanError.hidden = false;
                busyHapus = false; btnYa.disabled = false; btnYa.textContent = 'Hapus';
            });
    });

    /* ---------- Aksi di tabel ---------- */
    $('tabel').addEventListener('click', function (e) {
        var b = e.target.closest('button[data-act]');
        if (!b || b.getAttribute('aria-disabled') === 'true') return;
        var row = cariBaris(b.dataset.id); if (!row) return;

        if (b.dataset.act === 'toggle') {
            request('PATCH', URL_BASE + '/' + encodeURIComponent(row.id) + '/status').then(function (json) {
                data = data.map(function (r) { return String(r.id) === String(row.id) ? json.data : r; });
                muatUlang(true);              // tetap di halaman sekarang
            }).catch(function (err) { alert(err.message); });
        } else if (b.dataset.act === 'edit') {
            openForm(row);
        } else if (b.dataset.act === 'delete') {
            bukaHapus(row);
        } else if (b.dataset.act === 'pdf' && row.pdf_url) {
            window.open(row.pdf_url, '_blank', 'noopener');
        }
    });
})();
</script>
@endpush