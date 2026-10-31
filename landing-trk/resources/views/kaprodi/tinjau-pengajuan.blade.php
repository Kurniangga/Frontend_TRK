@extends('layouts.kaprodi')

@section('title', 'Tinjau Pengajuan Kegiatan')

@php
    $badge = [
        'Diajukan'              => 'border-[#AAC8F8] bg-[#CDDEF9] text-[#2277FF]',
        'Direvisi'              => 'border-[#FAD4A8] bg-[#FBE5CB] text-[#854700]',
        'Menunggu Tanda Tangan' => 'border-[#DCCBF7] bg-[#EEE5FB] text-[#6B2FC2]',
        'Disetujui'             => 'border-[#D6EDB5] bg-[#E6F3D3] text-[#4C7D08]',
        'Ditolak'               => 'border-[#FAA8A8] bg-[#FFE5E5] text-[#C00]',
    ][$status] ?? 'border-[#AAC8F8] bg-[#CDDEF9] text-[#2277FF]';

    $card  = 'flex flex-col gap-6 rounded-md border border-[#0A0A0A]/[0.06] bg-white px-4 pb-6 pt-4';
    $input = 'rounded-lg border border-[#000932]/[0.12] bg-white/90 p-3 text-base shadow-[0_1.5px_2px_rgba(0,0,0,0.1)_inset] outline-none placeholder:text-[#A8A8A8] focus:border-[#2277FF]';

    $tanggalSurat   = $tanggalSurat ?? \Carbon\Carbon::now()->locale('id')->translatedFormat('j F Y');
    $nomorTampil    = trim((string) $nomorSurat) !== '' ? $nomorSurat : '[nomor surat belum diisi]';
    $jumlahLampiran = ($adaLampiran ? 1 : 0) + count($lampiranDokumen);
@endphp

