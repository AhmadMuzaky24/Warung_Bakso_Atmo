<?php

namespace Database\Factories;

use App\Models\CateringOrder;
use App\Models\CateringOrderItem;
use App\Models\Menu;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CateringOrderItem>
 */
class CateringOrderItemFactory extends Factory
{
    public function definition(): array
    {
        $unitPrice = fake()->numberBetween(15, 60) * 1000;
        $quantity = fake()->numberBetween(1, 20);

        return [
            'catering_order_id' => CateringOrder::factory(),
            'menu_id' => Menu::factory(),
            'menu_name' => fake()->words(3, true),
            'unit_price' => $unitPrice,
            'quantity' => $quantity,
            'subtotal' => $unitPrice * $quantity,
        ];
    }

    public function forMenu(Menu $menu, int $quantity = 1): static
    {
        return $this->state(fn () => [
            'menu_id' => $menu->id,
            'menu_name' => $menu->name,
            'unit_price' => (int) $menu->price,
            'quantity' => $quantity,
            'subtotal' => (int) $menu->price * $quantity,
        ]);
    }
}
