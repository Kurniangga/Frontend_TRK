@extends('layouts.admin')

@section('title', 'Tinjau Pengajuan Kegiatan')

@php
    $badge = [
        'Diajukan'  => 'border-[#AAC8F8] bg-[#CDDEF9] text-[#2277FF]',
        'Direvisi'  => 'border-[#FAD4A8] bg-[#FBE5CB] text-[#854700]',
    ][$status] ?? 'border-[#AAC8F8] bg-[#CDDEF9] text-[#2277FF]';

    $card  = 'flex flex-col gap-6 rounded-md border border-[#0A0A0A]/[0.06] bg-white px-4 pb-6 pt-4';
    $input = 'rounded-lg border border-[#000932]/[0.12] bg-white/90 p-3 text-base shadow-[0_1.5px_2px_rgba(0,0,0,0.1)_inset] outline-none placeholder:text-[#A8A8A8] focus:border-[#2277FF]';

    // ---- Data untuk preview surat ----
    $infoMap       = collect($info)->mapWithKeys(fn ($r) => [$r[0] => $r[1]]);
    $ketua         = collect($anggota)->first(fn ($a) => strcasecmp($a[1], 'Ketua') === 0)[0] ?? ($anggota[0][0] ?? '-');
    $penyelenggara = $infoMap['Penyelenggara'] ?? '-';
    $tanggalKeg    = $infoMap['Tanggal'] ?? '-';
    $tanggalSurat = \Carbon\Carbon::now()->locale('id')->translatedFormat('j F Y');
    $nomorAwal     = trim((string) old('nomor_surat', $nomorSurat));
@endphp

