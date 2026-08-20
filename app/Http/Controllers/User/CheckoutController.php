<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\DetailPesanan;
use App\Models\Keranjang;
use App\Models\Pesanan;
use App\Models\Produk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CheckoutController extends Controller
{
    // Menampilkan halaman checkout (ringkasan belanja dan form alamat)
    public function index()
    {
        $keranjang = Keranjang::with('produk')
            ->where('pengguna_id', Auth::id())
            ->get();

        // Jika keranjang kosong, kembalikan ke halaman keranjang
        if ($keranjang->isEmpty()) {
            return redirect()->route('keranjang.index')->with('error', 'Keranjang belanja Anda masih kosong.');
        }

        // Hitung total harga belanjaan
        $totalHarga = $keranjang->sum(function ($item) {
            return $item->produk->harga * $item->jumlah;
        });

        $pengguna = Auth::user();

        return view('user.checkout.index', compact('keranjang', 'totalHarga', 'pengguna'));
    }

    // Memproses transaksi checkout
    public function store(Request $request)
    {
        $request->validate([
            'alamat_pengiriman' => 'required|string|max:500',
        ]);

        $penggunaId = Auth::id();

        // Ambil semua item keranjang milik user
        $keranjang = Keranjang::with('produk')
            ->where('pengguna_id', $penggunaId)
            ->get();

        if ($keranjang->isEmpty()) {
            return redirect()->route('keranjang.index')->with('error', 'Keranjang belanja Anda kosong.');
        }

        // Gunakan DB Transaction untuk keamanan integritas data
        try {
            DB::transaction(function () use ($request, $penggunaId, $keranjang) {

                // 1. Validasi stok akhir & Hitung total harga
                $totalHarga = 0;
                foreach ($keranjang as $item) {
                    // Kunci data produk untuk menghindari race condition
                    $produk = Produk::lockForUpdate()->findOrFail($item->produk_id);

                    if ($produk->stok < $item->jumlah) {
                        throw new \Exception("Stok untuk produk '{$produk->nama_produk}' tidak mencukupi.");
                    }

                    $totalHarga += $produk->harga * $item->jumlah;
                }

                // 2. Buat data Induk Pesanan di tb_pesanan
                $pesanan = Pesanan::create([
                    'pengguna_id'       => $penggunaId,
                    'total_harga'       => $totalHarga,
                    'alamat_pengiriman' => $request->alamat_pengiriman,
                    'status'            => 'pending', // Status awal pesanan
                ]);

                // 3. Pindahkan item keranjang ke tb_detail_pesanan & Potong stok produk
                foreach ($keranjang as $item) {
                    $produk = $item->produk;
                    $subtotal = $produk->harga * $item->jumlah;

                    // Buat detail pesanan
                    DetailPesanan::create([
                        'pesanan_id'   => $pesanan->id,
                        'produk_id'    => $produk->id,
                        'nama_produk'  => $produk->nama_produk, // Disimpan statis sebagai snapshot riwayat
                        'harga_satuan' => $produk->harga,
                        'jumlah'       => $item->jumlah,
                        'subtotal'     => $subtotal,
                    ]);

                    // Kurangi stok produk di katalog
                    $produk->decrement('stok', $item->jumlah);
                }

                // 4. Bersihkan keranjang belanja pengguna
                Keranjang::where('pengguna_id', $penggunaId)->delete();
            });

            return redirect()->route('pesanan.index')->with('success', 'Pesanan berhasil dibuat! Silakan lakukan pembayaran.');

        } catch (\Exception $e) {
            // Jika terjadi kegagalan, transaksi otomatis di-rollback
            return redirect()->back()->with('error', 'Gagal memproses pesanan: ' . $e->getMessage());
        }
    }
}