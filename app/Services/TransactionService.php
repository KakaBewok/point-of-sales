<?php

namespace App\Services;

use App\Models\Payment;
use App\Models\Product;
use App\Models\Setting;
use App\Models\Transaction;
use App\Models\TransactionItem;
use App\Models\Voucher;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TransactionService
{
    public function __construct(
        protected StockService $stockService,
        protected VoucherService $voucherService,
    ) {}

    /**
     * Create a new transaction from cart data.
     *
     * @param array $cartItems [['product_id' => int, 'quantity' => int], ...]
     * @param string|null $voucherCode
     * @param string|null $discountType 'percentage' or 'fixed' (manual discount)
     * @param float $discountValue manual discount value
     * @param string|null $notes
     * @return Transaction
     */
    public function createTransaction(
        array $cartItems,
        ?string $voucherCode = null,
        ?string $discountType = null,
        float $discountValue = 0,
        ?string $notes = null,
    ): Transaction {
        return DB::transaction(function () use ($cartItems, $voucherCode, $discountType, $discountValue, $notes) {
            // 1. Validate and gather products
            $subtotal = 0;
            $itemsData = [];

            foreach ($cartItems as $item) {
                $product = Product::lockForUpdate()->findOrFail($item['product_id']);

                if (!$product->is_active) {
                    throw new \Exception("Product {$product->name} is not active.");
                }

                if (!$product->isServiceType() && !$product->hasEnoughStock($item['quantity'])) {
                    throw new \Exception("Stock {$product->name} is not enough. Available: {$product->stock}, requested: {$item['quantity']}");
                }

                $itemSubtotal = $product->price * $item['quantity'];
                $subtotal += $itemSubtotal;

                $itemsData[] = [
                    'product' => $product,
                    'product_name' => $product->name,
                    'product_price' => $product->price,
                    'quantity' => $item['quantity'],
                    'subtotal' => $itemSubtotal,
                ];
            }

            // 2. Apply voucher discount if provided
            $voucher = null;
            $voucherDiscount = 0;

            if ($voucherCode) {
                $voucher = $this->voucherService->validateVoucher($voucherCode, $subtotal);
                $voucherDiscount = $this->voucherService->applyVoucher($voucher, $subtotal);
            }

            // 3. Calculate manual discount
            $manualDiscount = 0;
            if ($discountType && $discountValue > 0) {
                if ($discountType === 'percentage') {
                    $manualDiscount = $subtotal * ($discountValue / 100);
                } else {
                    $manualDiscount = min($discountValue, $subtotal);
                }
            }

            // Total discount = voucher + manual
            $totalDiscount = $voucherDiscount + $manualDiscount;

            // 4. Calculate tax
            $taxRate = 0;
            $taxAmount = 0;
            if (Setting::isTaxEnabled()) {
                $taxRate = Setting::getTaxRate();
                $taxAmount = round(($subtotal - $totalDiscount) * ($taxRate / 100));
            }

            // 5. Calculate grand total
            $grandTotal = $subtotal - $totalDiscount + $taxAmount;

            if ($grandTotal < 0) {
                $grandTotal = 0;
            }

            // 6. Create transaction
            $transaction = Transaction::create([
                'user_id' => Auth::id(),
                'subtotal' => $subtotal, // before discount and tax
                'discount_type' => $discountType, // percentage or fixed
                'discount_value' => $discountValue,
                'discount_amount' => $totalDiscount,
                'tax_rate' => $taxRate,
                'tax_amount' => $taxAmount,
                'grand_total' => $grandTotal, // total after discount and tax
                'voucher_id' => $voucher?->id,
                'status' => 'pending',
                'notes' => $notes,
            ]);

            // 7. Create transaction items
            foreach ($itemsData as $data) {
                TransactionItem::create([
                    'transaction_id' => $transaction->id,
                    'product_id' => $data['product']->id,
                    'product_name' => $data['product_name'],
                    'product_price' => $data['product_price'],
                    'quantity' => $data['quantity'],
                    'subtotal' => $data['subtotal'],
                ]);
            }

            // 8. Mark voucher as used
            if ($voucher) {
                $this->voucherService->markAsUsed($voucher);
            }

            return $transaction->load(['items', 'voucher']);
        });
    }

    /**
     * Complete a transaction (after payment is confirmed).
     * Reduces stock for all items.
     */
    public function completeTransaction(Transaction $transaction): Transaction
    {
        return DB::transaction(function () use ($transaction) {
            // Reduce stock for each item
            foreach ($transaction->items as $item) {
                if ($item->product && !$item->product->isServiceType()) {
                    $this->stockService->reduceStock(
                        $item->product,
                        $item->quantity,
                        $transaction->invoice_number,
                    );
                }
            }

            // Mark transaction as completed
            $transaction->markAsCompleted();

            return $transaction->fresh(['items', 'payment', 'voucher', 'user']);
        });
    }

    /**
     * Cancel a transaction. Restore stock and voucher usage.
     */
    public function cancelTransaction(Transaction $transaction): Transaction
    {
        return DB::transaction(function () use ($transaction) {
            // Only restore stock if transaction was completed
            if ($transaction->isCompleted()) {
                foreach ($transaction->items as $item) {
                    if ($item->product && !$item->product->isServiceType()) {
                        $this->stockService->returnStock(
                            $item->product,
                            $item->quantity,
                            $transaction->invoice_number,
                        );
                    }
                }
            }

            // Reverse voucher usage
            if ($transaction->voucher) {
                $this->voucherService->reverseUsage($transaction->voucher);
            }

            // Cancel payment if exists
            if ($transaction->payment && $transaction->payment->isPending()) {
                $transaction->payment->markAsFailed();
            }

            $transaction->markAsCancelled();

            return $transaction->fresh(['items', 'payment', 'voucher', 'user']);
        });
    }

    /**
     * Process cash payment for a transaction.
     */
    public function processCashPayment(Transaction $transaction, float $cashReceived): Payment
    {
        if ($cashReceived < $transaction->grand_total) {
            throw new \Exception('The amount of money received is less than the total transaction.');
        }

        return DB::transaction(function () use ($transaction, $cashReceived) {
            $payment = Payment::create([
                'transaction_id' => $transaction->id,
                'method' => 'cash',
                'amount' => $transaction->grand_total,
                'cash_received' => $cashReceived,
                'change_amount' => $cashReceived - $transaction->grand_total,
                'status' => 'success',
                'paid_at' => now(),
            ]);

            // Complete the transaction immediately for cash
            $this->completeTransaction($transaction);

            return $payment;
        });
    }
}
