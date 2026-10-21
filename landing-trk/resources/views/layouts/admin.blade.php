<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Dashboard') · Admin TRK</title>
    <meta name="robots" content="noindex">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-[#F5F5F5] font-['Plus_Jakarta_Sans',sans-serif] text-[#0A0A0A] antialiased">

    {{-- Sidebar mobile dibuka/tutup lewat checkbox ini (tanpa JavaScript) --}}
    <input type="checkbox" id="admin-menu" class="peer hidden">

    <aside class="fixed inset-y-0 left-0 z-40 flex w-[264px] -translate-x-full flex-col bg-white shadow-[2px_0_12px_rgba(0,0,0,0.06)] transition-transform peer-checked:translate-x-0 lg:translate-x-0">
        <a href="{{ route('admin.dashboard') }}" class="flex h-16 shrink-0 items-center gap-3 border-b border-[#E5E7EB] px-6">
            <img src="{{ asset('images/logo_trk.png') }}" alt="Teknologi Rekayasa Komputer" class="h-9 w-auto object-contain">
        </a>
        <x-admin.sidebar />
    </aside>

    <label for="admin-menu" class="fixed inset-0 z-30 hidden bg-black/40 peer-checked:block lg:peer-checked:hidden" aria-label="Tutup menu"></label>

    <div class="lg:pl-[264px]">
        {{-- Header --}}
        <header class="sticky top-0 z-20 flex h-16 items-center justify-between gap-4 border-b border-[#E5E7EB] bg-white px-4 sm:px-6">
            <label for="admin-menu" class="flex size-10 cursor-pointer items-center justify-center rounded-lg border border-[#E5E7EB] lg:hidden" aria-label="Buka menu">
                <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M4 6h16M4 12h16M4 18h16"/></svg>
            </label>

            <div class="ml-auto flex items-center gap-3">
                <div class="hidden text-right sm:block">
                    <p class="text-sm font-semibold leading-tight">{{ session('admin.nama', 'Admin') }}</p>
                    <p class="text-xs text-[#80838D]">{{ session('admin.email') }}</p>
                </div>
                <span class="flex size-10 items-center justify-center rounded-full bg-[#E4EDFB] text-sm font-bold text-[#226CFF]">{{ strtoupper(substr(session('admin.nama', 'A'), 0, 1)) }}</span>
            </div>
        </header>

        @yield('content')
    </div>

    @stack('scripts')
</body>
</html>
