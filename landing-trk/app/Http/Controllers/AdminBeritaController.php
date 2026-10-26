<?php

namespace App\Http\Controllers;

use App\Repositories\AdminBeritaStore;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/** Berita (admin): daftar, tambah, edit. Beda dengan BeritaController (halaman publik /kabar). */
class AdminBeritaController extends Controller
{
    public function __construct(private AdminBeritaStore $store) {}

    public function index()
    {
        return view('admin.berita', ['rows' => $this->store->all(), 'kegiatan' => AdminBeritaStore::KEGIATAN]);
    }

    /** Detail berita. */
    public function show(string $id)
    {
        $berita = $this->store->find($id);
        abort_unless($berita, 404);

        $tanggal = $berita['tanggal'] ? \Carbon\Carbon::parse($berita['tanggal'])->locale('id') : null;
        $kata = str_word_count(strip_tags($berita['isi']));

        return view('admin.berita-detail', [
            'berita' => $berita,
            'paragraf' => array_values(array_filter(preg_split('/\R{1,}/', trim($berita['isi'])))),
            'foto' => $this->store->fotoUrl($berita),
            'hariTanggal' => $tanggal ? $tanggal->translatedFormat('l, j F Y') : '-',
            'tanggalPendek' => $tanggal ? $tanggal->format('d/m/Y') : '-',
            'waktuBaca' => max(1, (int) ceil($kata / 200)).' menit',
            'terkait' => $this->store->related($berita),
        ]);
    }

    public function create()
    {
        return view('admin.berita-tambah', $this->opsi());
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validasi($request, true);

        // TODO-DB: simpan ke tabel berita (foto: simpan path-nya).
        $row = $this->store->create($data);

        return redirect()->route('admin.berita')->with('status', 'Berita "'.$row['judul'].'" disimpan sebagai '.$row['status'].'.');
    }

    public function edit(string $id)
    {
        $berita = $this->store->find($id);
        abort_unless($berita, 404);

        return view('admin.berita-edit', $this->opsi() + ['berita' => $berita]);
    }

    public function update(Request $request, string $id): RedirectResponse
    {
        abort_unless($this->store->find($id), 404);

        $data = $this->validasi($request, false);

        // TODO-DB: update tabel berita.
        $row = $this->store->update($id, $data);

        return redirect()->route('admin.berita')->with('status', 'Berita "'.$row['judul'].'" diperbarui ('.$row['status'].').');
    }

    private function opsi(): array
    {
        return ['kategori' => AdminBeritaStore::KEGIATAN, 'kegiatanList' => AdminBeritaStore::KEGIATAN_SEBELUMNYA];
    }

    /** Validasi + normalisasi input. $wajibFoto: foto wajib saat tambah, opsional saat edit. */
    private function validasi(Request $request, bool $wajibFoto): array
    {
        $ids = array_column(AdminBeritaStore::KEGIATAN_SEBELUMNYA, 'id');
        $prev = $request->input('sumber') === 'prev';

        $data = $request->validate([
            'sumber' => ['required', Rule::in(['prev', 'none'])],
            'kegiatan_id' => [$prev ? 'required' : 'nullable', Rule::in($ids)],
            'kategori' => [$prev ? 'nullable' : 'required', Rule::in(AdminBeritaStore::KEGIATAN)],
            'judul' => ['required', 'string', 'max:200'],
            'isi' => ['required', 'string', 'max:1000'],
            'foto' => [$wajibFoto ? 'required' : 'nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
            'status' => ['required', Rule::in(AdminBeritaStore::STATUS)],
        ], [
            'kegiatan_id.required' => 'Pilih kegiatan sebelumnya.',
            'kategori.required' => 'Pilih kategori kegiatan.',
            'judul.required' => 'Judul berita wajib diisi.',
            'isi.required' => 'Isi berita wajib diisi.',
            'isi.max' => 'Isi berita maksimal 1.000 karakter.',
            'foto.required' => 'Foto sampul wajib diunggah.',
            'foto.mimes' => 'Berkas harus PDF, JPG, atau PNG.',
            'foto.max' => 'Ukuran berkas maksimal 5 MB.',
        ]);

        // Bila dari kegiatan sebelumnya, kategori mengikuti kegiatan itu.
        if ($prev) {
            foreach (AdminBeritaStore::KEGIATAN_SEBELUMNYA as $k) {
                if ($k['id'] === $data['kegiatan_id']) {
                    $data['kategori'] = $k['kategori'];
                }
            }
        }

        // TODO-DB: simpan berkas ke storage final. Sementara: disk 'public' (butuh `php artisan storage:link`).
        $data['foto'] = $request->hasFile('foto') ? $request->file('foto')->store('berita', 'public') : null;

        return $data;
    }
}