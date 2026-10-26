{{--
    resources/views/kaprodi/berita-detail.blade.php
    Detail berita (kaprodi, baca saja). Isi ada di admin/partials/berita-detail.blade.php (dipakai bersama admin & kaprodi).
--}}
@extends('layouts.kaprodi')

@section('title', 'Detail Berita')

@section('content')
<main class="flex flex-col gap-6 px-4 py-8 sm:px-6">
    <div class="flex flex-col gap-2">
        <x-admin.breadcrumb :items="[['Dashboard', 'kaprodi.dashboard'], ['Berita', 'kaprodi.berita'], ['Detail']]" />
    </div>

    <div class="rounded-lg bg-white p-4 sm:p-6 shadow-[1px_0_3px_rgba(0,0,0,0.02),5px_2px_6px_rgba(0,0,0,0.02)]">
        @include('admin.partials.berita-detail', ['prefix' => 'kaprodi'])
    </div>
</main>
@endsection
