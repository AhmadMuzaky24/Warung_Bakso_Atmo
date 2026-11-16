<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Menu;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class MenuSeeder extends Seeder
{
    public function run(): void
    {
        Storage::disk('public')->makeDirectory('menus');

        $bakso = Category::where('name', 'Bakso Spesial')->first();
        $mie = Category::where('name', 'Mie & Bihun')->first();
        $katering = Category::where('name', 'Paket Katering')->first();
        $minuman = Category::where('name', 'Minuman')->first();

        $menus = [
            [
                'seed_key' => 'menu-1',
                'category_id' => $bakso->id,
                'name' => 'Bakso Urat Spesial',
                'description' => 'Bakso urat sapi kenyal disajikan dengan kuah kaldu gurih.',
                'price' => 25000,
                'is_available' => true,
            ],
            [
                'seed_key' => 'menu-2',
                'category_id' => $bakso->id,
                'name' => 'Bakso Halus Komplit',
                'description' => 'Bakso daging halus lengkap dengan tahu bakso dan siomay.',
                'price' => 22000,
                'is_available' => true,
            ],
            [
                'seed_key' => 'menu-3',
                'category_id' => $mie->id,
                'name' => 'Mie Ayam Bakso',
                'description' => 'Mie ayam berbumbu kecap manis gurih dengan topping bakso.',
                'price' => 23000,
                'is_available' => true,
            ],
            [
                'seed_key' => 'menu-4',
                'category_id' => $katering->id,
                'name' => 'Paket Katering Bakso 50 Porsi',
                'description' => 'Paket prasmanan bakso lengkap dengan mangkuk, sendok, dan pelengkap.',
                'price' => 950000,
                'is_available' => true,
            ],
            [
                'seed_key' => 'menu-5',
                'category_id' => $katering->id,
                'name' => 'Paket Katering Bakso 100 Porsi',
                'description' => 'Paket prasmanan acara besar siap saji di lokasi.',
                'price' => 1800000,
                'is_available' => true,
            ],
            [
                'seed_key' => 'menu-6',
                'category_id' => $minuman->id,
                'name' => 'Es Teh Manis',
                'description' => 'Teh melati seduh segar disajikan dingin.',
                'price' => 6000,
                'is_available' => true,
            ],
        ];

        foreach ($menus as $item) {
            $imagePath = $this->copySeedImage($item['seed_key']);

            Menu::updateOrCreate(
                ['name' => $item['name']],
                [
                    'category_id' => $item['category_id'],
                    'description' => $item['description'],
                    'price' => $item['price'],
                    'image_path' => $imagePath,
                    'is_available' => $item['is_available'],
                ]
            );
        }
    }

    private function copySeedImage(string $seedKey): ?string
    {
        $matches = glob(database_path("seeders/images/{$seedKey}.*"));

        if (empty($matches) || ! is_file($matches[0])) {
            return null;
        }

        $sourcePath = $matches[0];
        $targetPath = 'menus/' . basename($sourcePath);

        Storage::disk('public')->put($targetPath, file_get_contents($sourcePath));

        return $targetPath;
    }
}