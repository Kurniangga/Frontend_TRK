@extends('layouts.admin')

@section('title', 'Kategori Kegiatan')

@section('content')
<div>
    <h1 class="text-[32px] font-bold leading-10 tracking-[-0.3px] text-[#111]">Kategori Kegiatan</h1>

    {{-- Breadcrumb: Dashboard & Kegiatan bisa diklik, halaman aktif tidak --}}
    <nav aria-label="Breadcrumb" class="mt-0.5 text-2xl leading-8 text-[#A8A8A8]">
        <a href="{{ route('admin.dashboard') }}" class="transition-colors hover:text-[#2277FF] hover:underline">Dashboard</a>
        <span class="mx-1">/</span>
        <a href="{{ route('admin.kegiatan.mahasiswa') }}" class="transition-colors hover:text-[#2277FF] hover:underline">Kegiatan</a>
        <span class="mx-1">/</span>
        <span class="text-[#6E6E76]" aria-current="page">Kategori Kegiatan</span>
    </nav>

    <section class="mt-6 rounded-lg bg-white p-6 shadow-[0_1px_4px_rgba(0,0,0,0.06)]">
        <div class="flex flex-wrap items-start justify-between gap-4">
            <div>
                <h2 class="text-lg font-medium text-[#111]">Daftar Kategori Kegiatan</h2>
                <p class="text-sm text-[#8A8A90]">Kategori kegiatan yang tersedia untuk dosen dan mahasiswa</p>
            </div>
            <button type="button" id="kk-tambah"
                    class="flex h-10 items-center gap-2 rounded-md bg-[#2277FF] px-4 text-base text-white hover:bg-[#1b66e0]">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 5v14M5 12h14" /></svg>
                Kategori
            </button>
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

{{-- Pop-up tambah / edit (gaya desain Figma) --}}
<div id="kk-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/40 p-4" role="dialog" aria-modal="true">
    <div class="w-full max-w-[540px] rounded-lg bg-white px-6 pb-5 pt-6 shadow-xl">
        <h2 id="kk-modal-judul" class="text-xl font-bold leading-6 text-[#111]">Tambah Kategori Kegiatan</h2>
        <p id="kk-modal-sub" class="mt-1 text-xs text-[#B9B9BF]">Isi data kategori kegiatan baru program studi</p>

        <div class="mt-5 space-y-4">
            <div>
                <label for="kk-nama" class="mb-1.5 block text-sm font-semibold text-[#111]">Nama Kategori</label>
                <input id="kk-nama" type="text" maxlength="100" placeholder="Contoh: Kunjungan Industri"
                       class="h-9 w-full rounded-[5px] border border-[#DEDEE3] px-3 text-xs outline-none placeholder:text-[#B0B0B8] focus:border-[#2277FF]">
            </div>

            <div>
                <label for="kk-jenis" class="mb-1.5 block text-sm font-semibold text-[#111]">Jenis Kegiatan</label>
                <div class="relative">
                    <select id="kk-jenis"
                            class="h-9 w-full appearance-none rounded-[5px] border border-[#DEDEE3] bg-white px-3 pr-9 text-xs outline-none focus:border-[#2277FF]">
                        @foreach ($jenis as $j)
                            <option value="{{ $j }}">{{ $j }}</option>
                        @endforeach
                    </select>
                    <svg class="pointer-events-none absolute right-3 top-1/2 -translate-y-1/2 text-[#55555C]" xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 9l6 6l6-6" /></svg>
                </div>
            </div>

            <div>
                <label for="kk-ket" class="mb-1.5 block text-sm font-semibold text-[#111]">Keterangan</label>
                <textarea id="kk-ket" rows="4" maxlength="255" placeholder="Tuliskan keterangan singkat mengenai kegiatan ini"
                          class="block w-full resize-none rounded-[5px] border border-[#DEDEE3] px-3 py-2 text-xs outline-none placeholder:text-[#B0B0B8] focus:border-[#2277FF]"></textarea>
            </div>

            <p id="kk-error" class="hidden text-[13px] text-[#E5484D]"></p>
        </div>

        <div class="mt-4 flex justify-end gap-2">
            <button type="button" id="kk-batal"
                    class="h-8 rounded-md bg-[#FF4B4B] px-4 text-xs font-semibold text-white hover:bg-[#e63e3e]">Batal</button>
            <button type="button" id="kk-simpan"
                    class="h-8 rounded-md bg-[#2277FF] px-4 text-xs font-semibold text-white hover:bg-[#1b66e0] disabled:opacity-60">Simpan</button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
