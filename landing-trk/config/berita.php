<?php

// Data simulasi berita. Nanti diganti tabel `berita` di database.
// Kunci array = slug di URL: /kabar/{slug}
// Kunci HARUS sama persis dengan 'slug' di $news pada home.blade.php.

// Samakan dengan nilai $A di home.blade.php
$A = 'https://api.builder.io/api/v1/image/assets/TEMP/';

$buat = fn (string $slug, string $judul, string $img, string $kategori, array $paragraf) => [
    'slug'            => $slug,
    'judul'           => $judul,
    'penulis'         => 'Humas TRK',
    'kategori'        => $kategori,
    'tanggal_pendek'  => '09/10/2026',
    'tanggal_panjang' => 'Jumat, 9 Oktober 2026',
    'tanggal_ringkas' => '09 Okt 2026',
    'jam'             => '10:00',
    'dilihat'         => 0,
    'waktu_baca'      => '2 menit',
    'gambar'          => $img,
    'paragraf'        => $paragraf,
];

return [

    'pelatihan-sensor-sma-alam-ar-ridho' => $buat(
        'pelatihan-sensor-sma-alam-ar-ridho',
        'Tim Pengabdian TRK Beri Pelatihan Sensor Sederhana Pada Sistem Terbenam di SMP Alam Ar-Ridho',
        $A . 'eea1dae43f4c4adec94cdca526899e7374b5d9b4?width=1760',
        'Pengabdian',
        [
            'Sekolah Alam Ar Ridho merupakan sekolah yang berbasis pada eksplorasi alam sebagai bahan pendidikan dengan konsep penelitian dasar. Sekolah ini berlokasi di Rejosari RT 01/RW 11, Meteseh, Tembalang, Kota Semarang, Jawa Tengah. Sekolah Alam Ar-Ridho merupakan sekolah yang berbasis pada explorasi alam sebagai bahan pendidikan dengan konsep penelitian dasar dan memanfaatkan alam sebagai media penelitian dan penggalian ide dalam mengeksplorasi dan mengamati perubahan yang terjadi di sekitar mereka.',
            'Melalui program Pengabdian Kepada Masyarakat Pratama (PKMP) Polines, tim pengabdian program studi Teknologi Rekayasa Komputer (Prodi TRK) yang diketuai oleh Tri Raharjo Yudantoro, S.Kom., M.Kom. dengan anggota Mardiyono, S.Kom, M.Sc., Dr. Ir. Kurnianingsih, S.T., M.T., Nurseno Bayu Aji, S.Kom., Muhammad Irwan Yanwari, S.Kom., M.Eng., Kuwat Santoso, S.Kom., M.Kom., Wiktasari, S.T., M.Kom., Angga Wahyu Wibowo, S.Kom., M.Eng., dan Afandi Nur Aziz Thohari, ST, M.Sc serta 2 orang mahasiswa Prodi TRK (Ahmad Musyaffa’ Fathullah dan Elza Safira Permatasari) memberikan pelatihan teknologi terbenam untuk menunjang eksplorasi dan pengamatan lingkungan sekitar dengan memberikan pelatihan tentang teknologi Sistem Tertanam kepada guru dan murid dan memberikan Purwarupa/prototipe teknologi Sistem Tertanam yang nantinya dapat digunakan oleh guru dan siswa untuk mengembangkan sistem terbenam berdasarkan kebutuhan mereka.',
            'Kegiatan pengabdian diawali dengan proses Persiapan Awal untuk mengetahui sarana dan prasarana yang sudah ada serta metode pembelajaran yang sudah dilakukan. Dari kegiatan observasi ini tim pengusul memperoleh gambaran tentang konten teknologi Sistem Tertanam serta kebutuhan peralatannya. Selanjutnya Analisis permasalahan mitra & penyusunan materi dengan merancang materi pada kegiatan pengabdian masyarakat ini dan memberikan contoh yang sederhana, sehingga mudah dimengerti oleh peserta. Selanjutnya Pelatihan dan Pendampingan dalam penyusunan dan perakitan Sistem Tertanam dilakukan dengan melibatkan tenaga pendidik (guru) agar dapat mengerti dasar penggunaan alat sehingga tidak terhenti pada saat penyerahan peralatan. Evaluasi dilakukan untuk mengetahui hasil dari pengimplementasian solusi.',
            'Melalui pelatihan Sistem Tertanam akan membantu sekolah Alam Ar-Ridho dalam pengembangan proses belajar mengajar pada sekolah tersebut, diharapkan siswa dapat bereksplorasi lebih jauh dengan mempelajari tanaman yang tidak tersedia di lingkungan sekolah dengan memasang sensor-sensor untuk pengamatan tanaman.',
        ]
    ),

    'deputy-director-seameo-seamolec' => $buat(
        'deputy-director-seameo-seamolec',
        'Penugasan sebagai Deputy Director Program SEAMEO SEAMOLEC',
        $A . 'c03aeabc41a38010ff1ed3bb6aa5185ef5db5a31?width=974',
        'Kabar Kampus',
        ['SEMARANG – Politeknik Negeri Semarang (Polines) kembali memperkuat jejaring internasional melalui ... (isi lengkap berita).']
    ),

    'pengabdian-umkm-gemah-ripah' => $buat(
        'pengabdian-umkm-gemah-ripah',
        'Tim Pengabdian Prodi Teknologi Rekayasa Komputer Untuk UMKM Gemah Ripah',
        $A . 'fa2d7ab93ba4ebffe1bef04b7fdc1fd74963cae8?width=1024',
        'Pengabdian',
        ['Pelatihan Penerapan IoT Untuk Sistem Pengasap Telur Asin Dan Pelatihan Digital Marketing ... (isi lengkap berita).']
    ),

    'deputy-director-seameo-seamolec-2' => $buat(
        'deputy-director-seameo-seamolec-2',
        'Penugasan sebagai Deputy Director Program SEAMEO SEAMOLEC',
        $A . 'be0a1bcac500d7ee3265969f939ef4dcbf5f4065?width=820',
        'Kabar Kampus',
        ['SEMARANG – Politeknik Negeri Semarang (Polines) kembali memperkuat jejaring internasional melalui ... (isi lengkap berita).']
    ),

    'pengukuhan-guru-besar-polines' => $buat(
        'pengukuhan-guru-besar-polines',
        'Pengukuhan Guru Besar POLINES',
        $A . '3b6a6f87a3cafeffaa2d1a16bea075664fcb7385?width=974',
        'Kabar Kampus',
        ['SEMARANG – Politeknik Negeri Semarang (Polines) kembali memperkuat pendidikan melalui .... (isi lengkap berita).']
    ),

    'berita-keenam' => $buat(
        'berita-keenam',
        'Judul Berita Keenam',
        $A . '3b6a6f87a3cafeffaa2d1a16bea075664fcb7385?width=974', // ganti dengan gambar sendiri
        'Kegiatan',
        ['Isi berita keenam. Ganti dengan berita yang sebenarnya.']
    ),
        'fifa-asean-cup-2026' => $buat(
        'fifa-asean-cup-2026',
        'The Winner of FIFA ASEAN CUP 2026',
        $A . '4968f5c7b9f309e7174bca9d6daa415427643463?width=990',
        'Prestasi',
        ['Isi berita lengkap. Ganti dengan berita yang sebenarnya.']
    ),

    'juara-1-cybersecurity-olivia-2026' => $buat(
        'juara-1-cybersecurity-olivia-2026',
        'Juara 1 Cybersecurity Kategori Bidang Digital & Media Technology pada OLIVIA 2026',
        $A . '4e286dc44fd27faa65f73e3636c93d05414ebc75?width=610',
        'Prestasi',
        ['Isi berita lengkap. Ganti dengan berita yang sebenarnya.']
    ),

    'juara-2-elektro-expo-2026' => $buat(
        'juara-2-elektro-expo-2026',
        'Juara 2 Elektro Expo 2026 IoT-Desain Produk Inovasi',
        $A . '2ebd3fa8924f63ca443fd5416ad9abf263016bf5?width=610',
        'Prestasi',
        ['Isi berita lengkap. Ganti dengan berita yang sebenarnya.']
    ),

    'juara-3-cybersecurity-olivia-2026' => $buat(
        'juara-3-cybersecurity-olivia-2026',
        'Juara 3 Cybersecurity Kategori Bidang Digital & Media Technology pada OLIVIA 2026',
        $A . '3a7735c15bfc00b23797027d54c3b8e33e939ec5?width=670',
        'Prestasi',
        ['Isi berita lengkap. Ganti dengan berita yang sebenarnya.']
    ),
];