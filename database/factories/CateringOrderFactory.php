<?php

namespace Database\Factories;

use App\Models\CateringOrder;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CateringOrder>
 */
class CateringOrderFactory extends Factory
{
    public function definition(): array
    {
        $total = fake()->numberBetween(10, 50) * 25000;

        return [
            'booking_code' => 'BA-'.now()->format('Ymd').'-'.fake()->unique()->numerify('####'),
            'user_id' => User::factory(),
            'event_date' => now()->addDays(fake()->numberBetween(2, 30))->toDateString(),
            'event_address' => fake()->streetAddress(),
            'customer_phone' => '08'.fake()->numerify('##########'),
            'notes' => null,
            'total_amount' => $total,
            'dp_amount' => intdiv($total, 2),
            'status' => CateringOrder::STATUS_SUBMITTED,
            'payment_status' => CateringOrder::PAYMENT_PENDING,
        ];
    }
}
