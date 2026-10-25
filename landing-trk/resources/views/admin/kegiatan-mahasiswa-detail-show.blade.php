{{--
    resources/views/admin/kegiatan-mahasiswa-detail.blade.php
    Detail pengajuan kegiatan mahasiswa (admin, BACA SAJA). Dibuka dari ikon mata di daftar Kegiatan Mahasiswa.
    Data $d dikirim dari AdminKegiatanMahasiswaDetailShowController@show.
--}}
@extends('layouts.admin')

@section('title', 'Detail Kegiatan Mahasiswa')

@php
    $statusTone = [
        'Diajukan'  => 'border-[#CDDEF9] bg-[#CDDEF9] text-[#2277FF]',
        'Direvisi'  => 'border-[#FAD4A8] bg-[#FBE5CB] text-[#854700]',
        'Revisi'    => 'border-[#FAD4A8] bg-[#FBE5CB] text-[#854700]',
        'Disetujui' => 'border-[#CFE8C4] bg-[#E3F5DC] text-[#2F7A14]',
        'Ditolak'   => 'border-[#FAA8A8] bg-[#FDE0E0] text-[#E00000]',
    ];
    $badge = $statusTone[$d['status']] ?? 'border-[#E4E4E7] bg-[#F2F2F7] text-[#55555C]';

    $info = [
        'Pengaju'       => $d['pengaju'],
        'Kategori'      => $d['kategori'],
        'Jenis'         => $d['jenis'],
        'Penyelenggara' => $d['penyelenggara'],
        'Tanggal'       => $d['tanggal'],
    ];

    $section = 'flex flex-col gap-6 rounded-md border border-[#0A0A0A]/[0.06] bg-white px-4 pb-6 pt-4';
    $num     = 'flex size-6 items-center justify-center rounded-full bg-[#2277FF] text-sm font-semibold text-white';
    $tableBox = 'overflow-x-auto rounded-md border border-[#00002F]/15 bg-white/80 text-sm text-[#1C2024]';
@endphp

