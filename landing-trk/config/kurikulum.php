<?php

/*
 * DATA SIMULASI dokumen kurikulum (tab halaman Kurikulum & menu navbar).
 * Urutan di sini = urutan tab. 'file' = lokasi PDF di folder public/.
 * Nanti diganti database: lihat app/Repositories/EloquentKurikulumRepository.php
 */
return [
    'daftar' => [
        ['id' => '2021',          'nama' => 'Kurikulum 2021', 'file' => 'dokumen/kurikulum/kurikulum-2021.pdf'],
        ['id' => '2024',          'nama' => 'Kurikulum 2024', 'file' => 'dokumen/kurikulum/kurikulum-2024.pdf'],
        ['id' => '2026',          'nama' => 'Kurikulum 2026', 'file' => 'dokumen/kurikulum/kurikulum-2026.pdf'],
        ['id' => 'rpl-reguler',   'nama' => 'RPL Reguler',    'file' => 'dokumen/kurikulum/rpl-reguler.pdf'],
        ['id' => 'rpl-kerjasama', 'nama' => 'RPL Kerjasama',  'file' => 'dokumen/kurikulum/rpl-kerjasama.pdf'],
    ],
];
