<?php

namespace Database\Factories;

use App\Models\Store;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Store>
 */
class StoreFactory extends Factory
{
    protected $model = Store::class;

    public function definition(): array
    {
        $name = fake()->company();

        return [
            'name' => $name,
            'slug' => Str::slug($name) . '-' . Str::random(4),
            'address' => fake()->address(),
            'phone' => fake()->phoneNumber(),
            'subscription_status' => 'active',
            'trial_ends_at' => null,
        ];
    }

    public function trial(): static
    {
        return $this->state(fn (array $attributes) => [
            'subscription_status' => 'trial',
            'trial_ends_at' => now()->addDays(14),
        ]);
    }

    public function suspended(): static
    {
        return $this->state(fn (array $attributes) => [
            'subscription_status' => 'suspended',
        ]);
    }

    public function cancelled(): static
    {
        return $this->state(fn (array $attributes) => [
            'subscription_status' => 'cancelled',
        ]);
    }
}
