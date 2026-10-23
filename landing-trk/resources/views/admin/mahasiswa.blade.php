{{--
    resources/views/admin/mahasiswa.blade.php
    Daftar mahasiswa: cari, filter tingkat & status, paginasi (sudah berfungsi).
    Ikon mata membuka halaman detail. Tombol Tambah Mahasiswa dan ikon hapus baru tampilan
    (belum ada fungsinya). Tombol punya atribut data-act ("view" | "delete") dan data-id.
--}}
@extends('layouts.admin')

@section('title', 'Mahasiswa')

@section('content')
<main class="flex flex-col gap-10 bg-white px-4 py-8 sm:px-6">
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div class="flex flex-col gap-2">
            <h1 class="text-[32px] font-bold leading-[38px] tracking-[-0.2px]">Mahasiswa</h1>
            <x-admin.breadcrumb :items="[['Dashboard', 'admin.dashboard'], ['Data Mahasiswa']]" />
        </div>
        <button id="btnAdd" type="button" class="flex items-center gap-1 rounded-md bg-[#2277FF] px-4 py-2 text-lg font-medium tracking-[-0.18px] text-[#FEFEFE] shadow-[0_1px_2px_rgba(0,0,0,0.12)]">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 5l0 14" /><path d="M5 12l14 0" /></svg>
            Tambah Mahasiswa
        </button>
    </div>

    <section class="flex flex-col gap-8 rounded-lg bg-white px-4 pb-6 pt-4 shadow-[1px_0_3px_rgba(0,0,0,0.02),5px_2px_6px_rgba(0,0,0,0.02)]">

        {{-- Pencarian + filter --}}
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:gap-6">
            <div class="flex flex-1 items-center gap-2 rounded-lg border border-[#E8E8EC] bg-white px-3 py-1.5 focus-within:border-[#2277FF]">
                <svg class="text-[#A8A8A8]" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M10 10m-7 0a7 7 0 1 0 14 0a7 7 0 1 0 -14 0" /><path d="M21 21l-6 -6" /></svg>
                <input id="q" placeholder="Cari nama atau NIM..." class="min-w-0 flex-1 bg-transparent text-base tracking-[-0.18px] outline-none placeholder:text-[#A8A8A8]">
            </div>
            <div class="relative w-full sm:w-[187px]">
                <select id="fLevel" class="h-10 w-full appearance-none rounded-lg border border-[#E8E8EC] bg-white px-4 pr-10 text-base text-[#2E2E2E] shadow-[inset_0_0_0_1px_rgba(0,0,45,0.09)] outline-none focus:ring-2 focus:ring-[#2277FF]/40">
                    <option value="all">Semua Tingkat</option>
                    @foreach ($levels as $l)
                        <option value="{{ $l }}">{{ $l }}</option>
                    @endforeach
                </select>
                <svg class="pointer-events-none absolute right-4 top-1/2 -translate-y-1/2 text-[#1C2024]" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 9l6 6l6 -6" /></svg>
            </div>
            <div class="relative w-full sm:w-[187px]">
                <select id="fStatus" class="h-10 w-full appearance-none rounded-lg border border-[#E8E8EC] bg-white px-4 pr-10 text-base text-[#2E2E2E] shadow-[inset_0_0_0_1px_rgba(0,0,45,0.09)] outline-none focus:ring-2 focus:ring-[#2277FF]/40">
                    <option value="all">Semua Status</option>
                    <option value="Aktif">Aktif</option>
                    <option value="Cuti">Cuti</option>
                    <option value="Lulus">Lulus</option>
                    <option value="Non-aktif">Non-aktif</option>
                </select>
                <svg class="pointer-events-none absolute right-4 top-1/2 -translate-y-1/2 text-[#1C2024]" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 9l6 6l6 -6" /></svg>
            </div>
        </div>

        {{-- Tabel --}}
        <div class="overflow-x-auto border border-[#0A0A0A]/[0.06]">
            <table class="w-full min-w-[960px] border-collapse text-left text-sm text-[#1C2024]">
                <thead>
                    <tr class="bg-[rgba(0,0,85,0.02)]">
                        @foreach (['NIM', 'Nama', 'Tingkat', 'Kelas', 'Status', 'Aksi'] as $h)
                            <th class="min-h-9 w-1/6 min-w-40 p-2 text-sm font-medium">{{ $h }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody id="tbody"></tbody>
            </table>
        </div>

        {{-- Paginasi --}}
        <nav id="pager" class="flex items-center justify-center gap-2 p-2.5" aria-label="Halaman"></nav>
    </section>
</main>
@endsection

@push('scripts')
<script>
(function () {
    var data = @json($students);
    var PAGE_SIZE = 5;
    var q = '', fLevel = 'all', fStatus = 'all', page = 1;
    var URL_DETAIL = @js(route('admin.mahasiswa.show', ['id' => '__ID__']));

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
    var I_EYE   = svg(20, 1.5, '<path d="M10 12a2 2 0 1 0 4 0a2 2 0 0 0 -4 0" /><path d="M21 12c-2.4 4 -5.4 6 -9 6c-3.6 0 -6.6 -2 -9 -6c2.4 -4 5.4 -6 9 -6c3.6 0 6.6 2 9 6" />');
    var I_TRASH = svg(20, 1.5, '<path d="M4 7l16 0" /><path d="M10 11l0 6" /><path d="M14 11l0 6" /><path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" /><path d="M9 7v-3a1 1 0 0 1 1 -1h6a1 1 0 0 1 1 1v3" />');
    var I_LEFT  = svg(20, 2, '<path d="M15 6l-6 6l6 6" />');
    var I_RIGHT = svg(20, 2, '<path d="M9 6l6 6l-6 6" />');

    /* ---------- data terfilter ---------- */
    function filtered() {
        var s = q.toLowerCase();
        return data.filter(function (r) {
            return (!s || r.name.toLowerCase().indexOf(s) !== -1 || r.nim.indexOf(q) !== -1) &&
                   (fLevel === 'all' || r.level === fLevel) &&
                   (fStatus === 'all' || r.status === fStatus);
        });
    }

    /* ---------- tabel ---------- */
    function renderTable(list) {
        var rows = list.slice((page - 1) * PAGE_SIZE, page * PAGE_SIZE);
        tbody.innerHTML = rows.length ? rows.map(function (r) {
            var on = r.status === 'Aktif', id = esc(r.id);
            return '<tr class="border-t border-[rgba(0,0,47,0.15)]">' +
                '<td class="p-2">' + esc(r.nim) + '</td>' +
                '<td class="p-2">' + esc(r.name) + '</td>' +
                '<td class="p-2">' + esc(r.level) + '</td>' +
                '<td class="p-2">' + esc(r.className) + '</td>' +
                '<td class="p-2"><span class="inline-flex rounded-lg border px-2 py-0.5 text-xs font-medium leading-4 ' +
                    (on ? 'border-[#D6EDB5] bg-[#E6F3D3] text-[#4C7D08]' : (r.status === 'Cuti' ? 'border-[#FAD4A8] bg-[#FBE5CB] text-[#854700]' : 'border-[#E4E4E7] bg-[#EDEDF0] text-[#55555C]')) + '">' + esc(r.status) + '</span></td>' +
                '<td class="p-2"><div class="flex items-center gap-3">' +
                    '<button type="button" title="Lihat detail" aria-label="Lihat detail" data-act="view" data-id="' + id + '" class="rounded-md bg-[#CDDEF9] p-1.5 text-[#2277FF] hover:bg-[#BBD2F6]">' + I_EYE + '</button>' +
                    '<button type="button" title="Hapus" aria-label="Hapus" data-act="delete" data-id="' + id + '" class="rounded-md bg-[#FFD9D9] p-1.5 text-[#E5484D] hover:bg-[#FFC9C9]">' + I_TRASH + '</button>' +
                '</div></td></tr>';
        }).join('') :
            '<tr class="border-t border-[rgba(0,0,47,0.15)]"><td colspan="6" class="p-6 text-center text-[#A8A8A8]">Tidak ada data</td></tr>';
    }

    /* ---------- paginasi ---------- */
    function renderPager(total) {
        var out = '<button type="button" data-p="' + (page - 1) + '" aria-label="Sebelumnya" ' + (page <= 1 ? 'disabled' : '') +
                  ' class="flex size-8 items-center justify-center rounded bg-[#2277FF] text-white disabled:cursor-not-allowed">' + I_LEFT + '</button>';
        for (var n = 1; n <= total; n++) {
            out += '<button type="button" data-p="' + n + '" class="flex size-8 items-center justify-center rounded border bg-white text-sm font-bold tracking-[-0.16px] ' +
                   (n === page ? 'border-[#2277FF] text-[#2277FF]' : 'border-[#DFE3E8] text-[#0A0A0A] hover:border-[#2277FF]') + '">' + n + '</button>';
        }
        out += '<button type="button" data-p="' + (page + 1) + '" aria-label="Berikutnya" ' + (page >= total ? 'disabled' : '') +
               ' class="flex size-8 items-center justify-center rounded border border-[#2277FF] bg-[#CDDEF9] text-[#2277FF] disabled:cursor-not-allowed">' + I_RIGHT + '</button>';
        pager.innerHTML = out;
    }

    function render() {
        var list = filtered();
        var total = Math.max(1, Math.ceil(list.length / PAGE_SIZE));
        if (page > total) page = total;
        renderTable(list);
        renderPager(total);
    }

    /* ---------- event ---------- */
    $('q').addEventListener('input', function (e) { q = e.target.value.trim(); page = 1; render(); });
    $('fLevel').addEventListener('change', function (e) { fLevel = e.target.value; page = 1; render(); });
    $('fStatus').addEventListener('change', function (e) { fStatus = e.target.value; page = 1; render(); });

    pager.addEventListener('click', function (e) {
        var b = e.target.closest('button[data-p]'); if (!b || b.disabled) return;
        page = parseInt(b.dataset.p, 10); render();
    });

    tbody.addEventListener('click', function (e) {
        var b = e.target.closest('button[data-act]'); if (!b) return;
        if (b.dataset.act === 'view') {
            window.location.href = URL_DETAIL.replace('__ID__', encodeURIComponent(b.dataset.id));
        }
        /* TODO (tahap berikutnya):
             - #btnAdd                   -> halaman/pop-up Tambah Mahasiswa
             - button[data-act="delete"] -> konfirmasi + hapus */
    });

    render();
})();
</script>
@endpush