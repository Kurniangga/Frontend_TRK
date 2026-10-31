{{--
    resources/views/admin/dosen.blade.php
    Daftar dosen: cari, filter pangkat & status, paginasi (sudah berfungsi).
    Aksi: lihat (ke halaman detail) dan hapus (modal konfirmasi -> DELETE lewat form + CSRF; aktif bila route admin.dosen.destroy dibuat).
    Tombol Tambah Dosen menjadi link bila route admin.dosen.create dibuat.
    Tabel memakai DataTables 2.x (sorting & paginasi ditangani library). Tidak lagi memakai public/js/tabel-urut.js.
--}}
@extends('layouts.admin')

@section('title', 'Data Dosen')

@section('content')
{{-- CSS DataTables + penyesuaian tampilan (sama dengan halaman Kegiatan & Prestasi) --}}
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

    #tabel { width: 100%; min-width: 820px; table-layout: fixed; border-collapse: collapse; margin: 0 !important; font-size: 14px; color: #1C2024; }

    /* Lebar kolom (total harus 100%): NIP 20, Nama 30, Pangkat 26, Status 12, Aksi 12 */
    #tabel.dataTable > thead > tr > th:nth-child(1) { width: 20%; }
    #tabel.dataTable > thead > tr > th:nth-child(2) { width: 30%; }
    #tabel.dataTable > thead > tr > th:nth-child(3) { width: 26%; }
    #tabel.dataTable > thead > tr > th:nth-child(4) { width: 12%; }
    #tabel.dataTable > thead > tr > th:nth-child(5) { width: 12%; }

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
    <div class="flex flex-wrap items-start justify-between gap-4">
        <div>
            <h1 class="mt-[6px] text-[32px] font-bold leading-10 tracking-[-0.3px]">Data Dosen</h1>
            <x-admin.breadcrumb :items="[['Dashboard', 'admin.dashboard'], ['Data Dosen']]" />
        </div>
        <a id="btnAdd" href="{{ \Illuminate\Support\Facades\Route::has('admin.dosen.create') ? route('admin.dosen.create') : '#' }}" class="mt-2 flex h-10 cursor-pointer items-center gap-2 rounded-md bg-[#1F73FF] px-4 text-base font-medium text-white">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 5l0 14" /><path d="M5 12l14 0" /></svg>
            Tambah Dosen
        </a>
    </div>

    @if (session('success'))
        <div class="mt-4 rounded-md border border-[#D6EDB5] bg-[#E6F3D3] px-4 py-3 text-sm text-[#4C7D08]" role="status">{{ session('success') }}</div>
    @endif

    <section class="mt-6 rounded-lg bg-white p-4 shadow-[0_1px_4px_rgba(0,0,0,0.06)]">

        {{-- Pencarian + filter --}}
        <div class="flex flex-wrap gap-4">
            <div class="relative min-w-[220px] flex-1">
                <svg class="absolute left-3.5 top-2.5 text-[#8A8A90]" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M10 10m-7 0a7 7 0 1 0 14 0a7 7 0 1 0 -14 0" /><path d="M21 21l-6 -6" /></svg>
                <input id="q" placeholder="Cari nama atau NIP..." class="h-10 w-full rounded-md border border-[#E4E4E7] bg-white pl-[45px] pr-3 text-base outline-none placeholder:text-[#B3B3B8] focus:border-[#1F73FF]">
            </div>
            <div class="relative w-full sm:w-[190px]">
                <select id="fPangkat" class="h-10 w-full cursor-pointer appearance-none rounded-md border border-[#E4E4E7] bg-white px-4 pr-9 text-sm outline-none focus:border-[#1F73FF]">
                    <option value="all">Semua Pangkat</option>
                    @foreach ($jabatan as $j)
                        <option value="{{ $j }}">{{ $j }}</option>
                    @endforeach
                </select>
                <svg class="pointer-events-none absolute right-3 top-[11px]" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M6 9l6 6l6 -6" /></svg>
            </div>
            <div class="relative w-full sm:w-[190px]">
                <select id="fStatus" class="h-10 w-full cursor-pointer appearance-none rounded-md border border-[#E4E4E7] bg-white px-4 pr-9 text-sm outline-none focus:border-[#1F73FF]">
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
                        <th>NIP</th>
                        <th>Nama</th>
                        <th>Pangkat / Golongan</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
            </table>
        </div>
    </section>
</main>

