{{--
    resources/views/kaprodi/mata-kuliah-edit.blade.php
    Edit Mata Kuliah (kaprodi). Dibuka dari halaman Mata Kuliah. Form ada di partials/mata-kuliah-form.
--}}
@extends('layouts.kaprodi')

@section('title', 'Edit Mata Kuliah')

@section('content')
        <main class="px-4 py-8 sm:px-6">
            <h1 class="text-[32px] font-bold leading-[38px] tracking-[-0.2px]">Edit Mata Kuliah</h1>
            <div class="mt-2">
                <x-admin.breadcrumb :items="[['Dashboard', 'kaprodi.dashboard'], ['Kurikulum'], ['Mata Kuliah', 'kaprodi.kurikulum.mata-kuliah'], ['Edit']]" />
            </div>

            @include('kaprodi.partials.mata-kuliah-form', [
                'action' => route('kaprodi.kurikulum.mata-kuliah.update', $mk['id']),
                'method' => 'PUT',
                'kurikulumOptions' => $kurikulumOptions,
                'nilai' => $nilai,
            ])
        </main>
@endsection
