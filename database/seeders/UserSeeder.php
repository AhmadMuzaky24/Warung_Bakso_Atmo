<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Akun Admin untuk pengujian
        User::updateOrCreate(
            ['email' => 'admin.test@baksoatmo.local'],
            [
                'name' => 'Admin Test',
                'password' => 'BaksoAtmoTest123!',
                'role' => 'admin',
                'is_active' => true,
            ]
        );

        // Akun Customer nonaktif untuk pengujian
        User::updateOrCreate(
            ['email' => 'inactive.test@baksoatmo.local'],
            [
                'name' => 'Inactive Test',
                'password' => 'BaksoAtmoTest123!',
                'role' => 'customer',
                'is_active' => false,
            ]
        );
    }
}