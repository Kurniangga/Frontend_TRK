<?php

namespace App\Http\Controllers;

use App\Repositories\KategoriKegiatanStore;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class KategoriKegiatanController extends Controller
{
    public function __construct(private KategoriKegiatanStore $store)
    {
    }

    public function index()
    {
        return view('admin.kategori-kegiatan', [
            'rows'  => $this->store->all(),
            'jenis' => KategoriKegiatanStore::JENIS,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $this->validasi($request);

        if ($this->store->exists($data['name'], $data['jenis'])) {
            return $this->duplikat();
        }

        $row = $this->store->create($data['name'], $data['jenis'], $data['description'] ?? null);

        return response()->json(['ok' => true, 'row' => $row]);
    }

    public function update(Request $request, string $id): JsonResponse
    {
        $data = $this->validasi($request);

        if ($this->store->exists($data['name'], $data['jenis'], $id)) {
            return $this->duplikat();
        }

        $row = $this->store->update($id, $data['name'], $data['jenis'], $data['description'] ?? null);

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
            'jenis'       => ['required', Rule::in(KategoriKegiatanStore::JENIS)],
            'description' => ['nullable', 'string', 'max:255'],
        ], [
            'name.required'  => 'Nama kategori wajib diisi.',
            'name.max'       => 'Nama kategori maksimal 100 karakter.',
            'jenis.required' => 'Jenis kegiatan wajib dipilih.',
            'jenis.in'       => 'Jenis kegiatan tidak valid.',
        ]);
    }

    private function duplikat(): JsonResponse
    {
        return response()->json(['message' => 'Nama kategori sudah ada pada jenis kegiatan ini.'], 422);
    }
}