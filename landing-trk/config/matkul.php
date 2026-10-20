<?php

/*
 * Data sementara mata kuliah (pengganti database).
 * Nanti bisa diganti model Eloquent: Kurikulum -> hasMany MataKuliah.
 * Format baris: [kode, nama, sks_teori, sks_praktek, jam_teori, jam_praktek]
 */
return [
    'kurikulum' => ['2026', '2024', '2021'],

    'data' => [
        '2026' => [
            1 => [
                ['432-191-101', 'Etika Profesi & K3', 0, 0, 0, 0],
                ['432-191-102', 'Matematika Diskrit', 0, 0, 0, 0],
                ['432-191-103', 'Pengantar Teknologi Informasi', 0, 0, 0, 0],
                ['432-191-104', 'Arsitektur dan Organisasi Komputer', 0, 0, 0, 0],
                ['432-191-105', 'Sistem Operasi', 0, 0, 0, 0],
                ['432-191-106', 'Algoritma dan Pemrograman', 0, 0, 0, 0],
                ['432-191-107', 'Sistem Basis Data I', 0, 0, 0, 0],
            ],
            2 => [
                ['TRK201', 'Pemrograman Berorientasi Objek', 1, 2, 1, 4],
                ['TRK202', 'Basis Data', 1, 2, 1, 4],
                ['TRK203', 'Jaringan Komputer Dasar', 1, 2, 1, 4],
                ['TRK204', 'Statistika', 2, 0, 2, 0],
                ['TRK205', 'Mikrokontroler', 1, 2, 1, 4],
            ],
        ],
        '2024' => [
            1 => [
                ['TRK101', 'Pendidikan Agama', 2, 0, 2, 0],
                ['TRK102', 'Bahasa Indonesia', 2, 0, 2, 0],
                ['TRK103', 'Algoritma dan Pemrograman', 1, 2, 1, 4],
            ],
        ],
        '2021' => [],
    ],
];