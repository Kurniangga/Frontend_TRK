@extends('layouts.admin')

@section('title', 'Jenis Dokumen')

@section('content')
<div class="px-6 pb-8 pt-4 md:px-8">     ← ubah angka di sini
    <h1 class="text-[32px] font-bold ...">Jenis Dokumen</h1>
    {{-- Breadcrumb: Dashboard & Kegiatan bisa diklik, halaman aktif tidak --}}
    <nav aria-label="Breadcrumb" class="mt-[7px] text-xl leading-[26px] text-[#B9B9BF]">
        <a href="{{ route('admin.dashboard') }}" class="transition-colors hover:text-[#226CFF] hover:underline">Dashboard</a>
        <span class="mx-1">/</span>
        <a href="{{ route('admin.kegiatan.mahasiswa') }}" class="transition-colors hover:text-[#226CFF] hover:underline">Kegiatan</a>
        <span class="mx-1">/</span>
        <span class="text-[#6E6E76]" aria-current="page">Jenis Dokumen</span>
    </nav>

    <section class="mt-[26px] max-w-[1152px] rounded-lg border border-[#EFEFF1] bg-white p-[25px] shadow-[0_1px_3px_rgba(0,0,0,0.07)]">
        <div class="flex flex-wrap items-start justify-between gap-3">
            <div>
                <h3 class="text-base font-medium leading-6 text-[#111]">Daftar Jenis Dokumen</h3>
                <p class="text-[13px] leading-[18px] text-[#6E6E76]">Jenis dokumen yang tersedia untuk kegiatan dosen dan mahasiswa</p>
            </div>
            <button type="button" id="jd-tambah"
                    class="flex h-[39px] items-center gap-2 rounded-[7px] bg-[#226CFF] px-[17px] text-sm font-medium text-white hover:bg-[#1b5ae0]">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 5v14M5 12h14" /></svg>
                Jenis Dokumen
            </button>
        </div>

        {{-- Cari --}}
        <div class="relative mt-5">
            <svg class="absolute left-[15px] top-1/2 -translate-y-1/2 text-[#8E8E96]" xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><circle cx="10" cy="10" r="7" /><path d="M21 21l-6-6" /></svg>
            <input id="jd-cari" type="text" placeholder="Cari jenis dokumen..."
                   class="h-10 w-full rounded-[7px] border border-[#DEDEE3] pl-10 pr-3 text-sm outline-none placeholder:text-[#757575] focus:border-[#226CFF]">
        </div>

        {{-- Tabel --}}
        <div class="mt-[19px] overflow-x-auto">
            <table class="w-full min-w-[640px] border-collapse text-[13px]">
                <thead>
                    <tr class="h-11 bg-[#F3F3F7] text-left font-medium text-[#2B2B30]">
                        <th class="w-[90px] rounded-tl-[7px] px-[18px] font-medium">No</th>
                        <th class="w-[26%] px-[18px] font-medium">Nama Jenis Dokumen</th>
                        <th class="px-[18px] font-medium">Keterangan</th>
                        <th class="w-[182px] rounded-tr-[7px] px-[18px] font-medium">Aksi</th>
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

{{-- Pop-up tambah / edit --}}
<div id="jd-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/40 p-4" role="dialog" aria-modal="true">
    <div class="w-full max-w-[540px] rounded-lg bg-white px-6 pb-5 pt-6 shadow-xl">
        <h2 id="jd-modal-judul" class="text-xl font-bold leading-6 text-[#111]">Tambah Jenis Dokumen</h2>
        <p id="jd-modal-sub" class="mt-1 text-xs text-[#B9B9BF]">Isi data jenis dokumen baru program studi</p>

        <div class="mt-5 space-y-4">
            <div>
                <label for="jd-nama" class="mb-1.5 block text-sm font-semibold text-[#111]">Nama Jenis Dokumen</label>
                <input id="jd-nama" type="text" maxlength="100" placeholder="Contoh: Sertifikat"
                       class="h-9 w-full rounded-[5px] border border-[#DEDEE3] px-3 text-xs outline-none placeholder:text-[#B0B0B8] focus:border-[#226CFF]">
            </div>

            <div>
                <label for="jd-ket" class="mb-1.5 block text-sm font-semibold text-[#111]">Keterangan</label>
                <textarea id="jd-ket" rows="4" maxlength="255" placeholder="Tuliskan keterangan singkat mengenai jenis dokumen ini"
                          class="block w-full resize-none rounded-[5px] border border-[#DEDEE3] px-3 py-2 text-xs outline-none placeholder:text-[#B0B0B8] focus:border-[#226CFF]"></textarea>
            </div>

            <p id="jd-error" class="hidden text-[13px] text-[#E5484D]"></p>
        </div>

        <div class="mt-4 flex justify-end gap-2">
            <button type="button" id="jd-batal"
                    class="h-8 rounded-md bg-[#FF4B4B] px-4 text-xs font-semibold text-white hover:bg-[#e63e3e]">Batal</button>
            <button type="button" id="jd-simpan"
                    class="h-8 rounded-md bg-[#226CFF] px-4 text-xs font-semibold text-white hover:bg-[#1b5ae0] disabled:opacity-60">Simpan</button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
