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
            'nama'     => 'Administrator',
            'email'    => 'admin@example.com',
            'sandi'    => 'password123',
            'peran'    => 'admin',
        ]);

        // 2. Akun User / Pembeli Contoh
        Pengguna::create([
            'nama'     => 'Pembeli Sampel',
            'email'    => 'user@example.com',
            'sandi'    => 'password123',
            'peran'    => 'pengguna',
        ]);
    }
}