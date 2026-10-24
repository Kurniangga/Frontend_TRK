{{--
    resources/views/kaprodi/jenis-dokumen.blade.php
    Versi kaprodi (BACA SAJA) dari admin Jenis Dokumen. Data $rows: [id, name, description].
--}}
@extends('layouts.kaprodi')

@section('title', 'Jenis Dokumen')

@section('content')
<div class="px-6 pb-8 pt-4 md:px-8">
    <h1 class="text-[32px] font-bold leading-[38px] tracking-[-0.2px] text-[#111]">Jenis Dokumen</h1>

    <x-admin.breadcrumb :items="[
        ['Dashboard', 'kaprodi.dashboard'],
        ['Kegiatan', 'kaprodi.kegiatan.dosen'],
        ['Jenis Dokumen'],
    ]" />

    <section class="mt-[26px] max-w-[1152px] rounded-lg border border-[#EFEFF1] bg-white p-[25px] shadow-[0_1px_3px_rgba(0,0,0,0.07)]">
        <div>
            <h3 class="text-base font-medium leading-6 text-[#111]">Daftar Jenis Dokumen</h3>
            <p class="text-[13px] leading-[18px] text-[#6E6E76]">Jenis dokumen yang tersedia untuk kegiatan dosen dan mahasiswa</p>
        </div>

        {{-- Cari --}}
        <div class="relative mt-5">
            <svg class="absolute left-[15px] top-1/2 -translate-y-1/2 text-[#8E8E96]" xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><circle cx="10" cy="10" r="7" /><path d="M21 21l-6-6" /></svg>
            <input id="jd-cari" type="text" placeholder="Cari jenis dokumen..."
                   class="h-10 w-full rounded-[7px] border border-[#DEDEE3] pl-10 pr-3 text-sm outline-none placeholder:text-[#757575] focus:border-[#226CFF]">
        </div>

        {{-- Tabel --}}
        <div class="mt-[19px] overflow-x-auto">
            <table class="w-full min-w-[520px] border-collapse text-[13px]">
                <thead>
                    <tr class="h-11 bg-[#F3F3F7] text-left font-medium text-[#2B2B30]">
                        <th class="w-[90px] rounded-tl-[7px] px-[18px] font-medium">No</th>
                        <th class="w-[26%] px-[18px] font-medium">Nama Jenis Dokumen</th>
                        <th class="rounded-tr-[7px] px-[18px] font-medium">Keterangan</th>
                    </tr>
                </thead>
                <tbody id="jd-body"></tbody>
            </table>
        </div>

        {{-- Info + paginasi --}}
        <div class="mt-[18px] flex flex-wrap items-center justify-between gap-3 text-[13px] text-[#5E5E66]">
            <span id="jd-info"></span>
            <div id="jd-pager" class="flex gap-[6px] text-xs"></div>
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

    function filtered() {
        const k = q.trim().toLowerCase();
        return rows.filter((r) => String(r.name ?? '').toLowerCase().includes(k));
    }

    function render() {
        const data = filtered();
        const total = data.length;
        const lastPage = Math.max(1, Math.ceil(total / PER));
        if (page > lastPage) page = lastPage;

        const mulai = (page - 1) * PER;
        const slice = data.slice(mulai, mulai + PER);

        $('jd-body').innerHTML = slice.length
            ? slice.map((r, i) => `
                <tr class="h-16 border-b border-[#EFEFF2]">
                    <td class="px-[18px] text-[#222]">${mulai + i + 1}</td>
                    <td class="px-[18px] font-semibold text-[#111]">${esc(r.name)}</td>
                    <td class="px-[18px] text-[#5E5E66]">${esc(r.description)}</td>
                </tr>`).join('')
            : '<tr><td colspan="3" class="h-16 px-[18px] text-[#5E5E66]">Tidak ada jenis dokumen.</td></tr>';

        $('jd-info').textContent = total
            ? `Menampilkan ${mulai + 1}–${mulai + slice.length} dari ${total} jenis dokumen`
            : 'Menampilkan 0 dari 0 jenis dokumen';

        const btn = 'flex h-[30px] w-8 items-center justify-center rounded-md border';
        let html = `<button type="button" data-page="${page - 1}" ${page === 1 ? 'disabled' : ''} class="${btn} border-[#E3E3E8] text-[#8E8E96] disabled:opacity-50">‹</button>`;
        for (let p = 1; p <= lastPage; p++) {
            html += p === page
                ? `<span class="${btn} border-[#226CFF] bg-[#226CFF] font-medium text-white">${p}</span>`
                : `<button type="button" data-page="${p}" class="${btn} border-[#E3E3E8] text-[#5E5E66] hover:border-[#226CFF]">${p}</button>`;
        }
        html += `<button type="button" data-page="${page + 1}" ${page === lastPage ? 'disabled' : ''} class="${btn} border-[#E3E3E8] text-[#8E8E96] disabled:opacity-50">›</button>`;
        $('jd-pager').innerHTML = html;
    }

    $('jd-cari').addEventListener('input', (e) => { q = e.target.value; page = 1; render(); });
    $('jd-pager').addEventListener('click', (e) => {
        const b = e.target.closest('button[data-page]');
        if (!b || b.disabled) return;
        page = parseInt(b.dataset.page, 10);
        render();
    });

    render();
})();
</script>
@endpush