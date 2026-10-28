{{--
    resources/views/kaprodi/mata-kuliah-tambah.blade.php
    Tambah Mata Kuliah (kaprodi). Dibuka dari halaman Mata Kuliah. Form ada di partials/mata-kuliah-form.
--}}
@extends('layouts.kaprodi')

@section('title', 'Tambah Mata Kuliah')

@section('content')
        <main class="px-4 py-8 sm:px-6">
            <h1 class="text-[32px] font-bold leading-[38px] tracking-[-0.2px]">Tambah Mata Kuliah</h1>
            <div class="mt-2">
                <x-admin.breadcrumb :items="[['Dashboard', 'kaprodi.dashboard'], ['Kurikulum'], ['Mata Kuliah', 'kaprodi.kurikulum.mata-kuliah'], ['Tambah']]" />
            </div>

            @include('kaprodi.partials.mata-kuliah-form', [
                'action' => route('kaprodi.kurikulum.mata-kuliah.store'),
                'method' => 'POST',
                'kurikulumOptions' => $kurikulumOptions,
                'nilai' => $nilai,
            ])
        </main>
@endsection