@section('content')
<main class="flex flex-col gap-10 px-4 py-8 sm:px-6">

    {{-- Judul + status + tombol kembali --}}
    <div class="flex flex-wrap items-center justify-between gap-3">
        <div>
            <div class="flex flex-wrap items-center gap-2">
                <h1 class="text-[32px] font-bold leading-[38px] tracking-[-0.2px]">{{ $d['judul'] }}</h1>
                <span class="rounded-lg border px-2 py-0.5 text-xs font-medium {{ $badge }}">{{ $d['status'] }}</span>
            </div>
            <x-admin.breadcrumb :items="[
                ['Dashboard', 'admin.dashboard'],
                ['Kegiatan'],
                ['Kegiatan Mahasiswa', 'admin.kegiatan.mahasiswa'],
                ['Detail'],
            ]" />
        </div>

        
    </div>

    <div class="grid items-start gap-3 rounded-lg bg-white p-4 pb-6 lg:grid-cols-2">

        {{-- Kolom kiri --}}
        <div class="flex flex-col gap-3">

            {{-- 1. Informasi kegiatan --}}
            <section class="{{ $section }}">
                <div class="flex items-center gap-3">
                    <span class="{{ $num }}">1</span>
                    <h2 class="text-sm font-semibold text-[#0A0A0A]">Informasi kegiatan</h2>
                </div>
                <div class="flex flex-col">
                    @foreach ($info as $k => $v)
                        @unless ($loop->first)
                            <div class="h-px bg-[#0A0A0A]/[0.06]"></div>
                        @endunless
                        <div class="flex items-center justify-between gap-6 p-3 text-sm leading-5 text-[#2E2E2E]">
                            <span class="font-medium">{{ $k }}</span>
                            <span class="text-right font-semibold">{{ $v }}</span>
                        </div>
                    @endforeach
                </div>
            </section>

            {{-- 2. Anggota kegiatan --}}
            <section class="{{ $section }}">
                <div class="flex items-center gap-3">
                    <span class="{{ $num }}">2</span>
                    <h2 class="text-sm font-semibold text-[#0A0A0A]">Anggota Kegiatan</h2>
                </div>
                <div class="{{ $tableBox }}">
                    <table class="w-full min-w-[320px] text-left">
                        <thead>
                            <tr class="bg-[#000055]/[0.02] font-medium">
                                <th class="min-h-9 px-2 py-2 font-medium">Nama</th>
                                <th class="min-h-9 px-2 py-2 font-medium">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($d['anggota'] as $a)
                                <tr class="border-t border-[#00002F]/15">
                                    <td class="px-2 py-2">{{ $a['nama'] }}</td>
                                    <td class="px-2 py-2">{{ $a['status'] }}</td>
                                </tr>
                            @empty
                                <tr class="border-t border-[#00002F]/15">
                                    <td colspan="2" class="px-2 py-3 text-center text-[#80838D]">Tidak ada anggota.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>
        </div>

        {{-- Kolom kanan --}}
        <div class="flex flex-col gap-3">

            {{-- 3. Dokumen pendukung --}}
            <section class="{{ $section }}">
                <div class="flex items-center gap-3">
                    <span class="{{ $num }}">3</span>
                    <h2 class="text-sm font-semibold text-[#0A0A0A]">Dokumen Pendukung</h2>
                </div>
                <div class="{{ $tableBox }}">
                    <table class="w-full min-w-[320px] text-left">
                        <thead>
                            <tr class="bg-[#000055]/[0.02] font-medium">
                                <th class="min-h-9 px-2 py-2 font-medium">Nama</th>
                                <th class="min-h-9 px-2 py-2 font-medium">Kategori</th>
                                <th class="min-h-9 w-[70px] px-2 py-2 font-medium">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($d['dokumen'] as $doc)
                                <tr class="border-t border-[#00002F]/15">
                                    <td class="px-2 py-2"><span class="block max-w-[200px] truncate">{{ $doc['nama'] }}</span></td>
                                    <td class="px-2 py-2">{{ $doc['kategori'] }}</td>
                                    <td class="px-2 py-2">
                                        {{-- TODO-DB: isi 'url' dengan tautan file dokumen --}}
                                        <a href="{{ $doc['url'] ?? '#' }}" target="_blank" rel="noopener"
                                           aria-label="Lihat {{ $doc['nama'] }}"
                                           class="inline-flex rounded-md bg-[#CDDEF9] p-1.5 text-[#2277FF] hover:brightness-95">
                                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M10 12a2 2 0 1 0 4 0a2 2 0 0 0 -4 0" /><path d="M21 12c-2.4 4 -5.4 6 -9 6c-3.6 0 -6.6 -2 -9 -6c2.4 -4 5.4 -6 9 -6c3.6 0 6.6 2 9 6" /></svg>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr class="border-t border-[#00002F]/15">
                                    <td colspan="3" class="px-2 py-3 text-center text-[#80838D]">Tidak ada dokumen.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>

            {{-- 4. Riwayat status --}}
            <section class="{{ $section }}">
                <div class="flex items-center gap-3">
                    <span class="{{ $num }}">4</span>
                    <h2 class="text-sm font-semibold text-[#0A0A0A]">Riwayat Status</h2>
                </div>
                <ul class="flex flex-col gap-2">
                    @forelse ($d['riwayat'] as $r)
                        <li class="flex items-center gap-2 text-sm font-semibold leading-5 text-[#7F7F7F]">
                            <span class="size-1.5 shrink-0 rounded-full bg-[#7F7F7F]"></span>
                            {{ $r }}
                        </li>
                    @empty
                        <li class="text-sm text-[#80838D]">Belum ada riwayat.</li>
                    @endforelse
                </ul>
            </section>
        </div>
    </div>
</main>
@endsection