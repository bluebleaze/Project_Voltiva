<?php

namespace App\Http\Controllers;

use App\Models\Pengguna;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    // Halaman Form Login
    public function showLoginForm()
    {
        return view('auth.login');
    }

    // Proses Login (Tanpa Hash)
    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ], [
            'email.required'    => 'Email wajib diisi.',
            'email.email'       => 'Format email tidak valid.',
            'password.required' => 'Password wajib diisi.',
        ]);

        // Cari pengguna berdasarkan email dan password teks biasa
        $user = Pengguna::where('email', $request->email)
                        ->where('password', $request->password)
                        ->first();

        if ($user) {
            Auth::login($user);
            $request->session()->regenerate();

            if ($user->isAdmin()) {
                return redirect()->intended('/admin/dashboard');
            }

            return redirect()->intended('/');
        }

        return back()->withErrors([
            'email' => 'Email atau password yang Anda masukkan salah.',
        ])->onlyInput('email');
    }

    // Halaman Form Register
    public function showRegisterForm()
    {
        return view('auth.register');
    }

    // Proses Register (Tanpa Hash)
    public function register(Request $request)
    {
        $request->validate([
            'nama'     => 'required|string|max:255',
            'email'    => 'required|string|email|max:255|unique:tb_pengguna,email',
            'password' => 'required|string|min:8|confirmed',
        ], [
            'nama.required'     => 'Nama lengkap wajib diisi.',
            'email.required'    => 'Email wajib diisi.',
            'email.unique'      => 'Email sudah terdaftar.',
            'password.required' => 'Password wajib diisi.',
            'password.min'      => 'Password minimal 8 karakter.',
            'password.confirmed'=> 'Konfirmasi password tidak cocok.',
        ]);

        $user = Pengguna::create([
            'nama'     => $request->nama,
            'email'    => $request->email,
            'password' => $request->password, // Teks biasa
            'role'     => 'user',
        ]);

        Auth::login($user);

        return redirect('/')->with('success', 'Pendaftaran berhasil!');
    }

    // Proses Logout
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Anda telah berhasil keluar.');
    }
}