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
    $foto = asset('images/login/gedung-polines.jpg');
@endphp

@section('content')
<div class="min-h-screen bg-[#E3E3E3] p-3 font-['Plus_Jakarta_Sans',sans-serif] sm:p-6 lg:p-[3%]">
    <div class="mx-auto flex min-h-[calc(100vh-1.5rem)] flex-col rounded-[30px] bg-white p-3 sm:min-h-[calc(100vh-3rem)] lg:min-h-[calc(100vh-6%)] lg:flex-row lg:gap-6 lg:p-[51px]">

        {{-- Panel kiri --}}
        <section class="relative flex flex-col overflow-hidden rounded-[30px] bg-[#F5F5F5] lg:w-[52%]">
            <div class="flex flex-col gap-3 p-6 sm:p-10 lg:px-12 lg:pt-[49px]">
                <img src="{{ $logo }}" alt="Logo POLINES" onerror="this.style.visibility='hidden'" class="h-11 w-[231px] object-contain object-left">
                <p class="text-[28px] font-bold leading-[38px] tracking-[-0.2px] text-[#2c2c2c] sm:text-[32px]">Selamat datang di</p>
                <h1 class="text-4xl font-bold tracking-[-0.5px] text-[#2c2c2c] sm:text-5xl xl:text-[56px] xl:leading-[68px]">Teknologi Rekayasa Komputer</h1>
                <p class="max-w-[624px] text-lg font-medium leading-6 text-[#2c2c2c] sm:text-xl">Gerbang akses terintegrasi untuk seluruh layanan digital Teknologi Rekayasa Komputer</p>
            </div>
            <div class="mt-auto aspect-[109/56] w-full overflow-hidden rounded-[30px] bg-[linear-gradient(135deg,#CDDEF9,#9DBFF0)]">
                <img src="{{ $foto }}" alt="" onerror="this.style.visibility='hidden'" class="h-full w-full object-cover">
            </div>
        </section>

        {{-- Panel kanan --}}
        <section class="relative flex flex-1 flex-col items-center justify-center gap-8 px-2 py-8 lg:py-0">
            <a href="{{ route('home') }}"
               class="flex items-center gap-1 self-start rounded-[30px] bg-[#949494] p-0.5 pr-5 text-xl font-semibold text-white transition-colors hover:bg-[#7d7d7d] lg:absolute lg:left-[47px] lg:top-[68px]">
                <span class="flex size-[42px] items-center justify-center rounded-full bg-[#A8A8A8]">
                    <x-icon name="chevron-left" :size="24" :stroke="1.5" class="text-[#FEFEFE]" />
                </span>
                Kembali
            </a>

            <form method="POST" action="{{ route('login.attempt') }}"
                  class="flex w-full max-w-[560px] flex-col items-center gap-[25px] rounded-2xl border border-[#E5E7EB] bg-white p-6 shadow-sm sm:p-8">
                @csrf

                <div class="flex w-full flex-col gap-3">
                    <h2 class="text-2xl font-bold leading-[38px] tracking-[-0.2px] text-[#111827] sm:text-[32px]">Masuk Dengan Akun POLINES</h2>
                    <p class="text-sm text-[#6B7280]">Masukkan email yang terdaftar dengan POLINES.</p>
                </div>

                @if ($errors->any())
                    <div class="w-full rounded-lg border border-red-200 bg-red-50 px-3.5 py-3 text-sm text-red-700" role="alert">
                        {{ $errors->first() }}
                    </div>
                @endif

                <label class="flex w-full flex-col gap-1">
                    <span class="text-sm font-medium tracking-[-0.16px] text-[#374151]">Email</span>
                    <input type="email" name="email" value="{{ old('email') }}" required autocomplete="username" placeholder="Masukkan email terdaftar"
                           class="w-full rounded-md border border-[#BDBDBD] bg-[#FEFEFE] px-3.5 py-[13px] text-base text-[#2c2c2c] shadow-[1px_2px_2px_0_rgba(0,0,0,0.12)_inset] outline-none placeholder:text-[#A8A8A8] focus:border-[#2277FF]">
                    <span class="text-xs tracking-[-0.12px] text-[#6B7280]">Gunakan email yang terhubung dengan POLINES.</span>
                </label>

                <label class="flex w-full flex-col gap-1">
                    <span class="text-sm font-medium tracking-[-0.16px] text-[#374151]">Sandi</span>
                    <input type="password" name="password" required autocomplete="current-password" placeholder="Masukkan sandi"
                           class="w-full rounded-md border border-[#BDBDBD] bg-[#FEFEFE] px-3.5 py-[13px] text-base text-[#2c2c2c] shadow-[1px_2px_2px_0_rgba(0,0,0,0.12)_inset] outline-none placeholder:text-[#A8A8A8] focus:border-[#2277FF]">
                </label>

                <div class="flex w-full flex-col items-center gap-2.5">
                    <button type="submit"
                            class="flex h-12 w-full items-center justify-center gap-[5px] rounded-lg bg-[#2277FF] text-base font-medium tracking-[-0.18px] text-white shadow-[0_1px_2px_rgba(0,0,0,0.12)] transition-colors hover:bg-[#1a63d9]">
                        Masuk
                        <x-icon name="arrow-right" :size="18" :stroke="2" />
                    </button>
                    <a href="{{ route('password.request') }}" class="text-sm font-medium tracking-[-0.16px] text-[#757575] hover:text-[#2277FF]">Lupa sandi ?</a>
                </div>

                <div class="flex w-full items-start gap-2 rounded-lg bg-[#F9FAFB] p-3">
                    <svg class="size-4 shrink-0 text-[#2277FF]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M11.46 20.846a12 12 0 0 1 -7.96 -14.846a12 12 0 0 0 8.5 -3a12 12 0 0 0 8.5 3a12 12 0 0 1 -.09 7.06" /><path d="M15 19l2 2l4 -4" /></svg>
                    <p class="flex-1 text-xs tracking-[-0.12px] text-[#6B7280]">Gunakan akun yang sudah terdaftar dengan POLINES.</p>
                </div>
            </form>

            <p class="text-center text-base font-medium tracking-[-0.18px] text-[#757575] lg:absolute lg:bottom-[65px]">Perlu bantuan? Hubungi admin TRK.</p>
        </section>
    </div>
</div>
@endsection
