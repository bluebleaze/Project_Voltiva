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
        'brand_id',
        'nama_produk',
        'deskripsi_produk',
        'harga',
        'stok',
        'gambar',
    ];

    protected $casts = [
        'harga' => 'integer',
        'stok'  => 'integer',
    ];
}