(() => {
    const URL_STORE  = @json(route('admin.kegiatan.kategori-kegiatan.store'));
    const URL_UPDATE = @json(route('admin.kegiatan.kategori-kegiatan.update', ['id' => '__ID__']));
    const URL_DELETE = @json(route('admin.kegiatan.kategori-kegiatan.destroy', ['id' => '__ID__']));
    const CSRF = @json(csrf_token());
    const PER  = 10;

    let rows = @json($rows);
    let q = '';
    let page = 1;
    let editingId = null; // null = tambah baru

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
            r.description.toLowerCase().includes(k) ||
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
                            <button type="button" data-act="edit" data-id="${r.id}" aria-label="Edit"
                                    class="flex size-8 items-center justify-center rounded-md bg-[#E3EDFF] text-[#2277FF] hover:brightness-95">${IKON_EDIT}</button>
                            <button type="button" data-act="hapus" data-id="${r.id}" aria-label="Hapus"
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

    // ---------- Pop-up ----------
    function bukaModal(row) {
        editingId = row ? row.id : null;
        $('kk-modal-judul').textContent = row ? 'Edit Kategori Kegiatan' : 'Tambah Kategori Kegiatan';
        $('kk-modal-sub').textContent = row
            ? 'Ubah data kategori kegiatan program studi'
            : 'Isi data kategori kegiatan baru program studi';
        $('kk-nama').value = row ? row.name : '';
        $('kk-jenis').selectedIndex = 0;
        if (row) $('kk-jenis').value = row.jenis;
        $('kk-ket').value = row ? row.description : '';
        tampilError('');
        $('kk-modal').classList.remove('hidden');
        $('kk-modal').classList.add('flex');
        $('kk-nama').focus();
    }

    function tutupModal() {
        $('kk-modal').classList.add('hidden');
        $('kk-modal').classList.remove('flex');
    }

    function tampilError(pesan) {
        const el = $('kk-error');
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
        const name = $('kk-nama').value.trim();
        const jenis = $('kk-jenis').value;
        const description = $('kk-ket').value.trim();
        if (!name) { tampilError('Nama kategori wajib diisi.'); return; }

        const tombol = $('kk-simpan');
        tombol.disabled = true;
        try {
            if (editingId === null) {
                const { row } = await kirim(URL_STORE, 'POST', { name, jenis, description });
                rows.push(row);
                q = ''; $('kk-cari').value = '';
                page = Math.ceil(rows.length / PER); // lompat ke halaman terakhir
            } else {
                const { row } = await kirim(URL_UPDATE.replace('__ID__', editingId), 'PUT', { name, jenis, description });
                rows = rows.map((r) => (String(r.id) === String(row.id) ? row : r));
            }
            tutupModal();
            render();
        } catch (e) {
            tampilError(e.message);
        } finally {
            tombol.disabled = false;
        }
    }

    async function hapus(id) {
        const row = rows.find((r) => String(r.id) === String(id));
        if (!row || !confirm(`Hapus kategori "${row.name}"?`)) return;
        try {
            await kirim(URL_DELETE.replace('__ID__', id), 'DELETE');
            rows = rows.filter((r) => String(r.id) !== String(id));
            render();
        } catch (e) {
            alert(e.message);
        }
    }

    // ---------- Event ----------
    $('kk-tambah').addEventListener('click', () => bukaModal(null));
    $('kk-batal').addEventListener('click', tutupModal);
    $('kk-simpan').addEventListener('click', simpan);
    $('kk-modal').addEventListener('click', (e) => { if (e.target === $('kk-modal')) tutupModal(); });
    document.addEventListener('keydown', (e) => { if (e.key === 'Escape') tutupModal(); });
    // Enter menyimpan hanya dari kolom nama (kolom keterangan adalah textarea: Enter = baris baru)
    $('kk-nama').addEventListener('keydown', (e) => { if (e.key === 'Enter') simpan(); });

    $('kk-cari').addEventListener('input', (e) => { q = e.target.value; page = 1; render(); });

    $('kk-body').addEventListener('click', (e) => {
        const b = e.target.closest('button[data-act]');
        if (!b) return;
        if (b.dataset.act === 'edit') bukaModal(rows.find((r) => String(r.id) === b.dataset.id));
        if (b.dataset.act === 'hapus') hapus(b.dataset.id);
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