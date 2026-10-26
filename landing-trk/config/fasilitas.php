<?php

/*
 * DATA FASILITAS (halaman /fasilitas).
 * Urutan di sini = urutan foto kecil di bawah (dan urutan kartu di beranda).
 *
 * Menggunakan aset gambar Builder.io yang sama persis dengan halaman Beranda.
 */
$A = 'https://api.builder.io/api/v1/image/assets/TEMP/';

return [
    'daftar' => [
        [
            'nama' => 'Lab Komputer',
            'foto' => $A . 'd85de1fefd9f5de7f85646e5eae18e98f4154ff2?width=1200',
            'deskripsi' => 'Laboratorium komputer dengan spesifikasi tinggi untuk menunjang praktikum pemrograman, kecerdasan buatan, jaringan komputer, dan rekayasa perangkat keras.',
        ],
        [
            'nama' => 'Co-working Space',
            'foto' => $A . 'd4d26540f985d832c65d0719054314a08b288e4a?width=1200',
            'deskripsi' => 'Ruang kolaboratif terbuka yang nyaman dan fleksibel bagi mahasiswa TRK untuk berdiskusi, mengerjakan proyek kelompok, dan bertukar ide kreatif.',
        ],
        [
            'nama' => 'Discuss Room',
            'foto' => $A . 'ef269c73505424833dd0b3a65b588799a0a7b9bd?width=1200',
            'deskripsi' => 'Ruang diskusi tertutup yang kondusif untuk koordinasi tim proyek, bimbingan tugas akhir, maupun presentasi kelompok kecil.',
        ],
        [
            'nama' => 'Private Room',
            'foto' => $A . 'b85fffdbc922839aff2a4e96209ccefe9ac73fd6?width=1200',
            'deskripsi' => 'Ruangan fokus dan hening untuk belajar mandiri, riset mendalam, maupun persiapan kompetisi teknologi.',
        ],
        [
            'nama' => 'Ruang Kelas Gedung Kuliah Terpadu',
            'foto' => $A . '9cb23edb5c694e6944be6e883ae4547b5a1f42a7?width=1200',
            'deskripsi' => 'Ruang perkuliahan modern di Gedung Kuliah Terpadu (GKT) berfasilitas multimedia lengkap, proyektor, dan AC untuk kegiatan perkuliahan teori.',
        ],
    ],
];
