<?php

/*
 * ADMIN (simulasi, belum database)
 *
 * 'akun'  : akun admin simulasi untuk login. Hapus/ganti dengan tabel users saat database siap.
 * 'menu'  : isi sidebar admin. 'route' = nama route, 'url' = alamat di bawah /admin.
 *           Item dengan 'children' tampil sebagai dropdown.
 *           'icon'  = isi <svg> (path) bergaya garis, 24x24.
 */
return [
    'akun' => [
        'email' => 'trk@admin.polines.ac.id',
        'password' => env('ADMIN_SIM_PASSWORD', 'admin123'),   // sandi simulasi
        'nama' => 'Admin TRK',
    ],

    'menu' => [
        ['label' => 'Dashboard', 'route' => 'admin.dashboard', 'url' => 'dashboard',
            'icon' => '<path d="M4 4h6v6h-6z" /><path d="M14 4h6v6h-6z" /><path d="M4 14h6v6h-6z" /><path d="M14 14h6v6h-6z" />'],

        ['label' => 'Kurikulum',
            'icon' => '<path d="M3 12m0 1a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v6a1 1 0 0 1 -1 1h-4a1 1 0 0 1 -1 -1z" /><path d="M9 8m0 1a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v10a1 1 0 0 1 -1 1h-4a1 1 0 0 1 -1 -1z" /><path d="M15 4m0 1a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v14a1 1 0 0 1 -1 1h-4a1 1 0 0 1 -1 -1z" /><path d="M4 20l14 0" />',
            'children' => [
                ['label' => 'Kurikulum TRK', 'route' => 'admin.kurikulum.trk', 'url' => 'kurikulum/trk'],
                ['label' => 'Mata Kuliah', 'route' => 'admin.kurikulum.mata-kuliah', 'url' => 'kurikulum/mata-kuliah'],
            ]],

        ['label' => 'Data Dosen', 'route' => 'admin.dosen', 'url' => 'dosen',
            'icon' => '<path d="M8 7a4 4 0 1 0 8 0a4 4 0 0 0 -8 0" /><path d="M6 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2" />'],

        ['label' => 'Data Mahasiswa', 'route' => 'admin.mahasiswa', 'url' => 'mahasiswa',
            'icon' => '<path d="M5 7a4 4 0 1 0 8 0a4 4 0 0 0 -8 0" /><path d="M3 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2" /><path d="M16 3.13a4 4 0 0 1 0 7.75" /><path d="M21 21v-2a4 4 0 0 0 -3 -3.85" />'],

        ['label' => 'Kegiatan',
            'icon' => '<path d="M4 13a8 8 0 0 1 7 7a6 6 0 0 0 3 -5a9 9 0 0 0 6 -8a3 3 0 0 0 -3 -3a9 9 0 0 0 -8 6a6 6 0 0 0 -5 3" /><path d="M7 14a6 6 0 0 0 -3 6a6 6 0 0 0 6 -3" /><path d="M15 9m-1 0a1 1 0 1 0 2 0a1 1 0 1 0 -2 0" />',
            'children' => [
                ['label' => 'Kegiatan Mahasiswa', 'route' => 'admin.kegiatan.mahasiswa', 'url' => 'kegiatan/mahasiswa'],
                ['label' => 'Kegiatan Dosen', 'route' => 'admin.kegiatan.dosen', 'url' => 'kegiatan/dosen'],
                ['label' => 'Jenis Dokumen', 'route' => 'admin.kegiatan.jenis-dokumen', 'url' => 'kegiatan/jenis-dokumen'],
                ['label' => 'Kategori Kegiatan', 'route' => 'admin.kegiatan.kategori-kegiatan', 'url' => 'kegiatan/kategori-kegiatan'],
            ]],

        ['label' => 'Pengajuan', 'route' => 'admin.pengajuan', 'url' => 'pengajuan',
            'icon' => '<path d="M4 4m0 2a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v12a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2z"/><path d="M4 13h3l3 3h4l3 -3h3"/>'],

       
    ],
];