@section('content')
<main class="flex flex-col gap-10 px-4 py-8 sm:px-6">

    {{-- Judul + status + tombol kembali --}}
    <div class="flex flex-wrap items-center justify-between gap-2">
        <div class="flex flex-col gap-2">
            <div class="flex flex-wrap items-center gap-2">
                <h1 class="text-[32px] font-bold leading-[38px] tracking-[-0.2px]">{{ $judul }}</h1>
                <span class="rounded-lg border px-2 py-0.5 text-xs font-medium {{ $badge }}">{{ $status }}</span>
            </div>
            <p class="text-xl font-medium leading-[30px] tracking-[-0.15px] text-[#A8A8A8] sm:text-2xl">
                <a href="{{ route('admin.dashboard') }}" class="transition-colors hover:text-[#2277FF]">Dashboard</a> /
                <a href="{{ route('admin.pengajuan') }}" class="transition-colors hover:text-[#2277FF]">Pengajuan</a> /
                Tinjau Pengajuan Kegiatan
            </p>
        </div>
    </div>

    @if (session('success'))
        <div class="rounded-md border border-[#D6EDB5] bg-[#E6F3D3] px-4 py-3 text-sm text-[#4C7D08]" role="status">{{ session('success') }}</div>
    @endif

    <div class="grid items-stretch gap-3 rounded-lg bg-white p-4 pb-6 shadow-sm lg:grid-cols-2">

        {{-- KOLOM KIRI --}}
        <div class="flex flex-col gap-3">

            <section class="{{ $card }}">
                <div class="flex items-center gap-3">
                    <div class="flex size-6 items-center justify-center rounded-full bg-[#2277FF] text-sm font-semibold text-white">1</div>
                    <div class="text-sm font-semibold">Informasi kegiatan</div>
                </div>
                <div class="flex flex-col">
                    @foreach ($info as $i => [$k, $v])
                        <div class="flex items-center justify-between gap-14 p-3 text-sm text-[#2E2E2E] {{ $i ? 'border-t border-[#0A0A0A]/[0.06]' : '' }}">
                            <span class="font-medium">{{ $k }}</span>
                            <span class="text-right font-semibold">{{ $v }}</span>
                        </div>
                    @endforeach
                </div>
            </section>

            <section class="{{ $card }}">
                <div class="flex items-center gap-3">
                    <div class="flex size-6 items-center justify-center rounded-full bg-[#2277FF] text-sm font-semibold text-white">2</div>
                    <div class="text-sm font-semibold">Anggota Kegiatan</div>
                </div>
                <div class="overflow-x-auto rounded-md border border-[#00002F]/15 bg-white/80">
                    <table class="w-full text-sm text-[#1C2024]">
                        <thead>
                            <tr class="bg-[#000055]/[0.02] text-left font-medium">
                                <th class="min-w-[120px] p-2 font-medium">Nama</th>
                                <th class="min-w-[120px] p-2 font-medium">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($anggota as [$nama, $peran])
                                <tr class="border-t border-[#00002F]/15">
                                    <td class="p-2">{{ $nama }}</td>
                                    <td class="p-2">{{ $peran }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </section>

            <section class="{{ $card }} flex-1">
                <div class="flex items-center gap-3">
                    <div class="flex size-6 items-center justify-center rounded-full bg-[#2277FF] text-sm font-semibold text-white">3</div>
                    <div class="text-sm font-semibold">Dokumen Pendukung</div>
                </div>
                <div class="overflow-x-auto rounded-md border border-[#00002F]/15 bg-white/80">
                    <table class="w-full text-sm text-[#1C2024]">
                        <thead>
                            <tr class="bg-[#000055]/[0.02] text-left font-medium">
                                <th class="min-w-[120px] p-2 font-medium">Nama</th>
                                <th class="min-w-[120px] p-2 font-medium">Kategori</th>
                                <th class="min-w-[120px] p-2 font-medium">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($dokumen as [$nama, $kategori, $url])
                                <tr class="border-t border-[#00002F]/15">
                                    <td class="p-2">{{ $nama }}</td>
                                    <td class="p-2">{{ $kategori }}</td>
                                    <td class="p-2">
                                        <a href="{{ $url }}" target="_blank" rel="noopener" aria-label="Lihat dokumen"
                                            class="inline-flex rounded-md bg-[#CDDEF9] p-1.5 text-[#2277FF]">
                                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M10 12a2 2 0 1 0 4 0a2 2 0 0 0 -4 0"/><path d="M21 12c-2.4 4 -5.4 6 -9 6c-3.6 0 -6.6 -2 -9 -6c2.4 -4 5.4 -6 9 -6c3.6 0 6.6 2 9 6"/></svg>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </section>
        </div>

        {{-- KOLOM KANAN --}}
        <div class="flex flex-col gap-3">

            <section class="{{ $card }}">
                <div class="flex items-center gap-3">
                    <div class="flex size-6 items-center justify-center rounded-full bg-[#2277FF] text-sm font-semibold text-white">4</div>
                    <div class="text-sm font-semibold">Keputusan Admin</div>
                </div>

                <form method="POST" action="{{ route('admin.pengajuan.keputusan', $id) }}" class="flex flex-col gap-6 p-3" novalidate>
                    @csrf

                    {{-- Catatan revisi --}}
                    <div class="flex flex-col gap-2">
                        <label for="catatan" class="text-base font-medium text-[#2E2E2E]">Catatan revisi</label>
                        <textarea id="catatan" name="catatan" maxlength="1000"
                                  placeholder="Wajib diisi jika meminta revisi atau menolak"
                                  class="h-20 resize-none {{ $input }}">{{ old('catatan', $catatan) }}</textarea>
                        @error('catatan')
                            <p class="text-sm text-[#F00]">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Nomor surat --}}
                    <div class="flex flex-col gap-2">
                        <label for="nomor_surat" class="text-base font-medium text-[#2E2E2E]">Nomor surat</label>
                        <input id="nomor_surat" name="nomor_surat" type="text" maxlength="100"
                               value="{{ old('nomor_surat', $nomorSurat) }}"
                               placeholder="Contoh: 123/PL4.5/KP/2026 (wajib diisi jika menyetujui)"
                               class="{{ $input }}">
                        @error('nomor_surat')
                            <p class="text-sm text-[#F00]">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Card Spoiler Preview Surat (Klik untuk buka Modal) --}}
                    <div class="flex flex-col gap-2">
                        <div class="flex flex-wrap items-center justify-between gap-x-3 gap-y-1">
                            <span class="text-base font-medium text-[#2E2E2E]">Pratinjau Surat Persetujuan</span>
                            <span class="text-xs text-[#A8A8A8]">Klik untuk memperbesar</span>
                        </div>

                        <div onclick="openModalSurat()" 
                             class="group relative cursor-pointer overflow-hidden rounded-lg border border-[#0A0A0A]/[0.08] bg-[#F9FAFB] p-4 transition-all hover:border-[#2277FF] hover:bg-[#F0F5FF] hover:shadow-md">
                            
                            {{-- Efek Overlay Badge di Sudut --}}
                            <div class="absolute right-3 top-3 flex items-center gap-1 rounded-md bg-[#CDDEF9] px-2.5 py-1 text-xs font-semibold text-[#2277FF] shadow-sm transition-transform group-hover:scale-105">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 3h6v6"/><path d="M10 14L21 3"/><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/></svg>
                                Buka Surat
                            </div>

                            {{-- Mini Kop & Isi Spoiler --}}
                            <div class="pointer-events-none font-serif text-[10px] text-[#555]">
                                <div class="text-center font-bold uppercase tracking-wider text-[#111]">Politeknik Negeri Semarang</div>
                                <div class="my-2 border-b border-dashed border-gray-300"></div>
                                <div class="grid grid-cols-[60px_10px_1fr] gap-1 text-[10px]">
                                    <span>Nomor</span><span>:</span> <span id="pv-nomor-spoiler" class="font-semibold text-black">{{ $nomorAwal !== '' ? $nomorAwal : '[Belum diisi]' }}</span>
                                    <span>Hal</span><span>:</span> <span class="font-bold text-black">Persetujuan Kegiatan</span>
                                </div>
                                <p class="mt-2.5 line-clamp-2 text-justify text-[#666]">
                                    Berdasarkan pengajuan yang telah diterima, dengan ini kami menyetujui pelaksanaan kegiatan <b>“{{ $judul }}”</b>...
                                </p>
                            </div>
                        </div>
                        <p class="text-xs text-[#A8A8A8]">Nomor surat otomatis memperbarui isi pratinjau surat saat Anda mengetik.</p>
                    </div>

                    <div class="flex flex-col gap-3">
                        <button type="submit" name="aksi" value="setujui"
                                class="rounded-md border border-[#2277FF] px-4 py-2 text-lg font-medium text-[#2277FF] shadow-[0_1px_2px_rgba(0,0,0,0.12)] hover:bg-[#EEF4FF]">
                            Setujui
                        </button>
                        <button type="submit" name="aksi" value="revisi"
                                class="rounded-md border border-[#FAD4A8] px-4 py-2 text-lg font-medium text-[#F80] shadow-[0_1px_2px_rgba(0,0,0,0.12)] hover:bg-[#FFF6EC]">
                            Minta revisi
                        </button>
                        <button type="submit" name="aksi" value="tolak"
                                class="rounded-md border border-[#FAA8A8] px-4 py-2 text-lg font-medium text-[#F00] shadow-[0_1px_2px_rgba(0,0,0,0.12)] hover:bg-[#FFF0F0]">
                            Tolak
                        </button>
                    </div>
                </form>
            </section>

            <section class="{{ $card }} flex-1">
                <div class="flex items-center gap-3">
                    <div class="flex size-6 items-center justify-center rounded-full bg-[#2277FF] text-sm font-semibold text-white">5</div>
                    <div class="text-sm font-semibold">Riwayat Status</div>
                </div>
                <div class="flex flex-col gap-2">
                    @foreach ($riwayat as $r)
                        <div class="flex items-center gap-2">
                            <span class="size-1.5 shrink-0 rounded-full bg-[#7F7F7F]"></span>
                            <span class="text-sm font-semibold text-[#7F7F7F]">{{ $r }}</span>
                        </div>
                    @endforeach
                </div>
            </section>
        </div>
    </div>
