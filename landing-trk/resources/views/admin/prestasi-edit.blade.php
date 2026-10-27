@extends('layouts.admin')

@section('title', 'Edit Prestasi')

@section('content')
<main class="flex flex-col gap-10 px-4 py-8 sm:px-6">
    <div class="flex flex-col gap-2">
        <h1 class="text-[32px] font-bold leading-[38px] tracking-[-0.2px]">Edit Prestasi</h1>
        <x-admin.breadcrumb :items="[['Dashboard', 'admin.dashboard'], ['Prestasi', 'admin.prestasi'], ['Edit']]" />
    </div>

    @include('admin.partials.prestasi-form', [
        'action' => route('admin.prestasi.update', $prestasi['id']),
        'method' => 'PUT',
        'prestasi' => $prestasi,
    ])
</main>
@endsection