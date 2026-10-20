<?php

/*
 * DATA SIMULASI (sementara) - hanya dipakai oleh ConfigMataKuliahRepository.
 * Nanti diganti database: lihat app/Repositories/EloquentMataKuliahRepository.php
 *
 * 'kurikulum' : daftar untuk dropdown (id dipakai di URL, nama tampil di dropdown)
 * 'data'      : [id kurikulum][semester 1-8] => baris mata kuliah
 * Baris       : [kode, nama, sks_teori, sks_praktek, jam_teori, jam_praktek]
 * Jumlah baris per semester bebas (6, 7, 8, dst).
 */
return [
    'kurikulum' => [
        ['id' => '2026', 'nama' => 'Kurikulum 2026'],
        ['id' => '2024', 'nama' => 'Kurikulum 2024'],
        ['id' => '2021', 'nama' => 'Kurikulum 2021'],
    ],

    'data' => [
        '2026' => [
            1 => [
                ['432-191-101', 'Etika Profesi & K3', 2, 0, 2, 0],
                ['432-191-102', 'Matematika Diskrit', 2, 0, 2, 0],
                ['432-191-103', 'Pengantar Teknologi Informasi', 2, 0, 2, 0],
                ['432-191-104', 'Arsitektur dan Organisasi Komputer', 2, 1, 2, 2],
                ['432-191-105', 'Sistem Operasi', 1, 2, 1, 4],
                ['432-191-106', 'Algoritma dan Pemrograman', 1, 2, 1, 4],
                ['432-191-107', 'Sistem Basis Data I', 1, 2, 1, 4],
            ],
            2 => [
                ['432-191-201', 'Bahasa Inggris', 2, 0, 2, 0],
                ['432-191-202', 'Pendidikan Pancasila', 2, 0, 2, 0],
                ['432-191-203', 'Statistika', 2, 0, 2, 0],
                ['432-191-204', 'Struktur Data', 1, 2, 1, 4],
                ['432-191-205', 'Basis Data II', 1, 2, 1, 4],
                ['432-191-206', 'Jaringan Komputer Dasar', 1, 2, 1, 4],
                ['432-191-207', 'Pemrograman Web Dasar', 1, 2, 1, 4],
                ['432-191-208', 'Sistem Digital', 1, 1, 1, 2],
            ],
            3 => [
                ['432-191-301', 'Pemrograman Berorientasi Objek', 1, 2, 1, 4],
                ['432-191-302', 'Pemrograman Web Lanjut', 1, 2, 1, 4],
                ['432-191-303', 'Mikrokontroler', 1, 2, 1, 4],
                ['432-191-304', 'Jaringan Komputer Lanjut', 1, 2, 1, 4],
                ['432-191-305', 'Aljabar Linear', 2, 0, 2, 0],
                ['432-191-306', 'Interaksi Manusia dan Komputer', 2, 0, 2, 0],
                ['432-191-307', 'Rekayasa Perangkat Lunak', 2, 1, 2, 2],
                ['432-191-308', 'Keamanan Informasi', 2, 0, 2, 0],
            ],
            4 => [
                ['432-191-401', 'Pemrograman Mobile', 1, 2, 1, 4],
                ['432-191-402', 'Internet of Things', 1, 2, 1, 4],
                ['432-191-403', 'Sistem Tertanam', 1, 2, 1, 4],
                ['432-191-404', 'Kecerdasan Buatan', 2, 1, 2, 2],
                ['432-191-405', 'Basis Data Terdistribusi', 1, 1, 1, 2],
                ['432-191-406', 'Manajemen Proyek TI', 2, 0, 2, 0],
                ['432-191-407', 'Proyek Terpadu I', 0, 3, 0, 6],
            ],
            5 => [
                ['432-191-501', 'Pembelajaran Mesin', 2, 1, 2, 2],
                ['432-191-502', 'Visi Komputer', 1, 2, 1, 4],
                ['432-191-503', 'Komputasi Awan', 1, 2, 1, 4],
                ['432-191-504', 'Pengolahan Sinyal Digital', 2, 1, 2, 2],
                ['432-191-505', 'Kewirausahaan', 2, 0, 2, 0],
                ['432-191-506', 'Proyek Terpadu II', 0, 3, 0, 6],
            ],
            6 => [
                ['432-191-601', 'Sistem Kendali', 2, 1, 2, 2],
                ['432-191-602', 'Robotika', 1, 2, 1, 4],
                ['432-191-603', 'Data Mining', 1, 2, 1, 4],
                ['432-191-604', 'Etika dan Hukum TI', 2, 0, 2, 0],
                ['432-191-605', 'Mata Kuliah Pilihan I', 2, 0, 2, 0],
                ['432-191-606', 'Mata Kuliah Pilihan II', 2, 0, 2, 0],
                ['432-191-607', 'Proyek Terpadu III', 0, 3, 0, 6],
            ],
            7 => [
                ['432-191-701', 'Praktik Kerja Lapangan', 0, 6, 0, 12],
                ['432-191-702', 'Seminar', 1, 1, 1, 2],
                ['432-191-703', 'Metodologi Penelitian', 2, 0, 2, 0],
                ['432-191-704', 'Mata Kuliah Pilihan III', 2, 0, 2, 0],
                ['432-191-705', 'Mata Kuliah Pilihan IV', 2, 0, 2, 0],
                ['432-191-706', 'Technopreneurship', 2, 0, 2, 0],
            ],
            8 => [
                ['432-191-801', 'Tugas Akhir', 0, 6, 0, 12],
                ['432-191-802', 'Sidang Tugas Akhir', 0, 2, 0, 4],
            ],
        ],
        '2024' => [
            1 => [
                ['432-181-101', 'Etika Profesi & K3', 2, 0, 2, 0],
                ['432-181-102', 'Matematika Diskrit', 2, 0, 2, 0],
                ['432-181-103', 'Pengantar Teknologi Informasi', 2, 0, 2, 0],
                ['432-181-104', 'Arsitektur dan Organisasi Komputer', 2, 1, 2, 2],
                ['432-181-105', 'Sistem Operasi', 1, 2, 1, 4],
                ['432-181-106', 'Algoritma dan Pemrograman', 1, 2, 1, 4],
            ],
            2 => [
                ['432-181-201', 'Bahasa Inggris', 2, 0, 2, 0],
                ['432-181-202', 'Pendidikan Pancasila', 2, 0, 2, 0],
                ['432-181-203', 'Statistika', 2, 0, 2, 0],
                ['432-181-204', 'Struktur Data', 1, 2, 1, 4],
                ['432-181-205', 'Basis Data II', 1, 2, 1, 4],
                ['432-181-206', 'Jaringan Komputer Dasar', 1, 2, 1, 4],
                ['432-181-207', 'Pemrograman Web Dasar', 1, 2, 1, 4],
            ],
            3 => [
                ['432-181-301', 'Pemrograman Berorientasi Objek', 1, 2, 1, 4],
                ['432-181-302', 'Pemrograman Web Lanjut', 1, 2, 1, 4],
                ['432-181-303', 'Mikrokontroler', 1, 2, 1, 4],
                ['432-181-304', 'Jaringan Komputer Lanjut', 1, 2, 1, 4],
                ['432-181-305', 'Aljabar Linear', 2, 0, 2, 0],
                ['432-181-306', 'Interaksi Manusia dan Komputer', 2, 0, 2, 0],
                ['432-181-307', 'Rekayasa Perangkat Lunak', 2, 1, 2, 2],
                ['432-181-308', 'Keamanan Informasi', 2, 0, 2, 0],
            ],
            4 => [
                ['432-181-401', 'Pemrograman Mobile', 1, 2, 1, 4],
                ['432-181-402', 'Internet of Things', 1, 2, 1, 4],
                ['432-181-403', 'Sistem Tertanam', 1, 2, 1, 4],
                ['432-181-404', 'Kecerdasan Buatan', 2, 1, 2, 2],
                ['432-181-405', 'Basis Data Terdistribusi', 1, 1, 1, 2],
                ['432-181-406', 'Manajemen Proyek TI', 2, 0, 2, 0],
            ],
            5 => [
                ['432-181-501', 'Pembelajaran Mesin', 2, 1, 2, 2],
                ['432-181-502', 'Visi Komputer', 1, 2, 1, 4],
                ['432-181-503', 'Komputasi Awan', 1, 2, 1, 4],
                ['432-181-504', 'Pengolahan Sinyal Digital', 2, 1, 2, 2],
                ['432-181-505', 'Kewirausahaan', 2, 0, 2, 0],
                ['432-181-506', 'Proyek Terpadu II', 0, 3, 0, 6],
            ],
            6 => [
                ['432-181-601', 'Sistem Kendali', 2, 1, 2, 2],
                ['432-181-602', 'Robotika', 1, 2, 1, 4],
                ['432-181-603', 'Data Mining', 1, 2, 1, 4],
                ['432-181-604', 'Etika dan Hukum TI', 2, 0, 2, 0],
                ['432-181-605', 'Mata Kuliah Pilihan I', 2, 0, 2, 0],
            ],
            7 => [
                ['432-181-701', 'Praktik Kerja Lapangan', 0, 6, 0, 12],
                ['432-181-702', 'Seminar', 1, 1, 1, 2],
                ['432-181-703', 'Metodologi Penelitian', 2, 0, 2, 0],
                ['432-181-704', 'Mata Kuliah Pilihan III', 2, 0, 2, 0],
            ],
            8 => [
                ['432-181-801', 'Tugas Akhir', 0, 6, 0, 12],
                ['432-181-802', 'Sidang Tugas Akhir', 0, 2, 0, 4],
            ],
        ],
        '2021' => [
            1 => [
                ['432-161-101', 'Etika Profesi & K3', 2, 0, 2, 0],
                ['432-161-102', 'Matematika Diskrit', 2, 0, 2, 0],
                ['432-161-103', 'Pengantar Teknologi Informasi', 2, 0, 2, 0],
                ['432-161-104', 'Arsitektur dan Organisasi Komputer', 2, 1, 2, 2],
                ['432-161-105', 'Sistem Operasi', 1, 2, 1, 4],
            ],
            2 => [
                ['432-161-201', 'Bahasa Inggris', 2, 0, 2, 0],
                ['432-161-202', 'Pendidikan Pancasila', 2, 0, 2, 0],
                ['432-161-203', 'Statistika', 2, 0, 2, 0],
                ['432-161-204', 'Struktur Data', 1, 2, 1, 4],
                ['432-161-205', 'Basis Data II', 1, 2, 1, 4],
                ['432-161-206', 'Jaringan Komputer Dasar', 1, 2, 1, 4],
            ],
            3 => [
                ['432-161-301', 'Pemrograman Berorientasi Objek', 1, 2, 1, 4],
                ['432-161-302', 'Pemrograman Web Lanjut', 1, 2, 1, 4],
                ['432-161-303', 'Mikrokontroler', 1, 2, 1, 4],
                ['432-161-304', 'Jaringan Komputer Lanjut', 1, 2, 1, 4],
                ['432-161-305', 'Aljabar Linear', 2, 0, 2, 0],
                ['432-161-306', 'Interaksi Manusia dan Komputer', 2, 0, 2, 0],
                ['432-161-307', 'Rekayasa Perangkat Lunak', 2, 1, 2, 2],
            ],
            4 => [
                ['432-161-401', 'Pemrograman Mobile', 1, 2, 1, 4],
                ['432-161-402', 'Internet of Things', 1, 2, 1, 4],
                ['432-161-403', 'Sistem Tertanam', 1, 2, 1, 4],
                ['432-161-404', 'Kecerdasan Buatan', 2, 1, 2, 2],
                ['432-161-405', 'Basis Data Terdistribusi', 1, 1, 1, 2],
                ['432-161-406', 'Manajemen Proyek TI', 2, 0, 2, 0],
            ],
            5 => [
                ['432-161-501', 'Pembelajaran Mesin', 2, 1, 2, 2],
                ['432-161-502', 'Visi Komputer', 1, 2, 1, 4],
                ['432-161-503', 'Komputasi Awan', 1, 2, 1, 4],
                ['432-161-504', 'Pengolahan Sinyal Digital', 2, 1, 2, 2],
                ['432-161-505', 'Kewirausahaan', 2, 0, 2, 0],
            ],
            6 => [
                ['432-161-601', 'Sistem Kendali', 2, 1, 2, 2],
                ['432-161-602', 'Robotika', 1, 2, 1, 4],
                ['432-161-603', 'Data Mining', 1, 2, 1, 4],
                ['432-161-604', 'Etika dan Hukum TI', 2, 0, 2, 0],
            ],
        ],
    ],
];