{{--
    resources/views/kaprodi/kurikulum.blade.php
    Kurikulum TRK (kaprodi) - HANYA BACA. Tidak ada tombol tambah, ubah status, edit, maupun hapus.
    Dokumen dibaca lewat PDF (tombol "Lihat PDF" membuka di tab baru). Data dari KaprodiKurikulumController.
--}}
@extends('layouts.kaprodi')

@section('title', 'Kurikulum TRK')

@section('content')
<main class="p-4 pb-10 sm:p-6">
    <h1 class="mt-[6px] text-[32px] font-bold leading-10 tracking-[-0.3px]">Kurikulum TRK</h1>
    <x-admin.breadcrumb :items="[['Dashboard', 'kaprodi.dashboard'], ['Kurikulum'], ['Kurikulum TRK']]" />

    <section class="mt-6 rounded-lg bg-white p-6 shadow-[0_1px_4px_rgba(0,0,0,0.06)]">
        <div>
            <h2 class="text-lg font-medium">Daftar Kurikulum</h2>
            <p class="text-sm text-[#8A8A90]">Kurikulum yang digunakan oleh program studi</p>
        </div>

        {{-- Pencarian + filter status --}}
        <div class="mt-5 flex flex-wrap gap-4">
            <div class="relative min-w-[220px] flex-1">
                <svg class="absolute left-3.5 top-2.5 text-[#8A8A90]" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M10 10m-7 0a7 7 0 1 0 14 0a7 7 0 1 0 -14 0" /><path d="M21 21l-6 -6" /></svg>
                <input id="q" placeholder="Cari nama kurikulum..." class="h-10 w-full rounded-md border border-[#E4E4E7] bg-white pl-[45px] pr-3 text-base outline-none placeholder:text-[#B3B3B8] focus:border-[#1F73FF]">
            </div>
            <div class="relative w-full sm:w-[220px]">
                <select id="statusFilter" class="h-10 w-full appearance-none rounded-md border border-[#D4D4D8] bg-white px-[17px] text-sm sm:text-base outline-none focus:border-[#1F73FF]">
                    <option value="all">Semua status</option>
                    <option value="aktif">Aktif</option>
                    <option value="nonaktif">Nonaktif</option>
                </select>
                <svg class="pointer-events-none absolute right-3 top-[11px]" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M6 9l6 6l6 -6" /></svg>
            </div>
        </div>

        {{-- Tabel --}}
        <div class="mt-5 overflow-x-auto rounded-md border border-[#E4E4E7]">
            <table class="w-full min-w-[720px] border-collapse text-sm">
                <thead>
                    <tr class="h-11 bg-[#F2F2F7] text-left font-medium text-[#222]">
                        <th class="px-4 font-medium">Nama Kurikulum</th>
                        <th class="px-4 font-medium">Tahun Terbit</th>
                        <th class="px-4 font-medium">Status</th>
                        <th class="px-4 font-medium">Dokumen</th>
                    </tr>
                </thead>
                <tbody id="tbody"></tbody>
            </table>
        </div>

        <div class="mt-5">
            <span id="info" class="text-sm text-[#55555C]"></span>
        </div>
    </section>
</main>
@endsection

@push('scripts')
<script>
(function () {
    var data = @json($kurikulum);
    var q = '', statusFilter = 'all';
    var tbody = document.getElementById('tbody'), info = document.getElementById('info');

    function esc(s) {
        return String(s == null ? '' : s).replace(/[&<>"']/g, function (c) {
            return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c];
        });
    }
    var I_FILE = '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.33" stroke-linecap="round" stroke-linejoin="round"><path d="M14 3v4a1 1 0 0 0 1 1h4" /><path d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z" /><path d="M9 9l1 0" /><path d="M9 13l6 0" /><path d="M9 17l6 0" /></svg>';

    function render() {
        var list = data.filter(function (r) {
            return r.name.toLowerCase().indexOf(q.toLowerCase()) !== -1 &&
                   (statusFilter === 'all' || r.status === statusFilter);
        });

        tbody.innerHTML = list.length ? list.map(function (r) {
            var active = r.status === 'aktif';
            var pdf = r.pdf_url
                ? '<a href="' + esc(r.pdf_url) + '" target="_blank" rel="noopener" class="inline-flex h-8 items-center gap-1.5 rounded-md border border-[#1F73FF] bg-white px-3 text-sm font-medium text-[#1F73FF] hover:bg-[#F3F7FF]">' + I_FILE + 'Lihat PDF</a>'
                : '<span class="inline-flex h-8 items-center text-xs text-[#8A8A90]">Belum ada dokumen</span>';
            return '<tr class="h-[65px] border-t border-[#E4E4E7]">' +
                '<td class="px-4"><div>' + esc(r.name) + '</div><div class="text-xs text-[#8A8A90]">' + esc(r.description) + '</div></td>' +
                '<td class="px-4">' + esc(r.year) + '</td>' +
                '<td class="px-4"><span class="inline-flex h-[26px] items-center gap-1.5 rounded-full px-3 text-[13px] font-medium ' +
                    (active ? 'bg-[#E3F6E8] text-[#17803D]' : 'bg-[#EDEDF0] text-[#55555C]') + '">' +
                    '<span class="size-1.5 rounded-full ' + (active ? 'bg-[#17803D]' : 'bg-[#8A8A90]') + '"></span>' +
                    (active ? 'Aktif' : 'Nonaktif') + '</span></td>' +
                '<td class="px-4">' + pdf + '</td>' +
                '</tr>';
        }).join('') :
            '<tr class="h-[65px] border-t border-[#E4E4E7]"><td colspan="4" class="px-4 text-center text-[#8A8A90]">Tidak ada kurikulum.</td></tr>';

        info.textContent = 'Menampilkan ' + (list.length ? 1 : 0) + '–' + list.length + ' dari ' + data.length + ' kurikulum';
    }

    document.getElementById('q').addEventListener('input', function (e) { q = e.target.value; render(); });
    document.getElementById('statusFilter').addEventListener('change', function (e) { statusFilter = e.target.value; render(); });

    render();
})();
</script>
@endpush
