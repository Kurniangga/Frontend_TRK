{{--
    resources/views/admin/mata-kuliah.blade.php
    Daftar mata kuliah per kurikulum & semester. Ganti dropdown = muat ulang halaman (GET), cari = tekan Enter.
    Data dari MataKuliahRepository (sama dengan halaman publik).
--}}
@extends('layouts.admin')

@section('title', 'Mata Kuliah')

@php
    $selectCls = 'h-10 w-full appearance-none rounded-lg border border-[#E8E8EC] bg-white px-4 pr-10 text-sm sm:text-base text-[#2E2E2E] outline-none focus:border-[#2277FF]';
    $chev = '<svg class="pointer-events-none absolute right-4 top-1/2 -translate-y-1/2 text-[#1C2024]" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 9l6 6l6 -6" /></svg>';
    $th = 'border border-[#00002F]/15 px-4 py-3 text-center font-semibold';
    $td = 'border border-[#00002F]/15 px-4 py-3 text-center';
    // Halaman "Tambah Mata Kuliah" belum dibuat: tombol aktif otomatis begitu route ini didaftarkan.
    $urlTambah = \Illuminate\Support\Facades\Route::has('admin.kurikulum.mata-kuliah.create') ? route('admin.kurikulum.mata-kuliah.create') : '#';
@endphp

