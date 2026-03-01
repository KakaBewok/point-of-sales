<?php

namespace Database\Seeders;

use App\Models\Voucher;
use Illuminate\Database\Seeder;

class VoucherSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $vouchers = [
            [
                'code' => 'WELCOME10',
                'discount_type' => 'percentage',
                'discount_value' => 10,
                'min_transaction' => 50000,
                'max_discount' => 25000,
                'usage_limit' => 100,
                'used_count' => 0,
                'valid_from' => now(),
                'valid_until' => now()->addMonths(3),
                'is_active' => true,
            ],
            [
                'code' => 'DISKON20',
                'discount_type' => 'percentage',
                'discount_value' => 20,
                'min_transaction' => 100000,
                'max_discount' => 50000,
                'usage_limit' => 50,
                'used_count' => 0,
                'valid_from' => now(),
                'valid_until' => now()->addMonths(1),
                'is_active' => true,
            ],
            [
                'code' => 'HEMAT25K',
                'discount_type' => 'fixed',
                'discount_value' => 25000,
                'min_transaction' => 75000,
                'max_discount' => null,
                'usage_limit' => 200,
                'used_count' => 0,
                'valid_from' => now(),
                'valid_until' => now()->addMonths(2),
                'is_active' => true,
            ],
            [
                'code' => 'GRATIS50K',
                'discount_type' => 'fixed',
                'discount_value' => 50000,
                'min_transaction' => 150000,
                'max_discount' => null,
                'usage_limit' => 20,
                'used_count' => 0,
                'valid_from' => now(),
                'valid_until' => now()->addWeeks(2),
                'is_active' => true,
            ],
        ];

        foreach ($vouchers as $voucher) {
            Voucher::create($voucher);
        }
    }
}
