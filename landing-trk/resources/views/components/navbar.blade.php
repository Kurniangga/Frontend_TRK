@php
    // Menu dropdown Kurikulum dibuat dari data (bertambah/berkurang mengikuti upload & hapus admin)
    $kurikulumMenu = collect(app(\App\Repositories\KurikulumRepository::class)->all())
        ->map(fn ($k) => [$k['nama'], route('kurikulum', ['kurikulum' => $k['id']])])
        ->all();

    // Menu tanpa dropdown: [label, href]
    // Menu dengan dropdown: [label, [[sub-label, href], ...]]
    $menu = [
        ['Tentang TRK', [
            ['Tentang TRK', route('home') . '#tentang'],
            ['Mata Kuliah', route('mata-kuliah')],
            ['Akreditasi', route('akreditasi')],
        ]],
        ['Dosen', route('home') . '#dosen'],
        ['Kurikulum', array_merge([['Kurikulum', route('kurikulum')]], $kurikulumMenu)],
        ['Kabar TRK', route('home') . '#kabar'],
        ['Prestasi', route('home') . '#prestasi'],
        ['Fasilitas', route('home') . '#fasilitas'],
        ['Layanan', route('home') . '#layanan'],
    ];
@endphp

<header class="sticky top-0 z-50 bg-white shadow-[2px_5px_15px_4px_rgba(0,0,0,0.25)]">
    <div class="mx-auto flex h-16 max-w-[1728px] items-center justify-between gap-6 px-4 md:h-[70px] md:px-[74px]">
        <a href="{{ route('home') }}" class="flex items-center">
            <img src="{{ asset('images/logo_trk.png') }}" alt="Teknologi Rekayasa Komputer" class="h-9 w-auto object-contain md:h-11">
        </a>

        {{-- Desktop --}}
        <nav class="hidden items-center gap-8 xl:flex">
            @foreach ($menu as [$label, $target])
                @if (is_array($target))
                    <div class="group relative">
                        <button type="button" class="flex items-center gap-1 text-sm font-medium text-[#292E36] hover:text-[#27F] group-hover:text-[#27F] group-focus-within:text-[#27F]">
                            {{ $label }}
                            <svg class="h-3.5 w-3.5 transition-transform group-hover:rotate-180 group-focus-within:rotate-180" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 9l6 6l6 -6"/></svg>
                        </button>
                        {{-- pt-3 menjaga hover tidak putus di celah antara tombol dan menu --}}
                        <div class="invisible absolute left-0 top-full z-50 w-56 pt-3 opacity-0 transition group-hover:visible group-hover:opacity-100 group-focus-within:visible group-focus-within:opacity-100">
                            <ul class="overflow-hidden rounded-xl bg-white py-2 shadow-[0_8px_30px_rgba(0,0,0,0.18)] ring-1 ring-black/5">
                                @foreach ($target as [$subLabel, $subHref])
                                    <li>
                                        <a href="{{ $subHref }}" class="block px-5 py-2.5 text-sm font-medium text-[#292E36] hover:bg-[#EEF4FF] hover:text-[#27F]">{{ $subLabel }}</a>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                @else
                    <a href="{{ $target }}" class="text-sm font-medium text-[#292E36] hover:text-[#27F]">{{ $label }}</a>
                @endif
            @endforeach
        </nav>

        <div class="flex items-center gap-3">
            <a href="{{ route('home') }}#layanan" class="rounded-lg bg-[#27F] px-4 py-2 text-sm font-medium text-[#FEFEFE] shadow-sm">MASUK</a>

            {{-- Mobile / tablet --}}
            <details class="relative xl:hidden">
                <summary class="flex h-10 w-10 cursor-pointer list-none items-center justify-center rounded-lg border border-gray-200 [&::-webkit-details-marker]:hidden" aria-label="Menu">
                    <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M4 6h16M4 12h16M4 18h16"/></svg>
                </summary>
                <div class="absolute right-0 top-12 max-h-[75vh] w-72 overflow-y-auto rounded-xl bg-white p-2 shadow-[0_8px_30px_rgba(0,0,0,0.25)] ring-1 ring-black/5">
                    @foreach ($menu as [$label, $target])
                        @if (is_array($target))
                            <details class="group/sub">
                                <summary class="flex cursor-pointer list-none items-center justify-between rounded-lg px-4 py-3 font-medium text-[#292E36] hover:bg-[#EEF4FF] [&::-webkit-details-marker]:hidden">
                                    {{ $label }}
                                    <svg class="h-4 w-4 transition-transform group-open/sub:rotate-180" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 9l6 6l6 -6"/></svg>
                                </summary>
                                <ul class="ml-4 border-l-2 border-[#FFD121] pl-2">
                                    @foreach ($target as [$subLabel, $subHref])
                                        <li><a href="{{ $subHref }}" class="block rounded-lg px-3 py-2.5 text-[#4B5563] hover:text-[#27F]">{{ $subLabel }}</a></li>
                                    @endforeach
                                </ul>
                            </details>
                        @else
                            <a href="{{ $target }}" class="block rounded-lg px-4 py-3 font-medium text-[#292E36] hover:bg-[#EEF4FF]">{{ $label }}</a>
                        @endif
                    @endforeach
                </div>
            </details>
        </div>
    </div>
</header>