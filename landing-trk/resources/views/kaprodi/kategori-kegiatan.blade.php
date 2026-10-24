{{--
    resources/views/kaprodi/kategori-kegiatan.blade.php
    Versi kaprodi (BACA SAJA) dari admin Kategori Kegiatan. Data $rows: [id, name, jenis, description].
--}}
@extends('layouts.kaprodi')

@section('title', 'Kategori Kegiatan')

@section('content')
<div class="px-6 pb-8 pt-4 md:px-8">
    <h1 class="text-[32px] font-bold leading-[38px] tracking-[-0.2px] text-[#111]">Kategori Kegiatan</h1>

    <x-admin.breadcrumb :items="[
        ['Dashboard', 'kaprodi.dashboard'],
        ['Kegiatan', 'kaprodi.kegiatan.dosen'],
        ['Kategori Kegiatan'],
    ]" />

    <section class="mt-6 rounded-lg bg-white p-6 shadow-[0_1px_4px_rgba(0,0,0,0.06)]">
        <div>
            <h2 class="text-lg font-medium text-[#111]">Daftar Kategori Kegiatan</h2>
            <p class="text-sm text-[#8A8A90]">Kategori kegiatan yang tersedia untuk dosen dan mahasiswa</p>
        </div>

        {{-- Cari --}}
        <div class="relative mt-5">
            <svg class="absolute left-3.5 top-1/2 -translate-y-1/2 text-[#8A8A90]" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="10" cy="10" r="7" /><path d="M21 21l-6-6" /></svg>
            <input id="kk-cari" type="text" placeholder="Cari kategori kegiatan..."
                   class="h-10 w-full rounded-md border border-[#E4E4E7] bg-white pl-11 pr-3 text-base outline-none placeholder:text-[#B3B3B8] focus:border-[#2277FF]">
        </div>

        {{-- Tabel --}}
        <div class="mt-5 overflow-x-auto rounded-md border border-[#E4E4E7]">
            <table class="w-full min-w-[560px] text-left text-sm">
                <thead class="bg-[#F2F2F7] text-[#222]">
                    <tr class="h-11">
                        <th class="w-[90px] px-4 font-medium">No</th>
                        <th class="w-[289px] px-4 font-medium">Nama Kategori</th>
                        <th class="px-4 font-medium">Keterangan</th>
                    </tr>
                </thead>
                <tbody id="kk-body"></tbody>
            </table>
        </div>

        {{-- Info + paginasi --}}
        <div class="mt-5 flex flex-wrap items-center justify-between gap-3">
            <p id="kk-info" class="text-sm text-[#55555C]"></p>
            <div id="kk-pager" class="flex gap-2"></div>
        </div>
    </section>
</div>
@endsection

@push('scripts')
<script>
(() => {
    const PER = 10;
    const rows = @json($rows);
    let q = '';
    let page = 1;

    const $ = (id) => document.getElementById(id);
    const esc = (s) => String(s ?? '').replace(/[&<>"']/g, (c) =>
        ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]));

    const WARNA_JENIS = {
        'Kegiatan Dosen': 'bg-[#E3EDFF] text-[#2277FF]',
        'Kegiatan Mahasiswa': 'bg-[#FFF1CC] text-[#A86A00]',
    };

    function filtered() {
        const k = q.trim().toLowerCase();
        if (!k) return rows;
        return rows.filter((r) =>
            String(r.name ?? '').toLowerCase().includes(k) ||
            String(r.description ?? '').toLowerCase().includes(k) ||
            String(r.jenis ?? '').toLowerCase().includes(k));
    }

    function render() {
        const data = filtered();
        const total = data.length;
        const lastPage = Math.max(1, Math.ceil(total / PER));
        if (page > lastPage) page = lastPage;

        const mulai = (page - 1) * PER;
        const slice = data.slice(mulai, mulai + PER);

        $('kk-body').innerHTML = slice.length
            ? slice.map((r, i) => `
                <tr class="h-[65px] border-t border-[#E4E4E7]">
                    <td class="px-4 text-[#111]">${mulai + i + 1}</td>
                    <td class="px-4">
                        <div class="font-medium text-[#111]">${esc(r.name)}</div>
                        <span class="mt-1 inline-block rounded-full px-2 py-0.5 text-[11px] font-medium ${WARNA_JENIS[r.jenis] || 'bg-[#F2F2F7] text-[#55555C]'}">${esc(r.jenis)}</span>
                    </td>
                    <td class="px-4 text-[#55555C]">${esc(r.description)}</td>
                </tr>`).join('')
            : '<tr><td colspan="3" class="h-24 border-t border-[#E4E4E7] text-center text-[#55555C]">Tidak ada kategori.</td></tr>';

        $('kk-info').textContent = total
            ? `Menampilkan ${mulai + 1}–${mulai + slice.length} dari ${total} kategori`
            : 'Menampilkan 0 dari 0 kategori';

        const btn = 'size-8 rounded-md border text-[13px]';
        let html = `<button type="button" data-page="${page - 1}" ${page === 1 ? 'disabled' : ''} class="${btn} border-[#E4E4E7] bg-white text-[#8A8A90] disabled:opacity-60">‹</button>`;
        for (let p = 1; p <= lastPage; p++) {
            html += p === page
                ? `<span class="${btn} flex items-center justify-center border-[#2277FF] bg-[#2277FF] text-white">${p}</span>`
                : `<button type="button" data-page="${p}" class="${btn} border-[#E4E4E7] bg-white text-[#8A8A90] hover:border-[#2277FF]">${p}</button>`;
        }
        html += `<button type="button" data-page="${page + 1}" ${page === lastPage ? 'disabled' : ''} class="${btn} border-[#E4E4E7] bg-white text-[#8A8A90] disabled:opacity-60">›</button>`;
        $('kk-pager').innerHTML = html;
    }

    $('kk-cari').addEventListener('input', (e) => { q = e.target.value; page = 1; render(); });
    $('kk-pager').addEventListener('click', (e) => {
        const b = e.target.closest('button[data-page]');
        if (!b || b.disabled) return;
        page = parseInt(b.dataset.page, 10);
        render();
    });

    render();
})();
</script>
@endpush