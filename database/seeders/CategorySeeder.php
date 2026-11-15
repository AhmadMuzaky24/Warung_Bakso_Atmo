<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Bakso Spesial',
                'description' => 'Varian bakso daging sapi pilihan khas Bakso Atmo.',
            ],
            [
                'name' => 'Mie & Bihun',
                'description' => 'Pelengkap mie ayam, mie kuning, dan bihun kuah kaldu.',
            ],
            [
                'name' => 'Paket Katering',
                'description' => 'Menu porsi besar dan prasmanan untuk acara spesial.',
            ],
            [
                'name' => 'Minuman',
                'description' => 'Aneka minuman segar pendamping hidangan bakso.',
            ],
        ];

        foreach ($categories as $category) {
            Category::updateOrCreate(
                ['name' => $category['name']],
                ['description' => $category['description']]
            );
        }
    }
}