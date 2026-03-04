<?php

namespace Tests\Unit;

use App\Models\Category;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Voucher;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BulkDeleteEdgeCaseTest extends TestCase
{
    use RefreshDatabase;

    // ─── Bulk Delete Empty Selection ───────────────────────────

    public function test_bulk_delete_products_empty_selection_no_effect(): void
    {
        $product = Product::factory()->create();
        $selected = [];

        if (!empty($selected)) {
            Product::whereIn('id', $selected)->delete();
        }

        $this->assertNotNull(Product::find($product->id));
    }

    public function test_bulk_delete_vouchers_empty_selection_no_effect(): void
    {
        $voucher = Voucher::factory()->create();
        $selected = [];

        if (!empty($selected)) {
            Voucher::whereIn('id', $selected)->delete();
        }

        $this->assertNotNull(Voucher::find($voucher->id));
    }

    // ─── Soft Delete Preserves Data ────────────────────────────

    public function test_soft_deleted_voucher_still_exists_in_database(): void
    {
        $voucher = Voucher::factory()->create();
        $voucher->delete();

        // Not visible normally
        $this->assertNull(Voucher::find($voucher->id));

        // But exists with trashed
        $this->assertNotNull(Voucher::withTrashed()->find($voucher->id));
    }

    public function test_soft_deleted_transaction_still_exists(): void
    {
        $user = User::factory()->admin()->create();
        $transaction = Transaction::factory()->create(['user_id' => $user->id]);
        $transaction->delete();

        $this->assertNull(Transaction::find($transaction->id));
        $this->assertNotNull(Transaction::withTrashed()->find($transaction->id));
    }

    // ─── Bulk Delete Products ──────────────────────────────────

    public function test_bulk_delete_products_removes_all_selected(): void
    {
        $products = Product::factory()->count(5)->create();
        $ids = $products->pluck('id')->toArray();

        Product::whereIn('id', $ids)->delete();

        foreach ($ids as $id) {
            $this->assertSoftDeleted('products', ['id' => $id]);
        }
    }

    // ─── Bulk Delete Vouchers ──────────────────────────────────

    public function test_bulk_delete_vouchers_removes_all_selected(): void
    {
        $vouchers = Voucher::factory()->count(3)->create();
        $ids = $vouchers->pluck('id')->toArray();

        Voucher::whereIn('id', $ids)->delete();

        foreach ($ids as $id) {
            $this->assertSoftDeleted('vouchers', ['id' => $id]);
        }
    }

    // ─── Delete Used Voucher (Edge Case) ───────────────────────

    public function test_voucher_with_high_usage_can_be_deleted(): void
    {
        $voucher = Voucher::factory()->create([
            'used_count' => 100,
            'usage_limit' => 100,
        ]);

        $voucher->delete();

        $this->assertSoftDeleted($voucher);
    }

    // ─── Non-existent IDs in Bulk Delete ───────────────────────

    public function test_bulk_delete_with_nonexistent_ids_doesnt_fail(): void
    {
        $selected = [999998, 999999];

        // Should not throw, just delete 0 rows
        $deleted = Product::whereIn('id', $selected)->delete();
        $this->assertEquals(0, $deleted);
    }

    // ─── Category Bulk Delete Preserves Products ───────────────

    public function test_category_soft_delete_keeps_products(): void
    {
        $category = Category::factory()->create();
        $product = Product::factory()->create(['category_id' => $category->id]);

        $category->delete(); // soft delete

        // Product still exists and still references the category
        $this->assertNotNull(Product::find($product->id));
        $this->assertEquals($category->id, $product->fresh()->category_id);
    }
}
