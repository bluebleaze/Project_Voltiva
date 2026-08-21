<?php

namespace Database\Seeders;

use App\Models\Pengguna;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Akun Admin Utama
        Pengguna::create([
            'nama_lengkap'     => 'Administrator',
            'email'            => 'admin@contoh',
            'sandi'            => 'sandi123',
            'peran'            => 'admin',
        ]);

        // 2. Akun User / Pembeli Contoh
        Pengguna::create([
            'nama_lengkap'     => 'Pembeli Sampel',
            'email'            => 'pengguna@contoh',
            'sandi'            => 'sandi123',
            'peran'            => 'pengguna',
        ]);
    }
}