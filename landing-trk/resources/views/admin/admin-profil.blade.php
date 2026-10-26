@extends('layouts.admin')

@section('title', 'Profil Saya')

@section('content')
@php
    $inisial = strtoupper(mb_substr(trim($p['nama']) ?: 'U', 0, 1));
    $info = [
        'Email akun'   => $p['email'],
        'Role'         => $p['peran'],
        'Unit / Prodi' => $p['unit'],
        'Akun login'   => $p['status'],
    ];
@endphp

<main class="flex flex-col gap-6 px-4 py-8 sm:px-6">
    <h1 class="text-2xl font-bold text-[#0A0A0A]">Profil Saya</h1>

    <section class="rounded-xl bg-white p-5 sm:p-6">
        <div class="flex flex-wrap items-center gap-5">
            <span class="flex size-[86px] shrink-0 items-center justify-center rounded-full bg-[#D4D4D4] text-3xl font-semibold text-[#6B6B6B]">{{ $inisial }}</span>

            <div class="min-w-0">
                <div class="flex flex-wrap items-center gap-x-8 gap-y-1">
                    <h2 class="text-[26px] font-semibold leading-tight text-[#0A0A0A]">{{ $p['nama'] }}</h2>
                    <span class="rounded-md border border-[#BBE29A] bg-[#E6F4D7] px-2.5 py-0.5 text-sm font-medium text-[#3B7A0A]">{{ $p['status'] }}</span>
                </div>
                <p class="mt-1 text-lg text-[#A3A3A3]">
                    {{ $p['nip'] !== '-' ? 'NIP '.$p['nip'] : $p['kampus'] }}
                </p>
            </div>
        </div>

        <dl class="mt-6 grid gap-x-6 gap-y-5 rounded-lg bg-[#F1F6FF] p-5 sm:grid-cols-2 xl:grid-cols-4">
            @foreach ($info as $label => $nilai)
                <div class="min-w-0">
                    <dt class="text-lg text-[#A3A3A3]">{{ $label }}</dt>
                    <dd class="mt-1 break-words text-lg font-medium text-[#111]">{{ $nilai }}</dd>
                </div>
            @endforeach
        </dl>
    </section>
</main>
@endsection