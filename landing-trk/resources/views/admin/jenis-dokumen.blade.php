{{--
    resources/views/admin/jenis-dokumen.blade.php
    Daftar jenis dokumen (admin): tambah / edit lewat pop-up, hapus lewat modal konfirmasi.
    Tabel memakai DataTables 2.x (sorting, paginasi ditangani library).
--}}
@extends('layouts.admin')

@section('title', 'Jenis Dokumen')

@section('content')
{{-- CSS DataTables + penyesuaian tampilan --}}
<link rel="stylesheet" href="https://cdn.datatables.net/2.1.8/css/dataTables.dataTables.min.css">
<style>
    /* ---------- Kotak tabel: sama dengan halaman Kegiatan Dosen / Mahasiswa ---------- */
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
    #tabel.dataTable > thead > tr > th:nth-child(2) { width: 26%; }
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

    /* ---------- Kursor tangan pada tombol ---------- */
    #jd-tambah, #jd-batal, #jd-simpan { cursor: pointer; }
    #jd-simpan:disabled { cursor: not-allowed; }

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
    <h1 class="text-[32px] font-bold ...">Jenis Dokumen</h1>
    {{-- Breadcrumb: Dashboard & Kegiatan bisa diklik, halaman aktif tidak --}}
    <nav aria-label="Breadcrumb" class="mt-[7px] text-xl leading-[26px] text-[#B9B9BF]">
        <a href="{{ route('admin.dashboard') }}" class="transition-colors hover:text-[#226CFF] hover:underline">Dashboard</a>
        <span class="mx-1">/</span>
        <a href="{{ route('admin.kegiatan.mahasiswa') }}" class="transition-colors hover:text-[#226CFF] hover:underline">Kegiatan</a>
        <span class="mx-1">/</span>
        <span class="text-[#6E6E76]" aria-current="page">Jenis Dokumen</span>
    </nav>

    <section class="mt-[26px] max-w-[1152px] rounded-lg border border-[#EFEFF1] bg-white p-[25px] shadow-[0_1px_3px_rgba(0,0,0,0.07)]">
        <div class="flex flex-wrap items-start justify-between gap-3">
            <div>
                <h3 class="text-base font-medium leading-6 text-[#111]">Daftar Jenis Dokumen</h3>
                <p class="text-[13px] leading-[18px] text-[#6E6E76]">Jenis dokumen yang tersedia untuk kegiatan dosen dan mahasiswa</p>
            </div>
            <button type="button" id="jd-tambah"
                    class="flex h-[39px] items-center gap-2 rounded-[7px] bg-[#226CFF] px-[17px] text-sm font-medium text-white hover:bg-[#1b5ae0]">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 5v14M5 12h14" /></svg>
                Jenis Dokumen
            </button>
        </div>

        {{-- Cari --}}
        <div class="relative mt-5">
            <svg class="absolute left-[15px] top-1/2 -translate-y-1/2 text-[#8E8E96]" xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><circle cx="10" cy="10" r="7" /><path d="M21 21l-6-6" /></svg>
            <input id="jd-cari" type="text" placeholder="Cari jenis dokumen..."
                   class="h-10 w-full rounded-[7px] border border-[#DEDEE3] pl-10 pr-3 text-sm outline-none placeholder:text-[#757575] focus:border-[#226CFF]">
        </div>

        {{-- Tabel (isi <tbody> dan paginasi dibuat otomatis oleh DataTables) --}}
        <div id="tabel-wrap" class="mt-[19px]">
            <table id="tabel">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Jenis Dokumen</th>
                        <th>Keterangan</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
            </table>
        </div>
    </section>
</div>

