@extends('layouts.app')

@section('title', 'Mata Kuliah - Teknologi Rekayasa Komputer')
@section('description', 'Daftar mata kuliah Program Studi Teknologi Rekayasa Komputer')

@section('content')
<style>
    @keyframes matkulIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: none; } }
    .matkul-in { animation: matkulIn .45s cubic-bezier(.16, 1, .3, 1) both; }
</style>
<div class="min-h-screen bg-[#F2F6FD] font-['Plus_Jakarta_Sans',sans-serif]">

    <x-navbar />

    {{-- ===================== HEADER ===================== --}}
    <header class="font-['Poppins',sans-serif] relative overflow-hidden bg-[linear-gradient(91deg,#0A5BE0_0%,#00235C_100%)] px-6 pb-[25px] pt-[38px] text-white lg:px-[153px]">
        <div class="pointer-events-none absolute right-0 top-[-119px] size-[420px] rounded-full bg-white/[0.06]"></div>
        <div class="pointer-events-none absolute right-[250px] top-[75px] hidden size-[260px] rounded-full bg-[#FFC928]/[0.14] lg:block"></div>

        <p class="relative text-[13px] leading-4 tracking-[-0.4px] text-[#BFD6FF]">
            <a href="{{ route('home') }}" class="hover:underline">Beranda</a> › <a href="{{ route('home') }}#tentang" class="hover:underline">Tentang TRK</a> › <span class="text-[#FFC928]">Mata Kuliah TRK</span>
        </p>
        <h1 class="relative mt-[7px] text-[32px] font-bold leading-[58px] tracking-[-0.4px] sm:text-[44px]">Mata Kuliah</h1>
        <p class="relative mt-1.5 max-w-[459px] text-[15px] leading-5 tracking-[-0.4px] text-[#E3EEFF]">
            Daftar mata kuliah Program Studi Teknologi Rekayasa Komputer Politeknik Negeri Semarang
        </p>
    </header>

    <main class="mx-auto max-w-[1584px] px-4 sm:px-6 2xl:px-0">

        {{-- ============ BARIS KONTROL: Kembali | Filter | Statistik ============ --}}
        <div class="flex flex-col gap-6 pt-6 2xl:flex-row 2xl:items-center 2xl:pl-[18px] 2xl:pt-[18px]">

            {{-- Tombol Kembali --}}
            <a href="{{ route('home') }}"
               class="inline-flex items-center gap-1 self-start rounded-full bg-[#2277FF] p-0.5 pr-4 text-2xl font-semibold text-white 2xl:mr-[131px]">
                <span class="flex size-[42px] items-center justify-center rounded-full bg-[#5695FB]">
                    <svg class="size-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M15 6l-6 6l6 6" /></svg>
                </span>
                Kembali
            </a>

            {{-- Filter kurikulum + semester (diganti tanpa muat ulang oleh script di bawah) --}}
            <form id="matkul-form" method="GET" action="{{ route('mata-kuliah') }}"
                  class="flex w-full flex-col gap-6 sm:w-auto sm:flex-row 2xl:gap-[126px]">
                @foreach ($filters as $f)
                    <div class="flex w-full flex-col gap-2.5 sm:w-[300px]">
                        <label for="{{ $f['name'] }}" class="text-xl font-semibold leading-6 text-black">{{ $f['label'] }}</label>
                        <div class="relative flex h-[52px] items-center rounded-[13px] border-2 border-[#CFE0FF] bg-white shadow-[0_6px_16px_rgba(0,50,136,0.08)] focus-within:border-[#2174F5]">
                            <svg class="pointer-events-none absolute left-6 size-5 text-[#2174F5]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 5m0 2a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v12a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2z" /><path d="M16 3l0 4" /><path d="M8 3l0 4" /><path d="M4 11l16 0" /><path d="M8 15h2v2h-2z" /></svg>
                            <select id="{{ $f['name'] }}" name="{{ $f['name'] }}"
                                    class="h-full w-full cursor-pointer appearance-none rounded-[11px] bg-transparent pl-[60px] pr-10 text-xl font-semibold text-[#0647B8] outline-none">
                                @foreach ($f['options'] as $value => $text)
                                    <option value="{{ $value }}" @selected((string) $value === (string) $f['selected'])>{{ $text }}</option>
                                @endforeach
                            </select>
                            <svg class="pointer-events-none absolute right-6 size-4 text-[#0647B8]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M6 9l6 6l6 -6" /></svg>
                        </div>
                    </div>
                @endforeach
                <noscript><button type="submit" class="h-[52px] self-end rounded-[13px] bg-[#2277FF] px-6 font-semibold text-white">Tampilkan</button></noscript>
            </form>

            {{-- Kartu statistik --}}
            <div id="matkul-stats" class="flex w-full gap-3 transition-opacity duration-200 ease-out sm:w-[424px] sm:gap-5 2xl:ml-auto">
                @include('mata-kuliah._stats')
            </div>
        </div>

        {{-- ===================== KARTU TABEL ===================== --}}
        <div id="matkul-tabel" class="mt-5 transition-opacity duration-200 ease-out">
            @include('mata-kuliah._tabel')
        </div>

        <p class="mt-3 text-center text-base font-medium text-[#6B7A99] sm:text-xl">
            SKS = Satuan Kredit Semester • Jam = jam tatap muka per minggu
        </p>
    </main>

    <div class="pb-6"></div>
    <x-footer />
</div>
@endsection

@push('scripts')
<script>
    // Ganti kurikulum / semester tanpa muat ulang halaman
    (function () {
        var DATA_URL = @json(route('mata-kuliah.data'));
        var form = document.getElementById('matkul-form');
        var selKur = document.getElementById('kurikulum');
        var selSem = document.getElementById('semester');
        var statsEl = document.getElementById('matkul-stats');
        var tabelEl = document.getElementById('matkul-tabel');
        var ctrl = null;

        function loading(on) {
            [statsEl, tabelEl].forEach(function (el) { el.classList.toggle('opacity-40', on); });
            [selKur, selSem].forEach(function (el) { el.disabled = on; });
        }

        function fill(select, options, selected) {
            select.innerHTML = '';
            options.forEach(function (o) {
                var opt = document.createElement('option');
                opt.value = o.value; opt.textContent = o.label;
                if (String(o.value) === String(selected)) opt.selected = true;
                select.appendChild(opt);
            });
        }

        function load() {
            if (ctrl) ctrl.abort();
            ctrl = new AbortController();
            var qs = new URLSearchParams({ kurikulum: selKur.value, semester: selSem.value });
            loading(true);

            fetch(DATA_URL + '?' + qs, { headers: { 'Accept': 'application/json' }, signal: ctrl.signal })
                .then(function (r) { if (!r.ok) throw new Error(r.status); return r.json(); })
                .then(function (d) {
                    statsEl.innerHTML = d.statsHtml;
                    tabelEl.innerHTML = d.tableHtml;
                    fill(selSem, d.semesterOptions, d.semester);
                    [statsEl, tabelEl].forEach(function (el) {
                        el.classList.remove('matkul-in'); void el.offsetWidth; el.classList.add('matkul-in');
                    });
                    history.replaceState(null, '', '?' + new URLSearchParams({ kurikulum: d.kurikulum, semester: d.semester }));
                    loading(false);
                })
                .catch(function (e) {
                    if (e.name === 'AbortError') return;
                    loading(false);
                    form.submit(); // cadangan: muat ulang biasa
                });
        }

        selKur.addEventListener('change', load);
        selSem.addEventListener('change', load);
    })();
</script>
@endpush