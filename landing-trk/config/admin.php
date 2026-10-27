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

        ['label' => 'Akreditasi', 'route' => 'admin.akreditasi', 'url' => 'akreditasi',
            'icon' => '<path d="M12 9m-6 0a6 6 0 1 0 12 0a6 6 0 1 0 -12 0" /><path d="M12 15l3.4 5.89l1.6 -3.355l3.6 -.534l-3.4 -5.89" /><path d="M6.4 11l-3.4 5.89l3.6 .534l1.6 3.355l3.4 -5.89" />'],

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

        ['label' => 'Berita', 'route' => 'admin.berita', 'url' => 'berita',
            'icon' => '<path d="M16 6h3a1 1 0 0 1 1 1v11a2 2 0 0 1 -4 0v-13a1 1 0 0 0 -1 -1h-10a1 1 0 0 0 -1 1v12a3 3 0 0 0 3 3h11" /><path d="M8 8l4 0" /><path d="M8 12l4 0" /><path d="M8 16l4 0" />'],



         [
            'label' => 'Prestasi',
            'route' => 'admin.prestasi',
            'url'   => 'prestasi',
            'icon'  => '<svg class="size-5 shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M11.48 3.499a.562.562 0 011.04 0l2.125 5.111a.563.563 0 00.475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 00-.182.557l1.285 5.385a.562.562 0 01-.84.61l-4.725-2.885a.563.563 0 00-.586 0L6.982 20.54a.562.562 0 01-.84-.61l1.285-5.386a.562.562 0 00-.182-.557l-4.204-3.602a.563.563 0 01.321-.988l5.518-.442a.563.563 0 00.475-.345L11.48 3.5z" /></svg>',
        ],
        
    ],
];