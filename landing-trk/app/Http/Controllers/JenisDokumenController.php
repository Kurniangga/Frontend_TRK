<?php

namespace App\Http\Controllers;

use App\Repositories\JenisDokumenStore;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class JenisDokumenController extends Controller
{
    public function __construct(private JenisDokumenStore $store)
    {
    }

    public function index()
    {
        return view('admin.jenis-dokumen', ['rows' => $this->store->all()]);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $this->validasi($request);

        if ($this->store->exists($data['name'])) {
            return $this->duplikat();
        }

        $row = $this->store->create($data['name'], $data['description'] ?? null);

        return response()->json(['ok' => true, 'row' => $row]);
    }

    public function update(Request $request, string $id): JsonResponse
    {
        $data = $this->validasi($request);

        if ($this->store->exists($data['name'], $id)) {
            return $this->duplikat();
        }

        $row = $this->store->update($id, $data['name'], $data['description'] ?? null);

        if (! $row) {
            return response()->json(['message' => 'Data tidak ditemukan.'], 404);
        }

        return response()->json(['ok' => true, 'row' => $row]);
    }

    public function destroy(string $id): JsonResponse
    {
        if (! $this->store->delete($id)) {
            return response()->json(['message' => 'Data tidak ditemukan.'], 404);
        }

        return response()->json(['ok' => true]);
    }

    private function validasi(Request $request): array
    {
        return $request->validate([
            'name'        => ['required', 'string', 'max:100'],
            'description' => ['nullable', 'string', 'max:255'],
        ], [
            'name.required' => 'Nama jenis dokumen wajib diisi.',
            'name.max'      => 'Nama jenis dokumen maksimal 100 karakter.',
        ]);
    }

    private function duplikat(): JsonResponse
    {
        return response()->json(['message' => 'Nama jenis dokumen sudah ada.'], 422);
    }
}