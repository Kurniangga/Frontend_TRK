@extends('layouts.admin')

@section('title', $row ? 'Edit Kategori Kegiatan' : 'Tambah Kategori Kegiatan')

@section('content')
<div class="px-6 pb-8 pt-4 md:px-8">
    <h1 class="text-[32px] font-bold leading-[38px] tracking-[-0.2px]">{{ $row ? 'Edit Kategori Kegiatan' : 'Tambah Kategori Kegiatan' }}</h1>

    {{-- Breadcrumb --}}
    <nav aria-label="Breadcrumb" class="mt-0.5 text-2xl leading-8 text-[#A8A8A8]">
        <a href="{{ route('admin.dashboard') }}" class="transition-colors hover:text-[#2277FF] hover:underline">Dashboard</a>
        <span class="mx-1">/</span>
        <a href="{{ route('admin.kegiatan.mahasiswa') }}" class="transition-colors hover:text-[#2277FF] hover:underline">Kegiatan</a>
        <span class="mx-1">/</span>
        <a href="{{ route('admin.kegiatan.kategori-kegiatan') }}" class="transition-colors hover:text-[#2277FF] hover:underline">Kategori Kegiatan</a>
        <span class="mx-1">/</span>
        <span class="text-[#6E6E76]" aria-current="page">{{ $row ? 'Edit' : 'Tambah' }}</span>
    </nav>

    <div class="mt-6 grid items-start gap-4 lg:grid-cols-2">

        {{-- ============ KIRI: FORM ============ --}}
        <section class="rounded-lg bg-white p-6 shadow-[0_1px_4px_rgba(0,0,0,0.06)]">
            <h2 class="text-lg font-medium text-[#111]">Data Kategori</h2>
            <p class="text-sm text-[#8A8A90]">
                {{ $row ? 'Ubah data kategori kegiatan program studi' : 'Isi data kategori kegiatan baru program studi' }}
            </p>

            <div class="mt-5 space-y-4">
                <div>
                    <label for="kk-nama" class="mb-1.5 block text-sm font-semibold text-[#111]">Nama Kategori</label>
                    <input id="kk-nama" type="text" maxlength="100" placeholder="Contoh: Kunjungan Industri"
                           class="h-10 w-full rounded-md border border-[#DEDEE3] px-3 text-sm outline-none placeholder:text-[#B0B0B8] focus:border-[#2277FF]">
                </div>

                <div>
                    <label for="kk-jenis" class="mb-1.5 block text-sm font-semibold text-[#111]">Jenis Kegiatan</label>
                    <div class="relative">
                        <select id="kk-jenis"
                                class="h-10 w-full appearance-none rounded-md border border-[#DEDEE3] bg-white px-3 pr-9 text-sm outline-none focus:border-[#2277FF]">
                            @foreach ($jenis as $j)
                                <option value="{{ $j }}">{{ $j }}</option>
                            @endforeach
                        </select>
                        <svg class="pointer-events-none absolute right-3 top-1/2 -translate-y-1/2 text-[#55555C]" xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 9l6 6l6-6" /></svg>
                    </div>
                </div>

                <div>
                    <label for="kk-ket" class="mb-1.5 block text-sm font-semibold text-[#111]">Keterangan</label>
                    <textarea id="kk-ket" rows="3" maxlength="255" placeholder="Tuliskan keterangan singkat mengenai kegiatan ini"
                              class="block w-full resize-none rounded-md border border-[#DEDEE3] px-3 py-2 text-sm outline-none placeholder:text-[#B0B0B8] focus:border-[#2277FF]"></textarea>
                </div>

                <div>
                    <label for="kk-template" class="mb-1.5 block text-sm font-semibold text-[#111]">Template Surat Balasan (Opsional)</label>
                    <textarea id="kk-template" rows="10"
                              placeholder="Yth. Bapak/Ibu [NAMA_PENGAJU]&#10;di Tempat&#10;&#10;Yang bertanda tangan di bawah ini menerangkan bahwa:&#10;Nama: [NAMA_PENGAJU]&#10;Identitas: [IDENTITAS]&#10;Telah disetujui mengikuti [NAMA_KEGIATAN] pada [TANGGAL] di [LOKASI]."
                              class="block w-full resize-y rounded-md border border-[#DEDEE3] px-3 py-2 text-sm outline-none placeholder:text-[#B0B0B8] focus:border-[#2277FF]"></textarea>

                    <p class="mt-2 text-xs text-[#8A8A90]">Klik variabel untuk menyisipkannya di posisi kursor:</p>
                    <div id="kk-chips" class="mt-1.5 flex flex-wrap gap-1.5">
                        @foreach (['NAMA_PENGAJU', 'IDENTITAS', 'NAMA_KEGIATAN', 'TANGGAL', 'LOKASI', 'NOMOR_SURAT', 'TANGGAL_SURAT'] as $v)
                            <button type="button" data-var="[{{ $v }}]"
                                    class="rounded-md bg-[#E3EDFF] px-2 py-1 text-[11px] font-semibold text-[#2277FF] hover:brightness-95">[{{ $v }}]</button>
                        @endforeach
                    </div>
                </div>

                <p id="kk-error" class="hidden text-[13px] text-[#E5484D]"></p>
            </div>

            <div class="mt-6 flex justify-end gap-2">
                <a href="{{ route('admin.kegiatan.kategori-kegiatan') }}"
                   class="flex h-9 items-center rounded-md bg-[#FF4B4B] px-5 text-sm font-semibold text-white hover:bg-[#e63e3e]">Batal</a>
                <button type="button" id="kk-simpan"
                        class="h-9 rounded-md bg-[#2277FF] px-5 text-sm font-semibold text-white hover:bg-[#1b66e0] disabled:opacity-60">Simpan</button>
            </div>
        </section>

        {{-- ============ KANAN: PRATINJAU SURAT ============ --}}
        <section class="rounded-lg bg-white p-6 shadow-[0_1px_4px_rgba(0,0,0,0.06)] lg:sticky lg:top-4">
            <div class="flex flex-wrap items-center justify-between gap-2">
                <div>
                    <h2 class="text-lg font-medium text-[#111]">Pratinjau Surat</h2>
                    <p class="text-sm text-[#8A8A90]">Diperbarui otomatis saat template diketik</p>
                </div>
                <label class="flex cursor-pointer items-center gap-2 text-xs text-[#55555C]">
                    <input id="kk-raw" type="checkbox" class="size-4 accent-[#2277FF]">
                    Tampilkan variabel asli
                </label>
            </div>

            <div class="mt-4 max-h-[75vh] overflow-y-auto rounded-md bg-gray-100 p-4">
                <article class="mx-auto min-h-[520px] w-full bg-white px-8 py-8 font-serif text-[12px] leading-[1.6] text-[#0A0A0A] shadow-sm">
                    <header class="relative border-b border-gray-400 pb-2" style="font-family:'Times New Roman',Times,serif">
                        <img src="{{ asset('images/logo.png') }}" alt="" class="absolute left-1 top-1/2 size-10 -translate-y-[56%] object-contain" onerror="this.style.visibility='hidden'">
                        <div class="px-[50px] text-center leading-snug">
                            <div class="text-[7.5px] uppercase">Kementerian Pendidikan Tinggi, Sains, dan Teknologi</div>
                            <div class="text-[7.5px] uppercase">Politeknik Negeri Semarang</div>
                            <div class="mt-0.5 text-[6.5px]">Jalan Prof. H. Soedarto, S.H., Tembalang, Semarang 50275</div>
                            <div class="text-[6.5px]">Telepon (024) 7473417, 7499585, 7499586</div>
                            <div class="text-[6.5px]">Laman www.polines.ac.id, Surel sekretariat@polines.ac.id</div>
                        </div>
                    </header>

                    <div class="mt-5 text-right">Semarang, <span id="pv-tgl-surat"></span></div>

                    <table class="mt-2 text-[12px]">
                        <tbody>
                            <tr><td class="pr-3 align-top">Nomor</td><td class="pr-2 align-top">:</td><td id="pv-nomor" class="align-top"></td></tr>
                            <tr><td class="pr-3 align-top">Lampiran</td><td class="pr-2 align-top">:</td><td class="align-top">2 berkas</td></tr>
                            <tr><td class="pr-3 align-top">Hal</td><td class="pr-2 align-top">:</td><td class="align-top font-bold">Persetujuan Kegiatan</td></tr>
                        </tbody>
                    </table>

                    <div id="pv-isi" class="mt-5 whitespace-pre-line text-justify"></div>

                    <div class="mt-10 flex justify-end">
                        <div class="w-fit" style="text-align:left">
                            <div>Ketua Program Studi,</div>
                            <div class="h-14"></div>
                            <div><span class="font-bold underline">Nama Kaprodi</span></div>
                            <div>NIP. 198001012010101001</div>
                        </div>
                    </div>
                </article>
            </div>
            <p class="mt-2 text-xs text-[#8A8A90]">Data pada pratinjau hanya contoh. Surat asli memakai data pengajuan.</p>
        </section>
    </div>