{{-- Modal konfirmasi hapus (dibuka lewat JS: tombol Hapus di tabel) --}}
<dialog id="modalHapus" aria-labelledby="modalHapusJudul" aria-describedby="modalHapusTeks">
    <div class="modal-kartu">
        <div class="modal-ikon">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M4 7l16 0" /><path d="M10 11l0 6" /><path d="M14 11l0 6" /><path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" /><path d="M9 7v-3a1 1 0 0 1 1 -1h6a1 1 0 0 1 1 1v3" /></svg>
        </div>
        <h2 id="modalHapusJudul" class="modal-judul">Hapus dosen?</h2>
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
(function () {
    var data = @json($dosen);
    var URL_DETAIL = @js(route('admin.dosen.show', ['id' => '__ID__']));
    var URL_DELETE = @js(\Illuminate\Support\Facades\Route::has('admin.dosen.destroy') ? route('admin.dosen.destroy', ['id' => '__ID__']) : null);
    var CSRF = @json(csrf_token());

    var $ = function (id) { return document.getElementById(id); };

    // Nilai filter dropdown (dicek dari data asli baris)
    var filter = { pangkat: 'all', status: 'all' };

    function esc(s) {
        return String(s == null ? '' : s).replace(/[&<>"']/g, function (c) {
            return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c];
        });
    }
    function svg(w, sw, paths) {
        return '<svg width="' + w + '" height="' + w + '" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="' + sw + '" stroke-linecap="round" stroke-linejoin="round">' + paths + '</svg>';
    }
    var I_EYE   = svg(16, 1.5, '<path d="M10 12a2 2 0 1 0 4 0a2 2 0 0 0 -4 0" /><path d="M21 12c-2.4 4 -5.4 6 -9 6c-3.6 0 -6.6 -2 -9 -6c2.4 -4 5.4 -6 9 -6c3.6 0 6.6 2 9 6" />');
    var I_TRASH = svg(16, 1.5, '<path d="M4 7l16 0" /><path d="M10 11l0 6" /><path d="M14 11l0 6" /><path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" /><path d="M9 7v-3a1 1 0 0 1 1 -1h6a1 1 0 0 1 1 1v3" />');

    var TXT = DataTable.render.text(); // otomatis escape HTML
    function pangkat(r) { return (r.jabatan || '') + ', ' + (r.golongan || ''); }

    /* ---------- Filter pangkat + status ---------- */
    DataTable.ext.search.push(function (settings, searchData, dataIndex, r) {
        if (settings.nTable.id !== 'tabel') return true;
        return (filter.pangkat === 'all' || r.jabatan === filter.pangkat) &&
               (filter.status === 'all' || r.status === filter.status);
    });

    var table = new DataTable('#tabel', {
        data: data,
        autoWidth: false,
        pageLength: 10,
        order: [],                       // tanpa urutan awal = urutan asli dari server
        columns: [
            { data: 'nip',  render: TXT, className: 'truncate', orderable: false },   // NIP tidak bisa diurutkan (sama seperti sebelumnya)
            { data: 'nama', render: TXT, className: 'truncate' },
            {   // "Jabatan, Golongan"
                data: null, className: 'truncate', searchable: false,
                render: function (d, type, r) { return type === 'display' ? esc(pangkat(r)) : pangkat(r); }
            },
            {
                data: 'status', searchable: false,
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
                        '<button type="button" title="Lihat detail" aria-label="Lihat detail" data-act="view" data-id="' + id + '" class="flex size-8 cursor-pointer items-center justify-center rounded-md bg-[#D4E3FF] text-[#1F73FF] hover:bg-[#C2D8FF]">' + I_EYE + '</button>' +
                        '<button type="button" title="Hapus" aria-label="Hapus" data-act="delete" data-id="' + id + '" class="flex size-8 cursor-pointer items-center justify-center rounded-md bg-[#FFD9D9] text-[#E5484D] hover:bg-[#FFC9C9]">' + I_TRASH + '</button>' +
                    '</div>';
                }
            }
        ],
        // Tooltip: arahkan kursor ke teks yang terpotong "..." -> muncul teks lengkapnya
        createdRow: function (tr, r) {
            var td = tr.children;
            td[0].title = r.nip || '';
            td[1].title = r.nama || '';
            td[2].title = pangkat(r);
        },
        // Hanya paginasi (tengah bawah). Kotak cari, "show entries", dan info bawaan dimatikan
        layout: { topStart: null, topEnd: null, bottomStart: null, bottomEnd: null, bottom: 'paging' },
        pagingType: 'simple_numbers',
        language: {
            emptyTable: 'Tidak ada data dosen.',
            zeroRecords: 'Tidak ada data dosen.',
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
    // Cari di NIP dan nama (kolom pangkat & status tidak ikut dicari)
    $('q').addEventListener('input', function (e) { table.search(e.target.value.trim()).draw(); });
    $('fPangkat').addEventListener('change', function (e) { filter.pangkat = e.target.value; table.draw(); });
    $('fStatus').addEventListener('change', function (e) { filter.status = e.target.value; table.draw(); });

    /* ---------- Hapus: modal konfirmasi, lalu DELETE lewat form + CSRF (halaman dimuat ulang -> kembali ke halaman 1) ---------- */
    var modal = $('modalHapus'), btnYa = $('modalHapusYa'), pesanError = $('modalHapusError');
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
        var b = e.target.closest('button[data-act]'); if (!b) return;
        var r = data.filter(function (x) { return String(x.id) === b.dataset.id; })[0];

        if (b.dataset.act === 'view') {
            window.location.href = URL_DETAIL.replace('__ID__', encodeURIComponent(b.dataset.id));
            return;
        }
        if (b.dataset.act === 'delete') {
            idHapus = b.dataset.id;
            if (typeof modal.showModal !== 'function') {            // cadangan untuk browser lama
                if (URL_DELETE && confirm('Hapus dosen "' + (r ? r.nama : '') + '"?')) kirimHapus(idHapus);
                return;
            }
            $('modalHapusNama').textContent = '"' + (r ? r.nama : '') + '"';
            pesanError.hidden = true;
            modal.showModal();                                      // fokus awal di tombol Batal; Esc menutup modal
        }
    });

    $('modalHapusBatal').addEventListener('click', function () { modal.close(); });
    modal.addEventListener('click', function (e) { if (e.target === modal) modal.close(); });   // klik area gelap = batal
    modal.addEventListener('close', function () { idHapus = null; btnYa.disabled = false; btnYa.textContent = 'Hapus'; });
    btnYa.addEventListener('click', function () {
        if (!idHapus) return;
        if (!URL_DELETE) {                                          // route hapus belum dibuat di backend
            pesanError.textContent = 'Fitur hapus belum tersedia: route admin.dosen.destroy belum dibuat.';
            pesanError.hidden = false;
            return;
        }
        btnYa.disabled = true;                                      // cegah klik ganda
        btnYa.textContent = 'Menghapus...';
        kirimHapus(idHapus);
    });
})();
</script>
@endpush