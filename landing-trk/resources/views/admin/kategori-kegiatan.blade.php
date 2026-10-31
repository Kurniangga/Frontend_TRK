{{--
    resources/views/admin/kategori-kegiatan.blade.php
    Daftar kategori kegiatan (admin): tambah / edit lewat halaman terpisah (link), hapus lewat modal konfirmasi.
    Tabel memakai DataTables 2.x (sorting, pencarian, paginasi ditangani library).
--}}
@extends('layouts.admin')

@section('title', 'Kategori Kegiatan')

@section('content')
{{-- CSS DataTables + penyesuaian tampilan (sama dengan halaman Kegiatan & Jenis Dokumen) --}}
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
    #tabel.dataTable > thead > tr > th:nth-child(1) { width: 80px; }
    #tabel.dataTable > thead > tr > th:nth-child(2) { width: 289px; }
    #tabel.dataTable > thead > tr > th:nth-child(4) { width: 140px; }

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
        height: 64px; padding: 8px; text-align: left;
        border-top: 1px solid rgb(0 0 47 / .15);
        box-shadow: none;
    }
    #tabel.dataTable > tbody > tr:first-child > td { border-top: 0; }
    #tabel.dataTable > tbody > tr > td.truncate { overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
    #tabel.dataTable > tbody > tr > td.dt-empty { padding: 24px; text-align: center; color: #A8A8A8; }

    /* Kolom No: beri jarak dari tepi kiri kotak (header & isi sejajar) */
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

<div class="px-6 pb-8 pt-4 md:px-8">
    <h1 class="text-[32px] font-bold leading-[38px] tracking-[-0.2px]">Kategori Kegiatan</h1>

    {{-- Breadcrumb --}}
    <nav aria-label="Breadcrumb" class="mt-0.5 text-2xl leading-8 text-[#A8A8A8]">
        <a href="{{ route('admin.dashboard') }}" class="transition-colors hover:text-[#2277FF] hover:underline">Dashboard</a>
        <span class="mx-1">/</span>
        <a href="{{ route('admin.kegiatan.mahasiswa') }}" class="transition-colors hover:text-[#2277FF] hover:underline">Kegiatan</a>
        <span class="mx-1">/</span>
        <span class="text-[#6E6E76]" aria-current="page">Kategori Kegiatan</span>
    </nav>

    @if (request('status'))
        <div class="mt-4 rounded-md border border-[#D6EDB5] bg-[#E6F3D3] px-4 py-3 text-sm text-[#4C7D08]" role="status">
            Kategori kegiatan berhasil {{ request('status') === 'diperbarui' ? 'diperbarui' : 'ditambahkan' }}.
        </div>
    @endif

    <section class="mt-6 rounded-lg bg-white p-6 shadow-[0_1px_4px_rgba(0,0,0,0.06)]">
        <div class="flex flex-wrap items-start justify-between gap-4">
            <div>
                <h2 class="text-lg font-medium text-[#111]">Daftar Kategori Kegiatan</h2>
                <p class="text-sm text-[#8A8A90]">Kategori kegiatan yang tersedia untuk dosen dan mahasiswa</p>
            </div>
            <a href="{{ route('admin.kegiatan.kategori-kegiatan.create') }}"
               class="flex h-10 cursor-pointer items-center gap-2 rounded-md bg-[#2277FF] px-4 text-base text-white hover:bg-[#1b66e0]">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 5v14M5 12h14" /></svg>
                Kategori
            </a>
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
                        <th>Aksi</th>
                    </tr>
                </thead>
            </table>
        </div>
    </section>
</div>

