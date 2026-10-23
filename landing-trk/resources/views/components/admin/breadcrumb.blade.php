{{--
    resources/views/components/admin/breadcrumb.blade.php
    Pemakaian:
        <x-admin.breadcrumb :items="[['Dashboard', 'admin.dashboard'], ['Kurikulum'], ['Mata Kuliah']]" />
    Tiap item: [label, nama route]. Tanpa route (atau item terakhir) = teks biasa, tidak bisa diklik.
--}}
@props(['items' => []])

<nav aria-label="Breadcrumb" {{ $attributes->class('text-xl font-medium leading-[30px] tracking-[-0.15px] text-[#A8A8A8] sm:text-2xl') }}>
    <ol class="flex flex-wrap items-center gap-x-2">
        @foreach ($items as $item)
            @php($last = $loop->last)
            <li class="flex items-center gap-x-2">
                @if (! $last && ! empty($item[1]))
                    <a href="{{ route($item[1], $item[2] ?? []) }}" class="transition-colors hover:text-[#2277FF] hover:underline">{{ $item[0] }}</a>
                @else
                    <span @if ($last) aria-current="page" @endif>{{ $item[0] }}</span>
                @endif
                @unless ($last)<span aria-hidden="true">/</span>@endunless
            </li>
        @endforeach
    </ol>
</nav>
