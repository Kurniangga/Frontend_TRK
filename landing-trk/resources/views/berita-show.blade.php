@extends('layouts.app')

@section('title', $berita['judul'])

@section('content')
<div class="min-h-screen bg-white text-black" style="font-family: 'Plus Jakarta Sans', sans-serif;">

    {{-- Navbar bersama (sama seperti halaman publik lain) --}}
    <x-navbar />

    <main class="mx-auto max-w-[1728px] px-4 py-6 sm:px-8">
        <div class="flex flex-col gap-5 px-0 py-4 sm:px-4">
            <div class="flex flex-col gap-9">

                {{-- Breadcrumb + Judul --}}
                <div class="flex flex-col gap-1">
                    <p class="text-base font-normal tracking-[-0.18px] text-[#737C8D]">
                        <a href="{{ route('home') }}" class="hover:underline">Beranda</a>
                        / <a href="{{ route('home') }}#kabar" class="hover:underline">Berita</a>
                        / Detail Berita
                    </p>
                    <h1 class="max-w-[900px] text-[22px] font-semibold leading-[34px] tracking-[-0.2px] sm:text-[26px] sm:leading-[42px]">
                        {{ $berita['judul'] }}
                    </h1>
                </div>

                {{-- Penulis, tanggal, jumlah dilihat --}}
                <div class="flex flex-wrap items-center gap-x-5 gap-y-3 py-3">
                    <div class="flex h-11 w-11 items-center justify-center rounded-full bg-[#E3E3E3]">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none"
                             stroke="#2B3F6C" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="7" r="4" />
                            <path d="M6 21v-2a4 4 0 0 1 4-4h4a4 4 0 0 1 4 4v2" />
                        </svg>
                    </div>
                    <div class="flex min-w-[120px] flex-col gap-1">
                        <span class="text-[18px] font-medium leading-7 text-[#0A0A0A]">{{ $berita['penulis'] }}</span>
                        <span class="text-sm text-[#7F7F7F]">Penulis</span>
                    </div>
                    <div class="flex min-w-[120px] flex-col gap-1">
                        <span class="text-[18px] font-medium leading-7 text-[#0A0A0A]">{{ $berita['tanggal_pendek'] }}</span>
                        <span class="text-sm text-[#7F7F7F]">{{ $berita['jam'] }} WIB</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                             stroke="#2B3F6C" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="2" />
                            <path d="M22 12c-2.667 4.667-6 7-10 7s-7.333-2.333-10-7c2.667-4.667 6-7 10-7s7.333 2.333 10 7" />
                        </svg>
                        <span class="text-[18px] font-medium leading-7 text-[#0A0A0A]">{{ number_format($berita['dilihat'], 0, ',', '.') }}</span>
                    </div>
                </div>

                <div class="flex flex-col gap-9 lg:flex-row lg:items-start">

                    {{-- Isi berita --}}
                    <article class="flex min-w-0 flex-1 flex-col gap-[25px]">
                        <div class="aspect-[16/9] w-full overflow-hidden bg-[#F3F4F6]">
    <img src="{{ $berita['gambar'] }}" alt="{{ $berita['judul'] }}" class="h-full w-full object-cover">
</div>

                        <div class="flex flex-col gap-[30px] text-justify text-[18px] font-medium leading-[30px]">
                            @foreach ($berita['paragraf'] as $p)
                                <p>{{ $p }}</p>
                            @endforeach
                        </div>
                    </article>

                    {{-- Sidebar --}}
                    {{-- Sidebar menempel (sticky) di layar besar: tidak ikut bergulir --}}
                    <aside class="flex w-full shrink-0 flex-col gap-8 lg:sticky lg:top-[120px] lg:w-[400px]">

                        {{-- Kartu Informasi --}}
                        <div class="flex flex-col gap-3 rounded-[10px] border border-[#E3E3E3] px-[22px] pb-[22px] pt-[26px]">
                            <h2 class="text-[26px] font-bold leading-[32px] tracking-[-0.2px] text-[#071E38]">Informasi</h2>
                            @foreach ([
                                'Kategori'      => $berita['kategori'],
                                'Hari / Tanggal' => $berita['tanggal_panjang'],
                                'Waktu Baca'    => $berita['waktu_baca'],
                            ] as $label => $nilai)
                                <div class="flex flex-col gap-3">
                                    <span class="text-base font-semibold tracking-[-0.18px] text-[#64758A]">{{ $label }}</span>
                                    <span class="text-[18px] font-medium leading-6 text-[#071E38]">{{ $nilai }}</span>
                                </div>
                            @endforeach
                        </div>

                        {{-- Informasi Terkait --}}
                        <div class="px-3">
                            <h2 class="text-2xl font-bold leading-[30px] tracking-[-0.15px] text-[#071E38]">Informasi Terkait</h2>
                            <div class="h-1 bg-gradient-to-r from-[#3979BA] to-[#9CB7D5]"></div>

                            <div class="flex flex-col gap-3 pt-[14px]">
                                @forelse ($terkait as $item)
                                    <a href="{{ route('berita.show', $item['slug']) }}"
                                       class="flex h-[72px] items-center gap-[11px] overflow-hidden hover:opacity-80">
                                        <div class="h-[72px] w-[108px] shrink-0 overflow-hidden rounded-lg bg-[#F3F4F6]">
                                            <img src="{{ $item['gambar'] }}" alt="" class="h-full w-full object-cover">
                                        </div>
                                        <div class="flex flex-col gap-[5px]">
                                            <span class="line-clamp-2 text-base font-bold leading-[22px] tracking-[-0.18px] text-[#071E38]">
                                                {{ $item['judul'] }}
                                            </span>
                                            <span class="font-['Roboto'] text-xs text-[#7D8999]">{{ $item['tanggal_ringkas'] }}</span>
                                        </div>
                                    </a>
                                @empty
                                    <p class="text-sm text-[#7D8999]">Belum ada informasi terkait.</p>
                                @endforelse
                                <div class="h-px bg-[#E6E9EE]"></div>
                            </div>
                        </div>
                    </aside>
                </div>
            </div>
        </div>
    </main>

    {{-- Footer bersama --}}
    <x-footer />
</div>
@endsection