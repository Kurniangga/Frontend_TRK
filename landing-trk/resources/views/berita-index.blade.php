@extends('layouts.app')

@section('title', 'Berita TRK')

@section('content')
<div class="min-h-screen bg-white text-[#071E38]" style="font-family: 'Plus Jakarta Sans', sans-serif;">

    {{-- Navbar bersama --}}
    <x-navbar />

    <main class="mx-auto max-w-[1728px] px-5 pb-[105px] pt-12 lg:px-12">

        {{-- Breadcrumb + Judul --}}
        <div class="flex flex-col gap-2">
            <p class="text-base leading-[22px] tracking-[-0.18px] text-[#737C8D]">
                <a href="{{ route('home') }}" class="hover:underline">Beranda</a> / Berita
            </p>
            <h1 class="text-4xl font-bold leading-tight tracking-[-0.4px] text-[#071E38] md:text-5xl md:leading-[58px]">
                Berita TRK
            </h1>
        </div>

        <div class="mt-5 flex flex-col gap-6 lg:flex-row lg:items-start">

            {{-- Daftar berita + paginasi --}}
            <div class="flex w-full min-w-0 flex-col gap-5 lg:flex-1">
                <div class="flex flex-col">
                    @forelse ($berita as $item)
                        <div class="flex flex-col gap-[18px] pt-2.5">
                            <div class="flex flex-col items-start gap-[15px] sm:flex-row sm:items-center">
                                <a href="{{ route('berita.show', $item['slug']) }}" class="w-full shrink-0 sm:w-[280px]">
                                    <img src="{{ $item['gambar'] }}" alt="{{ $item['judul'] }}"
                                         class="h-[170px] w-full rounded-xl object-cover">
                                </a>
                                <div class="flex min-w-0 flex-1 flex-col gap-[5px]">
                                    <h2 class="text-base font-semibold leading-[22px] tracking-[-0.18px]">
                                        <a href="{{ route('berita.show', $item['slug']) }}" class="hover:underline">{{ $item['judul'] }}</a>
                                    </h2>
                                    <p class="text-xs leading-4 tracking-[-0.12px] text-[#7D8999]">{{ $item['tanggal_ringkas'] }}</p>
                                    <p class="text-justify text-xs leading-4 tracking-[-0.12px] text-black">
                                        {{ \Illuminate\Support\Str::limit($item['paragraf'][0] ?? '', 380) }}
                                    </p>
                                    <a href="{{ route('berita.show', $item['slug']) }}"
                                       class="text-xs font-semibold leading-4 tracking-[-0.12px] text-[#27F] hover:underline">
                                        Baca selengkapnya »
                                    </a>
                                </div>
                            </div>
                            <div class="h-px w-full bg-[#E6E9EE]"></div>
                        </div>
                    @empty
                        <p class="py-10 text-center text-[#7D8999]">Belum ada berita.</p>
                    @endforelse
                </div>

                {{-- Paginasi: 1 2 ... 9 10 --}}
                @if ($berita->lastPage() > 1)
                    @php
                        $now  = $berita->currentPage();
                        $last = $berita->lastPage();

                        // Halaman yang ditampilkan: awal, sekitar halaman aktif, dan akhir
                        $tampil = collect([1, 2, $now - 1, $now, $now + 1, $last - 1, $last])
                            ->filter(fn ($p) => $p >= 1 && $p <= $last)
                            ->unique()->sort()->values();

                        $daftar = [];
                        $prev = 0;
                        foreach ($tampil as $p) {
                            if ($p - $prev > 1) {
                                $daftar[] = '...';
                            }
                            $daftar[] = $p;
                            $prev = $p;
                        }
                    @endphp

                    <nav class="flex flex-wrap items-center justify-center gap-2 py-5" aria-label="Paginasi">
                        {{-- Sebelumnya --}}
                        @if ($berita->onFirstPage())
                            <span class="flex h-8 w-8 items-center justify-center rounded bg-[#27F] text-white opacity-50">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 6l-6 6l6 6" /></svg>
                            </span>
                        @else
                            <a href="{{ $berita->previousPageUrl() }}" aria-label="Sebelumnya"
                               class="flex h-8 w-8 items-center justify-center rounded bg-[#27F] text-white">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 6l-6 6l6 6" /></svg>
                            </a>
                        @endif

                        {{-- Nomor halaman --}}
                        @foreach ($daftar as $p)
                            @if ($p === '...')
                                <span class="flex h-8 w-8 items-center justify-center rounded border border-[#DFE3E8] text-sm font-bold">...</span>
                            @elseif ($p === $now)
                                <span class="flex h-8 w-8 items-center justify-center rounded border border-[#27F] bg-white text-sm font-bold tracking-[-0.16px] text-[#27F]">{{ $p }}</span>
                            @else
                                <a href="{{ $berita->url($p) }}"
                                   class="flex h-8 w-8 items-center justify-center rounded border border-[#DFE3E8] bg-white text-sm font-bold tracking-[-0.16px] text-[#0A0A0A] hover:border-[#27F] hover:text-[#27F]">{{ $p }}</a>
                            @endif
                        @endforeach

                        {{-- Berikutnya --}}
                        @if ($berita->hasMorePages())
                            <a href="{{ $berita->nextPageUrl() }}" aria-label="Berikutnya"
                               class="flex h-8 w-8 items-center justify-center rounded border border-[#27F] bg-[#CDDEF9] text-[#27F]">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 6l6 6l-6 6" /></svg>
                            </a>
                        @else
                            <span class="flex h-8 w-8 items-center justify-center rounded border border-[#27F] bg-[#CDDEF9] text-[#27F] opacity-50">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 6l6 6l-6 6" /></svg>
                            </span>
                        @endif
                    </nav>
                @endif
            </div>

            {{-- Sidebar: Berita Terbaru & Prestasi --}}
            {{-- Sidebar menempel (sticky) di layar besar: tidak ikut bergulir --}}
            <aside class="flex w-full shrink-0 flex-col gap-6 lg:sticky lg:top-[120px] lg:w-[420px]">
                @foreach (['Berita Terbaru' => $terbaru, 'Prestasi' => $prestasi] as $judulBlok => $daftarBlok)
                    <section class="w-full">
                        <h3 class="text-2xl font-bold leading-[30px] tracking-[-0.15px] text-[#071E38]">{{ $judulBlok }}</h3>
                        <div class="h-1 w-full bg-gradient-to-r from-[#3979BA] to-[#9CB7D5]"></div>

                        <div class="flex flex-col gap-3 pt-[14px]">
                            @forelse ($daftarBlok as $s)
                                <a href="{{ route('berita.show', $s['slug']) }}" class="flex items-center gap-[11px] hover:opacity-80">
                                    <div class="h-[64px] w-[96px] shrink-0 overflow-hidden rounded-lg bg-[#F3F4F6]">
                                        <img src="{{ $s['gambar'] }}" alt="" class="h-full w-full object-cover">
                                    </div>
                                    <div class="flex min-w-0 flex-col gap-[5px]">
                                        <p class="line-clamp-2 text-[15px] font-bold leading-[20px] tracking-[-0.18px] text-[#071E38]">{{ $s['judul'] }}</p>
                                        <p class="font-['Roboto'] text-xs leading-4 text-[#7D8999]">{{ $s['tanggal_ringkas'] }}</p>
                                    </div>
                                </a>
                            @empty
                                <p class="text-sm text-[#7D8999]">Belum ada data.</p>
                            @endforelse
                            <div class="h-px w-full bg-[#E6E9EE]"></div>
                        </div>
                    </section>
                @endforeach
            </aside>
        </div>
    </main>

    {{-- Footer bersama --}}
    <x-footer />
</div>
@endsection