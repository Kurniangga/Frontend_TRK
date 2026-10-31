@extends('layouts.admin')

@section('title', 'Tinjau Pengajuan Kegiatan')

@php
    $badge = [
        'Diajukan'  => 'border-[#AAC8F8] bg-[#CDDEF9] text-[#2277FF]',
        'Direvisi'  => 'border-[#FAD4A8] bg-[#FBE5CB] text-[#854700]',
    ][$status] ?? 'border-[#AAC8F8] bg-[#CDDEF9] text-[#2277FF]';

    $card  = 'flex flex-col gap-6 rounded-md border border-[#0A0A0A]/[0.06] bg-white px-4 pb-6 pt-4';
    $input = 'rounded-lg border border-[#000932]/[0.12] bg-white/90 p-3 text-base shadow-[0_1.5px_2px_rgba(0,0,0,0.1)_inset] outline-none placeholder:text-[#A8A8A8] focus:border-[#2277FF]';

    // ---- Data untuk preview surat ----
    $tanggalSurat = $tanggalSurat ?? \Carbon\Carbon::now()->locale('id')->translatedFormat('j F Y');
    $nomorAwal    = trim((string) old('nomor_surat', $nomorSurat));

    // Lampiran: berkas pengaju yang dicentang admin + daftar peserta (jika kategori memintanya)
    $lampiranDokumen = collect($lampiranTerpilih ?? [])->map(fn ($i) => $dokumen[$i])->values()->all();
    $jumlahLampiran  = ($adaLampiran ? 1 : 0) + count($lampiranDokumen);
@endphp

@section('content')
<style>
@media print {
    body * { visibility: hidden !important; }
    #area-cetak-surat, #area-cetak-surat * { visibility: visible !important; }
    #area-cetak-surat {
        position: absolute; left: 0; top: 0; width: 100%;
        min-height: auto !important; box-shadow: none !important; padding: 0 !important;
    }
    @page { margin: 2.5cm; }
    .lampiran-surat { border: 0 !important; margin-top: 0 !important; padding-top: 0 !important; break-before: page; page-break-before: always; }
}
</style>

