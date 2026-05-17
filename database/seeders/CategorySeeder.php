<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Daftar Kategori Standar Toko Sembako
        $categories = [
            'Makanan Pokok',      // Beras, Minyak, Gula, Telur
            'Makanan Ringan',     // Chiki, Biskuit (Pengganti 'Snack')
            'Minuman',            // Air Mineral, Kopi Botolan
            'Bumbu Dapur',        // Kecap, Garam, Penyedap Rasa
            'Perlengkapan Mandi', // Sabun, Shampo, Pasta Gigi
            'Perlengkapan Cuci',  // Deterjen, Pewangi Pakaian
            'Rokok',              // Tambahan baru sesuai request
            'Lain-lain'           // Kategori darurat
        ];

        foreach ($categories as $categoryName) {
            // firstOrCreate: Cek dulu di database.
            // Jika nama kategori sudah ada, dia diam saja (tidak duplikat).
            // Jika belum ada, baru dia buatkan.
            Category::firstOrCreate(['name' => $categoryName]);
        }
    }
}