<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Supplier;
use App\Models\Category;
use App\Models\Product;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create Roles
        $pemilikRole = Role::firstOrCreate(['name' => 'pemilik']);
        $adminGudangRole = Role::firstOrCreate(['name' => 'admin_gudang']);

        // Create Permissions
        $permissions = [
            'manage dashboard',
            'manage products',
            'manage suppliers',
            'manage sales',
            'view reports',
            'access pos', // <--- Permission baru untuk POS
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Assign permissions to roles
        $pemilikRole->syncPermissions(Permission::all()); // syncPermissions lebih aman daripada givePermissionTo berulang
        $adminGudangRole->syncPermissions(['manage dashboard', 'manage products', 'manage suppliers', 'manage sales']);
        
        // Setup Role Kasir
        $kasirRole = Role::firstOrCreate(['name' => 'kasir']);
        $kasirRole->syncPermissions(['access pos', 'manage products', 'manage sales']); 

        // Create Users
        $pemilik = User::firstOrCreate(
            ['email' => 'pemilik@sembako.com'],
            [
                'name' => 'Pemilik Toko',
                'password' => Hash::make('password')
            ]
        );
        $pemilik->assignRole('pemilik');

        $adminGudang = User::firstOrCreate(
            ['email' => 'admin@sembako.com'],
            [
                'name' => 'Admin Gudang',
                'password' => Hash::make('password')
            ]
        );
        $adminGudang->assignRole('admin_gudang');
        
        // Create User Kasir Default
        $kasir = User::firstOrCreate(
            ['email' => 'kasir@sembako.com'],
            [
                'name' => 'Kasir Toko',
                'password' => Hash::make('password')
            ]
        );
        $kasir->assignRole('kasir');

        // Call other seeders
        $this->call([
            RoleSeeder::class, // Pastikan permissions reset & setup benar
            DemoDataSeeder::class, // Setup data dummy lengkap
        ]);

        // Create Role Pekerja Gudang
        $pekerjaRole = Role::firstOrCreate(['name' => 'pekerja_gudang']);
        // Pekerja Gudang permissions: Add Product only (Managed via Routes principally, but we can give base permissions)
        $pekerjaRole->syncPermissions(['manage products']); 

        // Create User Pekerja Gudang
        $pekerja = User::firstOrCreate(
            ['email' => 'pekerja@sembako.com'],
            [
                'name' => 'Pekerja Gudang',
                'password' => Hash::make('password')
            ]
        );
        $pekerja->assignRole('pekerja_gudang');
    }
}