(() => {
    const URL_STORE  = @json(route('admin.kegiatan.jenis-dokumen.store'));
    const URL_UPDATE = @json(route('admin.kegiatan.jenis-dokumen.update', ['id' => '__ID__']));
    const URL_DELETE = @json(route('admin.kegiatan.jenis-dokumen.destroy', ['id' => '__ID__']));
    const CSRF = @json(csrf_token());
    const PER  = 10;

    let rows = @json($rows);
    let q = '';
    let page = 1;
    let editingId = null; // null = tambah baru

    const $ = (id) => document.getElementById(id);
    const esc = (s) => String(s ?? '').replace(/[&<>"']/g, (c) =>
        ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]));

    const IKON_EDIT = '<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M4 20h4L18.5 9.5a2.828 2.828 0 1 0-4-4L4 16v4M13.5 6.5l4 4"/></svg>';
    const IKON_HAPUS = '<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M4 7h16M10 11v6M14 11v6M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2l1-12M9 7V4h6v3"/></svg>';

    function filtered() {
        const k = q.trim().toLowerCase();
        return rows.filter((r) => r.name.toLowerCase().includes(k));
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
                    <td class="px-[18px]">
                        <div class="flex gap-[9px]">
                            <button type="button" data-act="edit" data-id="${r.id}" aria-label="Edit"
                                    class="flex h-7 w-[31px] items-center justify-center rounded-md bg-[#E3EDFF] text-[#226CFF]">${IKON_EDIT}</button>
                            <button type="button" data-act="hapus" data-id="${r.id}" aria-label="Hapus"
                                    class="flex h-7 w-[31px] items-center justify-center rounded-md bg-[#FFDCDC] text-[#E5484D]">${IKON_HAPUS}</button>
                        </div>
                    </td>
                </tr>`).join('')
            : '<tr><td colspan="4" class="h-16 px-[18px] text-[#5E5E66]">Tidak ada jenis dokumen.</td></tr>';

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

    // ---------- Pop-up ----------
    function bukaModal(row) {
        editingId = row ? row.id : null;
        $('jd-modal-judul').textContent = row ? 'Edit Jenis Dokumen' : 'Tambah Jenis Dokumen';
        $('jd-modal-sub').textContent = row
            ? 'Ubah data jenis dokumen program studi'
            : 'Isi data jenis dokumen baru program studi';
        $('jd-nama').value = row ? row.name : '';
        $('jd-ket').value = row ? row.description : '';
        tampilError('');
        $('jd-modal').classList.remove('hidden');
        $('jd-modal').classList.add('flex');
        $('jd-nama').focus();
    }

    function tutupModal() {
        $('jd-modal').classList.add('hidden');
        $('jd-modal').classList.remove('flex');
    }

    function tampilError(pesan) {
        const el = $('jd-error');
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
        const name = $('jd-nama').value.trim();
        const description = $('jd-ket').value.trim();
        if (!name) { tampilError('Nama jenis dokumen wajib diisi.'); return; }

        const tombol = $('jd-simpan');
        tombol.disabled = true;
        try {
            if (editingId === null) {
                const { row } = await kirim(URL_STORE, 'POST', { name, description });
                rows.push(row);
                page = Math.ceil(filtered().length / PER); // lompat ke halaman terakhir
            } else {
                const { row } = await kirim(URL_UPDATE.replace('__ID__', editingId), 'PUT', { name, description });
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
        if (!row || !confirm(`Hapus "${row.name}"?`)) return;
        try {
            await kirim(URL_DELETE.replace('__ID__', id), 'DELETE');
            rows = rows.filter((r) => String(r.id) !== String(id));
            render();
        } catch (e) {
            alert(e.message);
        }
    }

    // ---------- Event ----------
    $('jd-tambah').addEventListener('click', () => bukaModal(null));
    $('jd-batal').addEventListener('click', tutupModal);
    $('jd-simpan').addEventListener('click', simpan);
    $('jd-modal').addEventListener('click', (e) => { if (e.target === $('jd-modal')) tutupModal(); });
    document.addEventListener('keydown', (e) => { if (e.key === 'Escape') tutupModal(); });
    // Enter menyimpan hanya dari kolom nama (kolom keterangan adalah textarea: Enter = baris baru)
    $('jd-nama').addEventListener('keydown', (e) => { if (e.key === 'Enter') simpan(); });

    $('jd-cari').addEventListener('input', (e) => { q = e.target.value; page = 1; render(); });

    $('jd-body').addEventListener('click', (e) => {
        const b = e.target.closest('button[data-act]');
        if (!b) return;
        if (b.dataset.act === 'edit') bukaModal(rows.find((r) => String(r.id) === b.dataset.id));
        if (b.dataset.act === 'hapus') hapus(b.dataset.id);
    });

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