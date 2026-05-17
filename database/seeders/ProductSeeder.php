<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\Category;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Pastikan supplier dan kategori sudah ada
        $supplierIndofood = Supplier::where('name', 'PT Indofood Sukses Makmur')->first();
        $supplierWings = Supplier::where('name', 'PT Wings Surya')->first();
        $categoryPokok = Category::where('name', 'Makanan Pokok')->first();
        $categoryMinuman = Category::where('name', 'Minuman')->first();
        $categorySnack = Category::where('name', 'Makanan Ringan')->first();

        Product::firstOrCreate(['sku' => 'IND-001'], [
            'name' => 'Indomie Goreng',
            'category_id' => $categoryPokok->id,
            'supplier_id' => $supplierIndofood->id,
            'stock' => 150,
            'price' => 3000,
        ]);
        
        Product::firstOrCreate(['sku' => 'WNG-002'], [
            'name' => 'Mie Sedap Kuah',
            'category_id' => $categoryPokok->id,
            'supplier_id' => $supplierWings->id,
            'stock' => 120,
            'price' => 2800,
        ]);
        
        Product::firstOrCreate(['sku' => 'WNG-003'], [
            'name' => 'Ale-Ale',
            'category_id' => $categoryMinuman->id,
            'supplier_id' => $supplierWings->id,
            'stock' => 200,
            'price' => 2500,
        ]);
        
        Product::firstOrCreate(['sku' => 'IND-004'], [
            'name' => 'Chitato',
            'category_id' => $categorySnack->id,
            'supplier_id' => $supplierIndofood->id,
            'stock' => 100,
            'price' => 10000,
        ]);
    }
}