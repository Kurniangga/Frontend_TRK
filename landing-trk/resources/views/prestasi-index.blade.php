@extends('layouts.app')

@section('title', 'Daftar Prestasi')

@section('content')
@php
    // Warna lencana sesuai peringkat
    $warnaLencana = [1 => '#FFCA00', 2 => '#949494', 3 => '#AD5C00'];
@endphp

<div class="min-h-screen bg-[#F7F7F7]" style="font-family: 'Plus Jakarta Sans', sans-serif;">

    {{-- Navbar bersama --}}
    <x-navbar />

    <main class="mx-auto w-full max-w-[1728px] px-4 pb-16 pt-12 lg:px-20 2xl:px-[196px]">

        {{-- Judul halaman --}}
        <section class="mx-auto flex max-w-[1280px] flex-col items-center gap-3 text-center">
            <div class="text-base tracking-[-0.18px] text-[#737C8D]">
                <a href="{{ route('home') }}" class="hover:underline">Beranda</a> / Prestasi
            </div>
            <h1 class="text-4xl font-bold tracking-[-0.4px] text-[#2277FF] md:text-5xl md:leading-[58px]">
                Daftar Prestasi
            </h1>
            <p class="text-base font-semibold tracking-[-0.18px] text-[#4D5C73]">
                Capaian mahasiswa Teknologi Rekayasa Komputer · Politeknik Negeri Semarang
            </p>
            <div class="h-1 w-20 rounded-sm bg-[#FFD121]"></div>
        </section>

        {{-- Kartu prestasi --}}
        <section class="mt-16 grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
            @forelse ($prestasi as $p)
                <article class="relative aspect-[305/381] overflow-hidden rounded-2xl bg-neutral-200">
                    <img src="{{ $p['gambar'] }}" alt="{{ $p['judul'] }}" class="absolute inset-0 h-full w-full object-cover">

                    <span class="absolute left-3 top-3 rounded-full px-3 pb-[4.5px] pt-[3.5px] text-sm font-medium text-[#F7F7F7]"
                          style="background: {{ $warnaLencana[$p['juara']] ?? '#949494' }}; font-family: 'Poppins', sans-serif;">
                        Juara {{ $p['juara'] }}
                    </span>

                    {{-- Tinggi blok kuning dibuat tetap & seragam; teks menyesuaikan (judul maks. 3 baris) --}}
                    <div class="absolute inset-x-0 bottom-0 flex h-[112px] flex-col justify-center gap-1 bg-[linear-gradient(180deg,rgba(153,125,20,0)_-69.92%,#FFD121_21.37%)] px-4 pb-3 pt-5 text-[#003385] sm:h-[120px] sm:pb-4">
                        @php
                            // Judul jadi link hanya jika slug-nya ada di config/berita.php
                            $punyaBerita = ! empty($p['slug']) && array_key_exists($p['slug'], config('berita', []));
                        @endphp
                        <div class="line-clamp-3 text-[13px] font-semibold leading-[18px] sm:text-sm sm:leading-5">
                            @if ($punyaBerita)
                                <a href="{{ route('berita.show', $p['slug']) }}" class="hover:underline">{{ $p['judul'] }}</a>
                            @else
                                {{ $p['judul'] }}
                            @endif
                        </div>
                        <div class="truncate text-xs leading-4 sm:text-[13px] sm:leading-5">{{ $p['sub'] }}</div>
                    </div>
                </article>
            @empty
                <p class="col-span-full py-10 text-center text-[#7D8999]">Belum ada data prestasi.</p>
            @endforelse
        </section>

        {{-- Paginasi (komponen bersama) --}}
        <x-paginasi :paginator="$prestasi" class="mt-12" />
    </main>

    {{-- Footer bersama --}}
    <x-footer />
</div>
@endsection