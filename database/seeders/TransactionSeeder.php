<?php

namespace Database\Seeders;

use App\Models\Payment;
use App\Models\Product;
use App\Models\StockLog;
use App\Models\Transaction;
use App\Models\TransactionItem;
use App\Models\User;
use Illuminate\Database\Seeder;

class TransactionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $cashier = User::where('role', 'cashier')->first();
        $products = Product::all();

        // Create 15 sample completed transactions
        for ($i = 0; $i < 15; $i++) {
            // Pick 1-4 random products
            $selectedProducts = $products->random(rand(1, 4));

            $subtotal = 0;
            $items = [];

            foreach ($selectedProducts as $product) {
                $quantity = rand(1, 3);
                $itemSubtotal = $product->price * $quantity;
                $subtotal += $itemSubtotal;

                $items[] = [
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'product_price' => $product->price,
                    'quantity' => $quantity,
                    'subtotal' => $itemSubtotal,
                ];
            }

            // Apply tax (11%)
            $taxRate = 11;
            $taxAmount = round($subtotal * ($taxRate / 100));
            $grandTotal = $subtotal + $taxAmount;

            // Create transaction with random date in last 30 days
            $createdAt = now()->subDays(rand(0, 30))->setTime(rand(8, 21), rand(0, 59));

            $transaction = Transaction::create([
                'invoice_number' => sprintf('INV-%s-%04d', $createdAt->format('Ymd'), $i + 1),
                'user_id' => $cashier->id,
                'subtotal' => $subtotal,
                'discount_type' => null,
                'discount_value' => 0,
                'discount_amount' => 0,
                'tax_rate' => $taxRate,
                'tax_amount' => $taxAmount,
                'grand_total' => $grandTotal,
                'voucher_id' => null,
                'status' => 'completed',
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
            ]);

            // Create transaction items
            foreach ($items as $item) {
                TransactionItem::create(array_merge($item, [
                    'transaction_id' => $transaction->id,
                ]));
            }

            // Create cash payment
            $cashReceived = ceil($grandTotal / 10000) * 10000;
            Payment::create([
                'transaction_id' => $transaction->id,
                'method' => 'cash',
                'amount' => $grandTotal,
                'cash_received' => $cashReceived,
                'change_amount' => $cashReceived - $grandTotal,
                'status' => 'success',
                'paid_at' => $createdAt,
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
            ]);
        }
    }
}
