@php
    $svg  = 'width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"';
    $row  = 'flex h-10 w-full items-center gap-2 rounded-md px-3 text-base';
    // Sidebar gradasi biru (#1F66E5 -> #1550BD -> #0B2F7A): teks putih, item aktif = kotak putih bertulisan biru #1F66E5.
    $on      = 'bg-white font-semibold text-[#1F66E5]';   // menu aktif
    $onGroup = 'bg-white/15 text-white';                  // grup dropdown yang terbuka karena anaknya aktif
    $off     = 'text-white/85 hover:bg-white/10 hover:text-white';
@endphp

<nav class="mt-[27px] flex flex-1 flex-col gap-3 overflow-y-auto px-3" aria-label="Menu kaprodi">
    @foreach (config('kaprodi.menu') as $m)
        @if (isset($m['children']))
            @php $open = collect($m['children'])->contains(fn ($c) => request()->routeIs($c['route'], $c['route'].'.*')); @endphp
            <details class="group" @if ($open) open @endif>
                <summary class="{{ $row }} {{ $open ? $onGroup : $off }} cursor-pointer list-none [&::-webkit-details-marker]:hidden">
                    <svg {!! $svg !!}>{!! $m['icon'] !!}</svg>
                    <span class="flex-1">{{ $m['label'] }}</span>
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="transition-transform group-open:rotate-180" aria-hidden="true"><path d="M6 9l6 6l6 -6" /></svg>
                </summary>
                <ul class="mt-1 flex flex-col gap-0.5 pl-4">
                    @foreach ($m['children'] as $c)
                        @php $cur = request()->routeIs($c['route'], $c['route'].'.*'); @endphp
                        <li>
                            <a href="{{ route($c['route']) }}" @if ($cur) aria-current="page" @endif
                               class="flex h-9 items-center rounded-md px-3 text-[15px] {{ $cur ? $on : 'text-white/80 hover:bg-white/10 hover:text-white' }}">{{ $c['label'] }}</a>
                        </li>
                    @endforeach
                </ul>
            </details>
        @else
            @php $cur = request()->routeIs($m['route'], $m['route'].'.*'); @endphp
            <a href="{{ route($m['route']) }}" @if ($cur) aria-current="page" @endif class="{{ $row }} {{ $cur ? $on : $off }}">
                <svg {!! $svg !!}>{!! $m['icon'] !!}</svg>
                <span class="flex-1">{{ $m['label'] }}</span>
            </a>
        @endif
    @endforeach
</nav>
