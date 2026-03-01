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
            throw new \Exception('Kode voucher tidak ditemukan.');
        }

        if (!$voucher->is_active) {
            throw new \Exception('Voucher sudah tidak aktif.');
        }

        if (!$voucher->isValid()) {
            if (!$voucher->hasRemainingUsage()) {
                throw new \Exception('Voucher sudah mencapai batas penggunaan.');
            }
            throw new \Exception('Voucher sudah kadaluarsa.');
        }

        if ($subtotal < $voucher->min_transaction) {
            $minFormatted = 'Rp ' . number_format($voucher->min_transaction, 0, ',', '.');
            throw new \Exception("Minimum transaksi untuk voucher ini adalah {$minFormatted}.");
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
