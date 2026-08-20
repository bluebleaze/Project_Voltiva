<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Saran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SaranController extends Controller
{
    // Menampilkan halaman form masukan/saran
    public function create()
    {
        return view('user.saran.create');
    }

    // Menyimpan masukan dari pembeli ke database
    public function store(Request $request)
    {
        $request->validate([
            'subjek' => 'required|string|max:255',
            'pesan'  => 'required|string|min:10',
        ]);

        Saran::create([
            'pengguna_id' => Auth::id(), // Mengambil ID user yang sedang login
            'subjek'      => $request->subjek,
            'pesan'       => $request->pesan,
        ]);

        return redirect()->back()->with('success', 'Terima kasih! Masukan Anda berhasil dikirim.');
    }
}