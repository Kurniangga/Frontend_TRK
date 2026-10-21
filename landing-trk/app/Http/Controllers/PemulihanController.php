<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Validation\ValidationException;

/**
 * Pemulihan akun 3 tahap: email -> OTP -> sandi baru -> berhasil.
 *
 * SIMULASI (belum database): tahap disimpan di session. Setiap bagian yang
 * perlu database/email ditandai "TODO-DB" / "TODO-MAIL" — ganti nanti.
 */
class PemulihanController extends Controller
{
    private const OTP_MENIT = 5;

    public function show(Request $request)
    {
        if ($request->boolean('mulai')) {
            session()->forget('pemulihan');
        }

        $step = (int) session('pemulihan.step', 1);
        $data = [
            'step' => $step,
            'email' => session('pemulihan.email'),
            // Hanya saat APP_DEBUG=true: tampilkan OTP agar bisa dicoba tanpa email sungguhan.
            'otpSimulasi' => ($step === 2 && config('app.debug')) ? session('pemulihan.otp') : null,
        ];

        $view = view('pemulihan', $data);

        if ($step === 4) {            // selesai -> bersihkan, refresh kembali ke tahap 1
            session()->forget('pemulihan');
        }

        return $view;
    }

    /** Tahap 1: terima email, buat & "kirim" OTP. */
    public function kirimOtp(Request $request): RedirectResponse
    {
        $data = $request->validate(['email' => ['required', 'email']], [
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
        ]);

        // TODO-DB: pastikan email terdaftar (mis. User::where('email', ...)->exists()).
        //          Untuk keamanan, jangan beri tahu apakah email terdaftar atau tidak.
        $this->buatOtp($data['email']);

        return redirect()->route('password.request');
    }

    public function kirimUlang(): RedirectResponse
    {
        if ($email = session('pemulihan.email')) {
            $this->buatOtp($email);
        }

        return redirect()->route('password.request');
    }

    public function ubahEmail(): RedirectResponse
    {
        session(['pemulihan.step' => 1]);

        return redirect()->route('password.request');
    }

    /** Tahap 2: cek OTP. */
    public function verifikasi(Request $request): RedirectResponse
    {
        abort_unless(session('pemulihan.step') === 2, 404);

        $request->validate(['otp' => ['required', 'digits:6']], [
            'otp.required' => 'Masukkan 6 digit kode OTP.',
            'otp.digits' => 'Kode OTP harus 6 digit angka.',
        ]);

        if (now()->timestamp > session('pemulihan.expires')) {
            throw ValidationException::withMessages(['otp' => 'Kode OTP sudah kedaluwarsa. Kirim ulang OTP.']);
        }
        if (! hash_equals((string) session('pemulihan.otp'), (string) $request->input('otp'))) {
            throw ValidationException::withMessages(['otp' => 'Kode OTP salah. Coba lagi.']);
        }

        session(['pemulihan.step' => 3]);

        return redirect()->route('password.request');
    }

    /** Tahap 3: simpan sandi baru. */
    public function simpan(Request $request): RedirectResponse
    {
        abort_unless(session('pemulihan.step') === 3, 404);

        $request->validate([
            'password' => ['required', 'min:8', 'confirmed'],
        ], [
            'password.required' => 'Kata sandi baru wajib diisi.',
            'password.min' => 'Kata sandi minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi kata sandi tidak sama.',
        ]);

        // TODO-DB: simpan sandi baru, mis.
        //   User::where('email', session('pemulihan.email'))
        //       ->update(['password' => Hash::make($request->password)]);

        session(['pemulihan.step' => 4]);

        return redirect()->route('password.request');
    }

    private function buatOtp(string $email): void
    {
        $otp = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        // TODO-MAIL: kirim $otp ke $email, mis. Mail::to($email)->send(new KodeOtpMail($otp));
        session(['pemulihan' => [
            'step' => 2,
            'email' => $email,
            'otp' => $otp,
            'expires' => now()->addMinutes(self::OTP_MENIT)->timestamp,
        ]]);
    }
}
