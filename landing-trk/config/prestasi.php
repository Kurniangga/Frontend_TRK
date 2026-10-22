<?php

// Data simulasi prestasi. Nanti diganti tabel `prestasi` di database.
// 'juara' : 1, 2, atau 3 (menentukan warna lencana)
// 'besar' : true = judul pada kartu tampil dengan huruf lebih besar

$A = 'https://api.builder.io/api/v1/image/assets/TEMP/';

$juara3 = [
    'slug'   => 'juara-3-cybersecurity-olivia-2026',
    'juara'  => 3,
    'judul'  => 'Juara 3 Cybersecurity Kategori BIdang Digital & Media Technology pada OLIVIA 2026',
    'sub'    => 'Prestasi TRK',
    'gambar' => $A . '3a7735c15bfc00b23797027d54c3b8e33e939ec5?width=670',
];

return array_merge(
    [
        [
            'slug'   => 'fifa-asean-cup-2026',
            'juara'  => 1,
            'judul'  => 'The Winner of FIFA ASEAN CUP 2026',
            'sub'    => 'Timnas Indonesia',
            'gambar' => $A . '4968f5c7b9f309e7174bca9d6daa415427643463?width=990',
            'besar'  => true,
        ],
        [
            'slug'   => 'juara-1-cybersecurity-olivia-2026',
            'juara'  => 1,
            'judul'  => 'Juara 1 Cybersecurity Kategori BIdang Digital & Media Technology pada OLIVIA 2026',
            'sub'    => 'Prestasi TRK',
            'gambar' => $A . '4e286dc44fd27faa65f73e3636c93d05414ebc75?width=610',
        ],
        [
            'slug'   => 'juara-2-elektro-expo-2026',
            'juara'  => 2,
            'judul'  => 'Juara 2 Elektro Expo 2026 IoT-Desain Produk Inovasi',
            'sub'    => 'Prestasi TRK',
            'gambar' => $A . '2ebd3fa8924f63ca443fd5416ad9abf263016bf5?width=610',
            'besar'  => true,
        ],
    ],
    // 7 kartu Juara 3 (5 dari desain + 2 tambahan agar paginasi terlihat)
    array_fill(0, 7, $juara3)
);