@props(['paginator'])

{{-- Paginasi bergaya "1 2 ... 9 10". Pemakaian: <x-paginasi :paginator="$data" class="mt-12" /> --}}
@if ($paginator->lastPage() > 1)
    @php
        $now  = $paginator->currentPage();
        $last = $paginator->lastPage();

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

    <nav {{ $attributes->merge(['class' => 'flex flex-wrap items-center justify-center gap-2 py-5']) }} aria-label="Paginasi">
        {{-- Sebelumnya --}}
        @if ($paginator->onFirstPage())
            <span class="flex h-8 w-8 items-center justify-center rounded bg-[#2277FF] text-white opacity-50">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 6l-6 6l6 6" /></svg>
            </span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" aria-label="Sebelumnya"
               class="flex h-8 w-8 items-center justify-center rounded bg-[#2277FF] text-white">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 6l-6 6l6 6" /></svg>
            </a>
        @endif

        {{-- Nomor halaman --}}
        @foreach ($daftar as $p)
            @if ($p === '...')
                <span class="flex h-8 w-8 items-center justify-center rounded border border-[#DFE3E8] bg-white text-sm font-bold text-[#0A0A0A]">...</span>
            @elseif ($p === $now)
                <span class="flex h-8 w-8 items-center justify-center rounded border border-[#2277FF] bg-white text-sm font-bold tracking-[-0.16px] text-[#2277FF]">{{ $p }}</span>
            @else
                <a href="{{ $paginator->url($p) }}"
                   class="flex h-8 w-8 items-center justify-center rounded border border-[#DFE3E8] bg-white text-sm font-bold tracking-[-0.16px] text-[#0A0A0A] hover:border-[#2277FF] hover:text-[#2277FF]">{{ $p }}</a>
            @endif
        @endforeach

        {{-- Berikutnya --}}
        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" aria-label="Berikutnya"
               class="flex h-8 w-8 items-center justify-center rounded border border-[#2277FF] bg-[#CDDEF9] text-[#2277FF]">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 6l6 6l-6 6" /></svg>
            </a>
        @else
            <span class="flex h-8 w-8 items-center justify-center rounded border border-[#2277FF] bg-[#CDDEF9] text-[#2277FF] opacity-50">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 6l6 6l-6 6" /></svg>
            </span>
        @endif
    </nav>
@endif