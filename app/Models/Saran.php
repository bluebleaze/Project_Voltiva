<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Saran extends Model
{
    use HasFactory;

    protected $table = 'tb_saran';

    protected $fillable = [
        'pengguna_id',
        'nama',
        'email',
        'pesan',
    ];

    public function pengguna()
    {
        return $this->belongsTo(Pengguna::class, 'pengguna_id');
    }
}