<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Akun Admin utama untuk pengujian lokal
        User::updateOrCreate(
            ['email' => 'admin.test@baksoatmo.local'],
            [
                'name' => 'Admin Test',
                'phone' => null,
                'password' => 'BaksoAtmoTest123!',
                'role' => 'admin',
                'is_active' => true,
            ]
        );

        // Akun Staff operasional untuk pengujian lokal
        User::updateOrCreate(
            ['email' => 'staff.test@baksoatmo.local'],
            [
                'name' => 'Staff Test',
                'phone' => null,
                'password' => 'BaksoAtmoTest123!',
                'role' => 'staff',
                'is_active' => true,
            ]
        );

        // Akun Customer aktif untuk pengujian lokal
        User::updateOrCreate(
            ['email' => 'customer.test@baksoatmo.local'],
            [
                'name' => 'Customer Test',
                'phone' => '081200000001',
                'password' => 'BaksoAtmoTest123!',
                'role' => 'customer',
                'is_active' => true,
            ]
        );

        // Akun Customer nonaktif untuk pengujian autentikasi
        User::updateOrCreate(
            ['email' => 'inactive.test@baksoatmo.local'],
            [
                'name' => 'Inactive Test',
                'phone' => null,
                'password' => 'BaksoAtmoTest123!',
                'role' => 'customer',
                'is_active' => false,
            ]
        );

        // Akun Customer demo tambahan (data sintetis, bukan data pribadi nyata)
        $demoCustomers = [
            ['name' => 'Pelanggan Demo 01', 'phone' => '081200000101', 'email' => 'demo.customer01@pelanggan.baksoatmo.local'],
            ['name' => 'Pelanggan Demo 02', 'phone' => '081200000102', 'email' => 'demo.customer02@pelanggan.baksoatmo.local'],
            ['name' => 'Pelanggan Demo 03', 'phone' => '081200000103', 'email' => 'demo.customer03@pelanggan.baksoatmo.local'],
            ['name' => 'Pelanggan Demo 04', 'phone' => '081200000104', 'email' => 'demo.customer04@pelanggan.baksoatmo.local'],
            ['name' => 'Pelanggan Demo 05', 'phone' => '081200000105', 'email' => 'demo.customer05@pelanggan.baksoatmo.local'],
        ];

        foreach ($demoCustomers as $customer) {
            User::updateOrCreate(
                ['email' => $customer['email']],
                [
                    'name' => $customer['name'],
                    'phone' => $customer['phone'],
                    'password' => 'BaksoAtmoTest123!',
                    'role' => 'customer',
                    'is_active' => true,
                ]
            );
        }
    }
}
