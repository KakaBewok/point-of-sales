<?php

namespace Database\Factories;

use App\Models\Store;
use App\Models\User;
use App\Models\Transaction;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Transaction>
 */
class TransactionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $subtotal = fake()->numberBetween(20, 500) * 1000;
        $discountAmount = 0;
        $taxRate = 0;
        $taxAmount = 0;

        return [
            'store_id' => Store::factory(),
            'invoice_number' => Transaction::generateInvoiceNumber(),
            'user_id' => User::factory(),
            'subtotal' => $subtotal,
            'discount_type' => null,
            'discount_value' => 0,
            'discount_amount' => $discountAmount,
            'tax_rate' => $taxRate,
            'tax_amount' => $taxAmount,
            'grand_total' => $subtotal - $discountAmount + $taxAmount,
            'voucher_id' => null,
            'status' => 'completed',
            'notes' => null,
        ];
    }

    /**
     * Pending transaction.
     */
    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'pending',
        ]);
    }

    /**
     * Cancelled transaction.
     */
    public function cancelled(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'cancelled',
        ]);
    }
}
