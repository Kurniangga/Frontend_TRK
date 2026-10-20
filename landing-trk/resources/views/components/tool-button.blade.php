@props(['label', 'icon', 'action' => null])

@php
    $icons = [
        'menu' => 'M4 6l16 0 M4 12l16 0 M4 18l16 0',
        'minus' => 'M5 12l14 0',
        'plus' => 'M12 5l0 14 M5 12l14 0',
        'download' => 'M4 17v2a2 2 0 0 0 2 2h12a2 2 0 0 0 2 -2v-2 M7 11l5 5l5 -5 M12 4l0 12',
        'printer' => 'M17 17h2a2 2 0 0 0 2 -2v-4a2 2 0 0 0 -2 -2h-14a2 2 0 0 0 -2 2v4a2 2 0 0 0 2 2h2 M17 9v-4a2 2 0 0 0 -2 -2h-6a2 2 0 0 0 -2 2v4 M7 15a2 2 0 0 1 2 -2h6a2 2 0 0 1 2 2v4a2 2 0 0 1 -2 2h-6a2 2 0 0 1 -2 -2l0 -4',
        'maximize' => 'M16 4l4 0l0 4 M14 10l6 -6 M8 20l-4 0l0 -4 M4 20l6 -6 M16 20l4 0l0 -4 M14 14l6 6 M8 4l-4 0l0 4 M4 4l6 6',
    ];
@endphp

<button
    type="button"
    aria-label="{{ $label }}"
    @if ($action) data-action="{{ $action }}" @endif
    class="flex size-10 shrink-0 items-center justify-center rounded-[10px] border border-[#547BB6] bg-[#214D99] text-white transition-colors hover:bg-[#2c5cb3]"
>
    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none"
         stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
        <path d="{{ $icons[$icon] }}" />
    </svg>
</button>