<main class="flex flex-col gap-10 px-4 py-8 sm:px-6">

    {{-- Judul + status + tombol kembali --}}
    <div class="flex flex-wrap items-center justify-between gap-2">
        <div class="flex flex-col gap-2">
            <div class="flex flex-wrap items-center gap-2">
                <h1 class="text-[32px] font-bold leading-[38px] tracking-[-0.2px]">{{ $judul }}</h1>
                <span class="rounded-lg border px-2 py-0.5 text-xs font-medium {{ $badge }}">{{ $status }}</span>
            </div>
            <div class="flex flex-col gap-2">
                <x-admin.breadcrumb :items="[['Dashboard', 'admin.dashboard'], ['Pengajuan', 'admin.pengajuan'], ['Tinjau Pengajuan']]" />
            </div>
        </div>
    </div>

    @if (session('success'))
        <div class="rounded-md border border-[#D6EDB5] bg-[#E6F3D3] px-4 py-3 text-sm text-[#4C7D08]" role="status">{{ session('success') }}</div>
    @endif

    <div class="grid items-stretch gap-3 rounded-lg bg-white p-4 pb-6 shadow-sm lg:grid-cols-2">

        {{-- KOLOM KIRI --}}
        <div class="flex flex-col gap-3">

            <section class="{{ $card }}">
                <div class="flex items-center gap-3">
                    <div class="flex size-6 items-center justify-center rounded-full bg-[#2277FF] text-sm font-semibold text-white">1</div>
                    <div class="text-sm font-semibold">Informasi kegiatan</div>
                </div>
                <div class="flex flex-col">
                    @foreach ($info as $i => [$k, $v])
                        <div class="flex items-center justify-between gap-14 p-3 text-sm text-[#2E2E2E] {{ $i ? 'border-t border-[#0A0A0A]/[0.06]' : '' }}">
                            <span class="font-medium">{{ $k }}</span>
                            <span class="text-right font-semibold">{{ $v }}</span>
                        </div>
                    @endforeach
                </div>
            </section>

            <section class="{{ $card }}">
                <div class="flex items-center gap-3">
                    <div class="flex size-6 items-center justify-center rounded-full bg-[#2277FF] text-sm font-semibold text-white">2</div>
                    <div class="text-sm font-semibold">Dokumen Pendukung</div>
                </div>
                <p class="-mt-3 text-xs text-[#A8A8A8]">Centang kolom Lampiran untuk melampirkan berkas pada surat persetujuan.</p>
                <div class="overflow-x-auto rounded-md border border-[#00002F]/15 bg-white/80">
                    <table class="w-full text-sm text-[#1C2024]">
                        <thead>
                            <tr class="bg-[#000055]/[0.02] text-left font-medium">
                                <th class="min-w-[120px] p-2 font-medium">Nama</th>
                                <th class="min-w-[120px] p-2 font-medium">Kategori</th>
                                <th class="w-16 p-2 text-center font-medium">Aksi</th>
                                <th class="w-20 whitespace-nowrap p-2 text-center font-medium">Lampiran</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($dokumen as $i => [$nama, $kategori, $url])
                                <tr class="border-t border-[#00002F]/15">
                                    <td class="p-2">{{ $nama }}</td>
                                    <td class="p-2">{{ $kategori }}</td>
                                    <td class="p-2 text-center">
                                        <a href="{{ $url }}" target="_blank" rel="noopener" aria-label="Lihat dokumen"
                                            class="inline-flex rounded-md bg-[#CDDEF9] p-1.5 text-[#2277FF]">
                                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M10 12a2 2 0 1 0 4 0a2 2 0 0 0 -4 0"/><path d="M21 12c-2.4 4 -5.4 6 -9 6c-3.6 0 -6.6 -2 -9 -6c2.4 -4 5.4 -6 9 -6c3.6 0 6.6 2 9 6"/></svg>
                                        </a>
                                    </td>
                                    <td class="p-2 text-center">
                                        <input type="checkbox" form="form-keputusan" name="lampiran[]" value="{{ $i }}" data-nama="{{ $nama }}"
                                               aria-label="Jadikan {{ $nama }} sebagai lampiran"
                                               class="js-lampiran size-4 cursor-pointer accent-[#2277FF]" @checked(in_array($i, $lampiranTerpilih ?? [], true))>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </section>

            <section class="{{ $card }} flex-1">
                <div class="flex items-center gap-3">
                    <div class="flex size-6 items-center justify-center rounded-full bg-[#2277FF] text-sm font-semibold text-white">3</div>
                    <div class="text-sm font-semibold">Anggota Kegiatan</div>
                </div>
                <div class="overflow-x-auto rounded-md border border-[#00002F]/15 bg-white/80">
                    <table class="w-full text-sm text-[#1C2024]">
                        <thead>
                            <tr class="bg-[#000055]/[0.02] text-left font-medium">
                                <th class="min-w-[120px] p-2 font-medium">Nama</th>
                                <th class="min-w-[120px] p-2 font-medium">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($anggota as [$nama, $peran])
                                <tr class="border-t border-[#00002F]/15">
                                    <td class="p-2">{{ $nama }}</td>
                                    <td class="p-2">{{ $peran }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </section>
        </div>

        {{-- KOLOM KANAN --}}
        <div class="flex flex-col gap-3">

            <section class="{{ $card }}">
                <div class="flex items-center gap-3">
                    <div class="flex size-6 items-center justify-center rounded-full bg-[#2277FF] text-sm font-semibold text-white">4</div>
                    <div class="text-sm font-semibold">Keputusan Admin</div>
                </div>

                <form id="form-keputusan" method="POST" action="{{ route('admin.pengajuan.keputusan', $id) }}" class="flex flex-col gap-6 p-3" novalidate>
                    @csrf

                    {{-- Catatan revisi --}}
                    <div class="flex flex-col gap-2">
                        <label for="catatan" class="text-base font-medium text-[#2E2E2E]">Catatan revisi</label>
                        <textarea id="catatan" name="catatan" maxlength="1000"
                                  placeholder="Wajib diisi jika meminta revisi atau menolak"
                                  class="h-20 resize-none {{ $input }}">{{ old('catatan', $catatan) }}</textarea>
                        @error('catatan')
                            <p class="text-sm text-[#F00]">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Nomor surat --}}
                    <div class="flex flex-col gap-2">
                        <label for="nomor_surat" class="text-base font-medium text-[#2E2E2E]">Nomor surat</label>
                        <input id="nomor_surat" name="nomor_surat" type="text" maxlength="100"
                               value="{{ old('nomor_surat', $nomorSurat) }}"
                               placeholder="Contoh: 123/PL4.5/KP/2026 (wajib diisi jika menyetujui)"
                               class="{{ $input }}">
                        @error('nomor_surat')
                            <p class="text-sm text-[#F00]">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Pratinjau surat (klik untuk membuka penuh) --}}
                    <div class="flex flex-col gap-2">
                        <div class="flex flex-wrap items-center justify-between gap-x-3 gap-y-1">
                            <span class="text-base font-medium text-[#2E2E2E]">Pratinjau Surat Persetujuan</span>
                            <span class="text-xs text-[#A8A8A8]">Klik untuk memperbesar</span>
                        </div>

                        <button type="button" id="btn-buka-surat"
                                class="group relative w-full cursor-pointer overflow-hidden rounded-lg border border-[#0A0A0A]/[0.08] bg-[#F9FAFB] p-4 pb-12 text-left transition-all hover:border-[#2277FF] hover:bg-[#F0F5FF] hover:shadow-md focus:outline-none">

                            <span class="pointer-events-none absolute bottom-3 right-3 flex items-center gap-1 rounded-md bg-[#CDDEF9] px-2.5 py-1 text-xs font-semibold text-[#2277FF] shadow-sm transition-transform group-hover:scale-105">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 3h6v6"/><path d="M10 14L21 3"/><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/></svg>
                                Buka Surat
                            </span>

                            <div class="pointer-events-none font-serif text-[10px] text-[#555]">
                                {{-- Kop mini --}}
                                <div class="relative border-b border-gray-400 pb-2 text-[#111]" style="font-family:'Times New Roman',Times,serif">
                                    <img src="{{ asset('images/logo.png') }}" alt="" class="absolute left-1 top-1/2 size-11 -translate-y-[56%] object-contain" onerror="this.style.visibility='hidden'">
                                    <div class="px-[56px] text-center leading-snug">
                                        <div class="text-[9px] uppercase">Kementerian Pendidikan Tinggi, Sains, dan Teknologi</div>
                                        <div class="text-[9px] uppercase">Politeknik Negeri Semarang</div>
                                        <div class="mt-0.5 text-[8px]">Jalan Prof. H. Soedarto, S.H., Tembalang, Semarang 50275</div>
                                        <div class="text-[8px]">Telepon (024) 7473417, 7499585, 7499586</div>
                                        <div class="text-[8px]">Laman www.polines.ac.id, Surel sekretariat@polines.ac.id</div>
                                    </div>
                                </div>
                                <div class="mt-2"></div>
                                <div class="grid grid-cols-[60px_10px_1fr] gap-1 text-[10px]">
                                    <span>Nomor</span><span>:</span>
                                    <span class="js-nomor-surat font-semibold text-black">{{ $nomorAwal !== '' ? $nomorAwal : '[nomor surat belum diisi]' }}</span>
                                    <span>Hal</span><span>:</span>
                                    <span class="font-bold text-black">Persetujuan Kegiatan</span>
                                </div>
                                <div class="mt-2.5 line-clamp-3 text-justify text-[#666]">
                                    {!! $isiSurat !!}
                                </div>
                            </div>
                        </button>
                        <p class="text-xs text-[#A8A8A8]">Nomor surat otomatis memperbarui isi pratinjau surat saat Anda mengetik.</p>
                    </div>

                    <div class="flex flex-col gap-3">
                        <button type="submit" name="aksi" value="setujui"
                                class="rounded-md border border-[#2277FF] px-4 py-2 text-lg font-medium text-[#2277FF] shadow-[0_1px_2px_rgba(0,0,0,0.12)] hover:bg-[#EEF4FF]">
                            Setujui
                        </button>
                        <button type="submit" name="aksi" value="revisi"
                                class="rounded-md border border-[#FAD4A8] px-4 py-2 text-lg font-medium text-[#F80] shadow-[0_1px_2px_rgba(0,0,0,0.12)] hover:bg-[#FFF6EC]">
                            Minta revisi
                        </button>
                        <button type="submit" name="aksi" value="tolak"
                                class="rounded-md border border-[#FAA8A8] px-4 py-2 text-lg font-medium text-[#F00] shadow-[0_1px_2px_rgba(0,0,0,0.12)] hover:bg-[#FFF0F0]">
                            Tolak
                        </button>
                    </div>
                </form>
            </section>

            <section class="{{ $card }} flex-1">
                <div class="flex items-center gap-3">
                    <div class="flex size-6 items-center justify-center rounded-full bg-[#2277FF] text-sm font-semibold text-white">5</div>
                    <div class="text-sm font-semibold">Riwayat Status</div>
                </div>
                <div class="flex flex-col gap-2">
                    @foreach ($riwayat as $r)
                        <div class="flex items-center gap-2">
                            <span class="size-1.5 shrink-0 rounded-full bg-[#7F7F7F]"></span>
                            <span class="text-sm font-semibold text-[#7F7F7F]">{{ $r }}</span>
                        </div>
                    @endforeach
                </div>
            </section>
        </div>
    </div>