@section('content')
<style>
#print-root { display: none; }
@media print {
    body > *:not(#print-root) { display: none !important; }
    #print-root { display: block !important; }
    #print-root article {
        width: 100% !important; max-width: none !important; min-height: 0 !important;
        margin: 0 !important; padding: 0 !important; box-shadow: none !important;
    }
    @page { size: A4; margin: 2.5cm; }
    #print-root .lampiran-surat {
        border: 0 !important; margin-top: 0 !important; padding-top: 0 !important;
        break-before: page; page-break-before: always;
    }
}
.kotak-kotak {
    background-color: #fff;
    background-image:
        linear-gradient(45deg, #E5E7EB 25%, transparent 25%),
        linear-gradient(-45deg, #E5E7EB 25%, transparent 25%),
        linear-gradient(45deg, transparent 75%, #E5E7EB 75%),
        linear-gradient(-45deg, transparent 75%, #E5E7EB 75%);
    background-size: 16px 16px;
    background-position: 0 0, 0 8px, 8px -8px, -8px 0;
}
</style>

<main class="flex flex-col gap-10 px-4 py-8 sm:px-6">

    {{-- Judul + status --}}
    <div class="flex flex-wrap items-center justify-between gap-2">
        <div class="flex flex-col gap-2">
            <div class="flex flex-wrap items-center gap-2">
                <h1 class="text-[32px] font-bold leading-[38px] tracking-[-0.2px]">{{ $judul }}</h1>
                <span class="rounded-lg border px-2 py-0.5 text-xs font-medium {{ $badge }}">{{ $status }}</span>
            </div>
            <p class="text-xl font-medium leading-[30px] tracking-[-0.15px] text-[#A8A8A8] sm:text-2xl">
                <a href="{{ route('kaprodi.dashboard') }}" class="transition-colors hover:text-[#2277FF]">Dashboard</a> /
                <a href="{{ route('kaprodi.pengajuan') }}" class="transition-colors hover:text-[#2277FF]">Pengajuan</a> /
                Tinjau Pengajuan Kegiatan
            </p>
        </div>
    </div>

    @if (session('success'))
        <div class="rounded-md border border-[#D6EDB5] bg-[#E6F3D3] px-4 py-3 text-sm text-[#4C7D08]" role="status">{{ session('success') }}</div>
    @endif
    @error('aksi')
        <div class="rounded-md border border-[#FAA8A8] bg-[#FFF0F0] px-4 py-3 text-sm text-[#C00]" role="alert">{{ $message }}</div>
    @enderror

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
                    <div class="text-sm font-semibold">Surat &amp; Tanda Tangan Kaprodi</div>
                </div>

                <div class="flex flex-col gap-6 p-3">

                    {{-- Nomor surat (diisi admin, hanya baca) --}}
                    <div class="flex flex-col gap-1">
                        <span class="text-base font-medium text-[#2E2E2E]">Nomor surat</span>
                        <span class="text-sm font-semibold text-[#2E2E2E]">{{ $nomorSurat ?: '-' }}</span>
                    </div>

                    {{-- Tanda tangan tersimpan --}}
                    @unless ($sudahTtd)
                    <div class="flex flex-col gap-2">
                        <div class="flex flex-wrap items-center justify-between gap-2">
                            <span class="text-base font-medium text-[#2E2E2E]">Tanda tangan Anda</span>
                            @if ($bisaTtd)
                                <button type="button" id="btn-buka-ttd"
                                        class="rounded-md bg-[#CDDEF9] px-3 py-1.5 text-xs font-semibold text-[#2277FF] hover:bg-[#BBD2F6]">
                                    <span id="label-btn-ttd">{{ $ttdAktif ? 'Ganti tanda tangan' : 'Unggah tanda tangan' }}</span>
                                </button>
                            @endif
                        </div>
                        <div class="kotak-kotak flex h-28 items-center justify-center rounded-lg border border-dashed border-[#7AA3F4]">
                            <img id="ttd-saat-ini" src="{{ $ttdAktif ? $ttdUrl : '' }}" alt="Tanda tangan"
                                 class="max-h-24 max-w-[70%] object-contain {{ $ttdAktif ? '' : 'hidden' }}">
                            <span id="ttd-kosong" class="text-sm text-[#A8A8A8] {{ $ttdAktif ? 'hidden' : '' }}">Belum ada tanda tangan tersimpan</span>
                        </div>
                        <p class="text-xs text-[#A8A8A8]">Diunggah sekali, dipakai untuk semua surat. Anda bisa menggantinya kapan saja; surat yang sudah ditandatangani tidak ikut berubah.</p>
                        @error('tanda_tangan')
                            <p class="text-sm text-[#F00]">{{ $message }}</p>
                        @enderror
                    </div>
                    @endunless

                    {{-- Pratinjau surat --}}
                    <div class="flex flex-col gap-2">
                        <div class="flex flex-wrap items-center justify-between gap-x-3 gap-y-1">
                            <span class="text-base font-medium text-[#2E2E2E]">Pratinjau Surat Persetujuan</span>
                            <span class="text-xs text-[#A8A8A8]">Klik untuk memperbesar</span>
                        </div>

                        @if ($isiSurat !== null)
                        <button type="button" id="btn-buka-surat"
                                class="group relative w-full cursor-pointer overflow-hidden rounded-lg border border-[#0A0A0A]/[0.08] bg-[#F9FAFB] p-4 pb-12 text-left transition-all hover:border-[#2277FF] hover:bg-[#F0F5FF] hover:shadow-md focus:outline-none">
                            <span class="pointer-events-none absolute bottom-3 right-3 flex items-center gap-1 rounded-md bg-[#CDDEF9] px-2.5 py-1 text-xs font-semibold text-[#2277FF] shadow-sm transition-transform group-hover:scale-105">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 3h6v6"/><path d="M10 14L21 3"/><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/></svg>
                                Buka Surat
                            </span>
                            <div class="pointer-events-none font-serif text-[10px] text-[#555]">
                                <div class="relative border-b border-gray-400 pb-2 text-[#111]" style="font-family:'Times New Roman',Times,serif">
                                    <img src="{{ asset('images/logo.png') }}" alt="" class="absolute left-1 top-1/2 size-11 -translate-y-[56%] object-contain" onerror="this.style.visibility='hidden'">
                                    <div class="px-[56px] text-center leading-snug">
                                        <div class="text-[9px] uppercase">Kementerian Pendidikan Tinggi, Sains, dan Teknologi</div>
                                        <div class="text-[9px] uppercase">Politeknik Negeri Semarang</div>
                                        <div class="mt-0.5 text-[8px]">Jalan Prof. H. Soedarto, S.H., Tembalang, Semarang 50275</div>
                                    </div>
                                </div>
                                <div class="mt-2 grid grid-cols-[60px_10px_1fr] gap-1 text-[10px]">
                                    <span>Nomor</span><span>:</span>
                                    <span class="font-semibold text-black">{{ $nomorTampil }}</span>
                                    <span>Hal</span><span>:</span>
                                    <span class="font-bold text-black">Persetujuan Kegiatan</span>
                                </div>
                                <div class="mt-2.5 line-clamp-3 text-justify text-[#666]">{!! $isiSurat !!}</div>
                            </div>
                        </button>
                        @else
                            <div class="rounded-lg border border-dashed border-[#DEDEE3] bg-[#F9FAFB] px-4 py-6 text-center text-sm text-[#A8A8A8]">
                                Surat belum tersedia (admin belum menyetujui, atau kategori ini tidak punya template surat).
                            </div>
                        @endif
                    </div>

                    {{-- Keputusan --}}
                    @if ($bisaTtd)
                    <form id="form-keputusan" method="POST" action="{{ route('kaprodi.pengajuan.keputusan', $id) }}" class="flex flex-col gap-6" novalidate>
                        @csrf

                        <div class="flex flex-col gap-2">
                            <label for="catatan" class="text-base font-medium text-[#2E2E2E]">Catatan revisi</label>
                            <textarea id="catatan" name="catatan" maxlength="1000"
                                      placeholder="Wajib diisi jika meminta revisi atau menolak"
                                      class="h-20 resize-none {{ $input }}">{{ old('catatan', $catatan) }}</textarea>
                            @error('catatan')
                                <p class="text-sm text-[#F00]">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex flex-col gap-3">
                            <button type="submit" name="aksi" value="setujui" id="btn-setujui" @disabled(! $ttdAktif)
                                    class="rounded-md border border-[#2277FF] px-4 py-2 text-lg font-medium text-[#2277FF] shadow-[0_1px_2px_rgba(0,0,0,0.12)] hover:bg-[#EEF4FF] disabled:cursor-not-allowed disabled:opacity-50">
                                Tanda tangani &amp; Setujui
                            </button>
                            <p id="info-ttd-wajib" class="-mt-1 text-xs text-[#A8A8A8] {{ $ttdAktif ? 'hidden' : '' }}">Unggah tanda tangan terlebih dahulu untuk dapat menyetujui.</p>
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
                    @else
                        <div class="rounded-md border border-[#0A0A0A]/[0.06] bg-[#F9FAFB] px-4 py-3 text-sm text-[#55555C]">
                            @if ($sudahTtd)
                                Surat sudah ditandatangani dan pengajuan disetujui.
                            @elseif ($status === 'Direvisi' || $status === 'Ditolak')
                                Pengajuan ini sudah Anda putuskan ({{ $status }}).{{ $catatan ? ' Catatan: ' . $catatan : '' }}
                            @else
                                Tidak ada tindakan yang tersedia untuk status ini.
                            @endif
                        </div>
                    @endif
                </div>
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

{{-- MODAL UNGGAH TANDA TANGAN --}}
@if ($bisaTtd)
<div id="modal-ttd" class="fixed inset-0 hidden items-center justify-center bg-black/60 p-4" style="z-index:9999" role="dialog" aria-modal="true" aria-labelledby="modal-ttd-judul">
    <div class="relative flex max-h-[92vh] w-full max-w-[560px] flex-col overflow-hidden rounded-xl bg-white shadow-2xl">
        <div class="flex items-center justify-between border-b border-gray-100 px-6 py-4">
            <h3 id="modal-ttd-judul" class="text-lg font-bold text-[#2E2E2E]">Unggah Tanda Tangan</h3>
            <button type="button" id="btn-tutup-ttd" aria-label="Tutup"
                    class="rounded-full bg-gray-100 p-2 text-gray-600 transition-colors hover:bg-gray-200">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6l-12 12"/><path d="M6 6l12 12"/></svg>
            </button>
        </div>

        <div class="flex flex-col gap-4 overflow-y-auto px-6 py-5">
            <p class="text-sm text-[#55555C]">Foto atau pindai tanda tangan di kertas putih. Background dihapus otomatis di browser, lalu hasilnya disimpan sebagai PNG transparan.</p>

            <input id="ttd-file" type="file" accept="image/png,image/jpeg,image/webp"
                   class="block w-full text-sm text-[#55555C] file:mr-3 file:rounded-md file:border-0 file:bg-[#CDDEF9] file:px-3 file:py-2 file:text-sm file:font-semibold file:text-[#2277FF] hover:file:bg-[#BBD2F6]">

            <div id="ttd-area" class="hidden flex-col gap-3">
                <div class="kotak-kotak flex items-center justify-center rounded-lg border border-[#DEDEE3] p-2">
                    <canvas id="ttd-canvas" class="max-h-56 max-w-full"></canvas>
                </div>
                <div class="flex flex-col gap-1">
                    <label for="ttd-ambang" class="flex items-center justify-between text-sm font-medium text-[#2E2E2E]">
                        <span>Ambang penghapus background</span>
                        <span id="ttd-ambang-nilai" class="text-xs text-[#A8A8A8]">200</span>
                    </label>
                    <input id="ttd-ambang" type="range" min="80" max="250" value="200" class="w-full accent-[#2277FF]">
                    <p class="text-xs text-[#A8A8A8]">Geser ke kanan bila masih ada bayangan kertas, ke kiri bila goresan tanda tangan ikut terhapus.</p>
                </div>
            </div>

            <p id="ttd-error" class="hidden text-sm text-[#E5484D]"></p>
        </div>

        <div class="flex justify-end gap-2 border-t border-gray-100 px-6 py-4">
            <button type="button" id="btn-batal-ttd" class="h-9 rounded-md bg-[#FF4B4B] px-5 text-sm font-semibold text-white hover:bg-[#e63e3e]">Batal</button>
            <button type="button" id="btn-simpan-ttd" disabled class="h-9 rounded-md bg-[#2277FF] px-5 text-sm font-semibold text-white hover:bg-[#1b66e0] disabled:opacity-50">Simpan tanda tangan</button>
        </div>
    </div>
</div>
@endif

{{-- MODAL SURAT LENGKAP --}}
@if ($isiSurat !== null)
<div id="modal-surat" class="fixed inset-0 hidden items-center justify-center bg-black/60 p-4" style="z-index:9999" role="dialog" aria-modal="true" aria-labelledby="modal-surat-judul">
    <div class="relative flex max-h-[92vh] w-full max-w-[880px] flex-col overflow-hidden rounded-xl bg-white shadow-2xl">

        <div class="flex items-center justify-between border-b border-gray-100 px-6 py-4">
            <h3 id="modal-surat-judul" class="text-lg font-bold text-[#2E2E2E]">Pratinjau Dokumen Surat Resmi</h3>
            <div class="flex items-center gap-2">
                <button type="button" id="btn-cetak-surat" aria-label="Cetak / Simpan PDF"
                        class="flex items-center gap-2 rounded-md bg-[#2277FF] px-3 py-1.5 text-sm font-medium text-white transition-colors hover:bg-blue-600">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 9V2h12v7"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><path d="M6 14h12v8H6z"/></svg>
                    Cetak
                </button>
                <button type="button" id="btn-tutup-surat" aria-label="Tutup pratinjau"
                        class="rounded-full bg-gray-100 p-2 text-gray-600 transition-colors hover:bg-gray-200">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6l-12 12"/><path d="M6 6l12 12"/></svg>
                </button>
            </div>
        </div>

        <div class="flex-1 overflow-y-auto bg-gray-100 px-6 py-6">
            <article id="area-cetak-surat" class="mx-auto min-h-[1056px] w-full max-w-[800px] bg-white px-8 py-10 font-serif text-[13px] leading-[1.6] text-[#0A0A0A] shadow-sm sm:px-12">
                <header class="relative border-b border-gray-400 pb-3" style="font-family:'Times New Roman',Times,serif">
                    <img src="{{ asset('images/logo.png') }}" alt="" class="absolute left-2 top-1/2 size-[72px] -translate-y-[56%] object-contain" onerror="this.style.visibility='hidden'">
                    <div class="px-[92px] text-center leading-snug">
                        <div class="text-[14px] uppercase">Kementerian Pendidikan Tinggi, Sains, dan Teknologi</div>
                        <div class="text-[14px] uppercase">Politeknik Negeri Semarang</div>
                        <div class="mt-0.5 text-[11.5px]">Jalan Prof. H. Soedarto, S.H., Tembalang, Semarang 50275</div>
                        <div class="text-[11.5px]">Telepon (024) 7473417, 7499585, 7499586</div>
                        <div class="text-[11.5px]">Laman www.polines.ac.id, Surel sekretariat@polines.ac.id</div>
                    </div>
                </header>

                <div class="mt-6 flex justify-end">
                    <div class="text-right">Semarang, {{ $tanggalSurat }}</div>
                </div>

                <table class="mt-2 text-[13px]">
                    <tbody>
                        <tr>
                            <td class="pr-3 align-top">Nomor</td>
                            <td class="pr-2 align-top">:</td>
                            <td class="align-top">{{ $nomorTampil }}</td>
                        </tr>
                        @if ($jumlahLampiran > 0)
                        <tr>
                            <td class="pr-3 align-top">Lampiran</td>
                            <td class="pr-2 align-top">:</td>
                            <td class="align-top">{{ $jumlahLampiran }} berkas</td>
                        </tr>
                        @endif
                        <tr>
                            <td class="pr-3 align-top">Hal</td>
                            <td class="pr-2 align-top">:</td>
                            <td class="align-top font-bold">Persetujuan Kegiatan</td>
                        </tr>
                    </tbody>
                </table>

                <div class="mt-6 flex flex-col gap-2 text-justify text-[13px]">
                    {!! $isiSurat !!}
                </div>

                {{-- Tanda tangan --}}
                <div class="mt-14 flex justify-end">
                    <div class="w-fit" style="text-align:left">
                        <div>Ketua Program Studi,</div>
                        <div class="flex h-20 items-end">
                            <img class="js-ttd-img max-h-20 max-w-[200px] object-contain mix-blend-multiply {{ $ttdUrl ? '' : 'hidden' }}"
                                 src="{{ $ttdUrl }}" alt="Tanda tangan Kaprodi">
                        </div>
                        <div><span class="font-bold underline">{{ $namaKaprodi }}</span></div>
                        <div>NIP. {{ $nipKaprodi }}</div>
                    </div>
                </div>

                @if ($jumlahLampiran > 0)
                <section class="lampiran-surat mt-12 border-t-2 border-dashed border-gray-300 pt-8">
                    <table class="text-[13px]">
                        <tbody>
                            <tr><td class="pr-3 align-top">Lampiran</td><td class="pr-2 align-top">:</td><td class="align-top">Surat Persetujuan Kegiatan</td></tr>
                            <tr><td class="pr-3 align-top">Nomor</td><td class="pr-2 align-top">:</td><td class="align-top">{{ $nomorTampil }}</td></tr>
                            <tr><td class="pr-3 align-top">Tanggal</td><td class="pr-2 align-top">:</td><td class="align-top">{{ $tanggalSurat }}</td></tr>
                        </tbody>
                    </table>

                    <div class="mt-6 font-bold">Daftar Lampiran</div>
                    <ol class="ml-6 list-decimal text-[13px]">
                        @if ($adaLampiran)
                            <li>Daftar Peserta (halaman ini)</li>
                        @endif
                        @foreach ($lampiranDokumen as $nama)
                            <li>{{ $nama }}</li>
                        @endforeach
                    </ol>

                    @if ($adaLampiran)
                        <div class="mt-8 text-center font-bold uppercase">Daftar Peserta</div>
                        <div class="text-center">{{ $judul }}</div>

                        <table class="mt-4 w-full border-collapse text-[13px]">
                            <thead>
                                <tr>
                                    <th class="w-12 border border-black px-2 py-1 text-center font-bold">No</th>
                                    <th class="border border-black px-2 py-1 text-left font-bold">Nama</th>
                                    <th class="w-40 border border-black px-2 py-1 text-left font-bold">Keterangan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($peserta as $i => [$namaPeserta, $peranPeserta])
                                    <tr>
                                        <td class="border border-black px-2 py-1 text-center">{{ $i + 1 }}</td>
                                        <td class="border border-black px-2 py-1">{{ $namaPeserta }}</td>
                                        <td class="border border-black px-2 py-1">{{ $peranPeserta }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif

                    <div class="mt-10 flex justify-end">
                        <div class="w-fit" style="text-align:left">
                            <div>Ketua Program Studi,</div>
                            <div class="flex h-20 items-end">
                                <img class="js-ttd-img max-h-20 max-w-[200px] object-contain mix-blend-multiply {{ $ttdUrl ? '' : 'hidden' }}"
                                     src="{{ $ttdUrl }}" alt="Tanda tangan Kaprodi">
                            </div>
                            <div><span class="font-bold underline">{{ $namaKaprodi }}</span></div>
                            <div>NIP. {{ $nipKaprodi }}</div>
                        </div>
                    </div>
                </section>
                @endif
            </article>
        </div>
    </div>
</div>
@endif
@endsection

@push('scripts')
<script>
(() => {
    const URL_TTD = @json(route('kaprodi.tanda-tangan.store'));
    const CSRF    = @json(csrf_token());

    const $ = (id) => document.getElementById(id);

    /* ---------------- Modal surat ---------------- */
    const modalSurat = $('modal-surat');
    if (modalSurat) {
        document.body.appendChild(modalSurat); // lepas dari stacking context layout
        const bukaS  = () => { modalSurat.classList.remove('hidden'); modalSurat.classList.add('flex'); document.body.style.overflow = 'hidden'; };
        const tutupS = () => { modalSurat.classList.add('hidden'); modalSurat.classList.remove('flex'); document.body.style.overflow = ''; };
        $('btn-buka-surat')?.addEventListener('click', bukaS);
        $('btn-tutup-surat')?.addEventListener('click', tutupS);
        // Cetak: salin surat ke #print-root langsung di <body> (lepas dari modal yang overflow-hidden)
        // Nama berkas PDF = judul halaman saat mencetak: "nama kegiatan - pengaju"
        const JUDUL_ASLI  = document.title;
        const NAMA_BERKAS = @json($judul . ' - ' . (collect($info)->first(fn ($r) => $r[0] === 'Pengaju')[1] ?? ''))
            .replace(/[\\\/:*?"<>|]+/g, '').replace(/\s+/g, ' ').trim();
        const hapusCetak = () => {
            document.getElementById('print-root')?.remove();
            document.title = JUDUL_ASLI;
        };
        $('btn-cetak-surat')?.addEventListener('click', () => {
            const sumber = $('area-cetak-surat');
            if (!sumber) return;
            hapusCetak();
            document.title = NAMA_BERKAS;
            const root = document.createElement('div');
            root.id = 'print-root';
            const salinan = sumber.cloneNode(true);
            salinan.removeAttribute('id');
            root.appendChild(salinan);
            document.body.appendChild(root);
            window.print();
        });
        window.addEventListener('afterprint', hapusCetak);
        modalSurat.addEventListener('click', (e) => { if (e.target === modalSurat) tutupS(); });
        document.addEventListener('keydown', (e) => { if (e.key === 'Escape') tutupS(); });
    }

    /* ---------------- Konfirmasi sebelum tanda tangan ---------------- */
    $('form-keputusan')?.addEventListener('submit', (e) => {
        if (e.submitter && e.submitter.value === 'setujui') {
            if (!confirm('Tanda tangan Anda akan ditempelkan pada surat dan surat langsung tersedia bagi pengaju. Lanjutkan?')) {
                e.preventDefault();
            }
        }
    });

    /* ---------------- Modal unggah tanda tangan ---------------- */
    const modalTtd = $('modal-ttd');
    if (!modalTtd) return;
    document.body.appendChild(modalTtd);

    const berkas  = $('ttd-file');
    const area    = $('ttd-area');
    const cvs     = $('ttd-canvas');
    const ctx     = cvs.getContext('2d', { willReadFrequently: true });
    const slider  = $('ttd-ambang');
    const nilai   = $('ttd-ambang-nilai');
    const pesan   = $('ttd-error');
    const simpan  = $('btn-simpan-ttd');

    const MAKS_LEBAR = 900;   // perkecil foto besar supaya ringan
    const KELEMBUTAN = 50;    // lebar transisi tepi (semakin besar semakin halus)
    let asli = null;          // ImageData foto asli (di atas latar putih)

    const tampilError = (t) => { pesan.textContent = t || ''; pesan.classList.toggle('hidden', !t); };

    const bukaT = () => { modalTtd.classList.remove('hidden'); modalTtd.classList.add('flex'); document.body.style.overflow = 'hidden'; };
    const tutupT = () => {
        modalTtd.classList.add('hidden'); modalTtd.classList.remove('flex'); document.body.style.overflow = '';
        berkas.value = ''; asli = null; area.classList.add('hidden'); area.classList.remove('flex');
        simpan.disabled = true; tampilError('');
    };

    $('btn-buka-ttd')?.addEventListener('click', bukaT);
    $('btn-tutup-ttd').addEventListener('click', tutupT);
    $('btn-batal-ttd').addEventListener('click', tutupT);
    modalTtd.addEventListener('click', (e) => { if (e.target === modalTtd) tutupT(); });

    berkas.addEventListener('change', () => {
        const f = berkas.files[0];
        tampilError('');
        if (!f) return;
        if (f.size > 8 * 1024 * 1024) { tampilError('Foto terlalu besar (maksimal 8 MB).'); return; }

        const url = URL.createObjectURL(f);
        const img = new Image();
        img.onload = () => {
            const skala = Math.min(1, MAKS_LEBAR / img.width);
            cvs.width  = Math.max(1, Math.round(img.width * skala));
            cvs.height = Math.max(1, Math.round(img.height * skala));
            // Latar putih dulu: PNG transparan tidak dibaca sebagai piksel hitam
            ctx.fillStyle = '#fff';
            ctx.fillRect(0, 0, cvs.width, cvs.height);
            ctx.drawImage(img, 0, 0, cvs.width, cvs.height);
            asli = ctx.getImageData(0, 0, cvs.width, cvs.height);
            URL.revokeObjectURL(url);
            area.classList.remove('hidden'); area.classList.add('flex');
            proses();
        };
        img.onerror = () => { URL.revokeObjectURL(url); tampilError('File tidak dapat dibaca sebagai gambar.'); };
        img.src = url;
    });

    // Hapus background: piksel terang -> transparan, piksel gelap (tinta) dipertahankan
    function proses() {
        if (!asli) return;
        const T = parseInt(slider.value, 10);
        nilai.textContent = T;

        const out = new ImageData(new Uint8ClampedArray(asli.data), asli.width, asli.height);
        const d = out.data;
        for (let i = 0; i < d.length; i += 4) {
            const lum = 0.299 * d[i] + 0.587 * d[i + 1] + 0.114 * d[i + 2];
            const a = lum >= T ? 0 : Math.min(1, (T - lum) / KELEMBUTAN);
            d[i + 3] = Math.round(a * 255);
        }
        ctx.putImageData(out, 0, 0);
        simpan.disabled = false;
    }
    slider.addEventListener('input', proses);

    // Potong area kosong di sekeliling tanda tangan lalu keluarkan PNG
    function keBlob() {
        const { width: w, height: h } = cvs;
        const d = ctx.getImageData(0, 0, w, h).data;
        let x1 = w, y1 = h, x2 = -1, y2 = -1;
        for (let y = 0; y < h; y++) {
            for (let x = 0; x < w; x++) {
                if (d[(y * w + x) * 4 + 3] > 24) {
                    if (x < x1) x1 = x; if (x > x2) x2 = x;
                    if (y < y1) y1 = y; if (y > y2) y2 = y;
                }
            }
        }
        if (x2 < 0) return Promise.resolve(null);

        const pad = 8;
        x1 = Math.max(0, x1 - pad); y1 = Math.max(0, y1 - pad);
        x2 = Math.min(w - 1, x2 + pad); y2 = Math.min(h - 1, y2 + pad);

        const cw = x2 - x1 + 1, ch = y2 - y1 + 1;
        const hasil = document.createElement('canvas');
        hasil.width = cw; hasil.height = ch;
        hasil.getContext('2d').drawImage(cvs, x1, y1, cw, ch, 0, 0, cw, ch);
        return new Promise((res) => hasil.toBlob(res, 'image/png'));
    }

    simpan.addEventListener('click', async () => {
        tampilError('');
        simpan.disabled = true;
        try {
            const blob = await keBlob();
            if (!blob) { tampilError('Tanda tangan tidak terdeteksi. Turunkan ambang atau gunakan foto yang lebih jelas.'); simpan.disabled = false; return; }

            const fd = new FormData();
            fd.append('tanda_tangan', blob, 'tanda-tangan.png');

            const res = await fetch(URL_TTD, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
                body: fd,
            });
            const json = await res.json().catch(() => ({}));
            if (!res.ok) {
                throw new Error(json.errors ? Object.values(json.errors)[0][0] : (json.message || 'Gagal menyimpan tanda tangan.'));
            }

            // Perbarui tampilan tanpa memuat ulang halaman
            const img = $('ttd-saat-ini');
            img.src = json.url; img.classList.remove('hidden');
            $('ttd-kosong').classList.add('hidden');
            document.querySelectorAll('.js-ttd-img').forEach((el) => { el.src = json.url; el.classList.remove('hidden'); });
            $('label-btn-ttd').textContent = 'Ganti tanda tangan';
            $('btn-setujui')?.removeAttribute('disabled');
            $('info-ttd-wajib')?.classList.add('hidden');
            tutupT();
        } catch (e) {
            tampilError(e.message);
            simpan.disabled = false;
        }
    });
})();
</script>
@endpush