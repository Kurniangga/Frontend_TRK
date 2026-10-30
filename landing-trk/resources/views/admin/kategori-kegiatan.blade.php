@extends('layouts.admin')

@section('title', 'Kategori Kegiatan')

@section('content')
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
               class="flex h-10 items-center gap-2 rounded-md bg-[#2277FF] px-4 text-base text-white hover:bg-[#1b66e0]">
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

        {{-- Tabel --}}
        <div class="mt-5 overflow-x-auto rounded-md border border-[#E4E4E7]">
            <table class="w-full min-w-[640px] text-left text-sm">
                <thead class="bg-[#F2F2F7] text-[#222]">
                    <tr class="h-11">
                        <th class="w-[90px] px-4 font-medium">No</th>
                        <th class="w-[289px] px-4 font-medium">Nama Kategori</th>
                        <th class="px-4 font-medium">Keterangan</th>
                        <th class="w-[130px] px-4 font-medium">Aksi</th>
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
    const URL_EDIT   = @json(route('admin.kegiatan.kategori-kegiatan.edit', ['id' => '__ID__']));
    const URL_DELETE = @json(route('admin.kegiatan.kategori-kegiatan.destroy', ['id' => '__ID__']));
    const CSRF = @json(csrf_token());
    const PER  = 10;

    let rows = @json($rows);
    let q = '';
    let page = 1;

    const $ = (id) => document.getElementById(id);
    const esc = (s) => String(s ?? '').replace(/[&<>"']/g, (c) =>
        ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]));

    const IKON_EDIT = '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M4 20h4L18.5 9.5a2.828 2.828 0 1 0-4-4L4 16v4M13.5 6.5l4 4"/></svg>';
    const IKON_HAPUS = '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M4 7h16M10 11v6M14 11v6M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2l1-12M9 7V4h6v3"/></svg>';

    const WARNA_JENIS = {
        'Kegiatan Dosen': 'bg-[#E3EDFF] text-[#2277FF]',
        'Kegiatan Mahasiswa': 'bg-[#FFF1CC] text-[#A86A00]',
    };

    function filtered() {
        const k = q.trim().toLowerCase();
        if (!k) return rows;
        return rows.filter((r) =>
            r.name.toLowerCase().includes(k) ||
            (r.description && r.description.toLowerCase().includes(k)) ||
            r.jenis.toLowerCase().includes(k));
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
                    <td class="px-4">
                        <div class="flex gap-2">
                            <a href="${URL_EDIT.replace('__ID__', encodeURIComponent(r.id))}" aria-label="Edit"
                               class="flex size-8 items-center justify-center rounded-md bg-[#E3EDFF] text-[#2277FF] hover:brightness-95">${IKON_EDIT}</a>
                            <button type="button" data-act="hapus" data-id="${esc(r.id)}" aria-label="Hapus"
                                    class="flex size-8 items-center justify-center rounded-md bg-[#FFD9D9] text-[#E5484D] hover:brightness-95">${IKON_HAPUS}</button>
                        </div>
                    </td>
                </tr>`).join('')
            : '<tr><td colspan="4" class="h-24 border-t border-[#E4E4E7] text-center text-[#55555C]">Tidak ada kategori.</td></tr>';

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

    async function hapus(id) {
        const row = rows.find((r) => String(r.id) === String(id));
        if (!row || !confirm(`Hapus kategori "${row.name}"?`)) return;
        try {
            const res = await fetch(URL_DELETE.replace('__ID__', encodeURIComponent(id)), {
                method: 'DELETE',
                headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
            });
            const json = await res.json().catch(() => ({}));
            if (!res.ok) throw new Error(json.message || 'Terjadi kesalahan.');
            rows = rows.filter((r) => String(r.id) !== String(id));
            render();
        } catch (e) {
            alert(e.message);
        }
    }

    $('kk-cari').addEventListener('input', (e) => { q = e.target.value; page = 1; render(); });

    $('kk-body').addEventListener('click', (e) => {
        const b = e.target.closest('button[data-act="hapus"]');
        if (b) hapus(b.dataset.id);
    });

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