<?php

/*
 * KAPRODI (simulasi, belum database)
 *
 * 'akun'  : akun kaprodi simulasi untuk login. Hapus/ganti dengan tabel users (role = kaprodi) saat database siap.
 * 'menu'  : isi sidebar kaprodi. 'route' = nama route, 'url' = alamat di bawah /kaprodi.
 *           Item dengan 'children' tampil sebagai dropdown. 'icon' = isi <svg> (path) bergaya garis, 24x24.
 */
return [
    'akun' => [
        'email' => 'trk@kaprodi.polines.ac.id',
        'password' => env('KAPRODI_SIM_PASSWORD', 'kaprodi123'),   // sandi simulasi
        'nama' => 'Kaprodi TRK',
    ],

    'menu' => [
        ['label' => 'Dashboard', 'route' => 'kaprodi.dashboard', 'url' => 'dashboard',
            'icon' => '<path d="M4 4h6v6h-6z" /><path d="M14 4h6v6h-6z" /><path d="M4 14h6v6h-6z" /><path d="M14 14h6v6h-6z" />'],

        ['label' => 'Kurikulum',
            'icon' => '<path d="M3 12m0 1a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v6a1 1 0 0 1 -1 1h-4a1 1 0 0 1 -1 -1z" /><path d="M9 8m0 1a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v10a1 1 0 0 1 -1 1h-4a1 1 0 0 1 -1 -1z" /><path d="M15 4m0 1a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v14a1 1 0 0 1 -1 1h-4a1 1 0 0 1 -1 -1z" /><path d="M4 20l14 0" />',
            'children' => [
                ['label' => 'Kurikulum TRK', 'route' => 'kaprodi.kurikulum.trk', 'url' => 'kurikulum/trk'],
                ['label' => 'Mata Kuliah', 'route' => 'kaprodi.kurikulum.mata-kuliah', 'url' => 'kurikulum/mata-kuliah'],
            ]],

        ['label' => 'Data Dosen', 'route' => 'kaprodi.dosen', 'url' => 'dosen',
            'icon' => '<path d="M8 7a4 4 0 1 0 8 0a4 4 0 0 0 -8 0" /><path d="M6 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2" />'],

        ['label' => 'Data Mahasiswa', 'route' => 'kaprodi.mahasiswa', 'url' => 'mahasiswa',
            'icon' => '<path d="M5 7a4 4 0 1 0 8 0a4 4 0 0 0 -8 0" /><path d="M3 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2" /><path d="M16 3.13a4 4 0 0 1 0 7.75" /><path d="M21 21v-2a4 4 0 0 0 -3 -3.85" />'],

        ['label' => 'Kegiatan',
            'icon' => '<path d="M4 13a8 8 0 0 1 7 7a6 6 0 0 0 3 -5a9 9 0 0 0 6 -8a3 3 0 0 0 -3 -3a9 9 0 0 0 -8 6a6 6 0 0 0 -5 3" /><path d="M7 14a6 6 0 0 0 -3 6a6 6 0 0 0 6 -3" /><path d="M15 9m-1 0a1 1 0 1 0 2 0a1 1 0 1 0 -2 0" />',
            'children' => [
                ['label' => 'Kegiatan Dosen', 'route' => 'kaprodi.kegiatan.dosen', 'url' => 'kegiatan/dosen'],
                ['label' => 'Kegiatan Mahasiswa', 'route' => 'kaprodi.kegiatan.mahasiswa', 'url' => 'kegiatan/mahasiswa'],
                ['label' => 'Kategori Kegiatan', 'route' => 'kaprodi.kegiatan.kategori-kegiatan', 'url' => 'kegiatan/kategori-kegiatan'],
                ['label' => 'Jenis Dokumen', 'route' => 'kaprodi.kegiatan.jenis-dokumen', 'url' => 'kegiatan/jenis-dokumen'],
            ]],
    ],
];
