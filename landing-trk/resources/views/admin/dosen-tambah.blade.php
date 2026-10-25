{{--
    resources/views/admin/dosen-tambah.blade.php
    Form tambah dosen -> POST /admin/dosen (DosenController@store). Tanpa "Status akun" (akun baru otomatis aktif).
    Nilai terisi ulang otomatis (old) bila validasi gagal.
--}}
@extends('layouts.admin')

@section('title', 'Tambah Dosen')

@php
    $field = 'w-full rounded-lg border border-black/[0.06] bg-white px-4 text-base tracking-[-0.18px] text-[#2E2E2E] outline-none placeholder:text-[#A8A8A8] focus:border-[#2277FF]';
    $label = 'text-base font-medium leading-[22px] tracking-[-0.18px] text-[#0A0A0A]';
    $hint  = 'text-base leading-[22px] tracking-[-0.18px] text-[#A8A8A8]';
    $error = 'text-sm text-[#FF3F3F]';
    $chev  = '<svg class="pointer-events-none absolute right-4 top-1/2 size-[18px] -translate-y-1/2 text-[#1C2024]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 9l6 6l6 -6" /></svg>';
@endphp

@section('content')
<main class="flex flex-col gap-8 px-4 py-8 md:px-6">
    <div class="flex flex-col gap-2">
        <h1 class="text-[32px] font-bold leading-[38px] tracking-[-0.2px]">Tambah Dosen</h1>
        <x-admin.breadcrumb :items="[['Dashboard', 'admin.dashboard'], ['Data Dosen', 'admin.dosen'], ['Tambah']]" />
    </div>

    <form method="POST" action="{{ route('admin.dosen.store') }}" class="flex flex-col items-end gap-6 rounded-lg bg-white px-4 pb-6 pt-4">
        @csrf

        <div class="flex w-full flex-col gap-6">

            @if ($errors->any())
                <div class="rounded-md border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700" role="alert">
                    Dosen belum disimpan. Periksa isian yang bertanda merah di bawah.
                </div>
            @endif

            {{-- ============ 1. Identitas Dosen ============ --}}
            <section class="flex flex-col gap-6 rounded-md border border-black/[0.06] bg-white px-4 pb-6 pt-4">
                <div class="flex items-center gap-[11px]">
                    <span class="flex size-6 items-center justify-center rounded-full bg-[#2277FF] text-sm font-semibold text-white">1</span>
                    <h2 class="text-sm font-semibold tracking-[-0.16px] text-[#0A0A0A]">Identitas Dosen</h2>
                </div>

                <div class="flex flex-col gap-4 md:flex-row md:items-start">
                    <div class="flex min-w-0 flex-1 flex-col gap-2">
                        <label for="nama_lengkap" class="{{ $label }}">Nama lengkap<span class="text-[#FF3F3F]"> *</span></label>
                        <input id="nama_lengkap" name="nama_lengkap" required value="{{ old('nama_lengkap') }}" class="{{ $field }} h-[36px]">
                        @error('nama_lengkap') <p class="{{ $error }}">{{ $message }}</p> @enderror
                    </div>
                    <div class="flex min-w-0 flex-1 flex-col gap-2">
                        <label for="nip" class="{{ $label }}">NIP<span class="text-[#FF3F3F]"> *</span></label>
                        <input id="nip" name="nip" required inputmode="numeric" value="{{ old('nip') }}" class="{{ $field }} h-[36px]">
                        @error('nip') <p class="{{ $error }}">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="flex flex-col gap-4 md:flex-row md:items-start">
                    <div class="flex min-w-0 flex-1 flex-col gap-2">
                        <label for="status_dosen" class="{{ $label }}">Status dosen<span class="text-[#FF3F3F]"> *</span></label>
                        <div class="relative">
                            <select id="status_dosen" name="status_dosen" class="{{ $field }} h-10 appearance-none pr-10">
                                @foreach (['aktif' => 'Aktif', 'nonaktif' => 'Tidak Aktif'] as $v => $t)
                                    <option value="{{ $v }}" @selected(old('status_dosen', 'aktif') === $v)>{{ $t }}</option>
                                @endforeach
                            </select>
                            {!! $chev !!}
                        </div>
                        @error('status_dosen') <p class="{{ $error }}">{{ $message }}</p> @enderror
                    </div>
                    <div class="hidden flex-1 md:block"></div>
                </div>
            </section>

            {{-- ============ 2. Akun Login ============ --}}
            <section class="flex flex-col gap-6 rounded-md border border-black/[0.06] bg-white px-4 pb-6 pt-4">
                <div class="flex items-center gap-[11px]">
                    <span class="flex size-6 items-center justify-center rounded-full bg-[#2277FF] text-sm font-semibold text-white">2</span>
                    <h2 class="text-sm font-semibold tracking-[-0.16px] text-[#0A0A0A]">Akun Login</h2>
                </div>

                <div class="flex flex-col gap-4 md:flex-row md:items-start">
                    <div class="flex min-w-0 flex-1 flex-col gap-2">
                        <label for="email" class="{{ $label }}">Email akun<span class="text-[#FF3F3F]"> *</span></label>
                        <input id="email" name="email" type="email" required value="{{ old('email') }}" class="{{ $field }} h-[36px]">
                        <p class="{{ $hint }}">Email harus unik dan dipakai untuk login.</p>
                        @error('email') <p class="{{ $error }}">{{ $message }}</p> @enderror
                    </div>
                    <div class="flex min-w-0 flex-1 flex-col gap-2">
                        <label for="role" class="{{ $label }}">Role</label>
                        <div class="relative">
                            <select id="role" name="role" class="{{ $field }} h-10 appearance-none pr-10">
                                @foreach ($roles as $v => $t)
                                    <option value="{{ $v }}" @selected(old('role', 'dosen') === $v)>{{ $t }}</option>
                                @endforeach
                            </select>
                            {!! $chev !!}
                        </div>
                        @error('role') <p class="{{ $error }}">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="flex w-full flex-col gap-2 md:w-[535px] md:max-w-full">
                    <label for="password" class="{{ $label }}">Password<span class="text-[#FF3F3F]"> *</span></label>
                    <input id="password" name="password" type="password" required autocomplete="new-password" placeholder="Minimal 8 karakter" class="{{ $field }} h-[36px]">
                    <p class="{{ $hint }}">Password awal untuk login dosen. Minimal 8 karakter.</p>
                    @error('password') <p class="{{ $error }}">{{ $message }}</p> @enderror
                </div>
            </section>

            {{-- ============ 3. Pangkat dan golongan ============ --}}
            <section class="flex flex-col gap-6 rounded-md border border-black/[0.06] bg-white px-4 pb-6 pt-4">
                <div class="flex items-center gap-[11px]">
                    <span class="flex size-6 items-center justify-center rounded-full bg-[#2277FF] text-sm font-semibold text-white">3</span>
                    <h2 class="text-sm font-semibold tracking-[-0.16px] text-[#0A0A0A]">Pangkat dan golongan</h2>
                </div>

                <p class="text-base font-medium leading-[22px] tracking-[-0.18px] text-[#A8A8A8]">
                    Isi pangkat dan golongan saat ini. Data ini menjadi riwayat pertama dosen.
                </p>

                <div class="flex flex-col gap-4 md:flex-row md:items-start">
                    <div class="flex min-w-0 flex-1 flex-col gap-2">
                        <label for="pangkat" class="{{ $label }}">Pangkat<span class="text-[#FF3F3F]"> *</span></label>
                        <div class="relative">
                            <select id="pangkat" name="pangkat" required class="{{ $field }} h-10 appearance-none pr-10">
                                <option value="">Pilih pangkat</option>
                                @foreach ($jabatan as $o)
                                    <option value="{{ $o }}" @selected(old('pangkat') === $o)>{{ $o }}</option>
                                @endforeach
                            </select>
                            {!! $chev !!}
                        </div>
                        @error('pangkat') <p class="{{ $error }}">{{ $message }}</p> @enderror
                    </div>
                    <div class="flex min-w-0 flex-1 flex-col gap-2">
                        <label for="golongan" class="{{ $label }}">Golongan<span class="text-[#FF3F3F]"> *</span></label>
                        <div class="relative">
                            <select id="golongan" name="golongan" required class="{{ $field }} h-10 appearance-none pr-10">
                                <option value="">Pilih golongan</option>
                                @foreach ($golongan as $o)
                                    <option value="{{ $o }}" @selected(old('golongan') === $o)>{{ $o }}</option>
                                @endforeach
                            </select>
                            {!! $chev !!}
                        </div>
                        @error('golongan') <p class="{{ $error }}">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="flex flex-col gap-4 md:flex-row md:items-start">
                    <div class="flex min-w-0 flex-1 flex-col gap-2">
                        <label for="nomor_sk" class="{{ $label }}">Nomor SK<span class="text-[#FF3F3F]"> *</span></label>
                        <input id="nomor_sk" name="nomor_sk" required value="{{ old('nomor_sk') }}" placeholder="Nomor SK kenaikan pangkat" class="{{ $field }} h-[36px]">
                        @error('nomor_sk') <p class="{{ $error }}">{{ $message }}</p> @enderror
                    </div>
                    <div class="flex min-w-0 flex-1 flex-col gap-2">
                        <label for="tanggal_sk" class="{{ $label }}">Tanggal SK<span class="text-[#FF3F3F]"> *</span></label>
                        <div class="relative">
                            <input id="tanggal_sk" name="tanggal_sk" required type="date" value="{{ old('tanggal_sk') }}" class="{{ $field }} h-[36px] pr-10 [&::-webkit-calendar-picker-indicator]:opacity-0">
                            <svg class="pointer-events-none absolute right-4 top-1/2 size-6 -translate-y-1/2 text-[#A8A8A8]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M4 7a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v12a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2v-12z" /><path d="M16 3v4" /><path d="M8 3v4" /><path d="M4 11h16" /><path d="M11 15h1" /><path d="M12 15v3" /></svg>
                        </div>
                        @error('tanggal_sk') <p class="{{ $error }}">{{ $message }}</p> @enderror
                    </div>
                </div>
            </section>
        </div>

        {{-- Tombol --}}
        <div class="flex w-full flex-wrap items-center justify-end gap-4">
            <a href="{{ route('admin.dosen') }}" class="rounded-md bg-[#FF3F3F] px-4 py-2 text-lg font-medium tracking-[-0.18px] text-[#FEFEFE] shadow-[0_1px_2px_rgba(0,0,0,0.12)]">Batal</a>
            <button type="submit" class="rounded-md bg-[#2277FF] px-4 py-2 text-lg font-medium tracking-[-0.18px] text-[#FEFEFE] shadow-[0_1px_2px_rgba(0,0,0,0.12)]">Simpan</button>
        </div>
    </form>
</main>
@endsection
