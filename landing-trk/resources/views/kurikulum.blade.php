@extends('layouts.app')

@section('title', 'Kurikulum - Teknologi Rekayasa Komputer')
@section('description', $subtitle)

@php
    $aktif = collect($daftar)->firstWhere('id', $aktifId);
@endphp

@section('content')
<style>
    .thin-scroll { scrollbar-width: thin; scrollbar-color: #C3CFE6 transparent; }
    .thin-scroll::-webkit-scrollbar { width: 8px; height: 8px; }
    .thin-scroll::-webkit-scrollbar-thumb { background: #C3CFE6; border-radius: 9999px; }
    .thin-scroll::-webkit-scrollbar-track { background: transparent; }
</style>

<div class="min-h-screen bg-white font-['Plus_Jakarta_Sans',sans-serif]">

    <x-navbar />

    <div class="flex flex-1 flex-col items-center gap-6 bg-white px-4 py-8 sm:px-8 lg:px-12 min-[1728px]:px-56">

        {{-- Header --}}
        <header class="flex w-full max-w-[1280px] flex-col items-center justify-center gap-2.5 py-4 text-center">
            <p class="text-xs tracking-[-0.18px] text-[#737C8D] sm:text-base">
                <a href="{{ route('home') }}" class="hover:text-[#2277FF]">Beranda</a> /
                <a href="{{ route('kurikulum') }}" class="hover:text-[#2277FF]">Kurikulum</a> /
                <span data-tab-label>{{ $aktif['nama'] ?? '-' }}</span>
            </p>
            <h1 data-tab-label class="text-xl font-bold leading-tight tracking-[-0.4px] text-[#2277FF] sm:text-5xl sm:leading-[58px]">{{ $aktif['nama'] ?? 'Kurikulum' }}</h1>
            <p class="text-xs font-semibold tracking-[-0.18px] text-[#4D5C73] sm:text-base">{{ $subtitle }}</p>
            <div class="h-1 w-20 rounded-sm bg-[#FFD121]"></div>
        </header>

        @if ($aktif)
            {{-- Tab kurikulum (dibuat dari data, jadi bertambah/berkurang mengikuti upload & hapus admin) --}}
            <nav class="thin-scroll flex w-full max-w-[1280px] items-center gap-3 overflow-x-auto py-1" aria-label="Pilih kurikulum">
                @foreach ($daftar as $k)
                    <button type="button"
                            data-tab="{{ $k['id'] }}"
                            data-name="{{ $k['nama'] }}"
                            data-url="{{ $k['pdf_url'] }}"
                            data-file="{{ $k['file_name'] }}"
                            data-active="{{ $k['id'] === $aktifId ? 'true' : 'false' }}"
                            class="h-[44px] sm:h-[52px] min-w-[140px] sm:min-w-[160px] flex-1 whitespace-nowrap rounded-[10px] bg-white px-3 sm:px-4 text-sm sm:text-[17px] font-medium leading-[1.4] text-[#667085] transition-colors hover:bg-[#F1F6FE] data-[active=true]:bg-[#EAF2FD] data-[active=true]:font-semibold data-[active=true]:text-[#0B3A8C] data-[active=true]:hover:bg-[#EAF2FD] lg:max-w-[246px]">
                        {{ $k['nama'] }}
                    </button>
                @endforeach
            </nav>

            {{-- PDF viewer --}}
            <section id="pdf-viewer"
                     class="flex h-[450px] w-full max-w-[1280px] flex-col overflow-hidden rounded-[20px] border border-[#DCE6F7] bg-[#E0E8F5] sm:h-[656px] [&:fullscreen]:h-screen [&:fullscreen]:max-w-none [&:fullscreen]:rounded-none [&:fullscreen]:border-0">

                {{-- Toolbar --}}
                <div class="flex h-12 sm:h-16 shrink-0 items-center justify-between gap-2 sm:gap-3 overflow-x-auto bg-[#0B3A8C] px-3 sm:px-5">
                    <div class="flex items-center gap-2 sm:gap-3.5 text-sm sm:text-lg font-medium text-white">
                        <x-tool-button label="Tampilkan thumbnail" icon="menu" action="toggle-thumbs" />
                        <span class="hidden sm:inline">Halaman</span>
                        <span id="current-page" class="flex h-7 sm:h-9 min-w-8 sm:min-w-10 items-center justify-center rounded-lg bg-[#315B9E] px-2 text-xs sm:text-base">1</span>
                        <span class="whitespace-nowrap text-xs sm:text-base">/ <span id="total-pages">…</span></span>
                    </div>

                    <div class="flex items-center gap-2 sm:gap-4 text-sm sm:text-lg font-medium text-white">
                        <x-tool-button label="Zoom out" icon="minus" action="zoom-out" />
                        <span id="zoom-label" class="w-10 sm:w-14 text-center text-xs sm:text-base">100%</span>
                        <x-tool-button label="Zoom in" icon="plus" action="zoom-in" />
                    </div>

                    <div class="flex items-center gap-1.5 sm:gap-2.5">
                        <x-tool-button label="Unduh" icon="download" action="download" />
                        <x-tool-button label="Cetak" icon="printer" action="print" />
                        <x-tool-button label="Layar penuh" icon="maximize" action="fullscreen" />
                    </div>
                </div>

                <div class="flex min-h-0 flex-1">
                    {{-- Thumbnail --}}
                    <aside id="thumbs" class="thin-scroll hidden w-[148px] shrink-0 flex-col items-center overflow-y-auto bg-[#F1F6FE] p-4 sm:flex" aria-label="Thumbnail halaman"></aside>

                    {{-- Halaman --}}
                    <div id="scroller" class="thin-scroll relative min-w-0 flex-1 overflow-auto bg-[#DFE8F6]">
                        <div id="pages" class="mx-auto flex w-full flex-col items-center gap-6 px-6 py-8"></div>
                        <div id="state" class="absolute inset-0 flex items-center justify-center px-6 text-center text-base font-medium text-[#0B3A8C]">Memuat dokumen…</div>
                    </div>
                </div>
            </section>

            {{-- Baris bawah --}}
            <div class="flex w-full max-w-[1280px] flex-col items-center justify-center gap-4 sm:flex-row sm:justify-between sm:gap-10">
                <div class="flex w-full items-center justify-between gap-4 sm:w-auto">
                    <a href="{{ route('home') }}"
                       class="flex h-[40px] sm:h-[45px] w-auto shrink-0 items-center gap-1 rounded-full bg-[#2277FF] p-0.5 px-3 text-base sm:text-2xl font-semibold text-white">
                        <span class="flex size-[32px] sm:size-[41px] items-center justify-center rounded-full bg-[#5695FB]">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#FEFEFE" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M15 6l-6 6l6 6" /></svg>
                        </span>
                        Kembali
                    </a>

                    <div class="flex min-w-0 flex-1 items-center gap-2 text-sm sm:text-[17px] leading-[1.4]">
                        <span class="truncate font-medium text-[#0B3A8C]" data-file-label>{{ $aktif['file_name'] }}</span>
                        <span class="whitespace-nowrap text-[#4D5C73]">· <span id="page-count">…</span> halaman</span>
                    </div>
                </div>

                <button type="button" data-action="download"
                        class="flex h-[44px] sm:h-[52px] w-full items-center justify-center gap-3 rounded-xl border border-[#2277FF] bg-white text-sm sm:text-[17px] font-medium text-[#2277FF] transition-colors hover:bg-[#EAF2FD] sm:w-[220px]">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M4 17v2a2 2 0 0 0 2 2h12a2 2 0 0 0 2 -2v-2 M7 11l5 5l5 -5 M12 4l0 12" /></svg>
                    Unduh PDF
                </button>
            </div>
        @else
            <div class="flex min-h-[320px] w-full max-w-[1280px] flex-col items-center justify-center gap-4 rounded-[22px] border border-[#E4ECF9] bg-[#F1F6FE] px-6 text-center">
                <p class="text-lg font-semibold text-[#0B3A8C]">Dokumen kurikulum belum tersedia</p>
                <p class="max-w-md text-[#5B6B86]">Dokumen akan tampil di sini setelah diunggah oleh admin program studi.</p>
                <a href="{{ route('home') }}" class="rounded-full bg-[#2277FF] px-6 py-2.5 font-semibold text-white">Kembali ke Beranda</a>
            </div>
        @endif
    </div>
</div>
@endsection

@if ($aktif)
@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
<script>
(function () {
    pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';

    var APP_NAME = @js(config('app.name'));
    var BASE_W = 704, PAD = 64, THUMB_W = 76;
    var ZOOMS = [0.5, 0.75, 1, 1.25, 1.5, 2, 3];
    var dpr = Math.min(window.devicePixelRatio || 1, 2);

    var $ = function (s, r) { return (r || document).querySelector(s); };
    var $$ = function (s, r) { return Array.prototype.slice.call((r || document).querySelectorAll(s)); };

    var viewer = $('#pdf-viewer'), scroller = $('#scroller'), pagesEl = $('#pages'), thumbsEl = $('#thumbs'),
        stateEl = $('#state'), curEl = $('#current-page'), totalEl = $('#total-pages'), countEl = $('#page-count'),
        zoomLabel = $('#zoom-label');

    var cur = null;                       // kurikulum yang sedang tampil {url, name, file, id}
    var pdf = null, total = 0, zoomIdx = 2, current = 1, ratio = 0.707, token = 0, ticking = false;
    var ratios = [], pageEls = [], thumbEls = [], keys = [], tkeys = [], tasks = [], thumbsOn = true;

    function fitW()  { return Math.max(240, Math.min(BASE_W, scroller.clientWidth - PAD)); }
    function pageW() { return Math.round(fitW() * ZOOMS[zoomIdx]); }
    function thumbsVisible() { return thumbsEl.offsetParent !== null; }

    function layoutPages() {
        var w = pageW();
        pageEls.forEach(function (el, i) {
            el.style.width = w + 'px';
            el.style.height = Math.round(w * (ratios[i + 1] || ratio)) + 'px';
        });
    }

    async function renderPage(n, w, my) {
        keys[n] = w;
        var page = await pdf.getPage(n);
        if (my !== token) return;
        var vp1 = page.getViewport({ scale: 1 });
        ratios[n] = vp1.height / vp1.width;
        var el = pageEls[n - 1]; if (!el) return;
        el.style.height = Math.round(w * ratios[n]) + 'px';
        var vp = page.getViewport({ scale: (w / vp1.width) * dpr });
        var canvas = el.querySelector('canvas');
        canvas.width = vp.width; canvas.height = vp.height;
        if (tasks[n]) { try { tasks[n].cancel(); } catch (e) {} }
        tasks[n] = page.render({ canvasContext: canvas.getContext('2d'), viewport: vp });
        try { await tasks[n].promise; }
        catch (e) { if (!e || e.name !== 'RenderingCancelledException') console.error(e); keys[n] = 0; }
    }

    async function renderThumb(n, my) {
        tkeys[n] = 1;
        var page = await pdf.getPage(n);
        if (my !== token) return;
        var vp1 = page.getViewport({ scale: 1 });
        var vp = page.getViewport({ scale: (THUMB_W / vp1.width) * dpr });
        var canvas = thumbEls[n - 1].querySelector('canvas');
        canvas.width = vp.width; canvas.height = vp.height;
        await page.render({ canvasContext: canvas.getContext('2d'), viewport: vp }).promise;
    }

    function renderVisible() {
        if (!pdf) return;
        var my = token, w = pageW(), sr = scroller.getBoundingClientRect();
        pageEls.forEach(function (el, i) {
            var r = el.getBoundingClientRect();
            if (r.bottom > sr.top - 800 && r.top < sr.bottom + 800 && keys[i + 1] !== w) renderPage(i + 1, w, my);
        });
        if (thumbsVisible()) {
            var tr = thumbsEl.getBoundingClientRect();
            thumbEls.forEach(function (el, i) {
                var r = el.getBoundingClientRect();
                if (r.bottom > tr.top - 400 && r.top < tr.bottom + 400 && !tkeys[i + 1]) renderThumb(i + 1, my);
            });
        }
    }

    function setCurrent(n) {
        if (n === current) return;
        current = n; curEl.textContent = n;
        thumbEls.forEach(function (el, i) { el.dataset.active = (i + 1 === n) ? 'true' : 'false'; });
        var t = thumbEls[n - 1];
        if (t && thumbsVisible()) {
            var tr = thumbsEl.getBoundingClientRect(), r = t.getBoundingClientRect();
            if (r.top < tr.top + 8) thumbsEl.scrollTop -= (tr.top + 8 - r.top);
            else if (r.bottom > tr.bottom - 8) thumbsEl.scrollTop += (r.bottom - tr.bottom + 28);
        }
    }
    function updateCurrent() {
        var sr = scroller.getBoundingClientRect(), ref = sr.top + sr.height * 0.35, n = 1;
        for (var i = 0; i < total; i++) { if (pageEls[i].getBoundingClientRect().top <= ref) n = i + 1; else break; }
        setCurrent(n);
    }
    function goTo(n) {
        if (!total) return;
        n = Math.min(Math.max(1, n || 1), total);
        var r = pageEls[n - 1].getBoundingClientRect();
        scroller.scrollTop += r.top - scroller.getBoundingClientRect().top - 24;
        setCurrent(n); renderVisible();
    }

    function setZoom(idx) {
        idx = Math.min(Math.max(0, idx), ZOOMS.length - 1);
        var keep = current; zoomIdx = idx;
        layoutPages(); goTo(keep);
        zoomLabel.textContent = Math.round(ZOOMS[idx] * 100) + '%';
    }

    function build() {
        var fp = document.createDocumentFragment(), ft = document.createDocumentFragment();
        for (var i = 1; i <= total; i++) {
            var pg = document.createElement('div');
            pg.className = 'relative flex-none overflow-hidden rounded border border-[#CEDCF1] bg-white shadow-[0_4px_12px_0_rgba(10,51,102,0.09)]';
            pg.innerHTML = '<canvas class="block h-full w-full"></canvas>';
            fp.appendChild(pg); pageEls.push(pg);

            var btn = document.createElement('button');
            btn.type = 'button'; btn.dataset.n = i;
            btn.className = 'mb-4 flex w-[116px] shrink-0 flex-col items-center gap-1.5';
            btn.innerHTML =
                '<span class="block overflow-hidden rounded-[5px] border-2 border-[#DCE6F7] bg-white data-[active=true]:border-[#2277FF]" data-active="' + (i === 1) + '">' +
                '<canvas class="block" style="width:' + THUMB_W + 'px;height:' + Math.round(THUMB_W * ratio) + 'px"></canvas></span>' +
                '<span class="text-sm font-medium leading-[1.4] text-[#0B3A8C]">' + i + '</span>';
            ft.appendChild(btn); thumbEls.push(btn.firstChild);
        }
        pagesEl.appendChild(fp); thumbsEl.appendChild(ft);
        layoutPages();
    }

    function reset() {
        tasks.forEach(function (t) { try { t && t.cancel(); } catch (e) {} });
        if (pdf) { try { pdf.destroy(); } catch (e) {} }
        pdf = null; total = 0; current = 1; zoomIdx = 2; ratio = 0.707;
        ratios = []; pageEls = []; thumbEls = []; keys = []; tkeys = []; tasks = [];
        pagesEl.innerHTML = ''; thumbsEl.innerHTML = ''; scroller.scrollTop = 0; thumbsEl.scrollTop = 0;
        curEl.textContent = '1'; totalEl.textContent = '…'; countEl.textContent = '…'; zoomLabel.textContent = '100%';
    }

    async function openPdf(item) {
        var my = ++token;
        cur = item;
        reset();
        stateEl.textContent = 'Memuat dokumen…';
        stateEl.classList.remove('hidden');
        try {
            var doc = await pdfjsLib.getDocument(item.url).promise;
            if (my !== token) { doc.destroy(); return; }
            pdf = doc; total = doc.numPages;
            var first = await doc.getPage(1), vp = first.getViewport({ scale: 1 });
            if (my !== token) return;
            ratio = vp.height / vp.width;
            totalEl.textContent = total; countEl.textContent = total;
            build();
            stateEl.classList.add('hidden');
            renderVisible();
        } catch (err) {
            if (my !== token) return;
            console.error(err);
            stateEl.innerHTML = 'Dokumen tidak dapat ditampilkan. Silakan klik tombol <b>Unduh PDF</b>.';
            stateEl.classList.remove('hidden');
            totalEl.textContent = '-'; countEl.textContent = '-';
        }
    }

    /* ---------- tab ---------- */
    var tabs = $$('[data-tab]');
    function selectTab(btn, push) {
        tabs.forEach(function (b) { b.dataset.active = (b === btn) ? 'true' : 'false'; });
        var item = { id: btn.dataset.tab, name: btn.dataset.name, url: btn.dataset.url, file: btn.dataset.file };
        $$('[data-tab-label]').forEach(function (el) { el.textContent = item.name; });
        $$('[data-file-label]').forEach(function (el) { el.textContent = item.file; });
        document.title = item.name + ' - ' + APP_NAME;
        if (push) history.replaceState(null, '', '?' + new URLSearchParams({ kurikulum: item.id }));
        openPdf(item);
    }
    tabs.forEach(function (b) {
        b.addEventListener('click', function () { if (b.dataset.active !== 'true') selectTab(b, true); });
    });

    /* ---------- aksi toolbar ---------- */
    function download() {
        if (!cur) return;
        var a = document.createElement('a');
        a.href = cur.url; a.download = cur.file;
        document.body.appendChild(a); a.click(); a.remove();
    }
    function printPdf() {
        if (!cur) return;
        var url = cur.url, f = document.createElement('iframe');
        f.style.cssText = 'position:fixed;width:0;height:0;border:0;visibility:hidden';
        f.src = url;
        f.onload = function () {
            try { f.contentWindow.focus(); f.contentWindow.print(); }
            catch (e) { window.open(url, '_blank'); }
        };
        document.body.appendChild(f);
        setTimeout(function () { f.remove(); }, 60000);
    }
    var actions = {
        'toggle-thumbs': function () {
            thumbsOn = !thumbsOn;
            thumbsEl.classList.toggle('sm:flex', thumbsOn);
            thumbsEl.classList.toggle('sm:hidden', !thumbsOn);
            layoutPages(); renderVisible();
        },
        'zoom-out': function () { setZoom(zoomIdx - 1); },
        'zoom-in':  function () { setZoom(zoomIdx + 1); },
        'download': download,
        'print': printPdf,
        'fullscreen': function () {
            if (document.fullscreenElement) document.exitFullscreen();
            else if (viewer.requestFullscreen) viewer.requestFullscreen();
        }
    };
    $$('[data-action]').forEach(function (el) {
        el.addEventListener('click', function () { var fn = actions[el.dataset.action]; if (fn) fn(); });
    });

    /* ---------- event ---------- */
    scroller.addEventListener('scroll', function () {
        if (ticking) return; ticking = true;
        requestAnimationFrame(function () { ticking = false; updateCurrent(); renderVisible(); });
    });
    thumbsEl.addEventListener('scroll', renderVisible);
    thumbsEl.addEventListener('click', function (e) {
        var b = e.target.closest('button[data-n]'); if (b) goTo(parseInt(b.dataset.n, 10));
    });
    function relayout() { if (pdf) { layoutPages(); renderVisible(); } }
    document.addEventListener('fullscreenchange', relayout);
    window.addEventListener('resize', relayout);
    document.addEventListener('keydown', function (e) {
        if (e.key === 'ArrowRight' || e.key === 'PageDown') goTo(current + 1);
        if (e.key === 'ArrowLeft'  || e.key === 'PageUp')   goTo(current - 1);
    });

    /* ---------- mulai ---------- */
    var start = tabs.filter(function (b) { return b.dataset.active === 'true'; })[0] || tabs[0];
    selectTab(start, false);
})();
</script>
@endpush
@endif
