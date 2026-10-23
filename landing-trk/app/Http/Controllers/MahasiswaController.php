<?php

namespace App\Http\Controllers;

use App\Repositories\MahasiswaStore;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class MahasiswaController extends Controller
{
    public function __construct(private MahasiswaStore $store) {}

    /** Daftar mahasiswa. */
    public function index()
    {
        $students = array_map(fn ($r) => [
            'id' => $r['id'],
            'nim' => $r['nim'],
            'name' => $r['nama'],
            'level' => $r['level'],
            'className' => $r['kelas'],
            'status' => $r['status'],
        ], $this->store->all());

        return view('admin.mahasiswa', ['students' => $students, 'levels' => MahasiswaStore::TINGKAT]);
    }

    /** Detail satu mahasiswa. */
    public function show(string $id)
    {
        $row = $this->store->find($id);
        abort_unless($row, 404);

        // TODO-DB: riwayat & kegiatan diganti query database berdasarkan $id.
        $mahasiswa = ['id' => $row['id'], 'nama' => $row['nama'], 'nim' => $row['nim'], 'status' => $row['status']];

        $academic = [
            ['Angkatan', $row['angkatan']],
            ['Program studi', 'Teknik Informatika, D3'],
            ['Tingkat saat ini', $row['level']],
            ['Kelas saat ini', Str::after($row['kelas'], 'TI-')],
        ];

        $identity = [
            ['Email akun', $row['email']],
            ['Tanggal lahir', $row['tanggal_lahir'] ? Carbon::parse($row['tanggal_lahir'])->locale('id')->translatedFormat('j F Y') : '-'],
            ['No. telepon', $row['telepon']],
            ['Akun login', 'Aktif'],
        ];

        $history = [
            ['year' => '2025/2026', 'current' => true, 'sem' => 'Ganjil', 'prodi' => 'Teknik Informatika', 'level' => $row['level'], 'cls' => Str::after($row['kelas'], 'TI-')],
            ['year' => '2024/2025', 'sem' => 'Genap',  'prodi' => 'Teknik Informatika', 'level' => 'Tingkat 1', 'cls' => '2A'],
            ['year' => '2024/2025', 'sem' => 'Ganjil', 'prodi' => 'Teknik Informatika', 'level' => 'Tingkat 1', 'cls' => '2A'],
        ];

        $kegiatan = [
            ['title' => 'Kompetisi Pengembangan Aplikasi Web', 'year' => '2026', 'type' => 'Lomba',    'role' => 'Ketua Tim'],
            ['title' => 'Pelatihan Dasar Jaringan Komputer',   'year' => '2025', 'type' => 'Pelatihan', 'role' => 'Peserta'],
            ['title' => 'Seminar Nasional Teknologi Informasi', 'year' => '2025', 'type' => 'Seminar',  'role' => 'Panitia'],
        ];

        return view('admin.mahasiswa-detail', compact('mahasiswa', 'academic', 'identity', 'history', 'kegiatan'));
    }

    /** Form edit mahasiswa. */
    public function edit(string $id)
    {
        $mahasiswa = $this->store->find($id);
        abort_unless($mahasiswa, 404);

        $tahun = range((int) date('Y'), (int) date('Y') - 9);
        if (! in_array((int) $mahasiswa['angkatan'], $tahun, true)) {
            $tahun[] = (int) $mahasiswa['angkatan'];
            rsort($tahun);
        }

        return view('admin.mahasiswa-edit', [
            'mahasiswa' => $mahasiswa,
            'tahun' => $tahun,
            'statusAkad' => MahasiswaStore::STATUS,
        ]);
    }

    /** Simpan perubahan mahasiswa. */
    public function update(Request $request, string $id): RedirectResponse
    {
        abort_unless($this->store->find($id), 404);

        $data = $request->validate([
            'nama' => ['required', 'string', 'max:150'],
            'nim' => ['required', 'regex:/^\d{8,12}$/', function ($attr, $value, $fail) use ($id) {
                if ($this->store->usedByOther('nim', $value, $id)) {
                    $fail('NIM sudah dipakai mahasiswa lain.');
                }
            }],
            'tanggal_lahir' => ['nullable', 'date', 'before:today'],
            'telepon' => ['nullable', 'regex:/^[0-9+\-\s]{8,20}$/'],
            'angkatan' => ['required', 'integer', 'between:2000,'.((int) date('Y') + 1)],
            'status' => ['required', Rule::in(MahasiswaStore::STATUS)],
            'email' => ['required', 'email', 'max:150', function ($attr, $value, $fail) use ($id) {
                if ($this->store->usedByOther('email', $value, $id)) {
                    $fail('Email sudah dipakai akun lain.');
                }
            }],
            'password' => ['nullable', 'string', 'min:8'],
        ], [
            'nama.required' => 'Nama lengkap wajib diisi.',
            'nim.required' => 'NIM wajib diisi.',
            'nim.regex' => 'NIM harus berupa angka 8–12 digit.',
            'tanggal_lahir.date' => 'Tanggal lahir tidak valid.',
            'tanggal_lahir.before' => 'Tanggal lahir harus sebelum hari ini.',
            'telepon.regex' => 'No. telepon hanya boleh angka, spasi, tanda + dan -.',
            'angkatan.required' => 'Angkatan wajib dipilih.',
            'email.required' => 'Email akun wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'password.min' => 'Password baru minimal 8 karakter.',
        ]);

        // TODO-DB: simpan ke database (mahasiswa + akun login dalam satu transaksi).
        $this->store->update($id, $data);

        return redirect()->route('admin.mahasiswa.show', $id)->with('status', 'Perubahan data mahasiswa berhasil disimpan.');
    }
}