{{-- Modal konfirmasi hapus (dibuka lewat JS: tombol Hapus di tabel) --}}
<dialog id="modalHapus" aria-labelledby="modalHapusJudul" aria-describedby="modalHapusTeks">
    <div class="modal-kartu">
        <div class="modal-ikon">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M4 7l16 0" /><path d="M10 11l0 6" /><path d="M14 11l0 6" /><path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" /><path d="M9 7v-3a1 1 0 0 1 1 -1h6a1 1 0 0 1 1 1v3" /></svg>
        </div>
        <h2 id="modalHapusJudul" class="modal-judul">Hapus kategori?</h2>
        <p id="modalHapusTeks" class="modal-teks"><strong id="modalHapusNama"></strong> akan dihapus permanen.</p>
        <p id="modalHapusError" class="modal-error" hidden></p>
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
(() => {
    const URL_EDIT   = @json(route('admin.kegiatan.kategori-kegiatan.edit', ['id' => '__ID__']));
    const URL_DELETE = @json(route('admin.kegiatan.kategori-kegiatan.destroy', ['id' => '__ID__']));
    const CSRF = @json(csrf_token());
    const STATUS = @json(request('status'));   // 'ditambahkan' | 'diperbarui' | null

    const rows = @json($rows);
    let idHapus = null;

    const $ = (id) => document.getElementById(id);
    const esc = (s) => String(s ?? '').replace(/[&<>"']/g, (c) =>
        ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]));

    const IKON_EDIT = '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M4 20h4L18.5 9.5a2.828 2.828 0 1 0-4-4L4 16v4M13.5 6.5l4 4"/></svg>';
    const IKON_HAPUS = '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M4 7l16 0" /><path d="M10 11l0 6" /><path d="M14 11l0 6" /><path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" /><path d="M9 7v-3a1 1 0 0 1 1 -1h6a1 1 0 0 1 1 1v3" /></svg>';

    const WARNA_JENIS = {
        'Kegiatan Dosen': 'bg-[#E3EDFF] text-[#2277FF]',
        'Kegiatan Mahasiswa': 'bg-[#FFF1CC] text-[#A86A00]',
    };

    // ---------- DataTables ----------
    const table = new DataTable('#tabel', {
        data: rows,
        autoWidth: false,
        pageLength: 10,
        order: [],                       // tanpa urutan awal = urutan asli dari server
        columns: [
            { data: null, orderable: false, searchable: false, defaultContent: '' },   // No: diisi otomatis saat draw
            {   // Nama kategori + label jenis di bawahnya; pencarian mencakup nama DAN jenis
                data: 'name',
                createdCell: (td, d) => { td.title = d ?? ''; },
                render: (d, type, r) => {
                    if (type === 'display') {
                        return '<div style="overflow:hidden;text-overflow:ellipsis;white-space:nowrap">' + esc(d) + '</div>' +
                               '<span class="mt-1 inline-block rounded-full px-2 py-0.5 text-[11px] font-medium ' + (WARNA_JENIS[r.jenis] || 'bg-[#F2F2F7] text-[#55555C]') + '">' + esc(r.jenis) + '</span>';
                    }
                    if (type === 'filter') return (d ?? '') + ' ' + (r.jenis ?? '');
                    return d ?? '';
                }
            },
            {
                data: 'description', className: 'truncate',
                createdCell: (td, d) => { td.title = d ?? ''; },
                render: (d, type) => type === 'display' ? esc(d) : (d ?? '')
            },
            {
                data: null, orderable: false, searchable: false,
                render: (d, type, r) => `
                    <div class="flex items-center gap-3">
                        <a href="${URL_EDIT.replace('__ID__', encodeURIComponent(r.id))}" title="Edit" aria-label="Edit"
                           class="rounded-md bg-[#CDDEF9] p-1.5 text-[#2277FF] hover:bg-[#BBD2F6] cursor-pointer">${IKON_EDIT}</a>
                        <button type="button" data-act="hapus" data-id="${esc(r.id)}" title="Hapus" aria-label="Hapus"
                                class="rounded-md bg-[#FFD9D9] p-1.5 text-[#E5484D] hover:bg-[#FFC9C9] cursor-pointer">${IKON_HAPUS}</button>
                    </div>`
            }
        ],
        // Hanya paginasi (tengah bawah). Kotak cari, "show entries", dan info bawaan dimatikan
        layout: { topStart: null, topEnd: null, bottomStart: null, bottomEnd: null, bottom: 'paging' },
        pagingType: 'simple_numbers',
        language: {
            emptyTable: 'Tidak ada kategori.',
            zeroRecords: 'Tidak ada kategori.',
            paginate: { previous: 'Sebelumnya', next: 'Berikutnya' }
        }
    });

    // Nomor urut mengikuti tampilan (urutan + halaman saat ini)
    function isiNomor() {
        const mulai = table.page.info().start;
        table.column(0, { page: 'current' }).nodes().each((cell, i) => { cell.textContent = mulai + i + 1; });
    }
    // Sembunyikan paginasi kalau hanya ada 1 halaman
    function togglePager() {
        const p = document.querySelector('#tabel-wrap .dt-paging');
        if (p) p.style.display = table.page.info().pages > 1 ? '' : 'none';
    }
    table.on('draw.dt', () => { isiNomor(); togglePager(); });
    isiNomor();
    togglePager();

    const cariBaris = (id) => table.row((idx, data) => String(data.id) === String(id));

    // Setelah hapus: kalau halaman yang sedang dibuka jadi kosong, mundur ke halaman terakhir yang masih ada isinya
    function pindahJikaHalamanKosong() {
        const i = table.page.info();
        if (i.pages > 0 && i.page >= i.pages) table.page(i.pages - 1).draw('page');
    }

    // Baru saja menambah kategori (kembali dari halaman tambah): langsung ke halaman terakhir, tempat data baru berada
    if (STATUS === 'ditambahkan') table.page('last').draw('page');

    // ---------- Modal konfirmasi hapus ----------
    const modalHapus = $('modalHapus'), btnHapusYa = $('modalHapusYa');

    function bukaHapus(id) {
        const r = cariBaris(id).data();
        if (!r) return;
        idHapus = id;
        if (typeof modalHapus.showModal !== 'function') {          // cadangan untuk browser lama
            if (confirm(`Hapus kategori "${r.name}"?`)) prosesHapus();
            return;
        }
        $('modalHapusNama').textContent = `"${r.name}"`;
        $('modalHapusError').hidden = true;
        modalHapus.showModal();                                    // fokus awal di tombol Batal; Esc menutup modal
    }

    async function prosesHapus() {
        if (idHapus === null) return;
        btnHapusYa.disabled = true;                                // cegah klik ganda
        btnHapusYa.textContent = 'Menghapus...';
        try {
            const res = await fetch(URL_DELETE.replace('__ID__', encodeURIComponent(idHapus)), {
                method: 'DELETE',
                headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
            });
            const json = await res.json().catch(() => ({}));
            if (!res.ok) throw new Error(json.message || 'Terjadi kesalahan.');
            cariBaris(idHapus).remove().draw(false);
            pindahJikaHalamanKosong();                             // hapus data terakhir di halaman 2 -> otomatis ke halaman 1
            if (modalHapus.open) modalHapus.close();
        } catch (e) {
            $('modalHapusError').textContent = e.message;
            $('modalHapusError').hidden = false;
            btnHapusYa.disabled = false;
            btnHapusYa.textContent = 'Hapus';
        }
    }

    $('modalHapusBatal').addEventListener('click', () => modalHapus.close());
    modalHapus.addEventListener('click', (e) => { if (e.target === modalHapus) modalHapus.close(); });   // klik area gelap = batal
    modalHapus.addEventListener('close', () => { idHapus = null; btnHapusYa.disabled = false; btnHapusYa.textContent = 'Hapus'; });
    btnHapusYa.addEventListener('click', prosesHapus);

    // ---------- Event ----------
    // Cari di nama, keterangan, dan jenis kategori
    $('kk-cari').addEventListener('input', (e) => table.search(e.target.value.trim()).draw());

    $('tabel').addEventListener('click', (e) => {
        const b = e.target.closest('button[data-act="hapus"]');
        if (b) bukaHapus(b.dataset.id);
    });
})();
</script>
@endpush