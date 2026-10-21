@props(['back' => null])

@php
    /*
     * FOTO (taruh file di public/images/login/):
     *   logo-polines-login.png -> logo POLINES (di atas teks "Selamat datang di")
     *   gedung-polines.png       -> foto gedung di bagian bawah panel kiri
     */
    $logo = asset('images/login/logo-polines-login.png');
    $foto = asset('images/login/gedung-polines.png');
@endphp

{{-- Desktop (lg ke atas): tepat satu layar, tanpa scroll. Di bawah lg: menumpuk & bisa di-scroll. --}}
<div class="bg-[#E3E3E3] p-3 font-['Plus_Jakarta_Sans',sans-serif] sm:p-5 lg:h-screen lg:overflow-hidden lg:p-[clamp(12px,2.6vh,32px)]">
    <div class="mx-auto flex flex-col rounded-[24px] bg-white p-3 lg:h-full lg:flex-row lg:gap-[clamp(16px,2vw,32px)] lg:rounded-[30px] lg:p-[clamp(12px,2.6vh,32px)]">

        {{-- Panel kiri --}}
        <section class="flex flex-col overflow-hidden rounded-[20px] bg-[#F5F5F5] lg:w-[49%] lg:shrink-0 lg:rounded-[24px]">
            <div class="flex flex-col items-start gap-[clamp(8px,1.5vh,16px)] p-5 sm:p-8 lg:px-[clamp(24px,2.6vw,48px)] lg:pb-[clamp(8px,1.5vh,16px)] lg:pt-[clamp(16px,3.2vh,40px)]">

                {{-- 1. Tombol kembali --}}
                @if ($back)
                    <a href="{{ $back }}"
                       class="flex h-8 items-center gap-1 rounded-full bg-[#949494] p-0.5 pr-4 text-sm font-semibold text-white transition-colors hover:bg-[#7d7d7d]">
                        <span class="flex size-7 items-center justify-center rounded-full bg-[#A8A8A8]">
                            <x-icon name="chevron-left" :size="16" :stroke="1.8" class="text-[#FEFEFE]" />
                        </span>
                        Kembali
                    </a>
                @endif

                {{-- 2. Tempat logo POLINES --}}
                <div class="flex h-[clamp(28px,4.6vh,44px)] items-center">
                    <span class="flex h-full items-center rounded-md border border-dashed border-[#B8C4D6] px-4 text-[11px] font-medium text-[#8A94A6]">Logo POLINES</span>
                    <img src="{{ $logo }}" alt="Logo POLINES"
                         onload="this.classList.remove('hidden'); this.previousElementSibling.remove()"
                         onerror="this.remove()"
                         class="hidden h-full w-auto max-w-[231px] object-contain object-left">
                </div>

                {{-- 3. Teks --}}
                <div class="flex flex-col gap-[clamp(4px,1vh,12px)]">
                    <p class="text-lg font-bold tracking-[-0.2px] text-[#2c2c2c] lg:text-[clamp(16px,min(1.5vw,2.6vh),26px)]">Selamat datang di</p>
                    <h1 class="text-3xl font-bold leading-tight tracking-[-0.5px] text-[#2c2c2c] lg:text-[clamp(24px,min(2.7vw,5vh),48px)]">Teknologi Rekayasa Komputer</h1>
                    <p class="max-w-[560px] text-sm font-medium leading-snug text-[#2c2c2c] lg:text-[clamp(12px,min(1vw,1.9vh),17px)]">Gerbang akses terintegrasi untuk seluruh layanan digital Teknologi Rekayasa Komputer</p>
                </div>
            </div>

            {{-- 4. Foto gedung: menyatu dengan background #F5F5F5 tanpa terpotong --}}
            <div class="mt-auto flex w-full justify-center overflow-hidden bg-[#F5F5F5] lg:mt-0 lg:min-h-0 lg:flex-1">
                <img src="{{ $foto }}" alt="Gedung POLINES" onerror="this.style.visibility='hidden'" class="h-full w-full object-contain object-bottom">
            </div>
        </section>

        {{-- Panel kanan --}}
        <section class="relative flex flex-1 flex-col items-center justify-center gap-6 px-2 py-8 lg:min-h-0 lg:py-0">
            <div class="w-full max-w-[440px]">{{ $slot }}</div>
            <p class="text-center text-sm font-medium tracking-[-0.18px] text-[#757575] lg:absolute lg:bottom-[clamp(12px,3vh,40px)]">Perlu bantuan? Hubungi admin TRK.</p>
        </section>
    </div>
</div>