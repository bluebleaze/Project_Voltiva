<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pesanan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
        ], [
            'status.required' => 'Status pesanan wajib dipilih.',
            'status.in'       => 'Pilihan status tidak valid.',
        ]);

        // Cegah perubahan status jika transaksi sudah final
        if (in_array($pesanan->status, ['selesai', 'dibatalkan'])) {
            return redirect()->back()->with('error', 'Pesanan yang sudah selesai atau dibatalkan tidak dapat diubah lagi.');
        }

        try {
            DB::transaction(function () use ($request, $pesanan) {
                // Jika status diubah menjadi 'dibatalkan', kembalikan stok produk
                if ($request->status === 'dibatalkan') {
                    $pesanan->load('detailPesanan.produk');

                    foreach ($pesanan->detailPesanan as $detail) {
                        if ($detail->produk) {
                            $detail->produk->increment('stok', $detail->jumlah);
                        }
                    }
                }

                // Perbarui status pesanan
                $pesanan->update([
                    'status' => $request->status,
                ]);
            });

            return redirect()->back()->with('success', 'Status pesanan berhasil diperbarui.');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal memperbarui status: ' . $e->getMessage());
        }
    }
}