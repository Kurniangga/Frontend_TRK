@extends('layouts.app')

@section('title', $title . ' - Teknologi Rekayasa Komputer')
@section('description', $subtitle)

@section('content')
<style>
    .thin-scroll { scrollbar-width: thin; scrollbar-color: #C3CFE6 transparent; }
    .thin-scroll::-webkit-scrollbar { width: 8px; height: 8px; }
    .thin-scroll::-webkit-scrollbar-thumb { background: #C3CFE6; border-radius: 9999px; }
    .thin-scroll::-webkit-scrollbar-track { background: transparent; }
</style>

<div class="min-h-screen bg-white font-['Plus_Jakarta_Sans',sans-serif]">

    <x-navbar />

    <main class="pb-16 mx-auto max-w-[1278px] px-4 pt-10 md:pt-14 sm:px-6 xl:px-0">

        {{-- ===================== JUDUL HALAMAN ===================== --}}
        <div class="text-center">
            <nav class="text-base text-[#6B7280]" aria-label="Breadcrumb">
                <a href="{{ route('home') }}" class="hover:text-[#2277FF]">Beranda</a> / <a href="{{ route('home') }}#tentang" class="hover:text-[#2277FF]">Tentang TRK</a> / {{ $title }}
            </nav>
            <h1 class="mt-3 text-[40px] font-bold leading-[60px] tracking-[-0.5px] text-[#2277FF] sm:text-[50px]">{{ $title }}</h1>
            <p class="mx-auto mt-1.5 max-w-[900px] text-[15px] font-medium text-[#1F2937] sm:text-[17px]">{{ $subtitle }}</p>
            <div class="mx-auto mt-3 h-1 w-20 rounded-full bg-[#FFD121]"></div>
        </div>


@if ($pdfUrl)
        {{-- ===================== PDF VIEWER ===================== --}}
        <section id="viewer"
                 class="mt-8 flex h-[560px] flex-col overflow-hidden rounded-[22px] border border-[#E4ECF9] bg-[#DFE8F6] shadow-[0_6px_20px_rgba(0,50,136,0.08)] sm:h-[654px] [&:fullscreen]:h-screen [&:fullscreen]:rounded-none [&:fullscreen]:border-0">

            {{-- Toolbar --}}
            <div class="grid h-16 flex-none grid-cols-[1fr_auto_1fr] items-center bg-[#0B3A8C] px-3 text-white sm:px-5">
                <div class="flex items-center gap-2.5 sm:gap-4">
                    <button id="btnSidebar" type="button" aria-label="Tampilkan atau sembunyikan thumbnail"
                            class="inline-flex size-10 items-center justify-center rounded-lg border border-white/20 bg-white/10 text-white transition hover:bg-white/20">
                        <svg class="size-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 6l16 0" /><path d="M4 12l16 0" /><path d="M4 18l16 0" /></svg>
                    </button>
                    <span class="hidden text-lg font-medium md:inline">Halaman</span>
                    <input id="pageInput" type="text" inputmode="numeric" value="1" aria-label="Nomor halaman"
                           class="h-9 w-10 rounded-md bg-white/[0.17] text-center text-lg font-medium text-white outline-none focus:ring-2 focus:ring-white/60">
                    <span class="text-lg font-medium">/ <span id="totalPages">…</span></span>
                </div>

                <div class="flex items-center gap-2 sm:gap-4">
                    <button id="zoomOut" type="button" aria-label="Perkecil"
                            class="inline-flex size-10 items-center justify-center rounded-lg border border-white/20 bg-white/10 text-white transition hover:bg-white/20 disabled:cursor-not-allowed disabled:opacity-40">
                        <svg class="size-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12l14 0" /></svg>
                    </button>
                    <span id="zoomLabel" class="min-w-[52px] text-center text-lg font-medium">100%</span>
                    <button id="zoomIn" type="button" aria-label="Perbesar"
                            class="inline-flex size-10 items-center justify-center rounded-lg border border-white/20 bg-white/10 text-white transition hover:bg-white/20 disabled:cursor-not-allowed disabled:opacity-40">
                        <svg class="size-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 5l0 14" /><path d="M5 12l14 0" /></svg>
                    </button>
                </div>

                <div class="flex items-center justify-end gap-2 sm:gap-2.5">
                    <a href="{{ $pdfUrl }}" download="{{ $fileName }}" aria-label="Unduh PDF"
                       class="inline-flex size-10 items-center justify-center rounded-lg border border-white/20 bg-white/10 text-white transition hover:bg-white/20">
                        <svg class="size-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 17v2a2 2 0 0 0 2 2h12a2 2 0 0 0 2 -2v-2" /><path d="M7 11l5 5l5 -5" /><path d="M12 4l0 12" /></svg>
                    </a>
                    <button id="btnPrint" type="button" aria-label="Cetak"
                            class="hidden size-10 items-center justify-center rounded-lg border border-white/20 bg-white/10 text-white transition hover:bg-white/20 sm:inline-flex">
                        <svg class="size-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 17h2a2 2 0 0 0 2 -2v-4a2 2 0 0 0 -2 -2h-14a2 2 0 0 0 -2 2v4a2 2 0 0 0 2 2h2" /><path d="M17 9v-4a2 2 0 0 0 -2 -2h-6a2 2 0 0 0 -2 2v4" /><path d="M7 15a2 2 0 0 1 2 -2h6a2 2 0 0 1 2 2v4a2 2 0 0 1 -2 2h-6a2 2 0 0 1 -2 -2l0 -4" /></svg>
                    </button>
                    <button id="btnFull" type="button" aria-label="Layar penuh"
                            class="inline-flex size-10 items-center justify-center rounded-lg border border-white/20 bg-white/10 text-white transition hover:bg-white/20">
                        <svg class="size-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 8v-2a2 2 0 0 1 2 -2h2" /><path d="M4 16v2a2 2 0 0 0 2 2h2" /><path d="M16 4h2a2 2 0 0 1 2 2v2" /><path d="M16 20h2a2 2 0 0 0 2 -2v-2" /></svg>
                    </button>
                </div>
            </div>

            {{-- Isi: thumbnail + halaman --}}
            <div class="flex min-h-0 flex-1">
                <aside id="thumbs" class="thin-scroll w-[148px] flex-none overflow-y-auto bg-[#F1F6FE] px-[23px] py-7" aria-label="Thumbnail halaman"></aside>

                <div id="scroller" class="thin-scroll relative min-w-0 flex-1 overflow-auto">
                    <div id="pages" class="mx-auto flex w-max min-w-full flex-col items-center gap-6 px-10 py-12"></div>
                    <div id="state" class="absolute inset-0 flex items-center justify-center px-6 text-center text-base font-medium text-[#0B3A8C]">Memuat dokumen…</div>
                </div>
            </div>
        </section>

        {{-- ===================== BARIS BAWAH ===================== --}}
        <div class="mt-8 flex flex-col items-start gap-5 lg:flex-row lg:items-center lg:gap-10">
            <a href="{{ route('home') }}"
               class="inline-flex flex-none items-center gap-1 rounded-full bg-[#2277FF] p-0.5 pr-4 text-2xl font-semibold text-white">
                <span class="flex size-[42px] items-center justify-center rounded-full bg-[#5695FB]">
                    <svg class="size-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M15 6l-6 6l6 6" /></svg>
                </span>
                Kembali
            </a>

            <p class="min-w-0 flex-1 text-base font-medium text-[#0B3A8C] sm:text-lg">
                {{ $fileName }}
                <span class="font-normal text-[#5B6B86]">· <span id="pageCount">…</span> halaman</span>
            </p>

            <a href="{{ $pdfUrl }}" download="{{ $fileName }}"
               class="inline-flex h-[52px] w-full flex-none items-center justify-center gap-3 rounded-[10px] border border-[#2277FF] bg-white text-lg font-medium text-[#2277FF] transition hover:bg-[#EEF4FF] sm:w-[220px]">
                <svg class="size-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 17v2a2 2 0 0 0 2 2h12a2 2 0 0 0 2 -2v-2" /><path d="M7 11l5 5l5 -5" /><path d="M12 4l0 12" /></svg>
                Unduh PDF
            </a>
        </div>
@else
        <section class="mt-8 flex min-h-[320px] flex-col items-center justify-center gap-4 rounded-[22px] border border-[#E4ECF9] bg-[#F1F6FE] px-6 text-center">
            <svg class="size-14 text-[#2277FF]/60" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M14 3v4a1 1 0 0 0 1 1h4" /><path d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z" /></svg>
            <p class="text-lg font-semibold text-[#0B3A8C]">Dokumen akreditasi belum tersedia</p>
            <p class="max-w-md text-[#5B6B86]">Dokumen akan tampil di sini setelah diunggah oleh admin program studi.</p>
            <a href="{{ route('home') }}" class="mt-2 rounded-full bg-[#2277FF] px-6 py-2.5 font-semibold text-white">Kembali ke Beranda</a>
        </section>
@endif
    </main>
</div>
@endsection

@if ($pdfUrl)
@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
    <script>
    (function () {
        pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';

        var PDF_URL = @json($pdfUrl);
        var BASE_W  = 704;                       // lebar halaman (px) pada zoom 100%
        var PAD     = 80;                        // ruang kiri-kanan di area halaman
        var THUMB_W = 98;                        // lebar gambar thumbnail (px)
        var ZOOMS   = [0.5, 0.75, 1, 1.25, 1.5, 2, 3];
        var dpr     = Math.min(window.devicePixelRatio || 1, 2);

        var $ = function (id) { return document.getElementById(id); };
        var viewer = $('viewer'), scroller = $('scroller'), pagesEl = $('pages'), thumbsEl = $('thumbs'),
            stateEl = $('state'), pageInput = $('pageInput'), totalEl = $('totalPages'), countEl = $('pageCount'),
            zoomLabel = $('zoomLabel'), zoomIn = $('zoomIn'), zoomOut = $('zoomOut');

        var pdf = null, total = 0, zoomIdx = 2, current = 1, ratio = 0.707;
        var ratios = [], pageEls = [], thumbEls = [], keys = [], tkeys = [], tasks = [], ticking = false;

        function fitW()  { return Math.max(240, Math.min(BASE_W, scroller.clientWidth - PAD)); }
        function pageW() { return Math.round(fitW() * ZOOMS[zoomIdx]); }

        /* ---------- susun ukuran placeholder ---------- */
        function layoutPages() {
            var w = pageW();
            pageEls.forEach(function (el, i) {
                el.style.width  = w + 'px';
                el.style.height = Math.round(w * (ratios[i + 1] || ratio)) + 'px';
            });
        }

        /* ---------- render halaman utama ---------- */
        async function renderPage(n, w) {
            keys[n] = w;
            var page = await pdf.getPage(n);
            var vp1 = page.getViewport({ scale: 1 });
            ratios[n] = vp1.height / vp1.width;
            var el = pageEls[n - 1];
            el.style.height = Math.round(w * ratios[n]) + 'px';
            var vp = page.getViewport({ scale: (w / vp1.width) * dpr });
            var canvas = el.querySelector('canvas');
            canvas.width = vp.width; canvas.height = vp.height;
            if (tasks[n]) { try { tasks[n].cancel(); } catch (e) {} }
            tasks[n] = page.render({ canvasContext: canvas.getContext('2d'), viewport: vp });
            try { await tasks[n].promise; }
            catch (e) { if (!e || e.name !== 'RenderingCancelledException') console.error(e); keys[n] = 0; }
        }

        /* ---------- render thumbnail ---------- */
        async function renderThumb(n) {
            tkeys[n] = 1;
            var page = await pdf.getPage(n);
            var vp1 = page.getViewport({ scale: 1 });
            var vp = page.getViewport({ scale: (THUMB_W / vp1.width) * dpr });
            var canvas = thumbEls[n - 1].querySelector('canvas');
            canvas.width = vp.width; canvas.height = vp.height;
            await page.render({ canvasContext: canvas.getContext('2d'), viewport: vp }).promise;
        }

        /* ---------- render hanya yang terlihat ---------- */
        function renderVisible() {
            if (!pdf) return;
            var w = pageW(), sr = scroller.getBoundingClientRect();
            pageEls.forEach(function (el, i) {
                var r = el.getBoundingClientRect();
                if (r.bottom > sr.top - 800 && r.top < sr.bottom + 800 && keys[i + 1] !== w) renderPage(i + 1, w);
            });
            if (!thumbsEl.classList.contains('hidden')) {
                var tr = thumbsEl.getBoundingClientRect();
                thumbEls.forEach(function (el, i) {
                    var r = el.getBoundingClientRect();
                    if (r.bottom > tr.top - 400 && r.top < tr.bottom + 400 && !tkeys[i + 1]) renderThumb(i + 1);
                });
            }
        }

        /* ---------- halaman aktif ---------- */
        function setCurrent(n) {
            if (n === current) return;
            current = n;
            pageInput.value = n;
            thumbEls.forEach(function (el, i) { el.dataset.active = (i + 1 === n) ? 'true' : 'false'; });
            var t = thumbEls[n - 1];
            if (t && !thumbsEl.classList.contains('hidden')) {
                var tr = thumbsEl.getBoundingClientRect(), r = t.getBoundingClientRect();
                if (r.top < tr.top + 8) thumbsEl.scrollTop -= (tr.top + 8 - r.top);
                else if (r.bottom > tr.bottom - 8) thumbsEl.scrollTop += (r.bottom - tr.bottom + 28);
            }
        }
        function updateCurrent() {
            var sr = scroller.getBoundingClientRect(), ref = sr.top + sr.height * 0.35, n = 1;
            for (var i = 0; i < total; i++) {
                if (pageEls[i].getBoundingClientRect().top <= ref) n = i + 1; else break;
            }
            setCurrent(n);
        }
        function goTo(n) {
            n = Math.min(Math.max(1, n || 1), total);
            var r = pageEls[n - 1].getBoundingClientRect();
            scroller.scrollTop += r.top - scroller.getBoundingClientRect().top - 24;
            setCurrent(n);
            renderVisible();
        }

        /* ---------- zoom ---------- */
        function setZoom(idx) {
            idx = Math.min(Math.max(0, idx), ZOOMS.length - 1);
            var keep = current;
            zoomIdx = idx;
            layoutPages();
            goTo(keep);
            zoomLabel.textContent = Math.round(ZOOMS[idx] * 100) + '%';
            zoomOut.disabled = idx === 0;
            zoomIn.disabled = idx === ZOOMS.length - 1;
        }

        /* ---------- cetak ---------- */
        function printPdf() {
            var f = document.createElement('iframe');
            f.style.cssText = 'position:fixed;width:0;height:0;border:0;visibility:hidden';
            f.src = PDF_URL;
            f.onload = function () {
                try { f.contentWindow.focus(); f.contentWindow.print(); }
                catch (e) { window.open(PDF_URL, '_blank'); }
            };
            document.body.appendChild(f);
            setTimeout(function () { f.remove(); }, 60000);
        }

        /* ---------- bangun elemen ---------- */
        function build() {
            var fp = document.createDocumentFragment(), ft = document.createDocumentFragment();
            for (var i = 1; i <= total; i++) {
                var pg = document.createElement('div');
                pg.className = 'relative flex-none bg-white shadow-[0_2px_10px_rgba(0,0,0,0.18)]';
                pg.innerHTML = '<canvas class="block h-full w-full"></canvas>';
                fp.appendChild(pg); pageEls.push(pg);

                var btn = document.createElement('button');
                btn.type = 'button'; btn.dataset.n = i;
                btn.className = 'mb-4 flex w-full flex-col items-center gap-2';
                btn.innerHTML =
                    '<div class="overflow-hidden rounded-[5px] border-2 border-transparent bg-white shadow-[0_1px_4px_rgba(0,0,0,0.2)] data-[active=true]:border-[#2277FF]" data-active="' + (i === 1) + '">' +
                    '<canvas class="block" style="width:' + THUMB_W + 'px;height:' + Math.round(THUMB_W * ratio) + 'px"></canvas></div>' +
                    '<span class="text-sm text-[#0B2A6B]">' + i + '</span>';
                ft.appendChild(btn); thumbEls.push(btn.firstChild);
            }
            pagesEl.appendChild(fp); thumbsEl.appendChild(ft);
            layoutPages();
        }

        /* ---------- event ---------- */
        scroller.addEventListener('scroll', function () {
            if (ticking) return; ticking = true;
            requestAnimationFrame(function () { ticking = false; updateCurrent(); renderVisible(); });
        });
        thumbsEl.addEventListener('scroll', renderVisible);
        thumbsEl.addEventListener('click', function (e) {
            var b = e.target.closest('button[data-n]'); if (b) goTo(parseInt(b.dataset.n, 10));
        });
        pageInput.addEventListener('focus', function () { pageInput.select(); });
        pageInput.addEventListener('keydown', function (e) { if (e.key === 'Enter') pageInput.blur(); });
        pageInput.addEventListener('change', function () {
            var n = parseInt(pageInput.value, 10);
            if (isNaN(n)) { pageInput.value = current; return; }
            goTo(n); pageInput.value = current;
        });
        zoomIn.addEventListener('click',  function () { setZoom(zoomIdx + 1); });
        zoomOut.addEventListener('click', function () { setZoom(zoomIdx - 1); });
        $('btnPrint').addEventListener('click', printPdf);
        $('btnSidebar').addEventListener('click', function () {
            thumbsEl.classList.toggle('hidden');
            layoutPages(); renderVisible();
        });
        $('btnFull').addEventListener('click', function () {
            if (document.fullscreenElement) document.exitFullscreen();
            else if (viewer.requestFullscreen) viewer.requestFullscreen();
        });
        function relayout() { if (pdf) { layoutPages(); renderVisible(); } }
        document.addEventListener('fullscreenchange', relayout);
        window.addEventListener('resize', relayout);
        document.addEventListener('keydown', function (e) {
            if (e.target === pageInput) return;
            if (e.key === 'ArrowRight' || e.key === 'PageDown') goTo(current + 1);
            if (e.key === 'ArrowLeft'  || e.key === 'PageUp')   goTo(current - 1);
        });

        /* ---------- mulai ---------- */
        if (window.innerWidth < 768) thumbsEl.classList.add('hidden');
        (async function () {
            try {
                pdf = await pdfjsLib.getDocument(PDF_URL).promise;
                total = pdf.numPages;
                var first = await pdf.getPage(1), vp = first.getViewport({ scale: 1 });
                ratio = vp.height / vp.width;
                totalEl.textContent = total; countEl.textContent = total;
                pageInput.setAttribute('maxlength', String(total).length);
                build();
                stateEl.remove();
                renderVisible();
            } catch (err) {
                console.error(err);
                stateEl.innerHTML = 'Dokumen tidak dapat ditampilkan. Silakan klik tombol <b>Unduh PDF</b>.';
                totalEl.textContent = '-'; countEl.textContent = '-';
            }
        })();
    })();
    </script>
@endpush
@endif