</main>

{{-- MODAL / POP-UP SURAT LENGKAP --}}
<div id="modal-surat" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/60 p-4 backdrop-blur-xs">
    {{-- Kontainer Utama Modal (Sudut melengkung seragam: rounded-xl) --}}
    <div class="relative flex max-h-[90vh] w-full max-w-2xl flex-col overflow-hidden rounded-xl bg-white shadow-2xl">
        
        {{-- Header Modal (Tetap di atas, tidak ikut ter-scroll) --}}
        <div class="flex items-center justify-between border-b border-gray-100 px-6 py-4">
            <h3 class="text-lg font-bold text-[#2E2E2E]">Pratinjau Dokumen Surat Resmi</h3>
            {{-- Tombol Tutup Modal --}}
            <button onclick="closeModalSurat()" aria-label="Tutup pratinjau"
                    class="rounded-full bg-gray-100 p-2 text-gray-600 transition-colors hover:bg-gray-200">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6l-12 12"/><path d="M6 6l12 12"/></svg>
            </button>
        </div>

        {{-- Konten Dokumen Surat (Bagian inilah yang bisa di-scroll) --}}
        <div class="flex-1 overflow-y-auto px-6 py-6">
            {{-- Menggunakan rounded-none agar ujung card/kertas surat lancip seragam --}}
            <article class="mx-auto w-full rounded-none bg-white px-6 py-6 font-serif text-[11px] leading-[1.6] text-[#0A0A0A] border border-gray-200 shadow-sm sm:px-8">
                {{-- Kop surat --}}
                <header class="border-b-[3px] border-double border-[#0A0A0A] pb-2 text-center">
                    <div class="text-[10px] uppercase tracking-wide">Kementerian Pendidikan Tinggi, Sains, dan Teknologi</div>
                    <div class="text-sm font-bold uppercase leading-tight">Politeknik Negeri Semarang</div>
                    <div class="text-[10px]">Jl. Prof. Soedarto, S.H., Tembalang, Semarang 50275</div>
                </header>

                {{-- Nomor, lampiran, hal --}}
                <table class="mt-4 text-[11px]">
                    <tbody>
                        <tr>
                            <td class="pr-3 align-top">Nomor</td>
                            <td class="pr-2 align-top">:</td>
                            <td id="pv-nomor" class="align-top {{ $nomorAwal === '' ? 'italic text-[#A8A8A8]' : '' }}">{{ $nomorAwal !== '' ? $nomorAwal : '[nomor surat belum diisi]' }}</td>
                        </tr>
                        <tr>
                            <td class="pr-3 align-top">Lampiran</td>
                            <td class="pr-2 align-top">:</td>
                            <td class="align-top">{{ count($dokumen) }} berkas</td>
                        </tr>
                        <tr>
                            <td class="pr-3 align-top">Hal</td>
                            <td class="pr-2 align-top">:</td>
                            <td class="align-top font-bold">Persetujuan Kegiatan</td>
                        </tr>
                    </tbody>
                </table>

                {{-- Tujuan --}}
                <div class="mt-4">
                    <div>Yth. {{ $ketua }}</div>
                    <div>di Tempat</div>
                </div>

                {{-- Isi --}}
                <div class="mt-4 flex flex-col gap-2 text-justify">
                    <p>Dengan hormat,</p>
                    <p>
                        Berdasarkan pengajuan yang telah diterima, dengan ini kami menyetujui pelaksanaan kegiatan
                        <span class="font-bold">“{{ $judul }}”</span>
                        yang diselenggarakan oleh {{ $penyelenggara }}
                        pada tanggal {{ $tanggalKeg }}.
                    </p>

                    @if (count($anggota))
                        <p>Adapun anggota yang terlibat dalam kegiatan tersebut adalah:</p>
                        <ol class="list-decimal pl-5">
                            @foreach ($anggota as [$nama, $peran])
                                <li>{{ $nama }} ({{ $peran }})</li>
                            @endforeach
                        </ol>
                    @endif

                    <p>Demikian surat ini kami sampaikan. Atas perhatian dan kerja samanya, kami ucapkan terima kasih.</p>
                </div>

                {{-- Tanda tangan --}}
                <div class="mt-5 flex justify-end">
                    <div class="w-44 text-left">
                        <div>Semarang, {{ $tanggalSurat }}</div>
                        <div>Pejabat berwenang,</div>
                        <div class="h-12"></div>
                        <div>(............................)</div>
                    </div>
                </div>
            </article>
        </div>
    </div>
