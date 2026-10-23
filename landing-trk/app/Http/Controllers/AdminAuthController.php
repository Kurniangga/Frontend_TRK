<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

/**
 * Login SIMULASI untuk admin dan kaprodi (satu form /login).
 * Nanti ganti dengan Auth::attempt() + tabel users + kolom role.
 */
class AdminAuthController extends Controller
{
    /** role => [config akun, key session, route tujuan] */
    private const ROLE = [
        'admin'   => ['admin.akun',   'admin',   'admin.dashboard'],
        'kaprodi' => ['kaprodi.akun', 'kaprodi', 'kaprodi.dashboard'],
    ];

    public function login(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ], [
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'password.required' => 'Sandi wajib diisi.',
        ]);

        foreach (self::ROLE as [$cfg, $key, $tujuan]) {
            $akun = config($cfg);

            if (strcasecmp($data['email'], $akun['email']) !== 0) {
                continue;
            }

            if (! hash_equals($akun['password'], $data['password'])) {
                return back()->withInput($request->only('email'))->withErrors(['password' => 'Email atau sandi salah.']);
            }

            $request->session()->regenerate();
            $request->session()->forget(array_column(self::ROLE, 1));   // satu sesi = satu peran
            $request->session()->put($key, ['email' => $akun['email'], 'nama' => $akun['nama']]);

            return redirect()->route($tujuan);
        }

        // Akun selain admin/kaprodi belum tersedia (login dosen/mahasiswa menyusul).
        return back()->withInput($request->only('email'))
            ->withErrors(['email' => 'Akun belum terdaftar. Login dosen dan mahasiswa belum tersedia.']);
    }

    /** Dipakai admin.logout dan kaprodi.logout. */
    public function logout(Request $request): RedirectResponse
    {
        $request->session()->forget(array_column(self::ROLE, 1));
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }
}