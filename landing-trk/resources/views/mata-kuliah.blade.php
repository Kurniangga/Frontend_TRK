@extends('layouts.app')

@section('title', 'Mata Kuliah Semester ' . $semester . ' - Teknologi Rekayasa Komputer')
@section('description', 'Daftar mata kuliah Program Studi Teknologi Rekayasa Komputer')

@section('content')
<div class="min-h-screen bg-[#F2F6FD] font-['Plus_Jakarta_Sans',sans-serif]">

    <x-navbar />

    {{-- ===================== HEADER ===================== --}}
    <header class="font-['Poppins',sans-serif] relative overflow-hidden bg-[linear-gradient(91deg,#0A5BE0_0%,#00235C_100%)] px-6 pb-[25px] pt-[38px] text-white lg:px-[153px]">
        <div class="pointer-events-none absolute right-0 top-[-119px] size-[420px] rounded-full bg-white/[0.06]"></div>
        <div class="pointer-events-none absolute right-[250px] top-[75px] hidden size-[260px] rounded-full bg-[#FFC928]/[0.14] lg:block"></div>

        <p class="relative text-[13px] leading-4 tracking-[-0.4px] text-[#BFD6FF]">
            <a href="{{ route('home') }}" class="hover:underline">Beranda</a> › <a href="{{ route('home') }}#tentang" class="hover:underline">Tentang TRK</a> › <span class="text-[#FFC928]">Mata Kuliah TRK</span>
        </p>
        <h1 class="relative mt-[7px] text-[32px] font-bold leading-[58px] tracking-[-0.4px] sm:text-[44px]">Mata Kuliah</h1>
        <p class="relative mt-1.5 max-w-[459px] text-[15px] leading-5 tracking-[-0.4px] text-[#E3EEFF]">
            Daftar mata kuliah Program Studi Teknologi Rekayasa Komputer Politeknik Negeri Semarang
        </p>
    </header>

    <main class="mx-auto max-w-[1584px] px-4 sm:px-6 2xl:px-0">

        {{-- ============ BARIS KONTROL: Kembali | Filter | Statistik ============ --}}
        <div class="flex flex-col gap-6 pt-6 2xl:flex-row 2xl:items-center 2xl:pl-[18px] 2xl:pt-[18px]">

            {{-- Tombol Kembali --}}
            <a href="{{ route('home') }}"
               class="inline-flex items-center gap-1 self-start rounded-full bg-[#2277FF] p-0.5 pr-4 text-2xl font-semibold text-white 2xl:mr-[131px]">
                <span class="flex size-[42px] items-center justify-center rounded-full bg-[#5695FB]">
                    <svg class="size-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M15 6l-6 6l6 6" /></svg>
                </span>
                Kembali
            </a>

            {{-- Filter kurikulum + semester (otomatis submit saat diganti) --}}
            <form method="GET" action="{{ route('mata-kuliah') }}"
                  class="flex w-full flex-col gap-6 sm:w-auto sm:flex-row 2xl:gap-[126px]">
                @foreach ($filters as $f)
                    <div class="flex w-full flex-col gap-2.5 sm:w-[300px]">
                        <label for="{{ $f['name'] }}" class="text-xl font-semibold leading-6 text-black">{{ $f['label'] }}</label>
                        <div class="relative flex h-[52px] items-center rounded-[13px] border-2 border-[#CFE0FF] bg-white shadow-[0_6px_16px_rgba(0,50,136,0.08)] focus-within:border-[#2174F5]">
                            <svg class="pointer-events-none absolute left-6 size-5 text-[#2174F5]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 5m0 2a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v12a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2z" /><path d="M16 3l0 4" /><path d="M8 3l0 4" /><path d="M4 11l16 0" /><path d="M8 15h2v2h-2z" /></svg>
                            <select id="{{ $f['name'] }}" name="{{ $f['name'] }}" onchange="{{ $f['name'] === 'kurikulum' ? 'this.form.semester.disabled = true; ' : '' }}this.form.submit()"
                                    class="h-full w-full cursor-pointer appearance-none rounded-[11px] bg-transparent pl-[60px] pr-10 text-xl font-semibold text-[#0647B8] outline-none">
                                @foreach ($f['options'] as $value => $text)
                                    <option value="{{ $value }}" @selected((string) $value === (string) $f['selected'])>{{ $text }}</option>
                                @endforeach
                            </select>
                            <svg class="pointer-events-none absolute right-6 size-4 text-[#0647B8]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M6 9l6 6l6 -6" /></svg>
                        </div>
                    </div>
                @endforeach
            </form>

            {{-- Kartu statistik --}}
            <div class="flex w-full gap-3 sm:w-[424px] sm:gap-5 2xl:ml-auto">
                @foreach ($stats as $s)
                    <div class="flex h-[89px] flex-1 flex-col justify-end overflow-hidden rounded-[14px] bg-white text-center shadow-[0_6px_16px_rgba(0,50,136,0.08)]">
                        <div class="text-[40px] font-bold leading-[48px] tracking-[-0.3px] text-[#2174F5]">{{ $s['value'] }}</div>
                        <div class="flex h-[29px] items-center justify-center text-sm font-medium tracking-[-0.18px] text-[#5B6B86] sm:text-base">{{ $s['label'] }}</div>
                        <div class="h-1 bg-[#FFC928]"></div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- ===================== KARTU TABEL ===================== --}}
        <section class="mt-5 overflow-hidden rounded-[20px] bg-white shadow-[2px_5px_15px_4px_rgba(0,0,0,0.25)]">
            <div class="flex items-center gap-3 px-[18px] pb-[30px] pt-5">
                <h2 class="text-2xl font-bold leading-[30px] tracking-[-0.15px] text-[#0647B8]">Semester {{ $semester }}</h2>
                <span class="font-['Poppins',sans-serif] rounded-full bg-[#FFC928] px-3 py-1 text-xs font-bold text-[#0B2A6B]">{{ count($courses) }} Mata Kuliah</span>
            </div>

            <div class="overflow-x-auto px-[11px] pb-[19px]">
                <table class="w-full min-w-[900px] table-fixed border-collapse text-center">
                    <colgroup>
                        <col class="w-[7%]">
                        <col class="w-[25%]">
                        <col class="w-[35%]">
                        <col class="w-[8.25%]">
                        <col class="w-[8.25%]">
                        <col class="w-[8.25%]">
                        <col class="w-[8.25%]">
                    </colgroup>
                    <thead class="bg-[linear-gradient(90deg,#0A5BE0,#00235C)] text-white">
                        <tr class="h-[43px] text-lg font-semibold lg:text-2xl">
                            <th rowspan="2" class="border-r border-white/20 text-xl font-semibold lg:text-[32px]">No</th>
                            <th rowspan="2" class="border-r border-white/20 text-xl font-semibold lg:text-[32px]">Kode MK</th>
                            <th rowspan="2" class="border-r border-white/20 text-xl font-semibold lg:text-[32px]">Nama Mata Kuliah</th>
                            <th colspan="2" class="border-r border-white/20 font-semibold">SKS</th>
                            <th colspan="2" class="font-semibold">JAM</th>
                        </tr>
                        <tr class="h-[43px] text-base lg:text-[22px]">
                            <th class="font-semibold">Teori</th>
                            <th class="font-semibold">Praktek</th>
                            <th class="border-l border-white/20 font-semibold">Teori</th>
                            <th class="font-semibold">Praktek</th>
                        </tr>
                    </thead>

                    <tbody class="text-base font-semibold lg:text-2xl tracking-[-0.4px] text-[#00235C]">
                        @forelse ($courses as $c)
                            <tr class="h-14 lg:h-[73px] [&>td]:border-r [&>td]:border-white [&>td:last-child]:border-r-0 {{ $loop->even ? 'bg-[#D9D9D9]' : 'bg-white' }}">
                                <td class="text-[#848BAB]">{{ $loop->iteration }}</td>
                                <td>{{ $c['kode'] }}</td>
                                <td class="px-3">{{ $c['nama'] }}</td>
                                <td>{{ $c['sks_teori'] }}</td>
                                <td>{{ $c['sks_praktek'] }}</td>
                                <td>{{ $c['jam_teori'] }}</td>
                                <td>{{ $c['jam_praktek'] }}</td>
                            </tr>
                        @empty
                            <tr class="h-[73px] bg-white">
                                <td colspan="7" class="text-lg font-medium text-[#6B7A99]">Belum ada mata kuliah untuk semester ini.</td>
                            </tr>
                        @endforelse

                        {{-- Baris sub total --}}
                        <tr class="h-[55px] bg-[#FFF4CC] text-base font-bold lg:text-xl [&>td]:border-r [&>td]:border-white [&>td:last-child]:border-r-0">
                            <td colspan="3" class="pl-6 text-left">Sub Total Semester {{ $semester }}</td>
                            <td>{{ $total['sks_teori'] }}</td>
                            <td>{{ $total['sks_praktek'] }}</td>
                            <td>{{ $total['jam_teori'] }}</td>
                            <td>{{ $total['jam_praktek'] }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </section>

        <p class="mt-3 text-center text-base font-medium text-[#6B7A99] sm:text-xl">
            SKS = Satuan Kredit Semester • Jam = jam tatap muka per minggu
        </p>
    </main>

    <div class="pb-6"></div>
    <x-footer />
</div>
@endsection