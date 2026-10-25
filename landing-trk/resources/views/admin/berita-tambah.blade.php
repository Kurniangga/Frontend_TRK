{{--
    resources/views/admin/berita-tambah.blade.php
    Form tambah berita (POST /admin/berita). Isi form ada di partials/berita-form.blade.php (dipakai bersama tambah & edit).
--}}
@extends('layouts.admin')

@section('title', 'Tambah Berita')

@section('content')
<main class="flex flex-col gap-10 px-4 py-8 sm:px-6">
    <div class="flex flex-col gap-2">
        <h1 class="text-[32px] font-bold leading-[38px] tracking-[-0.2px]">Tambah Berita</h1>
        <x-admin.breadcrumb :items="[['Dashboard', 'admin.dashboard'], ['Berita', 'admin.berita'], ['Tambah']]" />
    </div>

    @include('admin.partials.berita-form', [
        'action' => route('admin.berita.store'),
        'method' => 'POST',
        'berita' => null,
        'kategori' => $kategori,
        'kegiatanList' => $kegiatanList,
        'max' => 1000,
    ])
</main>
@endsection
