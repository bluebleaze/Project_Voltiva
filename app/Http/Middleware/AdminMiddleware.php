<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        // 1. Tambahkan PHPDoc type hint agar VS Code tahu $user adalah instance dari model Pengguna
        /** @var \App\Models\Pengguna $user */
        $user = Auth::user();

        // 2. Lakukan pengecekan menggunakan variabel $user
        if (Auth::check() && $user->isAdmin()) {
            return $next($request);
        }

        return redirect()->route('home')->with('error', 'Anda tidak memiliki hak akses ke halaman Admin.');
    }
}