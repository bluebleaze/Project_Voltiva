<?php

namespace App\Http\Controllers;

use App\Models\Kategori;
use App\Models\Produk;

class HomeController extends Controller
{
    public function index()
    {
        // Ambil kategori untuk navigasi/shortcut di landing page
        $kategori = Kategori::all();

        // Ambil 8 produk terbaru yang berstatus aktif
        $produkTerbaru = Produk::where('is_aktif', true)
            ->latest()
            ->take(8)
            ->get();

        return view('home', compact('kategori', 'produkTerbaru'));
    }
}