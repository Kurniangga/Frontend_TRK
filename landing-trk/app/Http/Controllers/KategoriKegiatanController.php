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

    /** Halaman tambah: form di kiri, pratinjau surat di kanan. */
    public function create()
    {
        return view('admin.kategori-kegiatan-form', [
            'row'   => null,
            'jenis' => KategoriKegiatanStore::JENIS,
        ]);
    }

    /** Halaman edit: halaman yang sama, form terisi data kategori. */
    public function edit(string $id)
    {
        $row = collect($this->store->all())->first(fn ($r) => (string) $r['id'] === (string) $id);

        abort_if(! $row, 404);

        return view('admin.kategori-kegiatan-form', [
            'row'   => $row,
            'jenis' => KategoriKegiatanStore::JENIS,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $this->validasi($request);

        if ($this->store->exists($data['name'], $data['jenis'])) {
            return $this->duplikat();
        }

        $row = $this->store->create(
            $data['name'],
            $data['jenis'],
            $data['description'] ?? null,
            $data['template_surat'] ?? null
        );

        return response()->json(['ok' => true, 'row' => $row]);
    }

    public function update(Request $request, string $id): JsonResponse
    {
        $data = $this->validasi($request);

        if ($this->store->exists($data['name'], $data['jenis'], $id)) {
            return $this->duplikat();
        }

        $row = $this->store->update(
            $id,
            $data['name'],
            $data['jenis'],
            $data['description'] ?? null,
            $data['template_surat'] ?? null
        );

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
            'name'           => ['required', 'string', 'max:100'],
            'jenis'          => ['required', Rule::in(KategoriKegiatanStore::JENIS)],
            'description'    => ['nullable', 'string', 'max:255'],
            'template_surat' => ['nullable', 'string'],
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