</div>
@endsection

@push('scripts')
<script>
(() => {
    const URL_INDEX  = @json(route('admin.kegiatan.kategori-kegiatan'));
    const URL_STORE  = @json(route('admin.kegiatan.kategori-kegiatan.store'));
    const URL_UPDATE = @json(route('admin.kegiatan.kategori-kegiatan.update', ['id' => '__ID__']));
    const CSRF = @json(csrf_token());
    const ROW  = @json($row);

    const $ = (id) => document.getElementById(id);
    const esc = (s) => String(s ?? '').replace(/[&<>"']/g, (c) =>
        ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]));

    // ---------- Data contoh untuk pratinjau ----------
    const tglSurat = new Date().toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' });
    const SAMPLE = {
        'NAMA_PENGAJU':  'Kuwat Santoso',
        'IDENTITAS':     '3.34.23.0001',
        'NAMA_KEGIATAN': 'Seminar Nasional Kecerdasan Buatan',
        'TANGGAL':       '4 Februari 2026',
        'LOKASI':        'Politeknik Negeri Semarang',
        'NOMOR_SURAT':   '123/PL4.5/KP/2026',
        'TANGGAL_SURAT': tglSurat,
    };

    $('pv-tgl-surat').textContent = tglSurat;
    $('pv-nomor').textContent = SAMPLE.NOMOR_SURAT;

    function renderPratinjau() {
        const raw = $('kk-raw').checked;
        const teks = $('kk-template').value;

        if (!teks.trim()) {
            $('pv-isi').innerHTML = '<span class="italic text-[#A8A8A8]">Belum ada template. Surat akan memakai teks bawaan sistem.</span>';
            return;
        }

        // Escape dulu (aman XSS), baru ganti variabel dengan span
        $('pv-isi').innerHTML = esc(teks).replace(/\[([A-Z_]+)\]/g, (m, nama) => {
            if (!(nama in SAMPLE)) return m;
            return raw
                ? `<span class="rounded bg-[#E3EDFF] px-1 font-sans text-[11px] font-semibold text-[#2277FF]">${m}</span>`
                : `<span class="rounded bg-[#FFF3C4] px-0.5">${esc(SAMPLE[nama])}</span>`;
        });
    }

    $('kk-template').addEventListener('input', renderPratinjau);
    $('kk-raw').addEventListener('change', renderPratinjau);

    // ---------- Sisipkan variabel di posisi kursor ----------
    $('kk-chips').addEventListener('click', (e) => {
        const b = e.target.closest('button[data-var]');
        if (!b) return;
        const ta = $('kk-template');
        const awal = ta.selectionStart ?? ta.value.length;
        const akhir = ta.selectionEnd ?? ta.value.length;
        ta.value = ta.value.slice(0, awal) + b.dataset.var + ta.value.slice(akhir);
        const pos = awal + b.dataset.var.length;
        ta.focus();
        ta.setSelectionRange(pos, pos);
        renderPratinjau();
    });

    // ---------- Isi form saat edit ----------
    if (ROW) {
        $('kk-nama').value = ROW.name ?? '';
        $('kk-jenis').value = ROW.jenis;
        $('kk-ket').value = ROW.description ?? '';
        $('kk-template').value = ROW.template_surat ?? '';
    }
    renderPratinjau();

    // ---------- Simpan ----------
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
            body: JSON.stringify(body),
        });
        const json = await res.json().catch(() => ({}));
        if (!res.ok) {
            throw new Error(json.errors ? Object.values(json.errors)[0][0] : (json.message || 'Terjadi kesalahan.'));
        }
        return json;
    }

    async function simpan() {
        const body = {
            name: $('kk-nama').value.trim(),
            jenis: $('kk-jenis').value,
            description: $('kk-ket').value.trim(),
            template_surat: $('kk-template').value.trim(),
        };
        if (!body.name) { tampilError('Nama kategori wajib diisi.'); $('kk-nama').focus(); return; }

        const tombol = $('kk-simpan');
        tombol.disabled = true;
        tampilError('');
        try {
            if (ROW) {
                await kirim(URL_UPDATE.replace('__ID__', ROW.id), 'PUT', body);
            } else {
                await kirim(URL_STORE, 'POST', body);
            }
            window.location.href = URL_INDEX + '?status=' + (ROW ? 'diperbarui' : 'ditambahkan');
        } catch (e) {
            tampilError(e.message);
            tombol.disabled = false;
        }
    }

    $('kk-simpan').addEventListener('click', simpan);
    $('kk-nama').addEventListener('keydown', (e) => { if (e.key === 'Enter') simpan(); });
})();
</script>
@endpush