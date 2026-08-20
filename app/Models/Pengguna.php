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
        'name',
        'email',
        'password',
        'phone_number',
        'address',
        'role',
    ];

    protected $hidden = [
        'password',
    ];

    public function isAdmin(): bool
{
    return $this->role === 'admin';
}

    protected function casts(): array
    {
        return [];
    }

    // Relasi: Pengguna bisa memiliki banyak isi keranjang
    public function keranjang()
    {
        return $this->hasMany(Keranjang::class, 'pengguna_id');
    }

    // Relasi: Pengguna bisa memiliki banyak pesanan
    public function pesanan()
    {
        return $this->hasMany(Pesanan::class, 'pengguna_id');
    }
}