</main>

{{-- MODAL SURAT LENGKAP (dipindah ke <body> lewat JS agar lepas dari stacking context layout) --}}
<div id="modal-surat" class="fixed inset-0 hidden items-center justify-center bg-black/60 p-4" style="z-index:9999" role="dialog" aria-modal="true" aria-labelledby="modal-surat-judul">
    <div class="relative flex max-h-[92vh] w-full max-w-[880px] flex-col overflow-hidden rounded-xl bg-white shadow-2xl">

        {{-- Header Modal --}}
        <div class="flex items-center justify-between border-b border-gray-100 px-6 py-4">
            <h3 id="modal-surat-judul" class="text-lg font-bold text-[#2E2E2E]">Pratinjau Dokumen Surat Resmi</h3>
            <div class="flex items-center gap-2">
                <button type="button" id="btn-cetak-surat" aria-label="Cetak / Simpan PDF"
                        class="flex items-center gap-2 rounded-md bg-[#2277FF] px-3 py-1.5 text-sm font-medium text-white transition-colors hover:bg-blue-600">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 9V2h12v7"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><path d="M6 14h12v8H6z"/></svg>
                    Cetak
                </button>
                <button type="button" id="btn-tutup-surat" aria-label="Tutup pratinjau"
                        class="rounded-full bg-gray-100 p-2 text-gray-600 transition-colors hover:bg-gray-200">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6l-12 12"/><path d="M6 6l12 12"/></svg>
                </button>
            </div>
        </div>

        {{-- Konten Dokumen Surat (area cetak) --}}
        <div class="flex-1 overflow-y-auto bg-gray-100 px-6 py-6">
            <article id="area-cetak-surat" class="mx-auto min-h-[1056px] w-full max-w-[800px] bg-white px-8 py-10 font-serif text-[13px] leading-[1.6] text-[#0A0A0A] shadow-sm sm:px-12">
                {{-- Kop surat --}}
                <header class="relative border-b border-gray-400 pb-3" style="font-family:'Times New Roman',Times,serif">
                    <img src="{{ asset('images/logo.png') }}" alt="" class="absolute left-2 top-1/2 size-[72px] -translate-y-[56%] object-contain" onerror="this.style.visibility='hidden'">
                    <div class="px-[92px] text-center leading-snug">
                        <div class="text-[14px] uppercase">Kementerian Pendidikan Tinggi, Sains, dan Teknologi</div>
                        <div class="text-[14px] uppercase">Politeknik Negeri Semarang</div>
                        <div class="mt-0.5 text-[11.5px]">Jalan Prof. H. Soedarto, S.H., Tembalang, Semarang 50275</div>
                        <div class="text-[11.5px]">Telepon (024) 7473417, 7499585, 7499586</div>
                        <div class="text-[11.5px]">Laman www.polines.ac.id, Surel sekretariat@polines.ac.id</div>
                    </div>
                </header>

                {{-- Tanggal surat --}}
                <div class="mt-6 flex justify-end">
                    <div class="text-right">Semarang, {{ $tanggalSurat }}</div>
                </div>

                {{-- Nomor, lampiran, hal --}}
                <table class="mt-2 text-[13px]">
                    <tbody>
                        <tr>
                            <td class="pr-3 align-top">Nomor</td>
                            <td class="pr-2 align-top">:</td>
                            <td class="js-nomor-surat align-top">{{ $nomorAwal !== '' ? $nomorAwal : '[nomor surat belum diisi]' }}</td>
                        </tr>
                        <tr id="lampiran-baris" class="{{ $jumlahLampiran > 0 ? '' : 'hidden' }}">
                            <td class="pr-3 align-top">Lampiran</td>
                            <td class="pr-2 align-top">:</td>
                            <td id="lampiran-jumlah" class="align-top">{{ $jumlahLampiran }} berkas</td>
                        </tr>
                        <tr>
                            <td class="pr-3 align-top">Hal</td>
                            <td class="pr-2 align-top">:</td>
                            <td class="align-top font-bold">Persetujuan Kegiatan</td>
                        </tr>
                    </tbody>
                </table>

                {{-- Isi dinamis sesuai template kategori --}}
                <div class="mt-6 flex flex-col gap-2 text-justify text-[13px]">
                    {!! $isiSurat !!}
                </div>

                {{-- Tanda tangan --}}
                <div class="mt-14 flex justify-end">
                    <div class="w-fit" style="text-align:left">
                        <div>Ketua Program Studi,</div>
                        <div class="h-20"></div>
                        <div><span class="font-bold underline">{{ $namaKaprodi }}</span></div>
                        <div>NIP. {{ $nipKaprodi }}</div>
                    </div>
                </div>

                {{-- Lampiran (halaman terakhir): daftar lampiran + daftar peserta --}}
                <section id="lampiran-halaman" class="lampiran-surat mt-12 border-t-2 border-dashed border-gray-300 pt-8 {{ $jumlahLampiran > 0 ? '' : 'hidden' }}">
                    <table class="text-[13px]">
                        <tbody>
                            <tr><td class="pr-3 align-top">Lampiran</td><td class="pr-2 align-top">:</td><td class="align-top">Surat Persetujuan Kegiatan</td></tr>
                            <tr><td class="pr-3 align-top">Nomor</td><td class="pr-2 align-top">:</td><td class="js-nomor-surat align-top">{{ $nomorAwal !== '' ? $nomorAwal : '[nomor surat belum diisi]' }}</td></tr>
                            <tr><td class="pr-3 align-top">Tanggal</td><td class="pr-2 align-top">:</td><td class="align-top">{{ $tanggalSurat }}</td></tr>
                        </tbody>
                    </table>

                    <div class="mt-6 font-bold">Daftar Lampiran</div>
                    <ol id="lampiran-daftar" class="ml-6 list-decimal text-[13px]">
                        @if ($adaLampiran)
                            <li>Daftar Peserta (halaman ini)</li>
                        @endif
                        @foreach ($lampiranDokumen as $dok)
                            <li>{{ $dok[0] }}</li>
                        @endforeach
                    </ol>

                    @if ($adaLampiran)
                        <div class="mt-8 text-center font-bold uppercase">Daftar Peserta</div>
                        <div class="text-center">{{ $judul }}</div>

                        <table class="mt-4 w-full border-collapse text-[13px]">
                            <thead>
                                <tr>
                                    <th class="w-12 border border-black px-2 py-1 text-center font-bold">No</th>
                                    <th class="border border-black px-2 py-1 text-left font-bold">Nama</th>
                                    <th class="w-40 border border-black px-2 py-1 text-left font-bold">Keterangan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($peserta as $i => [$namaPeserta, $peranPeserta])
                                    <tr>
                                        <td class="border border-black px-2 py-1 text-center">{{ $i + 1 }}</td>
                                        <td class="border border-black px-2 py-1">{{ $namaPeserta }}</td>
                                        <td class="border border-black px-2 py-1">{{ $peranPeserta }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif

                    <div class="mt-10 flex justify-end">
                        <div class="w-fit" style="text-align:left">
                            <div>Ketua Program Studi,</div>
                            <div class="h-20"></div>
                            <div><span class="font-bold underline">{{ $namaKaprodi }}</span></div>
                            <div>NIP. {{ $nipKaprodi }}</div>
                        </div>
                    </div>
                </section>
            </article>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
(() => {
    const modal = document.getElementById('modal-surat');
    if (!modal) return;

    // Pindahkan ke <body> supaya tidak terpotong/tertutup stacking context layout
    document.body.appendChild(modal);

    const input = document.getElementById('nomor_surat');
    const FALLBACK = '[nomor surat belum diisi]';

    // Perbarui semua tempat nomor surat (pratinjau kecil, modal, dan [NOMOR_SURAT] di isi template)
    const sync = () => {
        const v = input ? input.value.trim() : '';
        document.querySelectorAll('.js-nomor-surat').forEach((el) => {
            el.textContent = v || FALLBACK;
        });
    };
    if (input) {
        input.addEventListener('input', sync);
        sync();
    }

    // Lampiran: surat mengikuti berkas yang dicentang admin
    const cekLampiran     = Array.from(document.querySelectorAll('.js-lampiran'));
    const ADA_PESERTA     = @json($adaLampiran);
    const barisLampiran   = document.getElementById('lampiran-baris');
    const jumlahLampiran  = document.getElementById('lampiran-jumlah');
    const halamanLampiran = document.getElementById('lampiran-halaman');
    const daftarLampiran  = document.getElementById('lampiran-daftar');

    const syncLampiran = () => {
        const dipilih = cekLampiran.filter((c) => c.checked).map((c) => c.dataset.nama);
        const total = (ADA_PESERTA ? 1 : 0) + dipilih.length;

        if (barisLampiran) barisLampiran.classList.toggle('hidden', total === 0);
        if (halamanLampiran) halamanLampiran.classList.toggle('hidden', total === 0);
        if (jumlahLampiran) jumlahLampiran.textContent = total + ' berkas';

        if (daftarLampiran) {
            daftarLampiran.replaceChildren();
            const tambah = (teks) => {
                const li = document.createElement('li');
                li.textContent = teks; // textContent: aman dari XSS
                daftarLampiran.appendChild(li);
            };
            if (ADA_PESERTA) tambah('Daftar Peserta (halaman ini)');
            dipilih.forEach(tambah);
        }
    };
    cekLampiran.forEach((c) => c.addEventListener('change', syncLampiran));
    syncLampiran();

    const buka = () => {
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        document.body.style.overflow = 'hidden';
    };
    const tutup = () => {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        document.body.style.overflow = '';
    };

    document.getElementById('btn-buka-surat')?.addEventListener('click', buka);
    document.getElementById('btn-tutup-surat')?.addEventListener('click', tutup);
    document.getElementById('btn-cetak-surat')?.addEventListener('click', () => window.print());

    modal.addEventListener('click', (e) => { if (e.target === modal) tutup(); });
    document.addEventListener('keydown', (e) => { if (e.key === 'Escape') tutup(); });
})();
</script>
@endpush