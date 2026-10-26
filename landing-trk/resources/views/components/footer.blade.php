@php
    $A = 'https://api.builder.io/api/v1/image/assets/TEMP/';

    $socials = ['instagram', 'dribbble', 'twitter', 'youtube'];

    $footerCols = [
        'Quick Links' => ['Portfolio', 'Blogs', 'About', 'Press', 'Careers'],
        'Services' => ['UX/UI Design', 'App Development', 'Web Development', 'Quality Assurance', 'Machine Learning'],
    ];
@endphp

{{-- Footer --}}
<footer class="border-t-4 border-[#FFD121] bg-[#0043AD] font-['Inter',sans-serif] text-[#D1D5DB]">
    <div class="mx-auto max-w-[1200px] px-4 py-7 sm:px-6 md:px-8 lg:px-10 lg:py-9">

        {{-- Grid Kolom Footer --}}
        <div class="grid grid-cols-1 gap-7 sm:grid-cols-2 lg:grid-cols-5 lg:gap-6 xl:gap-8">

            {{-- Kolom 1 & 2: Brand, Alamat, Sosial Media --}}
            <div class="flex flex-col gap-3.5 sm:col-span-2 lg:col-span-2">
                {{-- Logo POLINES & TRK --}}
                <div class="flex flex-wrap items-center gap-3.5 sm:gap-5">
                    <img src="{{ $A }}3d940e9141a4ffa2173204599e7c84d1f7f2e13c?width=200" alt="Logo POLINES" class="h-[44px] w-auto sm:h-[50px] md:h-[54px] object-contain">

                    <div class="flex items-center gap-2 sm:gap-2.5">
                        <img src="{{ asset('images/logo_trk remove.png') }}" alt="Logo TRK" class="h-[36px] w-auto sm:h-[42px] md:h-[46px] object-contain">
                        <div class="flex flex-col font-['Plus_Jakarta_Sans',sans-serif] font-extrabold uppercase leading-[1.08] tracking-tight text-white">
                            <span class="text-[11px] sm:text-xs md:text-[13px] font-extrabold">TEKNOLOGI</span>
                            <span class="text-[11px] sm:text-xs md:text-[13px] font-extrabold">REKAYASA KOMPUTER</span>
                        </div>
                    </div>
                </div>

                {{-- Alamat Instansi --}}
                <div class="max-w-[340px] font-['Plus_Jakarta_Sans',sans-serif] text-[#F5F5F5]">
                    <div class="text-sm font-bold tracking-wide sm:text-base">POLITEKNIK NEGERI SEMARANG</div>
                    <div class="mt-1 text-xs font-normal leading-relaxed text-[#D1D5DB] sm:text-sm">
                        Jl. Prof. H. Soedarto, S.H., Tembalang, Semarang, Jawa Tengah 50275
                    </div>
                </div>

                {{-- Sosial Media --}}
                <div class="mt-0.5 flex items-center gap-2 text-white">
                    @foreach ($socials as $icon)
                        <span class="flex size-7 items-center justify-center rounded-full border border-white/20 transition-colors hover:bg-white/10 sm:size-8">
                            <x-icon :name="$icon" :size="14" />
                        </span>
                    @endforeach
                </div>
            </div>

            {{-- Kolom 3 & 4: Quick Links & Services (di mobile bersebelahan 2 kolom agar hemat ruang) --}}
            <div class="grid grid-cols-2 gap-4 sm:col-span-2 sm:grid-cols-2 lg:col-span-2 lg:gap-6">
                @foreach ($footerCols as $heading => $items)
                    <div class="flex flex-col gap-2 sm:gap-2.5">
                        <div class="text-sm font-bold text-white sm:text-base">{{ $heading }}</div>
                        <div class="flex flex-col gap-1.5 sm:gap-2">
                            @foreach ($items as $item)
                                <a href="#" class="text-xs text-[#CBD5E1] transition-colors hover:text-white sm:text-sm">{{ $item }}</a>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Kolom 5: Reach Us --}}
            <div class="flex flex-col gap-2 sm:col-span-2 sm:gap-2.5 lg:col-span-1">
                <div class="text-sm font-bold text-white sm:text-base">Reach us</div>
                <div class="flex flex-col gap-2 text-xs text-[#CBD5E1] sm:text-sm">
                    <div class="flex items-center gap-2.5">
                        <x-icon name="mail" :size="15" class="shrink-0 text-white/70" />
                        <span class="break-all">sekretariat@polines.ac.id</span>
                    </div>
                    <div class="flex items-center gap-2.5">
                        <x-icon name="device-mobile" :size="15" class="shrink-0 text-white/70" />
                        <span>(024) 7473417</span>
                    </div>
                    <div class="flex items-start gap-2.5">
                        <x-icon name="map-pin" :size="15" class="mt-0.5 shrink-0 text-white/70" />
                        <span>Gedung TI, Tembalang</span>
                    </div>
                </div>
            </div>

        </div>

        {{-- Divider & Bottom Copyright --}}
        <div class="mt-6 border-t border-white/15 pt-4 sm:mt-7">
            <div class="flex flex-col items-center justify-between gap-2.5 text-[11px] text-[#A0AEC0] sm:text-xs md:flex-row">
                <span>© 2026 Teknologi Rekayasa Komputer POLINES. All rights reserved</span>
                <span class="flex flex-wrap items-center justify-center gap-2 md:gap-3">
                    <a href="#" class="hover:text-white transition-colors">Terms &amp; Conditions</a>
                    <span>|</span>
                    <a href="#" class="hover:text-white transition-colors">Privacy Policy</a>
                    <span>|</span>
                    <a href="#" class="hover:text-white transition-colors">Sitemap</a>
                    <span>|</span>
                    <a href="#" class="hover:text-white transition-colors">Disclaimer</a>
                </span>
            </div>
        </div>

    </div>
</footer>