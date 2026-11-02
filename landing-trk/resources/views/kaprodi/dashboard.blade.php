{{--
    resources/views/kaprodi/dashboard.blade.php
    Data (sementara contoh) datang dari KaprodiDashboardController. Ganti dengan database nanti.
    Tabel "Kegiatan Menunggu Validasi" memakai DataTables 2.x: tanpa paginasi, hanya 5 kegiatan terbaru (urut Tanggal Diajukan).
--}}
@extends('layouts.kaprodi')

@section('title', 'Dashboard')

@php
    /* ==================== HITUNGAN GRAFIK ==================== */
    // Batas atas sumbu Y = kelipatan 20, supaya 5 garis bantu selalu angka bulat (20, 15, 10, 5, 0)
    $axisMax    = max(20, (int) ceil(max($kegiatanPerTahun) / 20) * 20);
    $axisLabels = array_map(fn ($i) => (int) ($axisMax * $i / 4), [4, 3, 2, 1, 0]);

    $years = [];
    foreach ($kegiatanPerTahun as $tahun => $jumlah) {
        $years[] = ['y' => (string) $tahun, 'v' => $jumlah, 'ratio' => round($jumlah / $axisMax, 4)];
    }

    $cardShadow = 'shadow-[1px_0_3px_rgba(0,0,0,0.02),5px_2px_6px_rgba(0,0,0,0.02),12px_4px_8px_rgba(0,0,0,0.01)]';
@endphp

@section('content')
{{-- CSS DataTables + penyesuaian tampilan (sama dengan halaman Kegiatan, Berita, dll.) --}}
<link rel="stylesheet" href="https://cdn.datatables.net/2.1.8/css/dataTables.dataTables.min.css">
<style>
    /* ---------- Kotak tabel Menunggu Validasi ---------- */
    #validasi-wrap .dt-layout-row { margin: 0; }
    #validasi-wrap .dt-layout-row.dt-layout-table {
        border: 1px solid rgb(0 0 47 / .15);
        border-radius: 6px;
        overflow: hidden;                    /* latar header ikut terpotong sudut bulat */
        background: #fff;
    }
    #validasi-wrap .dt-layout-table .dt-layout-cell { width: 100%; padding: 0; overflow-x: auto; }

    #tabelValidasi { width: 100%; min-width: 640px; table-layout: fixed; border-collapse: collapse; margin: 0 !important; font-size: 14px; color: #1C2024; }

    /* Lebar kolom (total harus 100%): Nama Kegiatan 38, Nama Dosen 26, Tanggal Diajukan 24, Aksi 12 */
    #tabelValidasi.dataTable > thead > tr > th:nth-child(1) { width: 38%; }
    #tabelValidasi.dataTable > thead > tr > th:nth-child(2) { width: 26%; }
    #tabelValidasi.dataTable > thead > tr > th:nth-child(3) { width: 24%; }
    #tabelValidasi.dataTable > thead > tr > th:nth-child(4) { width: 12%; }

    /* Header: latar abu penuh, semua kolom rata kiri */
    #tabelValidasi.dataTable > thead > tr > th {
        padding: 8px; font-weight: 510; text-align: left;
        background: #F9F9FC;
        border-bottom: 1px solid rgb(0 0 47 / .15);
    }
    #tabelValidasi.dataTable > tbody > tr > td {
        padding: 8px; text-align: left;
        border-top: 1px solid rgb(0 0 47 / .15);
        box-shadow: none;
    }
    #tabelValidasi.dataTable > tbody > tr:first-child > td { border-top: 0; }
    #tabelValidasi.dataTable > tbody > tr > td.truncate { overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
    #tabelValidasi.dataTable > tbody > tr > td.dt-empty { padding: 24px; text-align: center; color: #80838D; }

    /* Kolom pertama: beri jarak dari tepi kiri kotak (header & isi sejajar) */
    #tabelValidasi.dataTable > thead > tr > th:first-child,
    #tabelValidasi.dataTable > tbody > tr > td:first-child:not(.dt-empty) { padding-left: 20px; }
</style>

