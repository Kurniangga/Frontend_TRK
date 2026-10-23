<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/** Menjaga semua halaman /kaprodi/*: hanya yang sudah login sebagai kaprodi (simulasi session). */
class KaprodiOnly
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->session()->has('kaprodi')) {
            return redirect()->route('login')
                ->withErrors(['email' => 'Silakan masuk sebagai kaprodi terlebih dahulu.']);
        }

        return $next($request);
    }
}
