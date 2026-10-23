{{--
    resources/views/admin/mahasiswa-edit.blade.php
    Form edit mahasiswa -> PUT /admin/mahasiswa/{id} (MahasiswaController@update).
    Nilai terisi ulang otomatis (old) bila validasi gagal.
--}}
@extends('layouts.admin')

@section('title', 'Edit Mahasiswa')

@php
    $field  = 'w-full rounded-lg border border-black/[0.06] bg-white px-4 py-1.5 text-base tracking-[-0.18px] text-[#2E2E2E] outline-none placeholder:text-[#A8A8A8] focus:border-[#2277FF]';
    $label  = 'text-base font-medium leading-[22px] tracking-[-0.18px] text-[#0A0A0A]';
    $hint   = 'text-base tracking-[-0.18px] text-[#A8A8A8]';
    $error  = 'text-sm text-[#FF3F3F]';
    $card   = 'flex flex-col gap-6 rounded-md border border-black/[0.06] bg-white px-4 pb-6 pt-4';
    $select = 'h-10 w-full appearance-none rounded-lg border border-black/[0.06] bg-white px-4 text-base tracking-[-0.18px] text-[#2E2E2E] outline-none focus:border-[#2277FF]';
@endphp

@section('content')
<main class="flex flex-col gap-8 px-4 py-8 lg:px-6">
    <div class="flex flex-col gap-2">
        <h1 class="text-[32px] font-bold leading-[38px] tracking-[-0.2px]">Edit Mahasiswa</h1>
        <div class="text-lg font-medium text-[#A8A8A8] lg:text-2xl lg:leading-[30px] lg:tracking-[-0.15px]">Dashboard / Data Mahasiswa / Detail / Edit</div>
    </div>

    <form method="POST" action="{{ route('admin.mahasiswa.update', $mahasiswa['id']) }}" class="flex flex-col gap-6 rounded-lg bg-white px-4 pb-6 pt-4">
        @csrf
        @method('PUT')

        {{-- ============ 1. Identitas Mahasiswa ============ --}}
        <div class="{{ $card }}">
            <div class="flex items-center gap-[11px]">
                <div class="flex size-6 items-center justify-center rounded-full bg-[#2277FF] text-sm font-semibold text-white">1</div>
                <div class="text-sm font-semibold text-[#0A0A0A]">Identitas Mahasiswa</div>
            </div>
            <div class="flex flex-col gap-4">
                <div class="flex flex-col gap-4 md:flex-row">
                    <div class="flex min-w-0 flex-1 flex-col gap-2">
                        <label for="nama" class="{{ $label }}">Nama lengkap<span class="text-[#FF3F3F]"> *</span></label>
                        <input id="nama" name="nama" required value="{{ old('nama', $mahasiswa['nama']) }}" class="{{ $field }}">
                        @error('nama') <p class="{{ $error }}">{{ $message }}</p> @enderror
                    </div>
                    <div class="flex min-w-0 flex-1 flex-col gap-2">
                        <label for="nim" class="{{ $label }}">NIM<span class="text-[#FF3F3F]"> *</span></label>
                        <input id="nim" name="nim" required value="{{ old('nim', $mahasiswa['nim']) }}" class="{{ $field }}">
                        @error('nim') <p class="{{ $error }}">{{ $message }}</p> @enderror
                    </div>
                </div>
                <div class="flex flex-col gap-4 md:flex-row">
                    <div class="flex min-w-0 flex-1 flex-col gap-2">
                        <label for="tanggal_lahir" class="{{ $label }}">Tanggal lahir</label>
                        <div class="relative">
                            <input id="tanggal_lahir" name="tanggal_lahir" type="date" value="{{ old('tanggal_lahir', $mahasiswa['tanggal_lahir']) }}" class="{{ $field }} pr-12 [&::-webkit-calendar-picker-indicator]:opacity-0">
                            <svg class="pointer-events-none absolute right-4 top-1/2 -translate-y-1/2 text-[#A8A8A8]" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M4 7a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v12a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2v-12z" /><path d="M16 3v4" /><path d="M8 3v4" /><path d="M4 11h16" /><path d="M11 15h1" /><path d="M12 15v3" /></svg>
                        </div>
                        @error('tanggal_lahir') <p class="{{ $error }}">{{ $message }}</p> @enderror
                    </div>
                    <div class="flex min-w-0 flex-1 flex-col gap-2">
                        <label for="telepon" class="{{ $label }}">No. telepon</label>
                        <input id="telepon" name="telepon" value="{{ old('telepon', $mahasiswa['telepon']) }}" class="{{ $field }}">
                        @error('telepon') <p class="{{ $error }}">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>
        </div>

        {{-- ============ 2. Data Akademik ============ --}}
        <div class="{{ $card }}">
            <div class="flex items-center gap-[11px]">
                <div class="flex size-6 items-center justify-center rounded-full bg-[#2277FF] text-sm font-semibold text-white">2</div>
                <div class="text-sm font-semibold text-[#0A0A0A]">Data Akademik</div>
            </div>
            <div class="flex flex-col gap-4">
                <div class="flex flex-col gap-4 md:flex-row">
                    <div class="flex min-w-0 flex-1 flex-col gap-2">
                        <label for="angkatan" class="{{ $label }}">Angkatan (tahun masuk)<span class="text-[#FF3F3F]"> *</span></label>
                        <div class="relative">
                            <select id="angkatan" name="angkatan" class="{{ $select }}">
                                @foreach ($tahun as $y)
                                    <option value="{{ $y }}" @selected((string) old('angkatan', $mahasiswa['angkatan']) === (string) $y)>{{ $y }}</option>
                                @endforeach
                            </select>
                            <svg class="pointer-events-none absolute right-4 top-1/2 -translate-y-1/2 text-[#1C2024]" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M6 9l6 6l6 -6" /></svg>
                        </div>
                        @error('angkatan') <p class="{{ $error }}">{{ $message }}</p> @enderror
                    </div>
                    <div class="flex min-w-0 flex-1 flex-col gap-2">
                        <label for="status" class="{{ $label }}">Status akademik<span class="text-[#FF3F3F]"> *</span></label>
                        <div class="relative">
                            <select id="status" name="status" class="{{ $select }}">
                                @foreach ($statusAkad as $s)
                                    <option value="{{ $s }}" @selected(old('status', $mahasiswa['status']) === $s)>{{ $s }}</option>
                                @endforeach
                            </select>
                            <svg class="pointer-events-none absolute right-4 top-1/2 -translate-y-1/2 text-[#1C2024]" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M6 9l6 6l6 -6" /></svg>
                        </div>
                        @error('status') <p class="{{ $error }}">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>
        </div>

        {{-- ============ 3. Akun Login ============ --}}
        <div class="{{ $card }}">
            <div class="flex items-center gap-[11px]">
                <div class="flex size-6 items-center justify-center rounded-full bg-[#2277FF] text-sm font-semibold text-white">3</div>
                <div class="text-sm font-semibold text-[#0A0A0A]">Akun Login</div>
            </div>
            <div class="flex flex-col gap-4">
                <div class="flex flex-col gap-4 md:flex-row">
                    <div class="flex min-w-0 flex-1 flex-col gap-2">
                        <label for="email" class="{{ $label }}">Email akun<span class="text-[#FF3F3F]"> *</span></label>
                        <input id="email" name="email" type="email" required value="{{ old('email', $mahasiswa['email']) }}" class="{{ $field }}">
                        <div class="{{ $hint }}">Email harus unik dan dipakai untuk login.</div>
                        @error('email') <p class="{{ $error }}">{{ $message }}</p> @enderror
                    </div>
                    <div class="hidden flex-1 md:block"></div>
                </div>
                <div class="flex md:w-[calc(50%-8px)]">
                    <div class="flex min-w-0 flex-1 flex-col gap-2">
                        <label for="password" class="{{ $label }}">Password baru<span class="text-[#A8A8A8]"> (opsional)</span></label>
                        <input id="password" name="password" type="password" autocomplete="new-password" placeholder="kosongkan jika tidak diubah" class="{{ $field }}">
                        <div class="{{ $hint }}">Password lama tidak ditampilkan. Isi hanya jika ingin menggantinya.</div>
                        @error('password') <p class="{{ $error }}">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>
        </div>

        {{-- Tombol --}}
        <div class="flex flex-wrap items-center gap-4">
            <a href="{{ route('admin.mahasiswa.show', $mahasiswa['id']) }}" class="rounded-md bg-[#FF3F3F] px-4 py-2 text-lg font-medium leading-[22px] tracking-[-0.18px] text-[#FEFEFE] shadow-[0_1px_2px_rgba(0,0,0,0.12)]">Batal</a>
            <button type="submit" class="rounded-md bg-[#2277FF] px-4 py-2 text-lg font-medium leading-[22px] tracking-[-0.18px] text-[#FEFEFE] shadow-[0_1px_2px_rgba(0,0,0,0.12)] disabled:opacity-60">Simpan Perubahan</button>
            @if ($errors->any())
                <span class="text-sm text-[#FF3F3F]">Gagal menyimpan. Periksa kembali isian di atas.</span>
            @endif
        </div>
    </form>
</main>
@endsection
