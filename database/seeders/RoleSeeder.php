<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Buat Permissions (Hak Akses Granular)
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [
            'manage products',
            'manage suppliers',
            'access pos',
            'manage sales',
            'view reports',
        ];

        foreach ($permissions as $permission) {
            \Spatie\Permission\Models\Permission::firstOrCreate(['name' => $permission]);
        }

        // 2. Buat & Setup Roles
        
        // A. KASIR
        $roleKasir = Role::firstOrCreate(['name' => 'kasir']);
        $roleKasir->givePermissionTo(['access pos', 'manage sales']);

        // B. ADMIN GUDANG
        $roleAdmin = Role::firstOrCreate(['name' => 'admin_gudang']);
        $roleAdmin->givePermissionTo(['manage products', 'manage suppliers']);

        // C. PEMILIK (Super Admin)
        $rolePemilik = Role::firstOrCreate(['name' => 'pemilik']);
        $rolePemilik->givePermissionTo(\Spatie\Permission\Models\Permission::all()); // Kasih semua akses
    }
}