@extends('layouts.app')

@section('title', 'Pemulihan Akun - Teknologi Rekayasa Komputer')
@section('description', 'Pemulihan akun POLINES')

@php
    // $step: 1 = email, 2 = OTP, 3 = sandi baru, 4 = berhasil
    $steps = ['Email', 'Verifikasi OTP', 'Kata Sandi Baru'];
    $done = $step === 4;

    $card  = 'flex w-full flex-col gap-[clamp(10px,1.8vh,16px)] rounded-2xl border border-[#E5E7EB] bg-white p-5 shadow-sm lg:p-[clamp(16px,2.6vh,26px)]';
    $title = 'text-xl font-bold leading-tight tracking-[-0.2px] text-[#111827] lg:text-[clamp(18px,min(1.5vw,2.8vh),26px)]';
    $desc  = 'text-xs text-[#6B7280] lg:text-[13px]';
    $label = 'text-[13px] font-medium tracking-[-0.16px] text-[#374151]';
    $input = 'h-10 w-full rounded-md border border-[#BDBDBD] bg-[#FEFEFE] px-3 text-sm text-[#2c2c2c] shadow-[1px_2px_2px_0_rgba(0,0,0,0.12)_inset] outline-none placeholder:text-[#A8A8A8] focus:border-[#2277FF]';
    $hint  = 'text-[11px] tracking-[-0.12px] text-[#6B7280]';
    $btn   = 'flex h-10 w-full items-center justify-center gap-1 rounded-lg bg-[#2277FF] text-sm font-medium tracking-[-0.18px] text-white shadow-[0_1px_2px_rgba(0,0,0,0.12)] transition-colors hover:bg-[#1a63d9]';
    $note  = 'flex w-full items-start gap-2 rounded-lg bg-[#F9FAFB] px-3 py-2.5';
    $tile  = 'flex size-9 items-center justify-center rounded-lg bg-[#EEF4FF] text-[#2277FF]';
    $svg   = 'fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"';
@endphp

