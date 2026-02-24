<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Hapus cache permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Membuat permissions untuk produk
        Permission::create(['name' => 'view products']);
        Permission::create(['name' => 'create products']);
        Permission::create(['name' => 'edit products']);
        Permission::create(['name' => 'delete products']);

        // Membuat permissions untuk pesanan
        Permission::create(['name' => 'view orders']);
        Permission::create(['name' => 'edit orders']);
        Permission::create(['name' => 'update order status']);

        // Membuat permissions untuk pengguna
        Permission::create(['name' => 'view users']);
        Permission::create(['name' => 'create users']);
        Permission::create(['name' => 'edit users']);
        Permission::create(['name' => 'delete users']);

        // Membuat permissions untuk laporan
        Permission::create(['name' => 'view reports']);

        // Membuat role admin
        $adminRole = Role::create(['name' => 'admin']);
        $adminRole->givePermissionTo(Permission::all());

        // Membuat role supplier
        $supplierRole = Role::create(['name' => 'supplier']);
        $supplierRole->givePermissionTo([
            'view products',
            'create products',
            'edit products',
            'view orders',
            'update order status'
        ]);

        // Membuat role customer
        $customerRole = Role::create(['name' => 'customer']);
        $customerRole->givePermissionTo([
            'view products'
        ]);

        // Membuat user admin default
        $adminUser = User::create([
            'username' => 'admin',
            'email' => 'admin@dapursppgmbg.co.id',
            'password' => bcrypt('admin123'),
            'role' => 'admin',
            'nama_lengkap' => 'Administrator Dapur SPPG'
        ]);
        $adminUser->assignRole('admin');

        // Membuat user supplier default
        $supplierUser = User::create([
            'username' => 'supplier',
            'email' => 'supplier@dapursppgmbg.co.id',
            'password' => bcrypt('supplier123'),
            'role' => 'supplier',
            'nama_lengkap' => 'Supplier Toko'
        ]);
        $supplierUser->assignRole('supplier');

        // Membuat user customer default
        $customerUser = User::create([
            'username' => 'customer',
            'email' => 'customer@dapursppgmbg.co.id',
            'password' => bcrypt('customer123'),
            'role' => 'customer',
            'nama_lengkap' => 'Pelanggan'
        ]);
        $customerUser->assignRole('customer');
    }
}