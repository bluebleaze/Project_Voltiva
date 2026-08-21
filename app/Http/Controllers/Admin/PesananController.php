<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pesanan;
use Illuminate\Http\Request;

class PesananController extends Controller
{
    // Menampilkan seluruh daftar pesanan masuk dari semua pembeli
    public function index()
    {
        $pesanan = Pesanan::with('pengguna')
            ->latest()
            ->paginate(15);

        return view('admin.pesanan.index', compact('pesanan'));
    }

    // Menampilkan rincian detail pesanan pembeli
    public function show(Pesanan $pesanan)
    {
        $pesanan->load(['pengguna', 'detailPesanan.produk']);

        return view('admin.pesanan.show', compact('pesanan'));
    }

    // Perbarui status pesanan (misal: pending, diproses, dikirim, selesai, dibatalkan)
    public function updateStatus(Request $request, Pesanan $pesanan)
    {
        $request->validate([
            'status' => 'required|in:pending,diproses,dikirim,selesai,dibatalkan',
        ]);

        if (in_array($pesanan->status, ['selesai', 'dibatalkan'])) {
            return redirect()->back()->with('error', 'Pesanan yang sudah selesai atau dibatalkan tidak dapat diubah statusnya.');
        }

        if ($request->status === 'dibatalkan') {
            $pesanan->load('detailPesanan.produk');
            
            foreach ($pesanan->detailPesanan as $detail) {
                if ($detail->produk) {
                    $detail->produk->increment('stok', $detail->jumlah);
                }
            }
        }

        $pesanan->update([
            'status' => $request->status,
        ]);

        return redirect()->back()->with('success', 'Status pesanan berhasil diperbarui.');
    }
}