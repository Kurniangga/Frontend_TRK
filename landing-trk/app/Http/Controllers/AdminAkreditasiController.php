<?php

namespace App\Http\Controllers;

use App\Repositories\AkreditasiStore;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/** Akreditasi (admin saja). Hanya SATU akreditasi yang boleh aktif. */
class AdminAkreditasiController extends Controller
{
    public function __construct(private AkreditasiStore $store) {}

    public function index()
    {
        $akreditasi = array_map(fn ($r) => $this->store->forAdmin($r), $this->store->all());

        return view('admin.akreditasi', [
            'akreditasi' => $akreditasi,
            'lembagaList' => AkreditasiStore::LEMBAGA,
            'peringkatList' => AkreditasiStore::PERINGKAT,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $row = $this->store->create($this->validated($request), $request->boolean('aktifkan'), $request->file('pdf'));

        // Kirim ulang semua baris: status akreditasi lain mungkin ikut berubah.
        return response()->json(['data' => $this->semua(), 'item' => $this->store->forAdmin($row)], 201);
    }

    public function update(Request $request, string $id): JsonResponse
    {
        $row = $this->store->update($id, $this->validated($request), $request->file('pdf'));
        abort_unless($row, 404);

        return response()->json(['data' => $this->semua()]);
    }

    /** Jadikan akreditasi ini yang aktif (yang lain otomatis nonaktif). */
    public function status(string $id): JsonResponse
    {
        abort_unless($this->store->activate($id), 404);

        return response()->json(['data' => $this->semua()]);
    }

    public function destroy(string $id): JsonResponse
    {
        $hasil = $this->store->delete($id);
        abort_if($hasil === 'tidak-ada', 404);

        if ($hasil === 'aktif') {
            return response()->json(['message' => 'Akreditasi yang aktif tidak bisa dihapus. Aktifkan akreditasi lain terlebih dahulu.'], 422);
        }

        return response()->json(['data' => $this->semua()]);
    }

    private function semua(): array
    {
        return array_map(fn ($r) => $this->store->forAdmin($r), $this->store->all());
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'lembaga' => ['required', 'string', 'max:80'],
            'peringkat' => ['required', Rule::in(AkreditasiStore::PERINGKAT)],
            'nomor_sk' => ['nullable', 'string', 'max:120'],
            'year' => ['required', 'digits:4', 'integer', 'between:1990,2100'],
            'berlaku_sampai' => ['nullable', 'date'],
            'pdf' => ['nullable', 'file', 'mimes:pdf', 'max:20480'],
        ], [
            'lembaga.required' => 'Lembaga akreditasi wajib diisi.',
            'peringkat.required' => 'Peringkat wajib dipilih.',
            'year.required' => 'Tahun terbit wajib diisi.',
            'year.digits' => 'Tahun terbit harus 4 angka, mis. 2026.',
            'year.between' => 'Tahun terbit tidak valid.',
            'berlaku_sampai.date' => 'Tanggal berlaku tidak valid.',
            'pdf.mimes' => 'Dokumen harus berformat PDF.',
            'pdf.max' => 'Ukuran PDF maksimal 20 MB.',
        ]);
    }
}
