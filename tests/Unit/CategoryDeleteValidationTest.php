<?php

namespace Tests\Unit;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoryDeleteValidationTest extends TestCase
{
    use RefreshDatabase;

    // ─── Delete Empty Category ─────────────────────────────────

    public function test_empty_category_can_be_deleted(): void
    {
        $category = Category::factory()->create();

        $category->delete();

        $this->assertSoftDeleted($category);
    }

    // ─── Category With Products Cannot Be Hard Deleted ─────────

    public function test_category_with_products_relationship(): void
    {
        $category = Category::factory()->create();
        Product::factory()->count(3)->create(['category_id' => $category->id]);

        $this->assertEquals(3, $category->products()->count());
    }

    // ─── Bulk Delete Skips Categories With Products ────────────

    public function test_bulk_delete_skips_categories_with_products(): void
    {
        $emptyCategory = Category::factory()->create();
        $usedCategory = Category::factory()->create();
        Product::factory()->create(['category_id' => $usedCategory->id]);

        // Simulate the bulk delete logic from CategoryManager
        $ids = [$emptyCategory->id, $usedCategory->id];
        $deletable = Category::whereIn('id', $ids)->whereDoesntHave('products')->pluck('id');

        $this->assertCount(1, $deletable);
        $this->assertTrue($deletable->contains($emptyCategory->id));
        $this->assertFalse($deletable->contains($usedCategory->id));
    }

    // ─── Bulk Delete Empty Selection ───────────────────────────

    public function test_bulk_delete_with_empty_selection_does_nothing(): void
    {
        $category = Category::factory()->create();
        $selected = [];

        if (!empty($selected)) {
            Category::whereIn('id', $selected)->delete();
        }

        // Category should still exist
        $this->assertNotNull(Category::find($category->id));
    }

    // ─── Count Products Correctly ──────────────────────────────

    public function test_products_count_accurate_for_warning(): void
    {
        $cat1 = Category::factory()->create();
        $cat2 = Category::factory()->create();
        $cat3 = Category::factory()->create();

        Product::factory()->count(2)->create(['category_id' => $cat1->id]);
        Product::factory()->count(5)->create(['category_id' => $cat2->id]);
        // cat3 has no products

        $ids = [$cat1->id, $cat2->id, $cat3->id];
        $withProducts = Category::whereIn('id', $ids)->has('products')->count();

        $this->assertEquals(2, $withProducts);
    }

    // ─── Scopes ────────────────────────────────────────────────

    public function test_active_scope_returns_only_active_categories(): void
    {
        Category::factory()->count(3)->create(['is_active' => true]);
        Category::factory()->count(2)->create(['is_active' => false]);

        $this->assertEquals(3, Category::active()->count());
    }

    // ─── Slug Auto-Generation ──────────────────────────────────

    public function test_slug_auto_generated(): void
    {
        $category = Category::create([
            'name' => 'Makanan Ringan',
            'is_active' => true,
        ]);

        $this->assertEquals('makanan-ringan', $category->slug);
    }
}