@section('content')
<main class="flex flex-col gap-10 px-4 py-8 sm:px-6">
    <div class="flex flex-col gap-2">
        <h1 class="text-[32px] font-bold leading-[38px] tracking-[-0.2px]">Mata Kuliah</h1>
        <x-admin.breadcrumb :items="[['Dashboard', 'admin.dashboard'], ['Kurikulum'], ['Mata Kuliah']]" />
    </div>

    @if (session('success'))
        <div class="rounded-md border border-[#D6EDB5] bg-[#E6F3D3] px-4 py-3 text-sm text-[#4C7D08]" role="status">{{ session('success') }}</div>
    @endif

    <div class="flex flex-col gap-6 rounded-lg bg-white p-4 pb-6 shadow-sm sm:p-6">

        {{-- Judul kartu + tombol --}}
        <div class="flex flex-col items-start justify-between gap-4 sm:flex-row sm:items-center">
            <div>
                <h2 class="text-lg font-bold text-[#0A0A0A]">Daftar Mata Kuliah</h2>
                <p class="mt-1 text-sm text-[#6B6B72]">Mata kuliah berdasarkan kurikulum dan semester</p>
            </div>
            <a href="{{ $urlTambah }}" class="flex shrink-0 items-center gap-1 rounded-md bg-[#2277FF] px-4 py-2 text-lg font-medium tracking-[-0.18px] text-[#FEFEFE] shadow-[0_1px_2px_rgba(0,0,0,0.12)]">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 5l0 14" /><path d="M5 12l14 0" /></svg>
                Mata Kuliah
            </a>
        </div>

        {{-- Filter --}}
        <form id="filter" method="GET" action="{{ route('admin.kurikulum.mata-kuliah') }}" class="flex flex-col gap-4 lg:flex-row lg:items-end">
            <div class="flex w-full flex-col gap-1.5 lg:w-64 lg:shrink-0">
                <label for="kurikulum" class="text-sm font-medium text-[#0A0A0A]">Kurikulum</label>
                <div class="relative">
                    <select id="kurikulum" name="kurikulum" class="{{ $selectCls }}">
                        @foreach ($kurikulumList as $k)
                            <option value="{{ $k['id'] }}" @selected($k['id'] === $kurikulumId)>{{ $k['nama'] }}</option>
                        @endforeach
                    </select>
                    {!! $chev !!}
                </div>
            </div>
            <div class="flex w-full flex-col gap-1.5 lg:w-48 lg:shrink-0">
                <label for="semester" class="text-sm font-medium text-[#0A0A0A]">Semester</label>
                <div class="relative">
                    <select id="semester" name="semester" class="{{ $selectCls }}">
                        @foreach ($semesterList as $n)
                            <option value="{{ $n }}" @selected($n === $semester)>Semester {{ $n }}</option>
                        @endforeach
                    </select>
                    {!! $chev !!}
                </div>
            </div>
            <div class="flex h-10 flex-1 items-center gap-2 rounded-lg border border-[#E8E8EC] bg-white px-3 focus-within:border-[#2277FF]">
                <svg class="size-6 shrink-0 text-[#A8A8A8]" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="10" cy="10" r="7"/><path d="M21 21l-6-6"/></svg>
                <input type="search" name="q" value="{{ $q }}" placeholder="Cari kode atau nama mata kuliah..." class="min-w-0 flex-1 bg-transparent text-base tracking-[-0.18px] outline-none placeholder:text-[#A8A8A8]">
            </div>
        </form>

        {{-- Tabel --}}
        <div class="overflow-x-auto rounded-sm border border-[#00002F]/15">
            <table class="w-full min-w-[800px] border-collapse text-left text-sm text-[#1C2024]">
                <thead class="bg-[#000055]/[0.03] text-[#0A0A0A]">
                    <tr>
                        <th rowspan="2" class="{{ $th }} w-16">Smt</th>
                        <th rowspan="2" class="{{ $th }} w-36">Kode MK/Blok</th>
                        <th rowspan="2" class="{{ $th }}">Nama MK/Blok</th>
                        <th colspan="2" class="{{ $th }} py-2">SKS</th>
                        <th colspan="2" class="{{ $th }} py-2">JAM</th>
                    </tr>
                    <tr>
                        @foreach (['Teori', 'Praktek', 'Teori', 'Praktek'] as $h)
                            <th class="{{ $th }} w-20 px-2 py-2">{{ $h }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody class="bg-white">
                    @forelse ($courses as $c)
                        <tr class="transition-colors hover:bg-[#F7F7F8]">
                            @if ($loop->first)
                                <td rowspan="{{ $loop->count }}" class="border border-[#00002F]/15 px-4 py-3 pt-6 text-center align-top font-medium">{{ $romawi }}</td>
                            @endif
                            <td class="{{ $td }}">{{ $c['kode'] }}</td>
                            <td class="border border-[#00002F]/15 px-4 py-3">
                                <div class="font-medium text-[#0A0A0A]">{{ $c['nama'] }}</div>
                                @if (! empty($c['nama_en']))
                                    <div class="mt-0.5 text-xs text-[#6B6B72]">({{ $c['nama_en'] }})</div>
                                @endif
                            </td>
                            <td class="{{ $td }}">{{ $c['sks_teori'] }}</td>
                            <td class="{{ $td }}">{{ $c['sks_praktek'] }}</td>
                            <td class="{{ $td }}">{{ $c['jam_teori'] }}</td>
                            <td class="{{ $td }}">{{ $c['jam_praktek'] }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="border border-[#00002F]/15 p-6 text-center text-[#A8A8A8]">Tidak ada mata kuliah{{ $q !== '' ? ' yang cocok dengan pencarian' : '' }}.</td></tr>
                    @endforelse
                </tbody>
                <tfoot class="bg-[#000055]/[0.05] font-bold text-[#0A0A0A]">
                    <tr>
                        <td colspan="3" class="border border-[#00002F]/15 px-4 py-3 text-left">Sub Total Smt {{ $romawi }}</td>
                        <td class="{{ $td }}">{{ $total['sks_teori'] }}</td>
                        <td class="{{ $td }}">{{ $total['sks_praktek'] }}</td>
                        <td class="{{ $td }}">{{ $total['jam_teori'] }}</td>
                        <td class="{{ $td }}">{{ $total['jam_praktek'] }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <p class="text-sm text-[#6B6B72]">
            Menampilkan {{ count($courses) }}{{ $q !== '' ? ' dari '.$jumlahSemua : '' }} mata kuliah pada Semester {{ $romawi }}
        </p>
    </div>
</main>
@endsection

@push('scripts')
<script>
    // Ganti kurikulum / semester -> muat ulang dengan pilihan baru (cari tetap terbawa).
    document.querySelectorAll('#kurikulum, #semester').forEach(function (el) {
        el.addEventListener('change', function () {
            // ganti kurikulum: semester ikut direset ke yang pertama
            if (el.id === 'kurikulum') document.getElementById('semester').disabled = true;
            document.getElementById('filter').submit();
        });
    });
</script>
@endpush