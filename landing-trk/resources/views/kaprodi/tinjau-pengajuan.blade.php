@extends('layouts.kaprodi')

@section('title', 'Tinjau Pengajuan Kegiatan')

@php
    $badge = [
        'Diajukan'  => 'border-[#AAC8F8] bg-[#CDDEF9] text-[#2277FF]',
        'Direvisi'  => 'border-[#FAD4A8] bg-[#FBE5CB] text-[#854700]',
    ][$status] ?? 'border-[#AAC8F8] bg-[#CDDEF9] text-[#2277FF]';

    $card  = 'flex flex-col gap-6 rounded-md border border-[#0A0A0A]/[0.06] bg-white px-4 pb-6 pt-4';
    $input = 'rounded-lg border border-[#000932]/[0.12] bg-white/90 p-3 text-base shadow-[0_1.5px_2px_rgba(0,0,0,0.1)_inset] outline-none placeholder:text-[#A8A8A8] focus:border-[#2277FF]';
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
                <a href="{{ route('kaprodi.dashboard') }}" class="transition-colors hover:text-[#2277FF]">Dashboard</a> /
                <a href="{{ route('kaprodi.pengajuan') }}" class="transition-colors hover:text-[#2277FF]">Pengajuan</a> /
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
                    <div class="text-sm font-semibold">Keputusan Kaprodi</div>
                </div>

                <form method="POST" action="{{ route('kaprodi.pengajuan.keputusan', $id) }}" enctype="multipart/form-data" class="flex flex-col gap-6 p-3" novalidate>
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

                    {{-- Upload Surat (Custom Style) --}}
                    <div class="flex flex-col gap-2">
                        <label class="text-base font-medium text-[#2E2E2E]">Upload Surat Keputusan</label>
                        <div class="relative flex w-full flex-col items-center justify-center rounded-md border border-dashed border-[#7AA3F4] bg-[#F9FBFF] py-6 transition-colors hover:bg-[#EEF4FF]">
                            <input id="surat_keputusan" name="surat_keputusan" type="file" accept=".pdf,.doc,.docx"
                                   class="absolute inset-0 z-50 h-full w-full cursor-pointer opacity-0"
                                   onchange="document.getElementById('file_name').textContent = this.files[0] ? this.files[0].name : ''">
                            
                            <div class="pointer-events-none flex flex-col items-center justify-center gap-1.5">
                                <div class="flex items-center gap-2 text-[#2277FF]">
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M12 17v-6"/><path d="M9 14l3 3l3 -3"/><path d="M20.88 18.09A5 5 0 0 0 18 9h-1.26A8 8 0 1 0 3 16.3"/>
                                    </svg>
                                    <span class="text-sm font-medium">Pilih file dari perangkat</span>
                                </div>
                                <span id="file_name" class="text-xs font-medium text-[#A8A8A8]"></span>
                            </div>
                        </div>
                        @error('surat_keputusan')
                            <p class="text-sm text-[#F00]">{{ $message }}</p>
                        @enderror
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
@endsection