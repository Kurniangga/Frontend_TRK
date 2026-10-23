@php
    $svg  = 'width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"';
    $row  = 'flex h-10 w-full items-center gap-2 rounded-md px-3 text-base';
    $on   = 'bg-[#2277FF] text-white';       // item aktif = biru penuh (desain kaprodi)
    $onGroup = 'text-[#1F73FF]';             // grup dropdown yang sedang terbuka karena anaknya aktif
    $off  = 'text-[#111] hover:bg-[#F5F8FF]';
@endphp

<nav class="mt-[27px] flex flex-1 flex-col gap-3 overflow-y-auto px-3" aria-label="Menu kaprodi">
    @foreach (config('kaprodi.menu') as $m)
        @if (isset($m['children']))
            @php $open = collect($m['children'])->contains(fn ($c) => request()->routeIs($c['route'], $c['route'].'.*')); @endphp
            <details class="group" @if ($open) open @endif>
                <summary class="{{ $row }} {{ $open ? $onGroup.' hover:bg-[#F5F8FF]' : $off }} cursor-pointer list-none [&::-webkit-details-marker]:hidden">
                    <svg {!! $svg !!}>{!! $m['icon'] !!}</svg>
                    <span class="flex-1">{{ $m['label'] }}</span>
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="transition-transform group-open:rotate-180" aria-hidden="true"><path d="M6 9l6 6l6 -6" /></svg>
                </summary>
                <ul class="mt-1 flex flex-col gap-0.5 pl-4">
                    @foreach ($m['children'] as $c)
                        @php $cur = request()->routeIs($c['route'], $c['route'].'.*'); @endphp
                        <li>
                            <a href="{{ route($c['route']) }}" @if ($cur) aria-current="page" @endif
                               class="flex h-9 items-center rounded-md px-3 text-[15px] {{ $cur ? 'bg-[#1F73FF] text-white' : 'text-[#333] hover:bg-[#F5F8FF]' }}">{{ $c['label'] }}</a>
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

{{-- Keluar --}}
<form method="POST" action="{{ route('kaprodi.logout') }}" class="px-3 pb-[17px]">
    @csrf
    <button type="submit" class="{{ $row }} {{ $off }}">
        <svg {!! $svg !!}><path d="M14 8v-2a2 2 0 0 0 -2 -2h-7a2 2 0 0 0 -2 2v12a2 2 0 0 0 2 2h7a2 2 0 0 0 2 -2v-2" /><path d="M9 12h12l-3 -3" /><path d="M18 15l3 -3" /></svg>
        <span class="flex-1 text-left">Keluar</span>
    </button>
</form>
