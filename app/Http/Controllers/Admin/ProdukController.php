<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kategori;
use App\Models\Produk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProdukController extends Controller
{
    // Tampil seluruh daftar produk admin
    public function index()
    {
        $produk = Produk::with(['kategori', 'brand'])->latest()->paginate(10);
        return view('admin.produk.index', compact('produk'));
    }

    // Form tambah produk
    public function create()
    {
        $kategori = Kategori::all();
        $brand = \App\Models\Brand::all();
        return view('admin.produk.create', compact('kategori', 'brand'));
    }

    // Simpan produk baru ke database
    public function store(Request $request)
    {
        $request->validate([
            'kategori_id' => 'required|exists:tb_kategori,id',
            'brand_id'    => 'nullable|exists:tb_brand,id',
            'nama_produk' => 'required|string|max:255',
            'brand'       => 'nullable|string|max:100',
            'deskripsi'   => 'required|string',
            'harga'       => 'required|numeric|min:0',
            'stok'        => 'required|integer|min:0',
            'gambar'      => 'required|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        // Upload gambar ke folder storage/app/public/produk
        $pathGambar = $request->file('gambar')->store('produk', 'public');

        Produk::create([
            'kategori_id' => $request->kategori_id,
            'brand_id'    => $request->brand_id,
            'nama_produk' => $request->nama_produk,
            'deskripsi'   => $request->deskripsi,
            'harga'       => $request->harga,
            'stok'        => $request->stok,
            'gambar'      => $pathGambar,
        ]);

        return redirect()->route('admin.produk.index')->with('success', 'Produk berhasil ditambahkan.');
    }

    // Form edit produk
    public function edit(string|int $id)
    {
        $produk = Produk::findOrFail($id);
        $kategori = Kategori::all();
        $brand = \App\Models\Brand::all();
        return view('admin.produk.edit', compact('produk', 'kategori', 'brand'));
    }

    // Perbarui data produk
    public function update(Request $request, string|int $id)
    {
        $produk = Produk::findOrFail($id);

        $request->validate([
            'kategori_id' => 'required|exists:tb_kategori,id',
            'brand_id'    => 'nullable|exists:tb_brand,id',
            'nama'        => 'required|string|max:255',
            'brand'       => 'nullable|string|max:100',
            'deskripsi'   => 'required|string',
            'harga'       => 'required|numeric|min:0',
            'stok'        => 'required|integer|min:0',
            'gambar'      => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $data = [
            'kategori_id' => $request->kategori_id,
            'brand_id'    => $request->brand_id,
            'nama'        => $request->nama,
            'deskripsi'   => $request->deskripsi,
            'harga'       => $request->harga,
            'stok'        => $request->stok,
        ];

        // Jika ada gambar baru yang diunggah
        if ($request->hasFile('gambar')) {
            // Hapus gambar lama jika ada
            if ($produk->gambar && Storage::disk('public')->exists($produk->gambar)) {
                Storage::disk('public')->delete($produk->gambar);
            }
            $data['gambar'] = $request->file('gambar')->store('produk', 'public');
        }

        $produk->update($data);

        return redirect()->route('admin.produk.index')->with('success', 'Produk berhasil diperbarui.');
    }

    // Hapus produk
    public function destroy(string|int $id)
    {
        $produk = Produk::findOrFail($id);

        // Hapus file gambar dari storage
        if ($produk->gambar && Storage::disk('public')->exists($produk->gambar)) {
            Storage::disk('public')->delete($produk->gambar);
        }

        $produk->delete();

        return redirect()->route('admin.produk.index')->with('success', 'Produk berhasil dihapus.');
    }
}