<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Voucher>
 */
class VoucherFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'code' => strtoupper(Str::random(8)),
            'discount_type' => fake()->randomElement(['percentage', 'fixed']),
            'discount_value' => fake()->randomElement([5, 10, 15, 20, 25000, 50000]),
            'min_transaction' => fake()->randomElement([0, 50000, 100000]),
            'max_discount' => fake()->randomElement([null, 25000, 50000, 100000]),
            'usage_limit' => fake()->randomElement([null, 10, 50, 100]),
            'used_count' => 0,
            'valid_from' => now()->subDays(5),
            'valid_until' => now()->addDays(30),
            'is_active' => true,
        ];
    }

    /**
     * Expired voucher.
     */
    public function expired(): static
    {
        return $this->state(fn (array $attributes) => [
            'valid_from' => now()->subDays(60),
            'valid_until' => now()->subDays(1),
        ]);
    }

    /**
     * Fully used voucher.
     */
    public function fullyUsed(): static
    {
        return $this->state(fn (array $attributes) => [
            'usage_limit' => 10,
            'used_count' => 10,
        ]);
    }
}
