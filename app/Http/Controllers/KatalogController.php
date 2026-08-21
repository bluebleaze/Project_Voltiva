<?php

namespace App\Http\Controllers;

use App\Models\Kategori;
use App\Models\Produk;
use Illuminate\Http\Request;

class KatalogController extends Controller
{
    // Menampilkan daftar seluruh produk dengan pencarian dan filter
    public function index(Request $request)
    {
        $kategori = Kategori::all();

        // Query dasar: hanya mengambil produk yang aktif
        $query = Produk::with(['kategori', 'brand']);

        // Filter berdasarkan pencarian nama atau brand
        if ($request->filled('q')) {
            $keyword = trim($request->q);
            $query->where(function ($q) use ($keyword) {
                $q->where('nama_produk', 'like', "%{$keyword}%")
                ->orWhereHas('brand', function ($b) use ($keyword) {
                    $b->where('nama_brand', 'like', "%{$keyword}%");
                });
            });
        }
        // Filter berdasarkan kategori tertentu
        if ($request->filled('kategori')) {
            $query->where('kategori_id', $request->kategori);
        }

        // Ambil data dengan paginasi (12 produk per halaman)
        $produk = $query->latest()->paginate(12)->withQueryString();

        return view('katalog.index', compact('produk', 'kategori'));
    }

    // Menampilkan halaman detail dari satu produk
    public function show(Produk $produk)
    {
        $produk->load(['kategori', 'brand']);

        // Rekomendasi produk terkait (kategori sama, kecuali produk yang sedang dilihat)
        $produkTerkait = Produk::where('kategori_id', $produk->kategori_id)
            ->where('id', '!=', $produk->id)
            ->latest()
            ->take(4)
            ->get();

        return view('katalog.show', compact('produk', 'produkTerkait'));
    }
}