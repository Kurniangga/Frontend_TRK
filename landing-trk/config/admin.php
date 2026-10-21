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
            'icon' => '<path d="M4 4h6v8h-6z"/><path d="M4 16h6v4h-6z"/><path d="M14 12h6v8h-6z"/><path d="M14 4h6v4h-6z"/>'],

        ['label' => 'Kurikulum',
            'icon' => '<path d="M3 19a9 9 0 0 1 9 0a9 9 0 0 1 9 0"/><path d="M3 6a9 9 0 0 1 9 0a9 9 0 0 1 9 0"/><path d="M3 6l0 13"/><path d="M12 6l0 13"/><path d="M21 6l0 13"/>',
            'children' => [
                ['label' => 'Kurikulum TRK', 'route' => 'admin.kurikulum.trk', 'url' => 'kurikulum/trk'],
                ['label' => 'Mata Kuliah', 'route' => 'admin.kurikulum.mata-kuliah', 'url' => 'kurikulum/mata-kuliah'],
            ]],

        ['label' => 'Data Dosen', 'route' => 'admin.dosen', 'url' => 'dosen',
            'icon' => '<path d="M9 7m-4 0a4 4 0 1 0 8 0a4 4 0 1 0 -8 0"/><path d="M3 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/><path d="M21 21v-2a4 4 0 0 0 -3 -3.85"/>'],

        ['label' => 'Data Mahasiswa', 'route' => 'admin.mahasiswa', 'url' => 'mahasiswa',
            'icon' => '<path d="M22 9l-10 -4l-10 4l10 4l10 -4v6"/><path d="M6 10.6v5.4a6 3 0 0 0 12 0v-5.4"/>'],

        ['label' => 'Kegiatan',
            'icon' => '<path d="M4 7a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v12a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2v-12"/><path d="M16 3v4"/><path d="M8 3v4"/><path d="M4 11h16"/><path d="M8 15h2v2h-2z"/>',
            'children' => [
                ['label' => 'Kegiatan Mahasiswa', 'route' => 'admin.kegiatan.mahasiswa', 'url' => 'kegiatan/mahasiswa'],
                ['label' => 'Kegiatan Dosen', 'route' => 'admin.kegiatan.dosen', 'url' => 'kegiatan/dosen'],
                ['label' => 'Jenis Dokumen', 'route' => 'admin.kegiatan.jenis-dokumen', 'url' => 'kegiatan/jenis-dokumen'],
                ['label' => 'Jenis Kegiatan', 'route' => 'admin.kegiatan.jenis-kegiatan', 'url' => 'kegiatan/jenis-kegiatan'],
            ]],

        ['label' => 'Pengajuan', 'route' => 'admin.pengajuan', 'url' => 'pengajuan',
            'icon' => '<path d="M4 4m0 2a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v12a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2z"/><path d="M4 13h3l3 3h4l3 -3h3"/>'],

        ['label' => 'Pengujian', 'route' => 'admin.pengujian', 'url' => 'pengujian',
            'icon' => '<path d="M9 3h6"/><path d="M10 3v6l-5 9a2 2 0 0 0 1.7 3h10.6a2 2 0 0 0 1.7 -3l-5 -9v-6"/><path d="M7.5 15h9"/>'],
    ],
];
