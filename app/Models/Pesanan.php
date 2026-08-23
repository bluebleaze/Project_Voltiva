<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pesanan extends Model
{
    use HasFactory;

    protected $table = 'tb_pesanan';

    protected $fillable = [
        'pengguna_id',
        'total_harga',
        'alamat_pengiriman',
        'status',
    ];

    protected $casts = [
        'total_harga' => 'integer',
    ];
}