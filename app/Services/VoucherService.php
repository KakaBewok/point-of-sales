<?php

namespace App\Services;

use App\Models\Voucher;

class VoucherService
{
    /**
     * Validate and retrieve a voucher by code.
     *
     * @throws \Exception
     */
    public function validateVoucher(string $code, float $subtotal): Voucher
    {
        $voucher = Voucher::where('code', strtoupper(trim($code)))->first();

        if (!$voucher) {
            throw new \Exception('Voucher code not found.');
        }

        if (!$voucher->is_active) {
            throw new \Exception('Voucher is not active.');
        }

        if (!$voucher->isValid()) {
            if (!$voucher->hasRemainingUsage()) {
                throw new \Exception('Voucher has reached its usage limit.');
            }
            throw new \Exception('Voucher has expired.');
        }

        if ($subtotal < $voucher->min_transaction) {
            $minFormatted = 'Rp ' . number_format($voucher->min_transaction, 0, ',', '.');
            throw new \Exception("Minimum transaction for this voucher is {$minFormatted}.");
        }

        return $voucher;
    }

    /**
     * Apply a voucher and return the discount amount.
     */
    public function applyVoucher(Voucher $voucher, float $subtotal): float
    {
        return $voucher->calculateDiscount($subtotal);
    }

    /**
     * Mark a voucher as used (increment usage count).
     */
    public function markAsUsed(Voucher $voucher): void
    {
        $voucher->incrementUsage();
    }

    /**
     * Reverse a voucher usage (e.g. when cancelling a transaction).
     */
    public function reverseUsage(Voucher $voucher): void
    {
        if ($voucher->used_count > 0) {
            $voucher->decrement('used_count');
        }
    }
}
