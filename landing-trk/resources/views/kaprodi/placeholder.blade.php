@extends('layouts.kaprodi')

@section('title', $judul)

@section('content')
<main class="p-6">
    <div class="flex flex-col gap-2">
        <h1 class="text-[32px] font-bold leading-[38px] tracking-[-0.2px]">{{ $judul }}</h1>
        <p class="text-2xl font-medium leading-[30px] tracking-[-0.15px] text-[#A8A8A8]">Halaman ini akan segera tersedia</p>
    </div>
</main>
@endsection
