<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Menu;
use Illuminate\Database\Seeder;

class MenuSeeder extends Seeder
{
    public function run(): void
    {
        $bakso = Category::where('name', 'Bakso Spesial')->first();
        $mie = Category::where('name', 'Mie & Bihun')->first();
        $katering = Category::where('name', 'Paket Katering')->first();
        $minuman = Category::where('name', 'Minuman')->first();

        $menus = [
            [
                'category_id' => $bakso->id,
                'name' => 'Bakso Urat Spesial',
                'description' => 'Bakso urat sapi kenyal disajikan dengan kuah kaldu gurih.',
                'price' => 25000,
                'is_available' => true,
            ],
            [
                'category_id' => $bakso->id,
                'name' => 'Bakso Halus Komplit',
                'description' => 'Bakso daging halus lengkap dengan tahu bakso dan siomay.',
                'price' => 22000,
                'is_available' => true,
            ],
            [
                'category_id' => $mie->id,
                'name' => 'Mie Ayam Bakso',
                'description' => 'Mie ayam berbumbu kecap manis gurih dengan topping bakso.',
                'price' => 23000,
                'is_available' => true,
            ],
            [
                'category_id' => $katering->id,
                'name' => 'Paket Katering Bakso 50 Porsi',
                'description' => 'Paket prasmanan bakso lengkap dengan mangkuk, sendok, dan pelengkap.',
                'price' => 950000,
                'is_available' => true,
            ],
            [
                'category_id' => $katering->id,
                'name' => 'Paket Katering Bakso 100 Porsi',
                'description' => 'Paket prasmanan acara besar siap saji di lokasi.',
                'price' => 1800000,
                'is_available' => true,
            ],
            [
                'category_id' => $minuman->id,
                'name' => 'Es Teh Manis',
                'description' => 'Teh melati seduh segar disajikan dingin.',
                'price' => 6000,
                'is_available' => true,
            ],
        ];

        foreach ($menus as $menu) {
            Menu::updateOrCreate(
                ['name' => $menu['name']],
                $menu
            );
        }
    }
}