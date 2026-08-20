<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Saran;

class SaranAdminController extends Controller
{
    // Menampilkan daftar seluruh saran masuk dari pengguna
    public function index()
    {
        $saran = Saran::with('pengguna')
            ->latest()
            ->paginate(15);

        return view('admin.saran.index', compact('saran'));
    }

    // Menghapus data saran
    public function destroy($id)
    {
        $saran = Saran::findOrFail($id);
        $saran->delete();

        return redirect()->back()->with('success', 'Pesan saran berhasil dihapus.');
    }
}