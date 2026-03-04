<?php

namespace Database\Factories;

use App\Models\Store;
use App\Models\Transaction;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Payment>
 */
class PaymentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'store_id' => Store::factory(),
            'transaction_id' => Transaction::factory(),
            'method' => 'cash',
            'amount' => fake()->numberBetween(20, 500) * 1000,
            'cash_received' => null,
            'change_amount' => null,
            'status' => 'success',
            'midtrans_transaction_id' => null,
            'midtrans_order_id' => null,
            'midtrans_response' => null,
            'va_number' => null,
            'qris_url' => null,
            'expires_at' => null,
            'paid_at' => now(),
        ];
    }

    /**
     * Cash payment.
     */
    public function cash(float $amount): static
    {
        $received = ceil($amount / 10000) * 10000;

        return $this->state(fn (array $attributes) => [
            'method' => 'cash',
            'amount' => $amount,
            'cash_received' => $received,
            'change_amount' => $received - $amount,
            'status' => 'success',
            'paid_at' => now(),
        ]);
    }

    /**
     * QRIS payment.
     */
    public function qris(): static
    {
        return $this->state(fn (array $attributes) => [
            'method' => 'qris',
            'status' => 'pending',
            'midtrans_order_id' => 'QRIS-' . strtoupper(\Illuminate\Support\Str::random(10)),
            'qris_url' => 'https://api.sandbox.midtrans.com/v2/qris/placeholder',
            'expires_at' => now()->addMinutes(15),
            'paid_at' => null,
        ]);
    }

    /**
     * VA payment.
     */
    public function va(): static
    {
        return $this->state(fn (array $attributes) => [
            'method' => 'va',
            'status' => 'pending',
            'midtrans_order_id' => 'VA-' . strtoupper(\Illuminate\Support\Str::random(10)),
            'va_number' => fake()->numerify('################'),
            'expires_at' => now()->addHours(24),
            'paid_at' => null,
        ]);
    }
}
