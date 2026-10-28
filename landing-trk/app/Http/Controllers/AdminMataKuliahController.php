<?php

namespace App\Http\Controllers;

use App\Repositories\KurikulumStore;
use App\Repositories\MataKuliahStore;
use Illuminate\Http\Request;

class AdminMataKuliahController extends Controller
{
    public function __construct(private MataKuliahStore $mk, private KurikulumStore $kurikulum) {}

    /** Daftar mata kuliah per kurikulum & semester (?kurikulum=..&semester=..&q=..). */
    public function index(Request $request)
    {
        $daftar = $this->kurikulum->options();
        $ids = array_column($daftar, 'id');

        $kurikulumId = in_array($request->query('kurikulum'), $ids, true) ? $request->query('kurikulum') : ($ids[0] ?? '');

        $semester = (int) $request->query('semester');
        if ($semester < 1 || $semester > 8) {
            $semester = $this->mk->firstSemester($kurikulumId);
        }

        $q = trim((string) $request->query('q'));

        return view('admin.mata-kuliah', [
            'kurikulumList' => $daftar,
            'kurikulumId' => $kurikulumId,
            'semesterList' => range(1, 8),
            'semester' => $semester,
            'q' => $q,
        ] + $this->mk->listing($kurikulumId, $semester, $q));
    }

    /** Form tambah mata kuliah. */
    public function create()
    {
        return view('admin.tambah-mata-kuliah', [
            'kurikulumOptions' => collect($this->kurikulum->options())->pluck('nama', 'id')->all(),
        ]);
    }

    /** Simpan mata kuliah baru. */
    public function store(Request $request)
    {
        $ids = array_column($this->kurikulum->options(), 'id');

        $data = $request->validate([
            'kurikulum' => ['required', 'in:'.implode(',', $ids)],
            'semester' => ['required', 'integer', 'between:1,8'],
            'kode' => ['required', 'string', 'max:30', function ($attribute, $value, $fail) use ($request) {
                if ($this->mk->kodeExists((string) $request->input('kurikulum'), trim($value))) {
                    $fail('Kode mata kuliah sudah dipakai pada kurikulum ini.');
                }
            }],
            'nama' => ['required', 'string', 'max:150'],
            'sks_teori' => ['required', 'integer', 'between:0,99'],
            'sks_praktek' => ['required', 'integer', 'between:0,99'],
            'jam_teori' => ['required', 'integer', 'between:0,99'],
            'jam_praktek' => ['required', 'integer', 'between:0,99'],
        ], [
            'required' => 'Kolom ini wajib diisi.',
            'integer' => 'Harus berupa angka bulat.',
            'between' => 'Nilai harus antara :min sampai :max.',
            'max' => 'Terlalu panjang (maksimal :max karakter).',
            'in' => 'Pilihan tidak valid.',
        ]);

        $this->mk->create($data);

        return redirect()
            ->route('admin.kurikulum.mata-kuliah', ['kurikulum' => $data['kurikulum'], 'semester' => $data['semester']])
            ->with('success', 'Mata kuliah "'.$data['nama'].'" berhasil ditambahkan.');
    }
}
