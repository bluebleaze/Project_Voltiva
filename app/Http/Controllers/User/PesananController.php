<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Pesanan;
use Illuminate\Support\Facades\Auth;

class PesananController extends Controller
{
    // Menampilkan seluruh riwayat transaksi pembeli yang sedang login
    public function index()
    {
        $pesanan = Pesanan::where('pengguna_id', Auth::id())
            ->latest()
            ->paginate(10);

        return view('user.pesanan.index', compact('pesanan'));
    }

    // Menampilkan detail invoice/rincian item dari satu pesanan
    public function show(string|int $id)
    {
        $pesanan = Pesanan::with(['detailPesanan.produk'])
            ->where('pengguna_id', Auth::id())
            ->findOrFail($id);

        return view('user.pesanan.show', compact('pesanan'));
    }
}