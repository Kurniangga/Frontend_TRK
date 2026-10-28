{{--
    resources/views/kaprodi/berita-tambah.blade.php
    Form tambah berita (POST /kaprodi/berita). Isi form ada di partials/berita-form.blade.php (dipakai bersama tambah & edit).
--}}
@extends('layouts.kaprodi')

@section('title', 'Tambah Berita')

@section('content')
<main class="flex flex-col gap-10 px-4 py-8 sm:px-6">
    <div class="flex flex-col gap-2">
        <h1 class="text-[32px] font-bold leading-[38px] tracking-[-0.2px]">Tambah Berita</h1>
        <x-admin.breadcrumb :items="[['Dashboard', 'kaprodi.dashboard'], ['Berita', 'kaprodi.berita'], ['Tambah']]" />
    </div>

    @include('admin.partials.berita-form', [
        'batal' => route('kaprodi.berita'),
        'action' => route('kaprodi.berita.store'),
        'method' => 'POST',
        'berita' => null,
        'kategori' => $kategori,
        'kegiatanList' => $kegiatanList,
        'max' => 1000,
    ])
</main>
@endsection
