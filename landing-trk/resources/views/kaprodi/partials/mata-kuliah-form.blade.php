{{--
    resources/views/kaprodi/partials/mata-kuliah-form.blade.php
    Form mata kuliah (kaprodi), dipakai halaman tambah & edit.
    Variabel: $action (url), $method (POST|PUT), $kurikulumOptions [id => nama], $nilai (array nilai awal / data yang diedit).
--}}
@php
    $semesters = [1, 2, 3, 4, 5, 6, 7, 8];

    $label  = 'text-base text-[#111]';
    $input  = 'h-10 w-full rounded-md border border-[#E4E4E7] bg-white px-4 text-base text-[#2E2E2E] outline-none placeholder:text-[#A8A8A8] focus:border-[#2277FF]';
    $select = 'h-10 w-full appearance-none rounded-md border border-[#E4E4E7] bg-white pl-4 pr-10 text-base text-[#2E2E2E] outline-none invalid:text-[#A8A8A8] focus:border-[#2277FF]';
    $num    = 'h-10 w-full rounded-md border border-[#E4E4E7] bg-white pl-4 pr-14 text-base text-[#2E2E2E] outline-none placeholder:text-[#A8A8A8] focus:border-[#2277FF] [appearance:textfield] [&::-webkit-inner-spin-button]:appearance-none [&::-webkit-outer-spin-button]:appearance-none';
    $unit   = 'pointer-events-none absolute right-4 top-1/2 -translate-y-1/2 text-sm text-[#A8A8A8]';
    $error  = 'text-sm text-[#FF3F3F]';
    $card   = 'flex flex-col gap-4 rounded-md border border-[#EDEDED] bg-white p-4';
