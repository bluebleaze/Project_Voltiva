<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailPesanan extends Model
{
    use HasFactory;

    protected $table = 'tb_detail_pesanan';

    protected $fillable = [
        'pesanan_id',
        'produk_id',
        'nama_produk',
        'harga_satuan',
        'jumlah',
        'subtotal',
    ];

    // Mengonversi tipe data secara otomatis
    protected $casts = [
        'harga_satuan' => 'integer',
        'jumlah'       => 'integer',
        'subtotal'     => 'integer',
    ];
    

}