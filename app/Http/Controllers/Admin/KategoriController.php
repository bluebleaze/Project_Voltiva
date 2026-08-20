<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kategori;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class KategoriController extends Controller
{
    // Menampilkan daftar seluruh kategori produk
    public function index()
    {
        $kategori = Kategori::withCount('produk')->latest()->paginate(10);
        
        return view('admin.kategori.index', compact('kategori'));
    }

    // Form tambah kategori baru
    public function create()
    {
        return view('admin.kategori.create');
    }

    // Menyimpan kategori baru ke database
    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:100|unique:tb_kategori,nama',
        ], [
            'nama.required' => 'Nama kategori wajib diisi.',
            'nama.unique'   => 'Nama kategori sudah ada.',
        ]);

        Kategori::create([
            'nama' => $request->nama,
            'slug' => Str::slug($request->nama),
        ]);

        return redirect()->route('admin.kategori.index')
            ->with('success', 'Kategori berhasil ditambahkan.');
    }

    // Form edit kategori
    public function edit(string|int $id)
    {
        $kategori = Kategori::findOrFail($id);

        return view('admin.kategori.edit', compact('kategori'));
    }

    // Memperbarui data kategori
    public function update(Request $request, string|int $id)
    {
        $kategori = Kategori::findOrFail($id);

        $request->validate([
            'nama' => 'required|string|max:100|unique:tb_kategori,nama,' . $id,
        ], [
            'nama.required' => 'Nama kategori wajib diisi.',
            'nama.unique'   => 'Nama kategori sudah digunakan.',
        ]);

        $kategori->update([
            'nama' => $request->nama,
            'slug' => Str::slug($request->nama),
        ]);

        return redirect()->route('admin.kategori.index')
            ->with('success', 'Kategori berhasil diperbarui.');
    }

    // Menghapus kategori
    public function destroy(string|int $id)
    {
        $kategori = Kategori::withCount('produk')->findOrFail($id);

        // Mencegah penghapusan jika masih ada produk yang terkait
        if ($kategori->produk_count > 0) {
            return redirect()->back()
                ->with('error', 'Kategori tidak dapat dihapus karena masih digunakan oleh beberapa produk.');
        }

        $kategori->delete();

        return redirect()->route('admin.kategori.index')
            ->with('success', 'Kategori berhasil dihapus.');
    }
}