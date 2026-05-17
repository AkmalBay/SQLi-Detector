<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Supplier;

class SupplierSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Supplier::firstOrCreate(['name' => 'PT Indofood Sukses Makmur'], [
            'contact_person' => 'Budi Susanto',
            'phone' => '081212345678',
            'address' => 'Jakarta, Indonesia'
        ]);
        
        Supplier::firstOrCreate(['name' => 'PT Wings Surya'], [
            'contact_person' => 'Siti Aisyah',
            'phone' => '085798765432',
            'address' => 'Surabaya, Indonesia'
        ]);
    }
}