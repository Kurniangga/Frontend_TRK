{{--
    resources/views/admin/pengajuan.blade.php
    Daftar pengajuan kegiatan: tab status, cari, filter semester/jenis pengaju, paginasi.
--}}
@extends('layouts.admin')

@section('title', 'Pengajuan Kegiatan')

@php
    $tabs = ['Semua', 'Diajukan', 'Direvisi', 'Ditolak'];
    $selectCls = 'h-10 w-full appearance-none rounded-lg border border-[#E8E8EC] bg-white px-4 pr-10 text-base text-[#2E2E2E] outline-none focus:border-[#2277FF]';
    $chev = '<svg class="pointer-events-none absolute right-4 top-1/2 -translate-y-1/2 text-[#1C2024]" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 9l6 6l6 -6" /></svg>';
@endphp

@section('content')
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
                        class="group relative flex h-8 shrink-0 items-center gap-2 px-1 text-xs text-[#0A0A0A]/30 transition-colors hover:text-[#0A0A0A]/60 aria-selected:font-medium aria-selected:text-[#0A0A0A]">
                    <span>{{ $t }}</span>
                    @if ($loop->first)
                        <span id="totalBadge" class="rounded-lg bg-[#2277FF] px-2 py-0.5 font-medium text-[#FEFEFE]">{{ count($rows) }}</span>
                    @endif
                    <span class="absolute inset-x-0 bottom-0 hidden h-0.5 bg-[#2277FF] group-aria-selected:block"></span>
                </button>
            @endforeach
        </div>

        {{-- Filter --}}
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

        {{-- Tabel --}}
        <div class="overflow-x-auto rounded-md border border-[#00002F]/15 bg-white/80">
            <table class="w-full table-fixed border-collapse text-left text-sm text-[#1C2024]">
                <thead>
                    <tr class="bg-[#000055]/[0.02] font-[510]">
                        {{-- data-sort = nama field di data; Aksi tidak bisa diurutkan --}}
                        @foreach ([['Nama Kegiatan', 'nama'], ['Kategori', 'kategori'], ['Pengaju', 'pengaju'], ['Tanggal', 'tanggal'], ['Status', 'status'], ['Aksi', null]] as [$h, $k])
                            <th @if ($k) data-sort="{{ $k }}" @endif @if ($k === 'tanggal') data-tipe="tanggal" @endif class="min-h-9 p-2 font-[510] leading-5">{{ $h }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody id="tbody"></tbody>
            </table>
        </div>

        {{-- Paginasi --}}
        <nav id="pager" class="flex items-center justify-center gap-2 p-2.5" aria-label="Halaman"></nav>
    </div>
</main>
@endsection

@push('scripts')
<script src="{{ asset('js/tabel-urut.js') }}"></script>
<script>
(function () {
    var data = @json($rows);
    var PER_PAGE = 10;
    
    // Variabel fTingkat dan fKelas dihapus, diganti dengan fJenis
    var tab = 'Semua', q = '', fSemester = '', fJenis = '', page = 1;

    var STATUS = {
        'Diajukan':  'border-[#AAC8F8] bg-[#CDDEF9] text-[#2277FF]',
        'Direvisi':  'border-[#FAD4A8] bg-[#FBE5CB] text-[#854700]',
        'Disetujui': 'border-[#D6EDB5] bg-[#E6F3D3] text-[#4C7D08]',
        'Ditolak':   'border-[#FAA8A8] bg-[#FBCBCB] text-[#FF3F3F]'
    };

    var $ = function (id) { return document.getElementById(id); };
    var tbody = $('tbody'), pager = $('pager');

    /* Sorting kolom (klik header: naik -> turun -> urutan awal). Kolom yang bisa diurutkan = <th data-sort>. Logika: public/js/tabel-urut.js */
    var urut = TabelUrut.pasang(document.querySelector('table'), {
        onUbah: function () { page = 1; render(); }
    });
    var tabs = Array.prototype.slice.call(document.querySelectorAll('[data-tab]'));

    function esc(s) {
        return String(s == null ? '' : s).replace(/[&<>"']/g, function (c) {
            return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c];
        });
    }
    function svg(paths, w, sw) {
        return '<svg width="' + w + '" height="' + w + '" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="' + sw + '" stroke-linecap="round" stroke-linejoin="round">' + paths + '</svg>';
    }
    var I_EYE   = svg('<circle cx="12" cy="12" r="2"/><path d="M22 12c-2.667 4.667-6 7-10 7s-7.333-2.333-10-7c2.667-4.667 6-7 10-7s7.333 2.333 10 7"/>', 20, 1.5);
    var I_LEFT  = svg('<path d="M15 6l-6 6l6 6"/>', 20, 2);
    var I_RIGHT = svg('<path d="M9 6l6 6l-6 6"/>', 20, 2);

    /* ---------- data terfilter ---------- */
    function filtered() {
        var s = q.toLowerCase();
        return urut.urutkan(data.filter(function (r) {
            return (tab === 'Semua' || r.status === tab) &&
                   r.nama.toLowerCase().indexOf(s) !== -1 &&
                   (!fSemester || r.semester === fSemester) &&
                   (!fJenis || r.jenis_pengaju === fJenis); // Pengecekan jenis pengaju
        }));
    }

    /* ---------- tabel ---------- */
    function renderTable(list) {
        var rows = list.slice((page - 1) * PER_PAGE, page * PER_PAGE);
        tbody.innerHTML = rows.length ? rows.map(function (r) {
            var id = esc(r.id);
            return '<tr class="border-t border-[#00002F]/15">' +
                '<td class="truncate p-2">' + esc(r.nama) + '</td>' +
                '<td class="truncate p-2">' + esc(r.kategori) + '</td>' +
                '<td class="truncate p-2">' + esc(r.pengaju) + '</td>' +
                '<td class="truncate p-2">' + esc(r.tanggal) + '</td>' +
                '<td class="p-2"><span class="inline-flex rounded-lg border px-2 py-0.5 text-xs font-medium ' + (STATUS[r.status] || '') + '">' + esc(r.status) + '</span></td>' +
                '<td class="p-2"><div class="flex items-center gap-3">' +
                    '<button type="button" title="Tinjau" aria-label="Tinjau" data-act="view" data-id="' + id + '" class="rounded-md bg-[#CDDEF9] p-1.5 text-[#2277FF] hover:bg-[#BBD2F6]">' + I_EYE + '</button>' +
                '</div></td></tr>';
        }).join('') :
            '<tr class="border-t border-[#00002F]/15"><td colspan="6" class="p-6 text-center text-sm text-[#A8A8A8]">Tidak ada kegiatan.</td></tr>';
    }

    /* ---------- paginasi: 1 2 ... 9 10 ---------- */
    function renderPager(total) {
        var pages = [];
        [1, 2, total - 1, total, page - 1, page, page + 1].forEach(function (p) {
            if (p >= 1 && p <= total && pages.indexOf(p) === -1) pages.push(p);
        });
        pages.sort(function (a, b) { return a - b; });

        var out = '', last = 0;
        out += '<button type="button" data-p="' + (page - 1) + '" aria-label="Sebelumnya" ' + (page <= 1 ? 'disabled' : '') +
               ' class="flex size-8 items-center justify-center rounded bg-[#2277FF] text-white disabled:cursor-not-allowed disabled:opacity-60">' + I_LEFT + '</button>';
        pages.forEach(function (p) {
            if (last && p - last > 1) out += '<span class="flex size-8 items-center justify-center text-sm text-[#55555C]">…</span>';
            out += '<button type="button" data-p="' + p + '" class="flex size-8 items-center justify-center rounded border bg-white text-sm font-bold tracking-[-0.16px] ' +
                   (p === page ? 'border-[#2277FF] text-[#2277FF]' : 'border-[#DFE3E8] text-[#0A0A0A] hover:border-[#2277FF]') + '">' + p + '</button>';
            last = p;
        });
        out += '<button type="button" data-p="' + (page + 1) + '" aria-label="Berikutnya" ' + (page >= total ? 'disabled' : '') +
               ' class="flex size-8 items-center justify-center rounded border border-[#2277FF] bg-[#CDDEF9] text-[#2277FF] disabled:cursor-not-allowed disabled:opacity-60">' + I_RIGHT + '</button>';
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
    tabs.forEach(function (t) {
        t.addEventListener('click', function () {
            tab = t.dataset.tab; page = 1; urut.reset();
            tabs.forEach(function (x) { x.setAttribute('aria-selected', x === t ? 'true' : 'false'); });
            render();
        });
    });
    $('q').addEventListener('input', function (e) { q = e.target.value.trim(); page = 1; urut.reset(); render(); });
    $('fSemester').addEventListener('change', function (e) { fSemester = e.target.value; page = 1; urut.reset(); render(); });
    $('fJenis').addEventListener('change', function (e) { fJenis = e.target.value; page = 1; urut.reset(); render(); });

    pager.addEventListener('click', function (e) {
        var b = e.target.closest('button[data-p]'); if (!b || b.disabled) return;
        page = parseInt(b.dataset.p, 10); render();
    });

    /* ---------- Aksi Ikon ---------- */
    tbody.addEventListener('click', function(e) {
        var btn = e.target.closest('button[data-act]');
        if (!btn) return;
        
        var act = btn.dataset.act;
        var id = btn.dataset.id;
        
        if (act === 'view') {
            // Arahkan ke URL detail berdasarkan ID baris yang diklik
            window.location.href = '/admin/pengajuan/' + id;
        }
    });

    render();
})();
</script>
@endpush