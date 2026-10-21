@php
    $svg = 'fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"';
    $item = 'flex w-full items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition-colors';
    $on   = 'bg-[#E4EDFB] text-[#226CFF]';
    $off  = 'text-[#2E2E2E] hover:bg-[#F1F5FD]';
@endphp

<nav class="flex flex-1 flex-col gap-1 overflow-y-auto px-4 py-4" aria-label="Menu admin">
    @foreach (config('admin.menu') as $m)
        @if (isset($m['children']))
            @php $open = collect($m['children'])->contains(fn ($c) => request()->routeIs($c['route'])); @endphp
            <details class="group" @if ($open) open @endif>
                <summary class="{{ $item }} {{ $open ? $on : $off }} cursor-pointer list-none [&::-webkit-details-marker]:hidden">
                    <svg class="size-5 shrink-0" viewBox="0 0 24 24" {!! $svg !!}>{!! $m['icon'] !!}</svg>
                    <span class="flex-1">{{ $m['label'] }}</span>
                    <svg class="size-4 transition-transform group-open:rotate-180" viewBox="0 0 24 24" {!! $svg !!}><path d="M6 9l6 6l6 -6"/></svg>
                </summary>
                <ul class="mb-1 ml-5 mt-1 flex flex-col gap-0.5 border-l border-[#D9E3F5] pl-3">
                    @foreach ($m['children'] as $c)
                        <li>
                            <a href="{{ route($c['route']) }}" @if (request()->routeIs($c['route'])) aria-current="page" @endif
                               class="block rounded-lg px-3 py-2 text-sm {{ request()->routeIs($c['route']) ? 'bg-[#E4EDFB] font-semibold text-[#226CFF]' : 'text-[#4B5563] hover:bg-[#F1F5FD]' }}">{{ $c['label'] }}</a>
                        </li>
                    @endforeach
                </ul>
            </details>
        @else
            <a href="{{ route($m['route']) }}" @if (request()->routeIs($m['route'])) aria-current="page" @endif
               class="{{ $item }} {{ request()->routeIs($m['route']) ? $on : $off }}">
                <svg class="size-5 shrink-0" viewBox="0 0 24 24" {!! $svg !!}>{!! $m['icon'] !!}</svg>
                {{ $m['label'] }}
            </a>
        @endif
    @endforeach
</nav>

{{-- Keluar --}}
<form method="POST" action="{{ route('admin.logout') }}" class="border-t border-[#E5E7EB] p-4">
    @csrf
    <button type="submit" class="{{ $item }} text-[#FF3F3F] hover:bg-[#FFF1F1]">
        <svg class="size-5 shrink-0" viewBox="0 0 24 24" {!! $svg !!}><path d="M14 8v-2a2 2 0 0 0 -2 -2h-7a2 2 0 0 0 -2 2v12a2 2 0 0 0 2 2h7a2 2 0 0 0 2 -2v-2"/><path d="M9 12h12l-3 -3"/><path d="M18 15l3 -3"/></svg>
        Keluar
    </button>
</form>
