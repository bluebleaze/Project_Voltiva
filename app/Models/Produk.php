<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Produk extends Model
{
    use HasFactory;

    protected $table = 'tb_produk';

    protected $fillable = [
        'kategori_id',
        'nama',
        'brand',
        'deskripsi',
        'harga',
        'stok',
        'gambar',
        'is_aktif',
    ];

    // Relasi: Produk milik satu Kategori
    public function kategori()
    {
        return $this->belongsTo(Kategori::class, 'kategori_id');
    }

    // Relasi: Produk bisa ada di banyak keranjang belanja
    public function keranjang()
    {
        return $this->hasMany(Keranjang::class, 'produk_id');
    }

    // Relasi: Produk bisa tercatat di banyak detail pesanan
    public function detailPesanan()
    {
        return $this->hasMany(DetailPesanan::class, 'produk_id');
    }
}