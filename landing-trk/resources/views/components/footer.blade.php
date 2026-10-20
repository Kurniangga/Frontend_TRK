@php
    $A = 'https://api.builder.io/api/v1/image/assets/TEMP/'; // ganti dengan asset('images/...') setelah gambar diunduh

    $socials = ['instagram', 'dribbble', 'twitter', 'youtube'];

    $footerCols = [
        'Quick Links' => ['Portfolio', 'Blogs', 'About', 'Press', 'Careers'],
        'Services' => ['UX/UI Design', 'App Development', 'Web Development', 'Quality Assurance', 'Machine Learning'],
    ];
@endphp

{{-- Footer --}}
<footer class="border-t-4 border-[#FFD121] bg-[#0043AD] font-['Inter',sans-serif] text-[#D1D5DB]">
    <div class="mx-auto flex max-w-[1440px] flex-col justify-between gap-10 px-6 py-12 lg:flex-row lg:gap-8 xl:px-4 xl:py-16">

        {{-- Kolom 1: Logo & Sosial Media --}}
        <div class="flex flex-col gap-6">
            <div class="flex items-center gap-5">
                <img src="{{ $A }}3d940e9141a4ffa2173204599e7c84d1f7f2e13c?width=200" alt="Logo POLINES" class="h-[70px] w-auto md:h-[85px]">
                <img src="{{ $A }}fa022d9c39750c88475b8c93ed284ed17b68405b?width=200" alt="Logo TI" class="h-[70px] w-auto md:h-[85px]">
            </div>
            <div class="flex gap-4 text-white">
                @foreach ($socials as $icon)
                    <span class="flex h-8 w-8 items-center justify-center rounded-full border border-white/20 hover:bg-white/10 transition-colors"><x-icon :name="$icon" :size="16" /></span>
                @endforeach
            </div>
        </div>

        {{-- Kolom 2: Alamat Instansi --}}
        <div class="max-w-[260px] font-['Plus_Jakarta_Sans',sans-serif] text-[#F5F5F5]">
            <div class="text-base font-bold tracking-wide md:text-lg">POLITEKNIK NEGERI SEMARANG</div>
            <div class="mt-4 text-sm font-medium leading-relaxed md:text-base">Jl. Prof. H. Soedarto, S.H.,<br>Semarang, 50275, Jawa Tengah</div>
        </div>

        {{-- Kolom 3 & 4: Quick Links & Services --}}
        @foreach ($footerCols as $heading => $items)
            <div class="flex flex-col gap-4">
                <div class="text-lg font-bold text-white">{{ $heading }}</div>
                <div class="flex flex-col gap-3">
                    @foreach ($items as $item)
                        <a href="#" class="text-sm hover:text-white transition-colors">{{ $item }}</a>
                    @endforeach
                </div>
            </div>
        @endforeach

        {{-- Kolom 5: Reach Us --}}
        <div class="flex flex-col gap-4">
            <div class="text-lg font-bold text-white">Reach us</div>
            <div class="flex flex-col gap-4 text-sm">
                <div class="flex items-center gap-3"><x-icon name="mail" :size="20" class="text-white/70" />sekretariat&#64;polines.ac.id</div>
                <div class="flex items-center gap-3"><x-icon name="device-mobile" :size="20" class="text-white/70" />+91 98765 43210</div>
                <div class="flex items-start gap-3"><x-icon name="map-pin" :size="20" class="shrink-0 text-white/70" /><div>Jl. Prof. H. Soedarto, S.H.,<br>Semarang, 50275, Jawa Tengah</div></div>
            </div>
        </div>

    </div>

    {{-- Bottom Copyright --}}
    <div class="mx-auto flex max-w-[1440px] flex-col items-center justify-between gap-4 px-6 pb-8 pt-4 text-xs md:flex-row xl:px-4">
        <span>© 2026 Teknologi Rekayasa Komputer POLINES. All rights reserved</span>
        <span class="flex flex-wrap items-center gap-2 md:gap-3">Terms &amp; Conditions <span class="hidden md:inline">|</span> Privacy Policy <span class="hidden md:inline">|</span> Sitemap <span class="hidden md:inline">|</span> Disclaimer</span>
    </div>
</footer>