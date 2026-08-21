<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class BrandController extends Controller
{
    // 1. Tampilkan Daftar Brand
    public function index()
    {
        $brands = Brand::latest()->paginate(10);
        return view('admin.brand.index', compact('brands'));
    }

    // 2. Tampilkan Form Tambah Brand
    public function create()
    {
        return view('admin.brand.create');
    }

    // 3. Simpan Brand Baru
    public function store(Request $request)
    {
        $request->validate([
            'nama_brand' => 'required|string|max:255|unique:tb_brand,nama_brand',
        ]);

        Brand::create([
            'nama_brand' => $request->nama_brand,
            'slug'       => Str::slug($request->nama_brand),
        ]);

        return redirect()->route('admin.brand.index')
            ->with('success', 'Brand berhasil ditambahkan!');
    }

    // 4. Tampilkan Form Edit Brand
    public function edit(Brand $brand)
    {
        return view('admin.brand.edit', compact('brand'));
    }

    // 5. Perbarui Data Brand
    public function update(Request $request, Brand $brand)
    {
        $request->validate([
            'nama_brand' => 'required|string|max:255|unique:tb_brand,nama_brand,' . $brand->id,
        ]);

        $brand->update([
            'nama_brand' => $request->nama_brand,
            'slug'       => Str::slug($request->nama_brand),
        ]);

        return redirect()->route('admin.brand.index')
            ->with('success', 'Brand berhasil diperbarui!');
    }

    // 6. Hapus Brand
    public function destroy(Brand $brand)
    {
        if ($brand->produk()->count() > 0) {
            return redirect()->route('admin.brand.index')
                ->with('error', 'Brand tidak dapat dihapus karena masih memiliki produk terkait!');
        }
        $brand->delete();

        return redirect()->route('admin.brand.index')
            ->with('success', 'Brand berhasil dihapus!');
    }
}