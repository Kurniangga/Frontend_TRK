{{--
    resources/views/kaprodi/kegiatan-mahasiswa.blade.php
    Daftar kegiatan mahasiswa (kaprodi): HANYA yang sudah disetujui. Aksi: lihat (link ke detail) & hapus. TANPA tambah / ubah.
    Laporan PDF/Excel per tahun tetap ada.
--}}
@extends('layouts.kaprodi')

@section('title', 'Kegiatan Mahasiswa')

@php
    $tabs = ['Semua', 'Diajukan', 'Direvisi', 'Disetujui', 'Ditolak'];
    $selectCls = 'h-10 w-full appearance-none rounded-lg border border-[#E8E8EC] bg-white px-4 pr-10 text-base text-[#2E2E2E] outline-none focus:border-[#2277FF]';
    $chev = '<svg class="pointer-events-none absolute right-4 top-1/2 -translate-y-1/2 text-[#1C2024]" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 9l6 6l6 -6" /></svg>';
@endphp

@section('content')
<main class="flex flex-col gap-10 px-4 py-8 sm:px-6">
    {{-- Header --}}
    <div class="flex flex-col gap-2">
        <h1 class="text-[32px] font-bold leading-[38px] tracking-[-0.2px]">Kegiatan Mahasiswa</h1>
        <x-admin.breadcrumb :items="[['Dashboard', 'kaprodi.dashboard'], ['Kegiatan'], ['Kegiatan Mahasiswa']]" />
    </div>

    @if (session('success'))
        <div class="rounded-md border border-[#D6EDB5] bg-[#E6F3D3] px-4 py-3 text-sm text-[#4C7D08]" role="status">{{ session('success') }}</div>
    @endif

    <div class="flex flex-col gap-8 rounded-lg bg-white p-4 pb-6 shadow-sm">

        {{-- Laporan data kegiatan: unduh PDF / Excel per kurun waktu (tahun). Diproses KegiatanLaporanController. --}}
        <form method="GET" action="{{ route('kaprodi.kegiatan.mahasiswa.laporan') }}" class="flex flex-col gap-4 rounded-md border border-[#E8E8EC] bg-[#F9F9FB] p-4 lg:flex-row lg:items-end">
            <div class="lg:mr-auto">
                <h2 class="text-sm font-semibold text-[#0A0A0A]">Laporan Data Kegiatan Mahasiswa</h2>
                <p class="mt-0.5 text-xs text-[#6B6B72]">Unduh rekap kegiatan berdasarkan kurun waktu (tahun).</p>
            </div>
            <label class="flex w-full flex-col gap-1.5 lg:w-36">
                <span class="text-xs font-medium text-[#0A0A0A]">Dari tahun</span>
                <div class="relative">
                    <select name="dari" class="{{ $selectCls }}">
                        @foreach ($tahunList as $t)
                            <option value="{{ $t }}" @selected($loop->last)>{{ $t }}</option>
                        @endforeach
                    </select>
                    {!! $chev !!}
                </div>
            </label>
            <label class="flex w-full flex-col gap-1.5 lg:w-36">
                <span class="text-xs font-medium text-[#0A0A0A]">Sampai tahun</span>
                <div class="relative">
                    <select name="sampai" class="{{ $selectCls }}">
                        @foreach ($tahunList as $t)
                            <option value="{{ $t }}" @selected($loop->last)>{{ $t }}</option>
                        @endforeach
                    </select>
                    {!! $chev !!}
                </div>
            </label>
            <div class="flex gap-2">
                <button type="submit" name="format" value="pdf" class="flex h-10 items-center gap-1.5 rounded-lg border border-[#E5484D] bg-white px-4 text-sm font-medium text-[#E5484D] hover:bg-[#FFF1F1]">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M12 4v11" /><path d="M8 11l4 4l4 -4" /><path d="M5 20h14" /></svg>
                    PDF
                </button>
                <button type="submit" name="format" value="xlsx" class="flex h-10 items-center gap-1.5 rounded-lg border border-[#17803D] bg-white px-4 text-sm font-medium text-[#17803D] hover:bg-[#F0FAF3]">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M12 4v11" /><path d="M8 11l4 4l4 -4" /><path d="M5 20h14" /></svg>
                    Excel
                </button>
            </div>
        </form>

        {{-- Kaprodi hanya melihat kegiatan yang sudah disetujui (sudah / sedang berjalan) --}}
        <div class="flex flex-wrap items-center gap-2 border-b border-[#0A0A0A]/[0.06] pb-3">
            <span class="rounded-lg border border-[#D6EDB5] bg-[#E6F3D3] px-2 py-0.5 text-xs font-medium text-[#4C7D08]">Disetujui</span>
            <span class="text-xs text-[#0A0A0A]/60">Menampilkan kegiatan yang sudah disetujui ({{ count($rows) }} kegiatan).</span>
        </div>

        {{-- Filter --}}
        <div class="flex flex-col gap-4 lg:flex-row lg:flex-wrap lg:items-center lg:gap-6">
            <div class="flex flex-1 items-center gap-2 rounded-lg border border-[#E8E8EC] bg-white px-3 py-1.5 focus-within:border-[#2277FF] lg:min-w-[260px]">
                <svg class="size-6 text-[#A8A8A8]" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="10" cy="10" r="7"/><path d="M21 21l-6-6"/></svg>
                <input id="q" type="text" placeholder="Cari nama kegiatan..." class="min-w-0 flex-1 bg-transparent text-base tracking-[-0.18px] outline-none placeholder:text-[#A8A8A8]">
            </div>
            <div class="relative w-full lg:w-[187px]">
                <select id="fTahun" class="{{ $selectCls }}">
                    <option value="">Semua Tahun</option>
                    @foreach (array_reverse($tahunList) as $t)
                        <option value="{{ $t }}">{{ $t }}</option>
                    @endforeach
                </select>
                {!! $chev !!}
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
                <select id="fTingkat" class="{{ $selectCls }}">
                    <option value="">Semua Tingkat</option>
                    <option value="1">Tingkat 1</option>
                    <option value="2">Tingkat 2</option>
                    <option value="3">Tingkat 3</option>
                </select>
                {!! $chev !!}
            </div>
            <div class="relative w-full lg:w-[187px]">
                <select id="fKelas" class="{{ $selectCls }}">
                    <option value="">Semua Kelas</option>
                    <option value="A">A</option>
                    <option value="B">B</option>
                </select>
                {!! $chev !!}
            </div>
        </div>

        {{-- Tabel --}}
        <div class="overflow-x-auto rounded-md border border-[#00002F]/15 bg-white/80">
            <table class="w-full min-w-[960px] table-fixed border-collapse text-left text-sm text-[#1C2024]">
                <thead>
                    <tr class="bg-[#000055]/[0.02] font-[510]">
                        @foreach (['Nama Kegiatan', 'Kategori', 'Pengaju', 'Tanggal', 'Status', 'Aksi'] as $h)
                            <th class="min-h-9 p-2 font-[510] leading-5">{{ $h }}</th>
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
<script>
(function () {
    var data = @json($rows);
    var PER_PAGE = 10;
    var tab = 'Semua', q = '', fSemester = '', fTahun = '', fTingkat = '', fKelas = '', page = 1;

    var STATUS = {
        'Diajukan':  'border-[#AAC8F8] bg-[#CDDEF9] text-[#2277FF]',
        'Direvisi':  'border-[#FAD4A8] bg-[#FBE5CB] text-[#854700]',
        'Disetujui': 'border-[#D6EDB5] bg-[#E6F3D3] text-[#4C7D08]',
        'Ditolak':   'border-[#FAA8A8] bg-[#FBCBCB] text-[#FF3F3F]'
    };

    var $ = function (id) { return document.getElementById(id); };
    var tbody = $('tbody'), pager = $('pager');
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
    var I_TRASH = svg('<path d="M4 7l16 0" /><path d="M10 11l0 6" /><path d="M14 11l0 6" /><path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" /><path d="M9 7v-3a1 1 0 0 1 1 -1h6a1 1 0 0 1 1 1v3" />', 20, 1.5);
    var I_LEFT  = svg('<path d="M15 6l-6 6l6 6"/>', 20, 2);
    var I_RIGHT = svg('<path d="M9 6l6 6l-6 6"/>', 20, 2);

    /* ---------- data terfilter ---------- */
    function filtered() {
        var s = q.toLowerCase();
        return data.filter(function (r) {
            return (tab === 'Semua' || r.status === tab) &&
                   r.nama.toLowerCase().indexOf(s) !== -1 &&
                   (!fTahun || String(r.tahun) === fTahun) &&
                   (!fSemester || r.semester === fSemester) &&
                   (!fTingkat || r.tingkat === fTingkat) &&
                   (!fKelas || r.kelas === fKelas);
        });
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
                    '<a href="' + URL_VIEW.replace('__ID__', encodeURIComponent(r.id)) + '" title="Lihat" aria-label="Lihat" class="rounded-md bg-[#CDDEF9] p-1.5 text-[#2277FF] hover:bg-[#BBD2F6] cursor-pointer">' + I_EYE + '</a>' +
                    '<button type="button" title="Hapus" aria-label="Hapus" data-act="delete" data-id="' + id + '" class="rounded-md bg-[#FFD9D9] p-1.5 text-[#E5484D] hover:bg-[#FFC9C9] cursor-pointer">' + I_TRASH + '</button>' +
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
            tab = t.dataset.tab; page = 1;
            tabs.forEach(function (x) { x.setAttribute('aria-selected', x === t ? 'true' : 'false'); });
            render();
        });
    });
    $('q').addEventListener('input', function (e) { q = e.target.value.trim(); page = 1; render(); });
    $('fSemester').addEventListener('change', function (e) { fSemester = e.target.value; page = 1; render(); });
    $('fTahun').addEventListener('change', function (e) { fTahun = e.target.value; page = 1; render(); });
    $('fTingkat').addEventListener('change', function (e) { fTingkat = e.target.value; page = 1; render(); });
    $('fKelas').addEventListener('change', function (e) { fKelas = e.target.value; page = 1; render(); });

    pager.addEventListener('click', function (e) {
        var b = e.target.closest('button[data-p]'); if (!b || b.disabled) return;
        page = parseInt(b.dataset.p, 10); render();
    });

    /* Aksi: lihat -> halaman detail (link <a>); hapus -> konfirmasi lalu DELETE lewat form + CSRF. */
    var URL_VIEW = @json(route('kaprodi.kegiatan.mahasiswa.show', ['id' => '__ID__']));
    var URL_DELETE = @json(route('kaprodi.kegiatan.mahasiswa.destroy', ['id' => '__ID__']));
    var CSRF = @json(csrf_token());

    tbody.addEventListener('click', function (e) {
        var b = e.target.closest('button[data-act="delete"]'); if (!b) return;
        var r = data.filter(function (x) { return String(x.id) === b.dataset.id; })[0];
        if (!confirm('Hapus kegiatan "' + (r ? r.nama : '') + '"? Tindakan ini tidak dapat dibatalkan.')) return;
        var f = document.createElement('form');
        f.method = 'POST';
        f.action = URL_DELETE.replace('__ID__', encodeURIComponent(b.dataset.id));
        f.innerHTML = '<input type="hidden" name="_token" value="' + CSRF + '"><input type="hidden" name="_method" value="DELETE">';
        document.body.appendChild(f);
        f.submit();
    });

    render();
})();
</script>
@endpush
