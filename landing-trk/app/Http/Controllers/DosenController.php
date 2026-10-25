<?php

namespace App\Http\Controllers;

use App\Repositories\DosenStore;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class DosenController extends Controller
{
    public function __construct(private DosenStore $store) {}

    /** Daftar dosen. (Hanya kolom yang dibutuhkan tabel — tidak mengirim hash sandi dll.) */
    public function index()
    {
        $dosen = array_map(fn ($r) => [
            'id' => $r['id'],
            'nip' => $r['nip'],
            'nama' => $r['nama'],
            'jabatan' => $r['jabatan'],
            'golongan' => $r['golongan'],
            'status' => $r['status'],
        ], $this->store->all());

        return view('admin.dosen', ['dosen' => $dosen, 'jabatan' => DosenStore::JABATAN]);
    }

    /** Detail satu dosen: profil, riwayat pangkat, dan aktivitas (4 tab). */
    public function show(string $id)
    {
        $row = $this->store->find($id);
        abort_unless($row, 404);

        $dosen = [
            'id' => $row['id'],
            'nama' => $row['nama'],
            'nip' => $row['nip'],
            'status' => $row['status'],
            'foto' => null,   // URL foto dosen; null = tampil inisial
        ];

        $info = [
            ['Email akun', $row['email']],
            ['Role', DosenStore::ROLE[$row['role']] ?? 'Dosen'],
            ['Pangkat / golongan saat ini', $row['jabatan'].', '.$row['golongan']],
            ['Akun login', $row['status_akun'] === 'aktif' ? 'Aktif' : 'Nonaktif'],
        ];

        $riwayat = [];
        foreach ($row['riwayat'] as $i => $r) {
            $riwayat[] = [
                'pangkat' => $r['pangkat'],
                'golongan' => $r['golongan'],
                'sk' => $r['sk'],
                'tanggal' => Carbon::parse($r['tanggal'])->locale('id')->translatedFormat('j F Y'),
                'now' => $i === 0,
            ];
        }

        return view('admin.dosen-detail', [
            'dosen' => $dosen,
            'info' => $info,
            'riwayat' => $riwayat,
            'aktivitas' => $this->aktivitas($row),
        ]);
    }

    /** Form tambah dosen. */
    public function create()
    {
        return view('admin.dosen-tambah', [
            'jabatan' => DosenStore::JABATAN,
            'golongan' => DosenStore::GOLONGAN,
            'roles' => DosenStore::ROLE,
        ]);
    }

    /** Simpan dosen baru. */
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'nama_lengkap' => ['required', 'string', 'max:150'],
            'nip' => ['required', 'regex:/^\d{8,20}$/', function ($attr, $value, $fail) {
                if ($this->store->exists('nip', $value)) {
                    $fail('NIP sudah terdaftar.');
                }
            }],
            'status_dosen' => ['required', Rule::in(['aktif', 'nonaktif'])],
            'email' => ['required', 'email', 'max:150', function ($attr, $value, $fail) {
                if ($this->store->exists('email', $value)) {
                    $fail('Email sudah dipakai akun lain.');
                }
            }],
            'role' => ['required', Rule::in(array_keys(DosenStore::ROLE))],
            'password' => ['required', 'string', 'min:8'],
            'pangkat' => ['required', Rule::in(DosenStore::JABATAN)],
            'golongan' => ['required', Rule::in(DosenStore::GOLONGAN)],
            'nomor_sk' => ['required', 'string', 'max:100'],
            'tanggal_sk' => ['required', 'date'],
        ], [
            'nama_lengkap.required' => 'Nama lengkap wajib diisi.',
            'nip.required' => 'NIP wajib diisi.',
            'nip.regex' => 'NIP harus berupa angka 8–20 digit.',
            'email.required' => 'Email akun wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'password.required' => 'Password wajib diisi.',
            'password.min' => 'Password minimal 8 karakter.',
            'pangkat.required' => 'Pangkat wajib dipilih.',
            'golongan.required' => 'Golongan wajib dipilih.',
            'nomor_sk.required' => 'Nomor SK wajib diisi.',
            'tanggal_sk.required' => 'Tanggal SK wajib diisi.',
            'tanggal_sk.date' => 'Tanggal SK tidak valid.',
        ]);

        // TODO-DB: simpan ke database (satu transaksi: dosen + akun login + riwayat pangkat pertama).
        $row = $this->store->create([
            'nama' => $data['nama_lengkap'],
            'nip' => $data['nip'],
            'status' => $data['status_dosen'],
            'email' => $data['email'],
            'role' => $data['role'],
            'password' => $data['password'],
            'pangkat' => $data['pangkat'],
            'golongan' => $data['golongan'],
            'sk' => $data['nomor_sk'],
            'tanggal' => $data['tanggal_sk'],
        ]);

        return redirect()->route('admin.dosen.show', $row['id'])->with('status', 'Dosen baru berhasil ditambahkan.');
    }

    /** Form edit. */
    public function edit(string $id)
    {
        $row = $this->store->find($id);
        abort_unless($row, 404);

        return view('admin.dosen-edit', [
            'dosen' => [
                'id' => $row['id'],
                'nama_lengkap' => $row['nama'],
                'nip' => $row['nip'],
                'status_dosen' => $row['status'],
                'email' => $row['email'],
                'role' => $row['role'],
                'status_akun' => $row['status_akun'],
                'pangkat' => '',      // bagian "tambah riwayat baru" selalu kosong saat dibuka
                'golongan' => '',
                'nomor_sk' => '',
                'tanggal_sk' => '',
            ],
            'jabatan' => DosenStore::JABATAN,
            'golongan' => DosenStore::GOLONGAN,
            'roles' => DosenStore::ROLE,
        ]);
    }

    /** Simpan perubahan. */
    public function update(Request $request, string $id): RedirectResponse
    {
        abort_unless($this->store->find($id), 404);

        $data = $request->validate([
            'nama_lengkap' => ['required', 'string', 'max:150'],
            'nip' => ['required', 'regex:/^\d{8,20}$/', function ($attr, $value, $fail) use ($id) {
                if ($this->store->usedByOther('nip', $value, $id)) {
                    $fail('NIP sudah dipakai dosen lain.');
                }
            }],
            'status_dosen' => ['required', Rule::in(['aktif', 'nonaktif'])],
            'email' => ['required', 'email', 'max:150', function ($attr, $value, $fail) use ($id) {
                if ($this->store->usedByOther('email', $value, $id)) {
                    $fail('Email sudah dipakai akun lain.');
                }
            }],
            'role' => ['required', Rule::in(array_keys(DosenStore::ROLE))],
            'status_akun' => ['required', Rule::in(['aktif', 'nonaktif'])],
            'password' => ['nullable', 'string', 'min:8'],
            // riwayat pangkat baru: opsional, tetapi kalau diisi harus lengkap
            'pangkat' => ['nullable', 'required_with:golongan,nomor_sk,tanggal_sk', Rule::in(DosenStore::JABATAN)],
            'golongan' => ['nullable', 'required_with:pangkat,nomor_sk,tanggal_sk', Rule::in(DosenStore::GOLONGAN)],
            'nomor_sk' => ['nullable', 'required_with:pangkat,golongan,tanggal_sk', 'string', 'max:100'],
            'tanggal_sk' => ['nullable', 'required_with:pangkat,golongan,nomor_sk', 'date'],
        ], [
            'nama_lengkap.required' => 'Nama lengkap wajib diisi.',
            'nip.required' => 'NIP wajib diisi.',
            'nip.regex' => 'NIP harus berupa angka 8–20 digit.',
            'email.required' => 'Email akun wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'password.min' => 'Password baru minimal 8 karakter.',
            'pangkat.required_with' => 'Pangkat wajib diisi bila menambah riwayat.',
            'golongan.required_with' => 'Golongan wajib diisi bila menambah riwayat.',
            'nomor_sk.required_with' => 'Nomor SK wajib diisi bila menambah riwayat.',
            'tanggal_sk.required_with' => 'Tanggal SK wajib diisi bila menambah riwayat.',
            'tanggal_sk.date' => 'Tanggal SK tidak valid.',
        ]);

        $riwayatBaru = ! empty($data['pangkat'])
            ? ['pangkat' => $data['pangkat'], 'golongan' => $data['golongan'], 'sk' => $data['nomor_sk'], 'tanggal' => $data['tanggal_sk']]
            : null;

        // TODO-DB: simpan ke database (di dalam satu transaksi: dosen + akun login + riwayat).
        $this->store->update($id, [
            'nama' => $data['nama_lengkap'],
            'nip' => $data['nip'],
            'status' => $data['status_dosen'],
            'email' => $data['email'],
            'role' => $data['role'],
            'status_akun' => $data['status_akun'],
            'password' => $data['password'] ?? null,
        ], $riwayatBaru);

        return redirect()->route('admin.dosen.show', $id)->with('status', 'Perubahan data dosen berhasil disimpan.');
    }

    /** Aktivitas dosen (4 tab). TODO-DB: ganti dengan query. */
    private function aktivitas(array $row): array
    {
        return [
            'penelitian' => ['label' => 'Penelitian', 'items' => [
                ['title' => 'Deteksi hoaks dengan machine learning', 'year' => '2026', 'type' => 'Penelitian Terapan',      'role' => 'Ketua'],
                ['title' => 'Analisis sentimen ulasan aplikasi',     'year' => '2025', 'type' => 'Penelitian Dasar',        'role' => 'Anggota'],
                ['title' => 'Sistem rekomendasi mata kuliah',        'year' => '2025', 'type' => 'Penelitian Pengembangan', 'role' => 'Anggota'],
            ]],
            'pengabdian' => ['label' => 'Pengabdian', 'items' => [
                ['title' => 'Pelatihan sensor sederhana pada sistem terbenam', 'year' => '2025', 'type' => 'Pengabdian kepada Masyarakat', 'role' => 'Ketua'],
                ['title' => 'Pendampingan digitalisasi UMKM',                  'year' => '2024', 'type' => 'Pengabdian kepada Masyarakat', 'role' => 'Anggota'],
            ]],
            'pengajaran' => ['label' => 'Pengajaran', 'items' => [
                ['title' => 'Pemrograman Web Lanjut', 'year' => '2026', 'type' => 'Semester Ganjil · 3 SKS', 'role' => 'Pengampu'],
                ['title' => 'Basis Data II',          'year' => '2026', 'type' => 'Semester Ganjil · 3 SKS', 'role' => 'Pengampu'],
                ['title' => 'Jaringan Komputer',      'year' => '2025', 'type' => 'Semester Genap · 3 SKS',  'role' => 'Koordinator'],
            ]],
            'kegiatan' => ['label' => 'Kegiatan', 'items' => [
                ['title' => 'Seminar Nasional AI', 'year' => '2026', 'type' => 'Seminar',            'role' => 'Pemateri'],
                ['title' => 'Pelatihan Industri',  'year' => '2025', 'type' => 'Pelatihan',          'role' => 'Peserta'],
                ['title' => 'Kunjungan Industri',  'year' => '2025', 'type' => 'Kunjungan Industri', 'role' => 'Pendamping'],
            ]],
        ];
    }
}