<main class="p-4 sm:p-6">
        <div class="flex flex-col gap-4 sm:gap-5">

            {{-- ===================== JUDUL + KARTU STATISTIK ===================== --}}
            <div class="flex flex-col gap-4 sm:gap-8">
                <div class="flex flex-col gap-1 sm:gap-2">
                    <h1 class="text-2xl sm:text-[32px] font-bold leading-tight sm:leading-[38px] tracking-[-0.2px]">Dashboard</h1>
                    <p class="text-base sm:text-2xl font-medium leading-snug sm:leading-[30px] tracking-[-0.15px] text-[#A8A8A8]">Ringkasan data dosen dan kegiatan</p>
                </div>

                <div class="grid grid-cols-2 gap-3 sm:gap-6 xl:grid-cols-4">
                    @foreach ($stats as $s)
                        <div class="group relative flex h-28 sm:h-[164px] flex-col justify-between rounded-lg bg-[#E4EDFB] p-3 sm:px-3 sm:pb-6 sm:pt-4 transition-shadow {{ ($s['url'] ?? false) ? 'hover:shadow-md' : '' }} {{ $cardShadow }}">
                            <div class="flex items-center gap-2.5">
                                <span class="flex-1 text-sm sm:text-xl font-medium leading-tight sm:leading-6">{{ $s['label'] }}</span>
                                @if ($s['url'] ?? false)
                                    {{-- after:absolute after:inset-0 = seluruh kartu ikut bisa diklik --}}
                                    <a href="{{ $s['url'] }}" aria-label="Buka {{ $s['label'] }}" class="flex size-5 sm:size-6 items-center justify-center rounded-full border border-[#0A0A0A] transition-colors group-hover:bg-[#0A0A0A] group-hover:text-white after:absolute after:inset-0 after:rounded-lg after:content-['']">
                                        <svg class="size-2.5 sm:size-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 7l-10 10" /><path d="M8 7l9 0l0 9" /></svg>
                                    </a>
                                @else
                                    <span class="flex size-5 sm:size-6 items-center justify-center rounded-full border border-[#0A0A0A] opacity-40">
                                        <svg class="size-2.5 sm:size-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 7l-10 10" /><path d="M8 7l9 0l0 9" /></svg>
                                    </span>
                                @endif
                            </div>
                            <div @class([
                                'text-3xl sm:text-5xl font-bold leading-none sm:leading-[58px] tracking-[-0.4px]',
                                'text-[#FF3F3F]' => $s['red'] ?? false,
                            ])>{{ $s['value'] }}</div>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- ===================== GRAFIK KEGIATAN PER TAHUN ===================== --}}
            <div class="flex flex-col gap-4 sm:gap-8 rounded-lg bg-white px-3 pb-4 pt-3 sm:px-4 sm:pb-6 sm:pt-4 {{ $cardShadow }}">
                <h2 class="text-lg sm:text-xl font-medium leading-6">Kegiatan Per Tahun</h2>

                <div class="flex h-[240px] sm:h-[340px] gap-2 text-[10px] sm:text-xs font-medium text-[#2E2E2E]">
                    {{-- Angka sumbu Y --}}
                    <div class="flex w-4 sm:w-5 flex-col justify-between pb-6 sm:pb-7 text-center">
                        @foreach ($axisLabels as $n)
                            <span class="leading-4">{{ $n }}</span>
                        @endforeach
                    </div>

                    <div class="relative flex-1 overflow-hidden">
                        {{-- Garis bantu --}}
                        <div class="absolute inset-x-0 bottom-6 sm:bottom-7 top-2 flex flex-col justify-between">
                            @for ($i = 0; $i < 5; $i++)
                                <div class="h-px bg-[#0A0A0A]/[0.06]"></div>
                            @endfor
                        </div>

                        {{-- Batang --}}
                        <div class="absolute inset-x-0 bottom-0 top-2 flex items-end justify-between sm:justify-around gap-1 sm:gap-2 overflow-x-auto pb-6 sm:pb-7 scrollbar-hide">
                            @foreach ($years as $d)
                                <div class="group relative flex h-full w-8 sm:w-12 shrink-0 flex-col items-center justify-end gap-2">
                                    <span class="absolute -top-1 z-10 hidden whitespace-nowrap rounded-[3px] bg-white px-1 py-0.5 text-[10px] sm:text-xs font-normal shadow-md group-hover:block"
                                          style="bottom: calc({{ $d['ratio'] * 100 }}% * 0.92 + 32px); top: auto">
                                        {{ $d['v'] }}
                                    </span>
                                    <div class="w-full bg-[#226CFF] rounded-t-sm" style="height: calc(100% * {{ $d['ratio'] }})"></div>
                                    <span class="absolute -bottom-6 sm:-bottom-7 leading-4">{{ $d['y'] }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            {{-- ===================== TABEL MENUNGGU VALIDASI ===================== --}}
            <div class="flex flex-col gap-4 sm:gap-8 rounded-lg bg-white px-3 pb-4 pt-3 sm:px-4 sm:pb-6 sm:pt-4 {{ $cardShadow }}">
                <div class="flex items-center gap-3">
                    <h2 class="flex-1 text-lg sm:text-xl font-medium leading-6">Kegiatan Menunggu Validasi</h2>
                    <a href="{{ route('kaprodi.kegiatan.dosen') }}" class="rounded-md px-2 py-1 text-sm sm:text-base font-medium text-[#226CFF]">Lihat semua</a>
                </div>

                {{-- Tabel (isi <tbody> dibuat otomatis oleh DataTables: 5 kegiatan terbaru, tanpa paginasi) --}}
                <div id="validasi-wrap">
                    <table id="tabelValidasi">
                        <thead>
                            <tr>
                                <th>Nama Kegiatan</th>
                                <th>Nama Dosen</th>
                                <th>Tanggal Diajukan</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>

        </div>
    </main>
@endsection

@push('scripts')
{{-- Hapus baris jQuery ini kalau layout kaprodi kamu sudah memuat jQuery 3.x --}}
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/2.1.8/js/dataTables.min.js"></script>
<script>
(function () {
    var data = @json($rows);
    var JUMLAH_TAMPIL = 5;    // tampilkan 5 kegiatan terbaru

    function esc(s) {
        return String(s == null ? '' : s).replace(/[&<>"']/g, function (c) {
            return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c];
        });
    }

    var TXT = DataTable.render.text(); // otomatis escape HTML

    // Tanggal -> angka untuk mengurutkan. Pakai raw_date kalau ada; kalau tidak, baca teks "21 Mei 2024"
    var BULAN = { jan: 1, feb: 2, mar: 3, apr: 4, mei: 5, may: 5, jun: 6, jul: 7, agu: 8, aug: 8, ags: 8, sep: 9, okt: 10, oct: 10, nov: 11, des: 12, dec: 12 };
    function tglKeAngka(r) {
        if (r.raw_date) { var t = new Date(r.raw_date).getTime(); if (!isNaN(t)) return t; }
        var m = String(r.tanggal || '').toLowerCase().match(/(\d{1,2})\s+([a-z]+)\.?\s+(\d{4})/);
        if (m && BULAN[m[2].slice(0, 3)]) return Date.UTC(+m[3], BULAN[m[2].slice(0, 3)] - 1, +m[1]);
        return 0;
    }

    // Urutkan dari tanggal diajukan terbaru, ambil 5 teratas (kalau tanggal tidak terbaca, urutan asli dipertahankan)
    var terbaru = data
        .map(function (r, i) { return { r: r, i: i, t: tglKeAngka(r) }; })
        .sort(function (a, b) { return (b.t - a.t) || (a.i - b.i); })
        .slice(0, JUMLAH_TAMPIL)
        .map(function (x) { return x.r; });

    var I_EDIT = '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M4 20h4l10.5 -10.5a2.828 2.828 0 1 0 -4 -4l-10.5 10.5v4" /><path d="M13.5 6.5l4 4" /></svg>';

    new DataTable('#tabelValidasi', {
        data: terbaru,
        autoWidth: false,
        paging: false,        // tanpa paginasi
        searching: false,
        info: false,
        ordering: false,      // urutan tetap "terbaru dulu" (header tidak bisa diklik)
        columns: [
            { data: 'kegiatan', render: TXT, className: 'truncate' },
            { data: 'dosen',    render: TXT, className: 'truncate' },
            { data: 'tanggal',  render: TXT, className: 'truncate' },
            {
                data: null,
                render: function () {
                    // Ganti <button> menjadi <a href="..."> bila mau menautkan ke halaman validasi
                    return '<button type="button" title="Tinjau kegiatan" aria-label="Tinjau kegiatan" class="flex size-8 cursor-pointer items-center justify-center rounded-md bg-[#FBE5CB] text-[#FFAA49] hover:bg-[#F8D9B5]">' + I_EDIT + '</button>';
                }
            }
        ],
        // Tooltip: arahkan kursor ke teks yang terpotong "..." -> muncul teks lengkapnya
        createdRow: function (tr, r) {
            var td = tr.children;
            td[0].title = r.kegiatan || '';
            td[1].title = r.dosen || '';
            td[2].title = r.tanggal || '';
        },
        // Hanya tabel: tanpa kotak cari, "show entries", info, maupun paginasi
        layout: { topStart: null, topEnd: null, bottomStart: null, bottomEnd: null },
        language: {
            emptyTable: 'Tidak ada kegiatan yang menunggu validasi.',
            zeroRecords: 'Tidak ada kegiatan yang menunggu validasi.'
        }
    });
})();
</script>
@endpush