</div>

<script>
(() => {
    const input = document.getElementById('nomor_surat');
    const targetNomor = document.getElementById('pv-nomor');
    const targetSpoiler = document.getElementById('pv-nomor-spoiler');
    const modal = document.getElementById('modal-surat');

    if (!input) return;

    const sync = () => {
        const v = input.value.trim();
        const displayVal = v || '[nomor surat belum diisi]';
        
        // Update di modal
        if (targetNomor) {
            targetNomor.textContent = displayVal;
            targetNomor.classList.toggle('italic', !v);
            targetNomor.classList.toggle('text-[#A8A8A8]', !v);
        }

        // Update di card spoiler kecil
        if (targetSpoiler) {
            targetSpoiler.textContent = v || '[Belum diisi]';
        }
    };

    input.addEventListener('input', sync);
    sync();
})();

// Fungsi Buka/Tutup Modal
function openModalSurat() {
    const modal = document.getElementById('modal-surat');
    if (modal) {
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }
}

function closeModalSurat() {
    const modal = document.getElementById('modal-surat');
    if (modal) {
        modal.classList.remove('flex');
        modal.classList.add('hidden');
    }
}

// Tutup modal jika klik di luar area modal konten
window.addEventListener('click', (e) => {
    const modal = document.getElementById('modal-surat');
    if (e.target === modal) {
        closeModalSurat();
    }
});
</script>
@endsection