{{-- Pop-up tambah / edit --}}
<div id="jd-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/40 p-4" role="dialog" aria-modal="true">
    <div class="w-full max-w-[540px] rounded-lg bg-white px-6 pb-5 pt-6 shadow-xl">
        <h2 id="jd-modal-judul" class="text-xl font-bold leading-6 text-[#111]">Tambah Jenis Dokumen</h2>
        <p id="jd-modal-sub" class="mt-1 text-xs text-[#B9B9BF]">Isi data jenis dokumen baru program studi</p>

        <div class="mt-5 space-y-4">
            <div>
                <label for="jd-nama" class="mb-1.5 block text-sm font-semibold text-[#111]">Nama Jenis Dokumen</label>
                <input id="jd-nama" type="text" maxlength="100" placeholder="Contoh: Sertifikat"
                       class="h-9 w-full rounded-[5px] border border-[#DEDEE3] px-3 text-xs outline-none placeholder:text-[#B0B0B8] focus:border-[#226CFF]">
            </div>

            <div>
                <label for="jd-ket" class="mb-1.5 block text-sm font-semibold text-[#111]">Keterangan</label>
                <textarea id="jd-ket" rows="4" maxlength="255" placeholder="Tuliskan keterangan singkat mengenai jenis dokumen ini"
                          class="block w-full resize-none rounded-[5px] border border-[#DEDEE3] px-3 py-2 text-xs outline-none placeholder:text-[#B0B0B8] focus:border-[#226CFF]"></textarea>
            </div>

            <p id="jd-error" class="hidden text-[13px] text-[#E5484D]"></p>
        </div>

        <div class="mt-4 flex justify-end gap-2">
            <button type="button" id="jd-batal"
                    class="h-8 rounded-md bg-[#FF4B4B] px-4 text-xs font-semibold text-white hover:bg-[#e63e3e]">Batal</button>
            <button type="button" id="jd-simpan"
                    class="h-8 rounded-md bg-[#226CFF] px-4 text-xs font-semibold text-white hover:bg-[#1b5ae0] disabled:opacity-60">Simpan</button>
        </div>
    </div>
</div>

