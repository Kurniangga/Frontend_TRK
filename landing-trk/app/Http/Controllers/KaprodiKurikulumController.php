<?php

namespace App\Http\Controllers;

use App\Repositories\KurikulumStore;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/** Kurikulum TRK untuk kaprodi: tambah / ubah / ubah status / hapus (data sama dengan admin: KurikulumStore). */
class KaprodiKurikulumController extends Controller
{
    public function __construct(private KurikulumStore $store) {}

    public function index()
    {
        $kurikulum = array_map(fn ($r) => $this->store->forAdmin($r), $this->store->all());

        return view('kaprodi.kurikulum', compact('kurikulum'));
    }

    public function store(Request $request): JsonResponse
    {
        $row = $this->store->create($this->validated($request), $request->file('pdf'));

        return response()->json(['data' => $this->store->forAdmin($row)], 201);
    }

    public function update(Request $request, string $id): JsonResponse
    {
        $row = $this->store->update($id, $this->validated($request), $request->file('pdf'));
        abort_unless($row, 404);

        return response()->json(['data' => $this->store->forAdmin($row)]);
    }

    public function status(string $id): JsonResponse
    {
        $row = $this->store->toggleStatus($id);
        abort_unless($row, 404);

        return response()->json(['data' => $this->store->forAdmin($row)]);
    }

    public function destroy(string $id): JsonResponse
    {
        abort_unless($this->store->delete($id), 404);

        return response()->json(['ok' => true]);
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'description' => ['nullable', 'string', 'max:255'],
            'year' => ['required', 'digits:4', 'integer', 'between:1990,2100'],
            'pdf' => ['nullable', 'file', 'mimes:pdf', 'max:20480'],
        ], [
            'name.required' => 'Nama kurikulum wajib diisi.',
            'year.required' => 'Tahun terbit wajib diisi.',
            'year.digits' => 'Tahun terbit harus 4 angka, mis. 2026.',
            'year.between' => 'Tahun terbit tidak valid.',
            'pdf.mimes' => 'Dokumen harus berformat PDF.',
            'pdf.max' => 'Ukuran PDF maksimal 20 MB.',
        ]);
    }
}