@endphp
<form method="POST" action="{{ $action }}" class="mt-6 flex flex-col gap-8 rounded-lg bg-white p-4 pb-6 shadow-[0_1px_4px_rgba(0,0,0,0.06)]">
                @csrf
                @if ($method !== 'POST')
                    @method($method)
                @endif

                <div class="flex flex-col gap-5">

                    {{-- ============ 1. Informasi Mata Kuliah ============ --}}
                    <section class="{{ $card }}">
                        <div class="flex items-center gap-2.5">
                            <span class="flex size-6 items-center justify-center rounded-full bg-[#2277FF] text-xs font-semibold text-white">1</span>
                            <h2 class="text-sm font-semibold text-[#0A0A0A]">Informasi Mata Kuliah</h2>
                        </div>

                        <div class="grid gap-4 md:grid-cols-2">
                            <div class="flex min-w-0 flex-col gap-2">
                                <label for="kurikulum" class="{{ $label }}">Kurikulum</label>
                                <div class="relative">
                                    <select id="kurikulum" name="kurikulum" required class="{{ $select }}">
                                        <option value="">Pilih kurikulum</option>
                                        @foreach ($kurikulumOptions as $value => $text)
                                            <option value="{{ $value }}" class="text-[#2E2E2E]" @selected((string) old('kurikulum', $nilai['kurikulum'] ?? '') === (string) $value)>{{ $text }}</option>
                                        @endforeach
                                    </select>
                                    <svg class="pointer-events-none absolute right-3.5 top-1/2 -translate-y-1/2 text-[#111]" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 9l6 6l6 -6" /></svg>
                                </div>
                                @error('kurikulum') <p class="{{ $error }}">{{ $message }}</p> @enderror
                            </div>

                            <div class="flex min-w-0 flex-col gap-2">
                                <label for="semester" class="{{ $label }}">Semester</label>
                                <div class="relative">
                                    <select id="semester" name="semester" required class="{{ $select }}">
                                        <option value="">Pilih semester</option>
                                        @foreach ($semesters as $s)
                                            <option value="{{ $s }}" class="text-[#2E2E2E]" @selected((string) old('semester', $nilai['semester'] ?? '') === (string) $s)>Semester {{ $s }}</option>
                                        @endforeach
                                    </select>
                                    <svg class="pointer-events-none absolute right-3.5 top-1/2 -translate-y-1/2 text-[#111]" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 9l6 6l6 -6" /></svg>
                                </div>
                                @error('semester') <p class="{{ $error }}">{{ $message }}</p> @enderror
                            </div>

                            <div class="flex min-w-0 flex-col gap-2">
                                <label for="kode" class="{{ $label }}">Kode mata kuliah</label>
                                <input id="kode" name="kode" required value="{{ old('kode', $nilai['kode'] ?? '') }}" placeholder="432-191-101" class="{{ $input }}">
                                @error('kode') <p class="{{ $error }}">{{ $message }}</p> @enderror
                            </div>

                            <div class="flex min-w-0 flex-col gap-2">
                                <label for="nama" class="{{ $label }}">Nama mata kuliah (Indonesia)</label>
                                <input id="nama" name="nama" required value="{{ old('nama', $nilai['nama'] ?? '') }}" placeholder="Pengantar Teknologi Informasi" class="{{ $input }}">
                                @error('nama') <p class="{{ $error }}">{{ $message }}</p> @enderror
                            </div>
                        </div>
                    </section>

                    {{-- ============ 2. Beban Studi ============ --}}
                    <section class="{{ $card }}">
                        <div class="flex items-center gap-2.5">
                            <span class="flex size-6 items-center justify-center rounded-full bg-[#2277FF] text-xs font-semibold text-white">2</span>
                            <h2 class="text-sm font-semibold text-[#0A0A0A]">Beban Studi</h2>
                        </div>

                        <div class="grid gap-4 md:grid-cols-2">
                            {{-- SKS --}}
                            <div class="flex flex-col gap-2 rounded-md border border-[#E4E4E7] bg-[#F9F9FB] p-4">
                                <h3 class="text-base font-semibold text-[#111]">SKS</h3>
                                <div class="grid gap-4 sm:grid-cols-2">
                                    <div class="flex min-w-0 flex-col gap-2">
                                        <label for="sks_teori" class="{{ $label }}">Teori</label>
                                        <div class="relative">
                                            <input id="sks_teori" name="sks_teori" type="number" min="0" step="1" inputmode="numeric" required value="{{ old('sks_teori', $nilai['sks_teori'] ?? '') }}" placeholder="0" class="{{ $num }}">
                                            <span class="{{ $unit }}">SKS</span>
                                        </div>
                                        @error('sks_teori') <p class="{{ $error }}">{{ $message }}</p> @enderror
                                    </div>
                                    <div class="flex min-w-0 flex-col gap-2">
                                        <label for="sks_praktek" class="{{ $label }}">Praktek</label>
                                        <div class="relative">
                                            <input id="sks_praktek" name="sks_praktek" type="number" min="0" step="1" inputmode="numeric" required value="{{ old('sks_praktek', $nilai['sks_praktek'] ?? '') }}" placeholder="0" class="{{ $num }}">
                                            <span class="{{ $unit }}">SKS</span>
                                        </div>
                                        @error('sks_praktek') <p class="{{ $error }}">{{ $message }}</p> @enderror
                                    </div>
                                </div>
                            </div>

                            {{-- Jam --}}
                            <div class="flex flex-col gap-2 rounded-md border border-[#E4E4E7] bg-[#F9F9FB] p-4">
                                <h3 class="text-base font-semibold text-[#111]">Jam</h3>
                                <div class="grid gap-4 sm:grid-cols-2">
                                    <div class="flex min-w-0 flex-col gap-2">
                                        <label for="jam_teori" class="{{ $label }}">Teori</label>
                                        <div class="relative">
                                            <input id="jam_teori" name="jam_teori" type="number" min="0" step="1" inputmode="numeric" required value="{{ old('jam_teori', $nilai['jam_teori'] ?? '') }}" placeholder="0" class="{{ $num }}">
                                            <span class="{{ $unit }}">Jam</span>
                                        </div>
                                        @error('jam_teori') <p class="{{ $error }}">{{ $message }}</p> @enderror
                                    </div>
                                    <div class="flex min-w-0 flex-col gap-2">
                                        <label for="jam_praktek" class="{{ $label }}">Praktek</label>
                                        <div class="relative">
                                            <input id="jam_praktek" name="jam_praktek" type="number" min="0" step="1" inputmode="numeric" required value="{{ old('jam_praktek', $nilai['jam_praktek'] ?? '') }}" placeholder="0" class="{{ $num }}">
                                            <span class="{{ $unit }}">Jam</span>
                                        </div>
                                        @error('jam_praktek') <p class="{{ $error }}">{{ $message }}</p> @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>
                </div>

                {{-- Tombol --}}
                <div class="flex flex-wrap items-center justify-between gap-4">
                    <a href="{{ route('kaprodi.kurikulum.mata-kuliah', array_filter(['kurikulum' => $nilai['kurikulum'] ?? null, 'semester' => $nilai['semester'] ?? null])) }}" class="inline-flex items-center gap-1 rounded-sm bg-[#CDDEF9] px-4 py-2 text-lg font-medium text-[#2277FF] shadow-[0_1px_2px_rgba(0,0,0,0.12)]">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M15 6l-6 6l6 6" /></svg>
                        Kembali
                    </a>
                    <div class="flex items-center gap-4 sm:pr-4">
                        <a href="{{ route('kaprodi.kurikulum.mata-kuliah', array_filter(['kurikulum' => $nilai['kurikulum'] ?? null, 'semester' => $nilai['semester'] ?? null])) }}" class="rounded-sm bg-[#FF3B3B] px-4 py-2 text-base font-medium text-white">Batal</a>
                        <button type="submit" class="rounded-sm bg-[#2277FF] px-4 py-2 text-base font-medium text-white">Simpan</button>
                    </div>
                </div>
            </form>
