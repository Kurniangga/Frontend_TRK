<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

/** Login admin SIMULASI. Nanti ganti dengan Auth::attempt() + tabel users + role. */
class AdminAuthController extends Controller
{
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

        $akun = config('admin.akun');

        if (strcasecmp($data['email'], $akun['email']) === 0) {
            if (! hash_equals($akun['password'], $data['password'])) {
                return back()->withInput($request->only('email'))->withErrors(['password' => 'Email atau sandi salah.']);
            }

            $request->session()->regenerate();
            $request->session()->put('admin', ['email' => $akun['email'], 'nama' => $akun['nama']]);

            return redirect()->route('admin.dashboard');
        }

        // Akun selain admin belum tersedia (login dosen/mahasiswa menyusul).
        return back()->withInput($request->only('email'))
            ->withErrors(['email' => 'Akun belum terdaftar. Login dosen dan mahasiswa belum tersedia.']);
    }

    public function logout(Request $request): RedirectResponse
    {
        $request->session()->forget('admin');
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }
}
