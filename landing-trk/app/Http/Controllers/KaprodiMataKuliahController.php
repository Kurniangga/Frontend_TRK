<?php

namespace App\Http\Controllers;

use App\Repositories\KurikulumStore;
use App\Repositories\MataKuliahStore;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

/** Mata Kuliah untuk kaprodi: lihat, tambah, ubah, hapus (data sama dengan admin & halaman publik: MataKuliahStore). */
class KaprodiMataKuliahController extends Controller
{
    public function __construct(private MataKuliahStore $mk, private KurikulumStore $kurikulum) {}

    /** Daftar per kurikulum & semester (?kurikulum=..&semester=..&q=..). */
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

        return view('kaprodi.mata-kuliah', [
            'kurikulumList' => $daftar,
            'kurikulumId' => $kurikulumId,
            'semesterList' => range(1, 8),
            'semester' => $semester,
            'q' => $q,
        ] + $this->mk->listing($kurikulumId, $semester, $q));
    }

    public function create(Request $request)
    {
        return view('kaprodi.mata-kuliah-tambah', [
            'kurikulumOptions' => $this->options(),
            // pilihan filter di halaman daftar ikut terisi di form
            'nilai' => [
                'kurikulum' => $request->query('kurikulum'),
                'semester' => $request->query('semester'),
            ],
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);
        $this->mk->create($data);

        return $this->kembali($data, 'Mata kuliah "'.$data['nama'].'" berhasil ditambahkan.');
    }

    public function edit(int $id)
    {
        $mk = $this->mk->find($id);
        abort_unless($mk, 404);

        return view('kaprodi.mata-kuliah-edit', [
            'kurikulumOptions' => $this->options(),
            'mk' => $mk,
            'nilai' => $mk,
        ]);
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        abort_unless($this->mk->find($id), 404);

        $data = $this->validated($request, $id);
        $this->mk->update($id, $data);

        return $this->kembali($data, 'Mata kuliah "'.$data['nama'].'" berhasil diperbarui.');
    }

    public function destroy(int $id): RedirectResponse
    {
        $mk = $this->mk->find($id);
        abort_unless($mk, 404);

        $this->mk->delete($id);

        return $this->kembali($mk, 'Mata kuliah "'.$mk['nama'].'" berhasil dihapus.');
    }

    /** @return array<string, string> id kurikulum => nama */
    private function options(): array
    {
        return collect($this->kurikulum->options())->pluck('nama', 'id')->all();
    }

    private function validated(Request $request, ?int $ignoreId = null): array
    {
        $ids = array_column($this->kurikulum->options(), 'id');

        return $request->validate([
            'kurikulum' => ['required', 'in:'.implode(',', $ids)],
            'semester' => ['required', 'integer', 'between:1,8'],
            'kode' => ['required', 'string', 'max:30', function ($attribute, $value, $fail) use ($request, $ignoreId) {
                if ($this->mk->kodeExists((string) $request->input('kurikulum'), trim($value), $ignoreId)) {
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
    }

    private function kembali(array $data, string $pesan): RedirectResponse
    {
        return redirect()
            ->route('kaprodi.kurikulum.mata-kuliah', ['kurikulum' => $data['kurikulum'], 'semester' => $data['semester']])
            ->with('success', $pesan);
    }
}