{{-- Modal konfirmasi hapus (dibuka lewat JS: tombol Hapus di tabel) --}}
<dialog id="modalHapus" aria-labelledby="modalHapusJudul" aria-describedby="modalHapusTeks">
    <div class="modal-kartu">
        <div class="modal-ikon">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M4 7l16 0" /><path d="M10 11l0 6" /><path d="M14 11l0 6" /><path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" /><path d="M9 7v-3a1 1 0 0 1 1 -1h6a1 1 0 0 1 1 1v3" /></svg>
        </div>
        <h2 id="modalHapusJudul" class="modal-judul">Hapus jenis dokumen?</h2>
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
    const URL_STORE  = @json(route('admin.kegiatan.jenis-dokumen.store'));
    const URL_UPDATE = @json(route('admin.kegiatan.jenis-dokumen.update', ['id' => '__ID__']));
    const URL_DELETE = @json(route('admin.kegiatan.jenis-dokumen.destroy', ['id' => '__ID__']));
    const CSRF = @json(csrf_token());

    const rows = @json($rows);
    let editingId = null; // null = tambah baru
    let idHapus = null;

    const $ = (id) => document.getElementById(id);
    const esc = (s) => String(s ?? '').replace(/[&<>"']/g, (c) =>
        ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]));

    const IKON_EDIT = '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M4 20h4L18.5 9.5a2.828 2.828 0 1 0-4-4L4 16v4M13.5 6.5l4 4"/></svg>';
    const IKON_HAPUS = '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M4 7l16 0" /><path d="M10 11l0 6" /><path d="M14 11l0 6" /><path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" /><path d="M9 7v-3a1 1 0 0 1 1 -1h6a1 1 0 0 1 1 1v3" /></svg>';

    // ---------- DataTables ----------
    const table = new DataTable('#tabel', {
        data: rows,
        autoWidth: false,
        pageLength: 10,
        order: [],                       // tanpa urutan awal = urutan asli dari server
        columns: [
            { data: null, orderable: false, searchable: false, defaultContent: '' },   // No: diisi otomatis saat draw
            { data: 'name',        className: 'truncate', createdCell: (td, d) => { td.title = d ?? ''; },
              render: (d, type) => type === 'display' ? esc(d) : (d ?? '') },
            { data: 'description', className: 'truncate', createdCell: (td, d) => { td.title = d ?? ''; },
              render: (d, type) => type === 'display' ? esc(d) : (d ?? '') },
            {
                data: null, orderable: false, searchable: false,
                render: (d, type, r) => `
                    <div class="flex items-center gap-3">
                        <button type="button" data-act="edit" data-id="${esc(r.id)}" title="Edit" aria-label="Edit"
                                class="rounded-md bg-[#CDDEF9] p-1.5 text-[#2277FF] hover:bg-[#BBD2F6] cursor-pointer">${IKON_EDIT}</button>
                        <button type="button" data-act="hapus" data-id="${esc(r.id)}" title="Hapus" aria-label="Hapus"
                                class="rounded-md bg-[#FFD9D9] p-1.5 text-[#E5484D] hover:bg-[#FFC9C9] cursor-pointer">${IKON_HAPUS}</button>
                    </div>`
            }
        ],
        // Hanya paginasi (tengah bawah). Kotak cari, "show entries", dan info bawaan dimatikan
        layout: { topStart: null, topEnd: null, bottomStart: null, bottomEnd: null, bottom: 'paging' },
        pagingType: 'simple_numbers',
        language: {
            emptyTable: 'Tidak ada jenis dokumen.',
            zeroRecords: 'Tidak ada jenis dokumen.',
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

    // ---------- Pop-up tambah / edit ----------
    function bukaModal(row) {
        editingId = row ? row.id : null;
        $('jd-modal-judul').textContent = row ? 'Edit Jenis Dokumen' : 'Tambah Jenis Dokumen';
        $('jd-modal-sub').textContent = row
            ? 'Ubah data jenis dokumen program studi'
            : 'Isi data jenis dokumen baru program studi';
        $('jd-nama').value = row ? row.name : '';
        $('jd-ket').value = row ? row.description : '';
        tampilError('');
        $('jd-modal').classList.remove('hidden');
        $('jd-modal').classList.add('flex');
        $('jd-nama').focus();
    }

    function tutupModal() {
        $('jd-modal').classList.add('hidden');
        $('jd-modal').classList.remove('flex');
    }

    function tampilError(pesan) {
        const el = $('jd-error');
        el.textContent = pesan;
        el.classList.toggle('hidden', !pesan);
    }

    async function kirim(url, method, body) {
        const res = await fetch(url, {
            method,
            headers: {
                'X-CSRF-TOKEN': CSRF,
                'Accept': 'application/json',
                'Content-Type': 'application/json',
            },
            body: body ? JSON.stringify(body) : undefined,
        });
        const json = await res.json().catch(() => ({}));
        if (!res.ok) {
            const pesan = json.errors ? Object.values(json.errors)[0][0] : (json.message || 'Terjadi kesalahan.');
            throw new Error(pesan);
        }
        return json;
    }

    async function simpan() {
        const name = $('jd-nama').value.trim();
        const description = $('jd-ket').value.trim();
        if (!name) { tampilError('Nama jenis dokumen wajib diisi.'); return; }

        const tombol = $('jd-simpan');
        tombol.disabled = true;
        try {
            if (editingId === null) {
                const { row } = await kirim(URL_STORE, 'POST', { name, description });
                table.order([]);                       // baris baru muncul di urutan akhir
                table.row.add(row).draw();
                table.page('last').draw('page');       // lompat ke halaman terakhir
            } else {
                const { row } = await kirim(URL_UPDATE.replace('__ID__', editingId), 'PUT', { name, description });
                cariBaris(row.id).data(row).draw(false);   // tetap di halaman yang sama
            }
            tutupModal();
        } catch (e) {
            tampilError(e.message);
        } finally {
            tombol.disabled = false;
        }
    }

    // ---------- Modal konfirmasi hapus ----------
    const modalHapus = $('modalHapus'), btnHapusYa = $('modalHapusYa');

    function bukaHapus(id) {
        const r = cariBaris(id).data();
        if (!r) return;
        idHapus = id;
        if (typeof modalHapus.showModal !== 'function') {          // cadangan untuk browser lama
            if (confirm(`Hapus "${r.name}"?`)) prosesHapus();
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
            await kirim(URL_DELETE.replace('__ID__', idHapus), 'DELETE');
            cariBaris(idHapus).remove().draw(false);
            pindahJikaHalamanKosong();                                 // hapus data terakhir di halaman 2 -> otomatis ke halaman 1
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
    $('jd-tambah').addEventListener('click', () => bukaModal(null));
    $('jd-batal').addEventListener('click', tutupModal);
    $('jd-simpan').addEventListener('click', simpan);
    $('jd-modal').addEventListener('click', (e) => { if (e.target === $('jd-modal')) tutupModal(); });
    document.addEventListener('keydown', (e) => { if (e.key === 'Escape') tutupModal(); });
    // Enter menyimpan hanya dari kolom nama (kolom keterangan adalah textarea: Enter = baris baru)
    $('jd-nama').addEventListener('keydown', (e) => { if (e.key === 'Enter') simpan(); });

    // Cari di kolom Nama Jenis Dokumen
    $('jd-cari').addEventListener('input', (e) => table.column(1).search(e.target.value.trim()).draw());

    $('tabel').addEventListener('click', (e) => {
        const b = e.target.closest('button[data-act]');
        if (!b) return;
        if (b.dataset.act === 'edit') { const r = cariBaris(b.dataset.id).data(); if (r) bukaModal(r); }
        if (b.dataset.act === 'hapus') bukaHapus(b.dataset.id);
    });
})();
</script>
@endpush