{{--
    resources/views/admin/dosen.blade.php
    Daftar dosen: cari, filter pangkat & status, paginasi (sudah berfungsi).
    Tombol Tambah Dosen dan aksi (lihat, ubah, hapus) baru tampilan — belum ada fungsinya.
    Titik sambung nanti: tombol punya atribut data-act ("view" | "edit" | "delete") dan data-id.
--}}
@extends('layouts.admin')

@section('title', 'Data Dosen')

@section('content')
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
@endsection

@push('scripts')
<script>
(function () {
    var data = @json($dosen);
    var PER_PAGE = 10;
    var q = '', fPangkat = 'all', fStatus = 'all', page = 1;

    var $ = function (id) { return document.getElementById(id); };
    var tbody = $('tbody'), pager = $('pager');

    function esc(s) {
        return String(s).replace(/[&<>"']/g, function (c) {
            return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c];
        });
    }
    function svg(w, sw, paths) {
        return '<svg width="' + w + '" height="' + w + '" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="' + sw + '" stroke-linecap="round" stroke-linejoin="round">' + paths + '</svg>';
    }
    var I_EYE   = svg(16, 1.5, '<path d="M10 12a2 2 0 1 0 4 0a2 2 0 0 0 -4 0" /><path d="M21 12c-2.4 4 -5.4 6 -9 6c-3.6 0 -6.6 -2 -9 -6c2.4 -4 5.4 -6 9 -6c3.6 0 6.6 2 9 6" />');
    var I_EDIT  = svg(16, 1.5, '<path d="M4 20h4l10.5 -10.5a2.828 2.828 0 1 0 -4 -4l-10.5 10.5v4" /><path d="M13.5 6.5l4 4" />');
    var I_TRASH = svg(16, 1.5, '<path d="M4 7l16 0" /><path d="M10 11l0 6" /><path d="M14 11l0 6" /><path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" /><path d="M9 7v-3a1 1 0 0 1 1 -1h6a1 1 0 0 1 1 1v3" />');
    var I_LEFT  = svg(16, 2, '<path d="M15 6l-6 6l6 6" />');
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
            var on = r.status === 'aktif', id = esc(r.id);
            return '<tr class="h-12 border-t border-[#E4E4E7]">' +
                '<td class="px-3">' + esc(r.nip) + '</td>' +
                '<td class="px-3">' + esc(r.nama) + '</td>' +
                '<td class="px-3">' + esc(r.jabatan) + ', ' + esc(r.golongan) + '</td>' +
                '<td class="px-3"><span class="inline-flex items-center rounded px-2 py-0.5 text-[11px] font-medium ' +
                    (on ? 'bg-[#DDF3D0] text-[#3A8A1E]' : 'bg-[#EDEDF0] text-[#55555C]') + '">' + (on ? 'Aktif' : 'Nonaktif') + '</span></td>' +
                '<td class="px-3"><div class="flex gap-2">' +
                    '<button type="button" title="Lihat detail" aria-label="Lihat detail" data-act="view" data-id="' + id + '" class="flex size-8 items-center justify-center rounded-md bg-[#D4E3FF] text-[#1F73FF] hover:bg-[#C2D8FF]">' + I_EYE + '</button>' +
                    '<button type="button" title="Ubah" aria-label="Ubah" data-act="edit" data-id="' + id + '" class="flex size-8 items-center justify-center rounded-md bg-[#FDE7CE] text-[#F0A35E] hover:bg-[#FBDDB9]">' + I_EDIT + '</button>' +
                    '<button type="button" title="Hapus" aria-label="Hapus" data-act="delete" data-id="' + id + '" class="flex size-8 items-center justify-center rounded-md bg-[#FFD9D9] text-[#E5484D] hover:bg-[#FFC9C9]">' + I_TRASH + '</button>' +
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
               ' class="' + arrow + 'bg-[#1F73FF] text-white disabled:cursor-not-allowed disabled:bg-[#CFE0FF] disabled:text-[#1F73FF]">' + I_LEFT + '</button>';
        pages.forEach(function (p) {
            if (last && p - last > 1) out += '<span class="flex size-8 items-center justify-center text-[13px] text-[#55555C]">…</span>';
            out += '<button type="button" data-p="' + p + '" class="size-8 rounded-md border text-[13px] ' +
                   (p === page ? 'border-[#1F73FF] font-medium text-[#1F73FF]' : 'border-[#E4E4E7] bg-white text-[#222] hover:border-[#1F73FF]') + '">' + p + '</button>';
            last = p;
        });
        out += '<button type="button" data-p="' + (page + 1) + '" aria-label="Berikutnya" ' + (page >= total ? 'disabled' : '') +
               ' class="' + arrow + 'bg-[#1F73FF] text-white disabled:cursor-not-allowed disabled:bg-[#CFE0FF] disabled:text-[#1F73FF]">' + I_RIGHT + '</button>';
        pager.innerHTML = total > 1 ? out : '';
    }

    function render() {
        var list = filtered();
        var total = Math.max(1, Math.ceil(list.length / PER_PAGE));
        if (page > total) page = total;
        renderTable(list);
        renderPager(total);
    }

    /* ---------- event ---------- */
    $('q').addEventListener('input', function (e) { q = e.target.value.trim(); page = 1; render(); });
    $('fPangkat').addEventListener('change', function (e) { fPangkat = e.target.value; page = 1; render(); });
    $('fStatus').addEventListener('change', function (e) { fStatus = e.target.value; page = 1; render(); });

    pager.addEventListener('click', function (e) {
        var b = e.target.closest('button[data-p]'); if (!b || b.disabled) return;
        page = parseInt(b.dataset.p, 10); render();
    });

    var URL_DETAIL = @js(route('admin.dosen.show', ['id' => '__ID__']));

    tbody.addEventListener('click', function (e) {
        var b = e.target.closest('button[data-act]'); if (!b) return;
        if (b.dataset.act === 'view') {
            window.location.href = URL_DETAIL.replace('__ID__', encodeURIComponent(b.dataset.id));
        }
        /* TODO (tahap berikutnya):
             - #btnAdd                   -> halaman/pop-up Tambah Dosen
             - button[data-act="edit"]   -> halaman Ubah Dosen
             - button[data-act="delete"] -> konfirmasi + hapus */
    });

    render();
})();
</script>
@endpush