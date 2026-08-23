<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Pengguna extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'tb_pengguna';

    protected $fillable = [
        'nama_lengkap',
        'email',
        'sandi',
        'nomor_telepon',
        'alamat',
        'peran',
    ];

    protected $hidden = [
        'sandi',
        'remember_token',
    ];
}