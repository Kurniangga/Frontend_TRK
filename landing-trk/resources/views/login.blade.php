@extends('layouts.app')

@section('title', 'Masuk - Teknologi Rekayasa Komputer')
@section('description', 'Masuk dengan akun POLINES')

@php
    /*
     * FOTO (taruh file di public/images/login/):
     *   logo-polines.png  -> logo POLINES (kiri atas)
     *   gedung-polines.jpg -> foto gedung di bagian bawah panel kiri
     */
    $logo = asset('images/login/logo-polines.png');
    $foto = asset('images/login/gedung-polines.png');
@endphp

@section('content')
{{-- Desktop (lg ke atas): tepat satu layar, tanpa scroll. Di bawah lg: menumpuk & bisa di-scroll. --}}
<div class="bg-[#E3E3E3] p-3 font-['Plus_Jakarta_Sans',sans-serif] sm:p-5 lg:h-screen lg:overflow-hidden lg:p-[clamp(12px,2.6vh,32px)]">
    <div class="mx-auto flex flex-col rounded-[24px] bg-white p-3 lg:h-full lg:flex-row lg:gap-[clamp(16px,2vw,32px)] lg:rounded-[30px] lg:p-[clamp(12px,2.6vh,32px)]">

        {{-- Panel kiri --}}
        <section class="flex flex-col overflow-hidden rounded-[20px] bg-[#F5F5F5] lg:w-[49%] lg:shrink-0 lg:rounded-[24px]">
            <div class="flex flex-col items-start gap-[clamp(8px,1.5vh,16px)] p-5 sm:p-8 lg:px-[clamp(24px,2.6vw,48px)] lg:pb-[clamp(8px,1.5vh,16px)] lg:pt-[clamp(16px,3.2vh,40px)]">
                {{-- 1. Tombol kembali --}}
                <a href="{{ route('home') }}"
                   class="flex h-8 items-center gap-1 rounded-full bg-[#949494] p-0.5 pr-4 text-sm font-semibold text-white transition-colors hover:bg-[#7d7d7d]">
                    <span class="flex size-7 items-center justify-center rounded-full bg-[#A8A8A8]">
                        <x-icon name="chevron-left" :size="16" :stroke="1.8" class="text-[#FEFEFE]" />
                    </span>
                    Kembali
                </a>

                {{-- 2. Tempat foto/logo --}}
                <img src="{{ $logo }}" alt="Logo POLINES" onerror="this.style.visibility='hidden'" class="h-[clamp(28px,4.6vh,44px)] w-auto max-w-[231px] object-contain object-left">

                {{-- 3. Teks --}}
                <div class="flex flex-col gap-[clamp(4px,1vh,12px)]">
                    <p class="text-lg font-bold tracking-[-0.2px] text-[#2c2c2c] lg:text-[clamp(16px,min(1.5vw,2.6vh),26px)]">Selamat datang di</p>
                    <h1 class="text-3xl font-bold leading-tight tracking-[-0.5px] text-[#2c2c2c] lg:text-[clamp(24px,min(2.7vw,5vh),48px)]">Teknologi Rekayasa Komputer</h1>
                    <p class="max-w-[560px] text-sm font-medium leading-snug text-[#2c2c2c] lg:text-[clamp(12px,min(1vw,1.9vh),17px)]">Gerbang akses terintegrasi untuk seluruh layanan digital Teknologi Rekayasa Komputer</p>
                </div>
            </div>

            {{-- 4. Foto gedung: mengisi sisa tinggi layar --}}
            <div class="mt-auto aspect-[109/56] w-full overflow-hidden bg-[linear-gradient(135deg,#CDDEF9,#9DBFF0)] lg:mt-0 lg:aspect-auto lg:min-h-0 lg:flex-1">
                <img src="{{ $foto }}" alt="" onerror="this.style.visibility='hidden'" class="h-full w-full object-cover object-top">
            </div>
        </section>

        {{-- Panel kanan --}}
        <section class="relative flex flex-1 flex-col items-center justify-center gap-6 px-2 py-8 lg:min-h-0 lg:py-0">
            <form method="POST" action="{{ route('login.attempt') }}"
                  class="flex w-full max-w-[440px] flex-col gap-[clamp(10px,1.8vh,18px)] rounded-2xl border border-[#E5E7EB] bg-white p-5 shadow-sm sm:p-6 lg:p-[clamp(16px,2.6vh,28px)]">
                @csrf

                <div class="flex flex-col gap-1">
                    <h2 class="text-xl font-bold leading-tight tracking-[-0.2px] text-[#111827] lg:text-[clamp(18px,min(1.5vw,2.8vh),26px)]">Masuk Dengan Akun POLINES</h2>
                    <p class="text-xs text-[#6B7280] lg:text-[13px]">Masukkan email yang terdaftar dengan POLINES.</p>
                </div>

                @if ($errors->any())
                    <div class="w-full rounded-lg border border-red-200 bg-red-50 px-3 py-2 text-xs text-red-700" role="alert">
                        {{ $errors->first() }}
                    </div>
                @endif

                <label class="flex w-full flex-col gap-1">
                    <span class="text-[13px] font-medium tracking-[-0.16px] text-[#374151]">Email</span>
                    <input type="email" name="email" value="{{ old('email') }}" required autocomplete="username" placeholder="Masukkan email terdaftar"
                           class="h-10 w-full rounded-md border border-[#BDBDBD] bg-[#FEFEFE] px-3 text-sm text-[#2c2c2c] shadow-[1px_2px_2px_0_rgba(0,0,0,0.12)_inset] outline-none placeholder:text-[#A8A8A8] focus:border-[#2277FF]">
                    <span class="text-[11px] tracking-[-0.12px] text-[#6B7280]">Gunakan email yang terhubung dengan POLINES.</span>
                </label>

                <label class="flex w-full flex-col gap-1">
                    <span class="text-[13px] font-medium tracking-[-0.16px] text-[#374151]">Sandi</span>
                    <input type="password" name="password" required autocomplete="current-password" placeholder="Masukkan sandi"
                           class="h-10 w-full rounded-md border border-[#BDBDBD] bg-[#FEFEFE] px-3 text-sm text-[#2c2c2c] shadow-[1px_2px_2px_0_rgba(0,0,0,0.12)_inset] outline-none placeholder:text-[#A8A8A8] focus:border-[#2277FF]">
                </label>

                <div class="flex w-full flex-col items-center gap-2">
                    <button type="submit"
                            class="flex h-10 w-full items-center justify-center gap-1 rounded-lg bg-[#2277FF] text-sm font-medium tracking-[-0.18px] text-white shadow-[0_1px_2px_rgba(0,0,0,0.12)] transition-colors hover:bg-[#1a63d9]">
                        Masuk
                        <x-icon name="arrow-right" :size="16" :stroke="2" />
                    </button>
                    <a href="{{ route('password.request') }}" class="text-xs font-medium tracking-[-0.16px] text-[#757575] hover:text-[#2277FF]">Lupa sandi ?</a>
                </div>

                <div class="flex w-full items-center gap-2 rounded-lg bg-[#F9FAFB] px-3 py-2.5">
                    <svg class="size-4 shrink-0 text-[#2277FF]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M11.46 20.846a12 12 0 0 1 -7.96 -14.846a12 12 0 0 0 8.5 -3a12 12 0 0 0 8.5 3a12 12 0 0 1 -.09 7.06" /><path d="M15 19l2 2l4 -4" /></svg>
                    <p class="flex-1 text-[11px] tracking-[-0.12px] text-[#6B7280]">Gunakan akun yang sudah terdaftar dengan POLINES.</p>
                </div>
            </form>

            <p class="text-center text-sm font-medium tracking-[-0.18px] text-[#757575] lg:absolute lg:bottom-[clamp(12px,3vh,40px)]">Perlu bantuan? Hubungi admin TRK.</p>
        </section>
    </div>
</div>
@endsection