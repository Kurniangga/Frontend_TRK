{{--
    resources/views/kaprodi/dashboard.blade.php
    Data (sementara contoh) datang dari KaprodiDashboardController. Ganti dengan database nanti.
--}}
@extends('layouts.kaprodi')

@section('title', 'Dashboard')

@php
    /* ==================== HITUNGAN GRAFIK ==================== */
    // Batas atas sumbu Y = kelipatan 20, supaya 5 garis bantu selalu angka bulat (20, 15, 10, 5, 0)
    $axisMax    = max(20, (int) ceil(max($kegiatanPerTahun) / 20) * 20);
    $axisLabels = array_map(fn ($i) => (int) ($axisMax * $i / 4), [4, 3, 2, 1, 0]);

    $years = [];
    foreach ($kegiatanPerTahun as $tahun => $jumlah) {
        $years[] = ['y' => (string) $tahun, 'v' => $jumlah, 'ratio' => round($jumlah / $axisMax, 4)];
    }

    $cardShadow = 'shadow-[1px_0_3px_rgba(0,0,0,0.02),5px_2px_6px_rgba(0,0,0,0.02),12px_4px_8px_rgba(0,0,0,0.01)]';
@endphp

@section('content')
<main class="p-6">
        <div class="flex flex-col gap-5">

            {{-- ===================== JUDUL + KARTU STATISTIK ===================== --}}
            <div class="flex flex-col gap-8">
                <div class="flex flex-col gap-2">
                    <h1 class="text-[32px] font-bold leading-[38px] tracking-[-0.2px]">Dashboard</h1>
                    <p class="text-2xl font-medium leading-[30px] tracking-[-0.15px] text-[#A8A8A8]">Ringkasan data dosen dan kegiatan</p>
                </div>

                <div class="grid gap-6 sm:grid-cols-2 xl:grid-cols-4">
                    @foreach ($stats as $s)
                        <div class="group relative flex h-[164px] flex-col gap-[18px] rounded-lg bg-[#E4EDFB] px-3 pb-6 pt-4 transition-shadow {{ $s['url'] ? 'hover:shadow-md' : '' }} {{ $cardShadow }}">
                            <div class="flex items-center gap-2.5">
                                <span class="flex-1 text-xl font-medium leading-6">{{ $s['label'] }}</span>
                                @if ($s['url'])
                                    {{-- after:absolute after:inset-0 = seluruh kartu ikut bisa diklik --}}
                                    <a href="{{ $s['url'] }}" aria-label="Buka {{ $s['label'] }}" class="flex size-6 items-center justify-center rounded-full border border-[#0A0A0A] transition-colors group-hover:bg-[#0A0A0A] group-hover:text-white after:absolute after:inset-0 after:rounded-lg after:content-['']">
                                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 7l-10 10" /><path d="M8 7l9 0l0 9" /></svg>
                                    </a>
                                @else
                                    <span class="flex size-6 items-center justify-center rounded-full border border-[#0A0A0A] opacity-40">
                                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 7l-10 10" /><path d="M8 7l9 0l0 9" /></svg>
                                    </span>
                                @endif
                            </div>
                            <div @class([
                                'flex flex-1 items-end text-5xl font-bold leading-[58px] tracking-[-0.4px]',
                                'text-[#FF3F3F]' => $s['red'] ?? false,
                            ])>{{ $s['value'] }}</div>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- ===================== GRAFIK KEGIATAN PER TAHUN ===================== --}}
            <div class="flex flex-col gap-8 rounded-lg bg-white px-4 pb-6 pt-4 {{ $cardShadow }}">
                <h2 class="text-xl font-medium leading-6">Kegiatan Per Tahun</h2>

                <div class="flex h-[340px] gap-2 text-xs font-medium text-[#2E2E2E]">
                    {{-- Angka sumbu Y --}}
                    <div class="flex w-5 flex-col justify-between pb-7 text-center">
                        @foreach ($axisLabels as $n)
                            <span class="leading-4">{{ $n }}</span>
                        @endforeach
                    </div>

                    <div class="relative flex-1">
                        {{-- Garis bantu --}}
                        <div class="absolute inset-x-0 bottom-7 top-2 flex flex-col justify-between">
                            @for ($i = 0; $i < 5; $i++)
                                <div class="h-px bg-[#0A0A0A]/[0.06]"></div>
                            @endfor
                        </div>

                        {{-- Batang --}}
                        <div class="absolute inset-x-0 bottom-0 top-2 flex items-end justify-around gap-2 overflow-x-auto">
                            @foreach ($years as $d)
                                <div class="group relative flex h-full w-12 shrink-0 flex-col items-center justify-end gap-3">
                                    <span class="absolute z-10 hidden whitespace-nowrap rounded-[3px] bg-white px-1 py-0.5 text-xs font-normal shadow-md group-hover:block"
                                          style="top: max(0px, calc({{ (1 - $d['ratio']) * 100 }}% - {{ ((1 - $d['ratio']) * 28) + 24 }}px))">
                                        {{ $d['v'] }} Kegiatan
                                    </span>
                                    <div class="w-12 bg-[#226CFF]" style="height: calc((100% - 28px) * {{ $d['ratio'] }})"></div>
                                    <span class="leading-4">{{ $d['y'] }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            {{-- ===================== TABEL MENUNGGU VALIDASI ===================== --}}
            <div class="flex flex-col gap-8 rounded-lg bg-white px-4 pb-6 pt-4 {{ $cardShadow }}">
                <div class="flex items-center gap-3">
                    <h2 class="flex-1 text-xl font-medium leading-6">Kegiatan Menunggu Validasi</h2>
                    <a href="{{ route('kaprodi.kegiatan.dosen') }}" class="rounded-md px-4 py-1.5 text-base font-medium tracking-[-0.011em] text-[#226CFF]">Lihat semua</a>
                </div>

                <div class="overflow-x-auto rounded-md border border-[rgba(0,0,47,0.15)] bg-white/80">
                    <table class="w-full min-w-[640px] text-left text-sm">
                        <thead>
                            <tr class="bg-[rgba(0,0,85,0.02)] font-medium text-[#1C2024]">
                                @foreach (['Nama Kegiatan', 'Nama Dosen', 'Tanggal Diajukan', 'Aksi'] as $h)
                                    <th class="min-h-9 p-2 font-medium">{{ $h }}</th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody class="text-[#1C2024]">
                            @forelse ($rows as $r)
                                <tr class="border-t border-[rgba(0,0,47,0.15)]">
                                    <td class="p-2">{{ $r['kegiatan'] }}</td>
                                    <td class="p-2">{{ $r['dosen'] }}</td>
                                    <td class="p-2">{{ $r['tanggal'] }}</td>
                                    <td class="p-2">
                                        {{-- Ganti <button> menjadi <a href="..."> bila mau menautkan ke halaman validasi --}}
                                        <button type="button" aria-label="Tinjau kegiatan"
                                                class="flex size-8 items-center justify-center rounded-md bg-[#FBE5CB] text-[#FFAA49]">
                                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M4 20h4l10.5 -10.5a2.828 2.828 0 1 0 -4 -4l-10.5 10.5v4" /><path d="M13.5 6.5l4 4" /></svg>
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr class="border-t border-[rgba(0,0,47,0.15)]">
                                    <td colspan="4" class="p-4 text-center text-[#80838D]">Tidak ada kegiatan yang menunggu validasi.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </main>
@endsection