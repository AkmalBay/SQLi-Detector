<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\Supplier;
use App\Models\Product;
use App\Models\InventoryLog;
use Carbon\Carbon;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        // 0. Get Admin Gudang User ID for Logs
        // Cari user dengan email admin@sembako.com (yang dibuat di DatabaseSeeder)
        $adminUser = \App\Models\User::where('email', 'admin@sembako.com')->first();
        // Fallback ke ID 1 jika tidak ditemukan (untuk safety)
        $adminId = $adminUser ? $adminUser->id : 1;

        // 1. Seed Categories
        // 1. Seed Categories (Updated to match CategorySeeder & User Request)
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

        foreach ($categories as $cat) {
            Category::firstOrCreate(['name' => $cat]);
        }

        // 2. Seed Suppliers
        $suppliers = [
            [
                'name' => 'PT. Sumber Rejeki',
                'contact_person' => 'Budi Santoso',
                'phone' => '081234567890',
                'address' => 'Jl. Raya Bogor KM 30'
            ],
            [
                'name' => 'Agen Makmur Jaya',
                'contact_person' => 'Siti Aminah',
                'phone' => '089876543210',
                'address' => 'Pasar Induk Kramat Jati'
            ]
        ];

        foreach ($suppliers as $sup) {
            Supplier::firstOrCreate(['name' => $sup['name']], $sup);
        }

        // 3. Seed Products & Inventory Logs (DISABLED FOR CLEAN SLATE)
        /*
        // Ambil ID Kategori dan Supplier untuk referensi
        $catSembako = Category::where('name', 'Sembako')->first()->id;
        // ... (Disabled code)
        */
    }
}
