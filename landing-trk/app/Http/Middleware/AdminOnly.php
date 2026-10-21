<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/** Menjaga semua halaman /admin/*: hanya yang sudah login sebagai admin (simulasi session). */
class AdminOnly
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->session()->has('admin')) {
            return redirect()->route('login')
                ->withErrors(['email' => 'Silakan masuk sebagai admin terlebih dahulu.']);
        }

        return $next($request);
    }
}