@section('content')
<x-auth-layout :back="$done ? null : route('login')">

    {{-- Judul + stepper --}}
    <div class="mb-3 flex items-center justify-between lg:mb-[clamp(8px,1.6vh,16px)]">
        <p class="text-sm font-bold tracking-[-0.2px] text-[#023389]">PEMULIHAN AKUN</p>
        <p class="text-xs text-[#6B7280]">{{ $done ? 'Selesai' : "Langkah $step dari 3" }}</p>
    </div>
    <ol class="mb-3 flex items-center justify-between lg:mb-[clamp(8px,1.6vh,16px)]">
        @foreach ($steps as $i => $name)
            @php $n = $i + 1; $on = $n <= $step; @endphp
            <li class="flex items-center gap-2 text-xs {{ $on ? 'font-medium text-[#023389]' : 'text-[#6B7280]' }}">
                <span class="flex size-5 items-center justify-center rounded-full border text-[11px] font-semibold
                    {{ $on ? 'border-[#2277FF] bg-[#2277FF] text-white' : 'border-[#BDBDBD] bg-white text-[#6B7280]' }}">
                    @if ($done)
                        <svg class="size-3" viewBox="0 0 24 24" {!! $svg !!} stroke-width="3"><path d="M5 12l5 5l10 -10"/></svg>
                    @else
                        {{ $n }}
                    @endif
                </span>
                {{ $name }}
            </li>
        @endforeach
    </ol>

    {{-- Pesan error (validasi / OTP salah) --}}
    @php $error = $errors->first(); @endphp

    {{-- ============ TAHAP 1: EMAIL ============ --}}
    @if ($step === 1)
        <form method="POST" action="{{ route('password.email') }}" class="{{ $card }}">
            @csrf
            <span class="{{ $tile }}"><svg class="size-[18px]" viewBox="0 0 24 24" {!! $svg !!}><rect x="3" y="5" width="18" height="14" rx="2"/><path d="M3 7l9 6l9 -6"/></svg></span>
            <div class="flex flex-col gap-1">
                <h2 class="{{ $title }}">Lupa Kata Sandi?</h2>
                <p class="{{ $desc }}">Masukkan email yang terdaftar dengan POLINES.</p>
            </div>
            @if ($error)<div class="rounded-lg border border-red-200 bg-red-50 px-3 py-2 text-xs text-red-700" role="alert">{{ $error }}</div>@endif
            <label class="flex flex-col gap-1">
                <span class="{{ $label }}">Email</span>
                <input type="email" name="email" value="{{ old('email', session('pemulihan.email')) }}" required autocomplete="username" placeholder="Masukkan email terdaftar" class="{{ $input }}">
                <span class="{{ $hint }}">Gunakan email yang terdaftar dengan POLINES.</span>
            </label>
            <button type="submit" class="{{ $btn }}">Kirim kode OTP <x-icon name="arrow-right" :size="16" :stroke="2" /></button>
            <div class="{{ $note }}">
                <svg class="mt-0.5 size-4 shrink-0 text-[#2277FF]" viewBox="0 0 24 24" {!! $svg !!}><path d="M12 3l8 3v6c0 5 -3.5 8 -8 9c-4.5 -1 -8 -4 -8 -9v-6z"/></svg>
                <p class="flex-1 {{ $hint }}">Kode OTP hanya dikirim ke email terdaftar. Pastikan kamu masih dapat mengakses email tersebut.</p>
            </div>
        </form>

    {{-- ============ TAHAP 2: OTP ============ --}}
    @elseif ($step === 2)
        <form method="POST" action="{{ route('password.otp') }}" id="form-otp" class="{{ $card }}">
            @csrf
            <span class="{{ $tile }}"><svg class="size-[18px]" viewBox="0 0 24 24" {!! $svg !!}><rect x="3" y="5" width="18" height="14" rx="2"/><path d="M3 7l9 6l9 -6"/></svg></span>
            <div class="flex flex-col gap-1">
                <h2 class="{{ $title }}">Masukkan kode OTP</h2>
                <p class="{{ $desc }}">Masukkan 6 digit kode OTP yang telah kami kirimkan ke</p>
                <div class="flex items-center justify-between gap-2">
                    <span class="truncate text-xs font-semibold text-[#111827]">{{ $email }}</span>
                    <button type="submit" form="form-ubah-email" class="shrink-0 text-xs font-medium text-[#2277FF] hover:underline">Ubah email</button>
                </div>
            </div>
            @if ($error)<div class="rounded-lg border border-red-200 bg-red-50 px-3 py-2 text-xs text-red-700" role="alert">{{ $error }}</div>@endif
            @if (! empty($otpSimulasi))
                <div class="rounded-lg border border-amber-200 bg-amber-50 px-3 py-2 text-xs text-amber-800">Mode simulasi (APP_DEBUG): kode OTP = <b>{{ $otpSimulasi }}</b></div>
            @endif

            <div class="flex flex-col gap-2">
                <span class="{{ $label }}">Kode OTP</span>
                <div class="flex justify-between gap-2" id="otp-boxes">
                    @for ($i = 0; $i < 6; $i++)
                        <input type="text" inputmode="numeric" maxlength="1" autocomplete="{{ $i === 0 ? 'one-time-code' : 'off' }}" placeholder="0" aria-label="Digit {{ $i + 1 }}"
                               class="size-11 rounded-lg border border-[#BDBDBD] bg-[#F5F5F5] text-center text-base font-semibold text-[#2c2c2c] outline-none placeholder:text-[#A8A8A8] focus:border-[#2277FF] focus:bg-white">
                    @endfor
                </div>
                <input type="hidden" name="otp" id="otp-value">
                <span class="{{ $hint }}">Kode berlaku selama 5 menit.</span>
            </div>

            <button type="submit" class="{{ $btn }}">Verifikasi kode OTP <x-icon name="arrow-right" :size="16" :stroke="2" /></button>
            <p class="text-center text-xs text-[#757575]">Belum menerima kode?
                <button type="submit" form="form-kirim-ulang" class="font-medium text-[#2277FF] hover:underline">Kirim ulang OTP</button>
            </p>
            <div class="{{ $note }}">
                <svg class="mt-0.5 size-4 shrink-0 text-[#2277FF]" viewBox="0 0 24 24" {!! $svg !!}><path d="M12 3l8 3v6c0 5 -3.5 8 -8 9c-4.5 -1 -8 -4 -8 -9v-6z"/></svg>
                <p class="flex-1 {{ $hint }}">Periksa folder spam jika email belum masuk. Demi keamanan, jangan bagikan kode OTP kepada siapa pun.</p>
            </div>
        </form>
        <form id="form-ubah-email" method="POST" action="{{ route('password.ubah-email') }}" class="hidden">@csrf</form>
        <form id="form-kirim-ulang" method="POST" action="{{ route('password.kirim-ulang') }}" class="hidden">@csrf</form>

        @push('scripts')
        <script>
            (function () {
                var boxes = Array.prototype.slice.call(document.querySelectorAll('#otp-boxes input'));
                var hidden = document.getElementById('otp-value');
                function sync() { hidden.value = boxes.map(function (b) { return b.value; }).join(''); }
                boxes.forEach(function (b, i) {
                    b.addEventListener('input', function () {
                        b.value = b.value.replace(/\D/g, '').slice(-1);
                        if (b.value && boxes[i + 1]) boxes[i + 1].focus();
                        sync();
                    });
                    b.addEventListener('keydown', function (e) {
                        if (e.key === 'Backspace' && !b.value && boxes[i - 1]) { boxes[i - 1].focus(); boxes[i - 1].value = ''; sync(); }
                        if (e.key === 'ArrowLeft' && boxes[i - 1]) boxes[i - 1].focus();
                        if (e.key === 'ArrowRight' && boxes[i + 1]) boxes[i + 1].focus();
                    });
                    b.addEventListener('paste', function (e) {
                        var t = (e.clipboardData || window.clipboardData).getData('text').replace(/\D/g, '').slice(0, 6);
                        if (!t) return;
                        e.preventDefault();
                        t.split('').forEach(function (c, k) { boxes[k].value = c; });
                        boxes[Math.min(t.length, 5)].focus();
                        sync();
                    });
                });
                boxes[0].focus();
            })();
        </script>
        @endpush

    {{-- ============ TAHAP 3: SANDI BARU ============ --}}
    @elseif ($step === 3)
        <form method="POST" action="{{ route('password.simpan') }}" class="{{ $card }}">
            @csrf
            <span class="{{ $tile }}"><svg class="size-[18px]" viewBox="0 0 24 24" {!! $svg !!}><rect x="5" y="11" width="14" height="9" rx="2"/><path d="M8 11v-3a4 4 0 0 1 8 0v3"/></svg></span>
            <div class="flex flex-col gap-1">
                <h2 class="{{ $title }}">Buat Kata Sandi Baru</h2>
                <p class="{{ $desc }}">Email berhasil diverifikasi. Buat kata sandi yang kuat dan berbeda dari kata sandi sebelumnya.</p>
            </div>
            @if ($error)<div class="rounded-lg border border-red-200 bg-red-50 px-3 py-2 text-xs text-red-700" role="alert">{{ $error }}</div>@endif
            <label class="flex flex-col gap-1">
                <span class="{{ $label }}">Kata Sandi Baru</span>
                <input type="password" name="password" required minlength="8" autocomplete="new-password" placeholder="Masukkan kata sandi baru" class="{{ $input }}">
                <span class="{{ $hint }}">Minimal 8 karakter, kombinasi huruf, angka, dan simbol.</span>
            </label>
            <label class="flex flex-col gap-1">
                <span class="{{ $label }}">Konfirmasi Kata Sandi Baru</span>
                <input type="password" name="password_confirmation" required autocomplete="new-password" placeholder="Masukkan kembali kata sandi baru" class="{{ $input }}">
            </label>
            <button type="submit" class="{{ $btn }}">Simpan Kata Sandi Baru <x-icon name="arrow-right" :size="16" :stroke="2" /></button>
            <div class="{{ $note }}">
                <svg class="mt-0.5 size-4 shrink-0 text-[#2277FF]" viewBox="0 0 24 24" {!! $svg !!}><path d="M12 3l8 3v6c0 5 -3.5 8 -8 9c-4.5 -1 -8 -4 -8 -9v-6z"/></svg>
                <p class="flex-1 {{ $hint }}">Setelah kata sandi disimpan, gunakan kata sandi baru untuk masuk kembali ke akun Anda.</p>
            </div>
        </form>

    {{-- ============ BERHASIL ============ --}}
    @else
        <div class="{{ $card }} items-center text-center">
            <span class="flex size-11 items-center justify-center rounded-full bg-[#E6F4D7] text-[#4C8A1F]"><svg class="size-5" viewBox="0 0 24 24" {!! $svg !!}><circle cx="12" cy="12" r="9"/><path d="M8.5 12.5l2.5 2.5l4.5 -5"/></svg></span>
            <div class="flex flex-col gap-1">
                <h2 class="{{ $title }}">Kata Sandi Baru<br>Berhasil Disimpan</h2>
                <p class="{{ $desc }}">Silakan masuk kembali menggunakan kata sandi baru untuk mengakses akun Anda.</p>
            </div>
            <a href="{{ route('login') }}" class="{{ $btn }}">Kembali ke Login <x-icon name="arrow-right" :size="16" :stroke="2" /></a>
            <div class="{{ $note }} text-left">
                <svg class="mt-0.5 size-4 shrink-0 text-[#2277FF]" viewBox="0 0 24 24" {!! $svg !!}><path d="M12 3l8 3v6c0 5 -3.5 8 -8 9c-4.5 -1 -8 -4 -8 -9v-6z"/></svg>
                <p class="flex-1 {{ $hint }}">Jaga kerahasiaan kata sandi Anda. Jangan bagikan kepada siapa pun, termasuk pihak yang mengatasnamakan TRK.</p>
            </div>
        </div>
    @endif
</x-auth-layout>
@endsection
