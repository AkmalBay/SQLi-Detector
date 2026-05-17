<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Buat Akun KASIR (Sesuai Request)
        $kasir = User::firstOrCreate([
            'email' => 'kasir@sembako.com'
        ], [
            'name' => 'Kasir Toko',
            'password' => Hash::make('password'), // Password: "password"
        ]);

        // Berikan Role 'kasir' (Pastikan RoleSeeder sudah dijalankan sebelumnya)
        $kasir->assignRole('kasir');


        // --- OPSIONAL: JIKA ANDA BELUM PUNYA AKUN LAIN, SAYA BUATKAN SEKALIAN ---
        
        // 2. Buat Akun PEMILIK (Owner)
        $pemilik = User::firstOrCreate([
            'email' => 'pemilik@sembako.com'
        ], [
            'name' => 'Pak Bos',
            'password' => Hash::make('password'),
        ]);
        $pemilik->assignRole('pemilik');

        // 3. Buat Akun ADMIN GUDANG
        $gudang = User::firstOrCreate([
            'email' => 'gudang@sembako.com'
        ], [
            'name' => 'Staf Gudang',
            'password' => Hash::make('password'),
        ]);
        $gudang->assignRole('admin_gudang');
    }
}