{{--
    resources/views/kaprodi/berita-edit.blade.php
    Form edit berita (PUT /kaprodi/berita/{id}). Isi form ada di partials/berita-form.blade.php (dipakai bersama tambah & edit).
--}}
@extends('layouts.kaprodi')

@section('title', 'Edit Berita')

@section('content')
<main class="flex flex-col gap-10 px-4 py-8 sm:px-6">
    <div class="flex flex-col gap-2">
        <h1 class="text-[32px] font-bold leading-[38px] tracking-[-0.2px]">Edit Berita</h1>
        <x-admin.breadcrumb :items="[['Dashboard', 'kaprodi.dashboard'], ['Berita', 'kaprodi.berita'], ['Edit']]" />
    </div>

    @include('admin.partials.berita-form', [
        'batal' => route('kaprodi.berita'),
        'action' => route('kaprodi.berita.update', $berita['id']),
        'method' => 'PUT',
        'berita' => $berita,
        'kategori' => $kategori,
        'kegiatanList' => $kegiatanList,
        'max' => 1000,
    ])
</main>
@endsection
