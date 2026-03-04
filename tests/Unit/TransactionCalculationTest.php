<?php

namespace Tests\Unit;

use App\Models\Category;
use App\Models\Product;
use App\Models\Setting;
use App\Models\Transaction;
use App\Models\Voucher;
use App\Services\TransactionService;
use App\Services\VoucherService;
use App\Services\StockService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TransactionCalculationTest extends TestCase
{
    use RefreshDatabase;

    protected TransactionService $transactionService;
    protected Category $category;
    protected Product $product;

    protected function setUp(): void
    {
        parent::setUp();
        $this->transactionService = app(TransactionService::class);
        $this->category = Category::factory()->create();
        $this->product = Product::factory()->create([
            'category_id' => $this->category->id,
            'price' => 100000,
            'cost_price' => 60000,
            'stock' => 50,
            'type' => 'product',
        ]);
    }

    // ─── Subtotal Calculation ──────────────────────────────────

    public function test_subtotal_calculated_correctly_for_single_item(): void
    {
        $user = \App\Models\User::factory()->admin()->create();
        $this->actingAs($user);

        // Disable tax for predictable assertions
        Setting::set('tax_enabled', '0', 'tax');

        $transaction = $this->transactionService->createTransaction([
            ['product_id' => $this->product->id, 'quantity' => 3],
        ]);

        $this->assertEquals(300000, $transaction->subtotal);
    }

    public function test_subtotal_calculated_correctly_for_multiple_items(): void
    {
        $user = \App\Models\User::factory()->admin()->create();
        $this->actingAs($user);

        $product2 = Product::factory()->create([
            'category_id' => $this->category->id,
            'price' => 50000,
            'stock' => 20,
            'type' => 'product',
        ]);

        Setting::set('tax_enabled', '0', 'tax');

        $transaction = $this->transactionService->createTransaction([
            ['product_id' => $this->product->id, 'quantity' => 2],
            ['product_id' => $product2->id, 'quantity' => 3],
        ]);

        // 2*100000 + 3*50000 = 350000
        $this->assertEquals(350000, $transaction->subtotal);
    }

    // ─── Discount Calculation ──────────────────────────────────

    public function test_percentage_discount_applied_correctly(): void
    {
        $user = \App\Models\User::factory()->admin()->create();
        $this->actingAs($user);

        Setting::set('tax_enabled', '0', 'tax');

        $transaction = $this->transactionService->createTransaction(
            [['product_id' => $this->product->id, 'quantity' => 1]],
            null,
            'percentage',
            10, // 10%
        );

        // 100000 - 10% = 90000
        $this->assertEquals(10000, $transaction->discount_amount);
        $this->assertEquals(90000, $transaction->grand_total);
    }

    public function test_fixed_discount_applied_correctly(): void
    {
        $user = \App\Models\User::factory()->admin()->create();
        $this->actingAs($user);

        Setting::set('tax_enabled', '0', 'tax');

        $transaction = $this->transactionService->createTransaction(
            [['product_id' => $this->product->id, 'quantity' => 1]],
            null,
            'fixed',
            25000,
        );

        $this->assertEquals(25000, $transaction->discount_amount);
        $this->assertEquals(75000, $transaction->grand_total);
    }

    public function test_discount_cannot_exceed_subtotal(): void
    {
        $user = \App\Models\User::factory()->admin()->create();
        $this->actingAs($user);

        Setting::set('tax_enabled', '0', 'tax');

        $transaction = $this->transactionService->createTransaction(
            [['product_id' => $this->product->id, 'quantity' => 1]],
            null,
            'fixed',
            200000, // More than subtotal
        );

        // grand_total should be 0, not negative
        $this->assertGreaterThanOrEqual(0, $transaction->grand_total);
    }

    // ─── Tax Calculation ───────────────────────────────────────

    public function test_tax_applied_when_enabled(): void
    {
        $user = \App\Models\User::factory()->admin()->create();
        $this->actingAs($user);

        Setting::set('tax_enabled', '1', 'tax');
        Setting::set('tax_rate', '11', 'tax');

        $transaction = $this->transactionService->createTransaction(
            [['product_id' => $this->product->id, 'quantity' => 1]],
        );

        // Tax = 11% of 100000 = 11000
        $this->assertEquals(11, $transaction->tax_rate);
        $this->assertEquals(11000, $transaction->tax_amount);
        $this->assertEquals(111000, $transaction->grand_total);
    }

    public function test_no_tax_when_disabled(): void
    {
        $user = \App\Models\User::factory()->admin()->create();
        $this->actingAs($user);

        Setting::set('tax_enabled', '0', 'tax');

        $transaction = $this->transactionService->createTransaction(
            [['product_id' => $this->product->id, 'quantity' => 1]],
        );

        $this->assertEquals(0, $transaction->tax_rate);
        $this->assertEquals(0, $transaction->tax_amount);
        $this->assertEquals(100000, $transaction->grand_total);
    }

    // ─── Payment Change Calculation ────────────────────────────

    public function test_cash_payment_change_calculated_correctly(): void
    {
        $user = \App\Models\User::factory()->admin()->create();
        $this->actingAs($user);

        Setting::set('tax_enabled', '0', 'tax');

        $transaction = $this->transactionService->createTransaction(
            [['product_id' => $this->product->id, 'quantity' => 1]],
        );

        $payment = $this->transactionService->processCashPayment($transaction, 150000);

        $this->assertEquals(100000, $payment->amount);
        $this->assertEquals(150000, $payment->cash_received);
        $this->assertEquals(50000, $payment->change_amount);
    }

    public function test_cash_payment_fails_when_insufficient(): void
    {
        $user = \App\Models\User::factory()->admin()->create();
        $this->actingAs($user);

        Setting::set('tax_enabled', '0', 'tax');

        $transaction = $this->transactionService->createTransaction(
            [['product_id' => $this->product->id, 'quantity' => 1]],
        );

        $this->expectException(\Exception::class);
        $this->transactionService->processCashPayment($transaction, 50000);
    }

    // ─── Stock Deduction ───────────────────────────────────────

    public function test_stock_deducted_after_payment(): void
    {
        $user = \App\Models\User::factory()->admin()->create();
        $this->actingAs($user);

        Setting::set('tax_enabled', '0', 'tax');

        $initialStock = $this->product->stock;

        $transaction = $this->transactionService->createTransaction(
            [['product_id' => $this->product->id, 'quantity' => 5]],
        );

        $this->transactionService->processCashPayment($transaction, 500000);

        $this->product->refresh();
        $this->assertEquals($initialStock - 5, $this->product->stock);
    }

    public function test_service_type_not_deducted_stock(): void
    {
        $user = \App\Models\User::factory()->admin()->create();
        $this->actingAs($user);

        $service = Product::factory()->create([
            'category_id' => $this->category->id,
            'price' => 75000,
            'stock' => null,
            'type' => 'service',
        ]);

        Setting::set('tax_enabled', '0', 'tax');

        $transaction = $this->transactionService->createTransaction(
            [['product_id' => $service->id, 'quantity' => 2]],
        );

        $this->transactionService->processCashPayment($transaction, 150000);

        $service->refresh();
        $this->assertNull($service->stock);
    }

    // ─── Inactive Product Rejection ────────────────────────────

    public function test_inactive_product_rejected(): void
    {
        $user = \App\Models\User::factory()->admin()->create();
        $this->actingAs($user);

        $this->product->update(['is_active' => false]);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('is not active');

        $this->transactionService->createTransaction(
            [['product_id' => $this->product->id, 'quantity' => 1]],
        );
    }

    // ─── Out of Stock Rejection ────────────────────────────────

    public function test_out_of_stock_rejected(): void
    {
        $user = \App\Models\User::factory()->admin()->create();
        $this->actingAs($user);

        $this->product->update(['stock' => 1]);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('not enough');

        $this->transactionService->createTransaction(
            [['product_id' => $this->product->id, 'quantity' => 5]],
        );
    }
}
