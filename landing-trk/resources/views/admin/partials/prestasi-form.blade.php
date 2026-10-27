@php
    $p = $prestasi ?? [];
    $labelCls = 'text-[13px] font-semibold leading-[1.5] text-[#111827]';
    $inputCls = 'h-[36px] w-full rounded-md border border-[#E4E4E7] bg-white px-3 text-sm outline-none placeholder:text-[#B3B3B8] focus:border-[#2277FF]';
    $selectCls = 'h-[36px] w-full appearance-none rounded-lg border border-[#E5E7EB] bg-white px-3 pr-10 text-sm text-[#111827] outline-none focus:border-[#2277FF]';
    $hint = 'text-xs leading-[1.5] text-[#6B7280]';
    $chev = '<svg class="pointer-events-none absolute right-3 top-1/2 -translate-y-1/2 text-[#6B7280]" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 9l6 6l6 -6" /></svg>';
    $adaFoto = !empty($p['gambar']);
@endphp

<form method="POST" action="{{ $action }}" enctype="multipart/form-data" class="rounded-lg bg-white p-4 pb-6 shadow-[1px_0_3px_rgba(0,0,0,0.02),5px_2px_6px_rgba(0,0,0,0.02)]">
    @csrf
    @if ($method !== 'POST') @method($method) @endif

    <div class="flex flex-col gap-6 overflow-hidden rounded-2xl border border-[#E5E7EB] bg-white p-6">
        <p class="{{ $hint }}">* Wajib diisi</p>

        {{-- Detail Prestasi --}}
        <div class="flex flex-col gap-4">
            <h3 class="text-[18px] font-bold leading-[1.4] text-[#111827]">Detail Prestasi</h3>
            
            <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                <div class="flex flex-col gap-1.5">
                    <label for="nama" class="{{ $labelCls }}">Nama Prestasi / Pencapaian *</label>
                    <input id="nama" name="nama" type="text" value="{{ old('nama', $p['nama'] ?? '') }}" placeholder="Contoh: Juara 1 Web Design" class="{{ $inputCls }}">
                </div>
                
                <div class="flex flex-col gap-1.5">
                    <label for="kategori" class="{{ $labelCls }}">Kategori *</label>
                    <div class="relative">
                        <select id="kategori" name="kategori" class="{{ $selectCls }}">
                            <option value="">Pilih Kategori</option>
                            <option value="Lomba" @selected(old('kategori', $p['kategori'] ?? '') === 'Lomba')>Lomba</option>
                            <option value="Penghargaan" @selected(old('kategori', $p['kategori'] ?? '') === 'Penghargaan')>Penghargaan</option>
                        </select>
                        {!! $chev !!}
                    </div>
                </div>

                <div class="flex flex-col gap-1.5">
                    <label for="semester" class="{{ $labelCls }}">Semester *</label>
                    <div class="relative">
                        <select id="semester" name="semester" class="{{ $selectCls }}">
                            <option value="">Pilih Semester</option>
                            <option value="Ganjil" @selected(old('semester', $p['semester'] ?? '') === 'Ganjil')>Ganjil</option>
                            <option value="Genap" @selected(old('semester', $p['semester'] ?? '') === 'Genap')>Genap</option>
                        </select>
                        {!! $chev !!}
                    </div>
                </div>

                <div class="flex flex-col gap-1.5">
                    <label for="tanggal" class="{{ $labelCls }}">Tanggal Diraih *</label>
                    <input id="tanggal" name="tanggal" type="text" value="{{ old('tanggal', $p['tanggal'] ?? '') }}" placeholder="Contoh: 15 Agustus 2026" class="{{ $inputCls }}">
                </div>
            </div>
        </div>

        <div class="h-px bg-[#E5E7EB]"></div>

        {{-- Data Peraih --}}
        <div class="flex flex-col gap-4">
            <h3 class="text-[18px] font-bold leading-[1.4] text-[#111827]">Informasi Peraih Prestasi</h3>
            
            <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                <div class="flex flex-col gap-1.5">
                    <label for="tipe" class="{{ $labelCls }}">Tipe Peraih *</label>
                    <div class="relative">
                        <select id="tipe" name="tipe" class="{{ $selectCls }}">
                            <option value="">Pilih Tipe</option>
                            <option value="Mahasiswa" @selected(old('tipe', $p['tipe'] ?? '') === 'Mahasiswa')>Mahasiswa</option>
                            <option value="Dosen" @selected(old('tipe', $p['tipe'] ?? '') === 'Dosen')>Dosen</option>
                        </select>
                        {!! $chev !!}
                    </div>
                </div>

                <div class="flex flex-col gap-1.5">
                    <label for="pengunggah" class="{{ $labelCls }}">Diunggah Oleh (Nama)*</label>
                    <input id="pengunggah" name="pengunggah" type="text" value="{{ old('pengunggah', $p['pengunggah'] ?? '') }}" placeholder="Nama pengunggah" class="{{ $inputCls }}">
                </div>

                <div class="flex flex-col gap-1.5 md:col-span-2">
                    <label for="anggota" class="{{ $labelCls }}">Anggota Tim / Peraih *</label>
                    <textarea id="anggota" name="anggota" placeholder="Pisahkan dengan koma (contoh: Budi, Siti, Andi)" class="min-h-[80px] resize-y rounded-lg border border-[#E5E7EB] p-3 text-sm outline-none focus:border-[#2277FF]">{{ old('anggota', isset($p['anggota']) ? implode(', ', $p['anggota']) : '') }}</textarea>
                </div>
            </div>
        </div>

        <div class="h-px bg-[#E5E7EB]"></div>

        {{-- File Upload --}}
        <div class="flex flex-col items-center gap-3 overflow-hidden rounded-lg border border-dashed border-[#AAC8F8] bg-[#F9FAFB] p-6 text-center">
            <svg class="text-[#0B3A8C]" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M15 15m-3 0a3 3 0 1 0 6 0a3 3 0 1 0 -6 0"/><path d="M13 17.5v4.5l2 -1.5l2 1.5v-4.5"/><path d="M10 19h-5a2 2 0 0 1 -2 -2v-10c0 -1.1 .9 -2 2 -2h14a2 2 0 0 1 2 2v10a2 2 0 0 1 -1 1.99"/><path d="M6 9l12 0"/><path d="M6 12l3 0"/></svg>
            <p class="text-sm font-semibold leading-[1.5] text-[#111827]">Unggah sertifikat / foto bukti{{ $adaFoto ? '' : ' *' }}</p>
            <label class="flex cursor-pointer items-center gap-2 rounded-lg border border-[#E5E7EB] bg-white px-4 py-[11px] text-sm font-semibold text-[#0062FF]">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M4 17v2a2 2 0 0 0 2 2h12a2 2 0 0 0 2 -2v-2"/><path d="M7 9l5 -5l5 5"/><path d="M12 4l0 12"/></svg>
                Pilih berkas
                <input id="foto" name="foto" type="file" accept=".pdf,.jpg,.jpeg,.png" class="sr-only">
            </label>
            <p class="{{ $hint }}">PDF, JPG, atau PNG • Maksimal 5 MB</p>
        </div>

        <div class="h-px bg-[#E5E7EB]"></div>

        {{-- Tombol Aksi --}}
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-end">
            <div class="flex flex-wrap gap-3 text-sm font-semibold">
                <a href="{{ route('admin.prestasi') }}" class="flex h-10 items-center rounded-lg border border-[#E5E7EB] bg-white px-4 text-[#0062FF]">Batal</a>
                <button type="submit" class="h-10 rounded-lg border border-[#2277FF] bg-[#2277FF] px-4 text-white">Simpan Prestasi</button>
            </div>
        </div>
    </div>
</form>