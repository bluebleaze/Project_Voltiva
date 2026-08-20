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
    public function show(string|int $id)
    {
        $pesanan = Pesanan::with(['pengguna', 'detailPesanan'])->findOrFail($id);

        return view('admin.pesanan.show', compact('pesanan'));
    }

    // Perbarui status pesanan (misal: pending, diproses, dikirim, selesai, dibatalkan)
    public function updateStatus(Request $request, string|int $id)
    {
        $request->validate([
            'status' => 'required|in:pending,diproses,dikirim,selesai,dibatalkan',
        ]);

        $pesanan = Pesanan::with('detailPesanan.produk')->findOrFail($id);
        if ($request->status === 'dibatalkan' && $pesanan->status !== 'dibatalkan') {
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