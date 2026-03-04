<?php

namespace Tests\Unit;

use App\Models\Voucher;
use App\Services\VoucherService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VoucherValidationTest extends TestCase
{
    use RefreshDatabase;

    protected VoucherService $voucherService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->voucherService = new VoucherService();
    }

    // ─── Valid Voucher ─────────────────────────────────────────

    public function test_valid_voucher_passes_validation(): void
    {
        $voucher = Voucher::factory()->create([
            'code' => 'VALID10',
            'discount_type' => 'percentage',
            'discount_value' => 10,
            'min_transaction' => 50000,
            'is_active' => true,
            'valid_from' => now()->subDay(),
            'valid_until' => now()->addDays(30),
        ]);

        $result = $this->voucherService->validateVoucher('VALID10', 100000);
        $this->assertEquals($voucher->id, $result->id);
    }

    // ─── Not Found ─────────────────────────────────────────────

    public function test_nonexistent_voucher_throws_exception(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('not found');

        $this->voucherService->validateVoucher('NOTEXIST', 100000);
    }

    // ─── Inactive Voucher ──────────────────────────────────────

    public function test_inactive_voucher_throws_exception(): void
    {
        Voucher::factory()->create([
            'code' => 'INACTIVE',
            'is_active' => false,
        ]);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('not active');

        $this->voucherService->validateVoucher('INACTIVE', 100000);
    }

    // ─── Expired Voucher ───────────────────────────────────────

    public function test_expired_voucher_throws_exception(): void
    {
        Voucher::factory()->expired()->create([
            'code' => 'EXPIRED',
        ]);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('expired');

        $this->voucherService->validateVoucher('EXPIRED', 100000);
    }

    // ─── Usage Limit Reached ───────────────────────────────────

    public function test_fully_used_voucher_throws_exception(): void
    {
        Voucher::factory()->fullyUsed()->create([
            'code' => 'MAXED',
        ]);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('usage limit');

        $this->voucherService->validateVoucher('MAXED', 100000);
    }

    // ─── Minimum Transaction ───────────────────────────────────

    public function test_below_min_transaction_throws_exception(): void
    {
        Voucher::factory()->create([
            'code' => 'MIN100K',
            'min_transaction' => 100000,
        ]);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Minimum transaction');

        $this->voucherService->validateVoucher('MIN100K', 50000);
    }

    // ─── Percentage Discount Calculation ───────────────────────

    public function test_percentage_discount_calculated_correctly(): void
    {
        $voucher = Voucher::factory()->create([
            'discount_type' => 'percentage',
            'discount_value' => 20,
            'max_discount' => null,
            'min_transaction' => 0,
        ]);

        $discount = $this->voucherService->applyVoucher($voucher, 200000);
        $this->assertEquals(40000, $discount);
    }

    // ─── Percentage With Max Cap ───────────────────────────────

    public function test_percentage_discount_capped_by_max_discount(): void
    {
        $voucher = Voucher::factory()->create([
            'discount_type' => 'percentage',
            'discount_value' => 50,
            'max_discount' => 30000,
            'min_transaction' => 0,
        ]);

        // 50% of 200000 = 100000, but capped at 30000
        $discount = $this->voucherService->applyVoucher($voucher, 200000);
        $this->assertEquals(30000, $discount);
    }

    // ─── Fixed Discount ────────────────────────────────────────

    public function test_fixed_discount_applied_correctly(): void
    {
        $voucher = Voucher::factory()->create([
            'discount_type' => 'fixed',
            'discount_value' => 25000,
            'min_transaction' => 0,
        ]);

        $discount = $this->voucherService->applyVoucher($voucher, 100000);
        $this->assertEquals(25000, $discount);
    }

    // ─── Usage Increment ───────────────────────────────────────

    public function test_voucher_usage_incremented(): void
    {
        $voucher = Voucher::factory()->create([
            'used_count' => 0,
            'usage_limit' => 10,
        ]);

        $this->voucherService->markAsUsed($voucher);
        $this->assertEquals(1, $voucher->fresh()->used_count);
    }

    // ─── Usage Reversal ────────────────────────────────────────

    public function test_voucher_usage_reversed(): void
    {
        $voucher = Voucher::factory()->create([
            'used_count' => 5,
        ]);

        $this->voucherService->reverseUsage($voucher);
        $this->assertEquals(4, $voucher->fresh()->used_count);
    }

    public function test_usage_reversal_does_not_go_below_zero(): void
    {
        $voucher = Voucher::factory()->create([
            'used_count' => 0,
        ]);

        $this->voucherService->reverseUsage($voucher);
        $this->assertEquals(0, $voucher->fresh()->used_count);
    }

    // ─── Case Insensitive Code ─────────────────────────────────

    public function test_voucher_code_is_case_insensitive(): void
    {
        Voucher::factory()->create([
            'code' => 'MYCODE99',
            'min_transaction' => 0,
        ]);

        $result = $this->voucherService->validateVoucher('mycode99', 100000);
        $this->assertEquals('MYCODE99', $result->code);
    }

    // ─── Soft Delete Used Voucher ──────────────────────────────

    public function test_used_voucher_can_be_soft_deleted(): void
    {
        $voucher = Voucher::factory()->create([
            'used_count' => 5,
        ]);

        $voucher->delete();

        $this->assertSoftDeleted($voucher);
        $this->assertNull(Voucher::find($voucher->id));
        $this->assertNotNull(Voucher::withTrashed()->find($voucher->id));
    }
}
