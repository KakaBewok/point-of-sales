<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use App\Livewire\ProductManager;
use Tests\TestCase;

class ProductValidationTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected Category $category;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->admin()->create();
        $this->category = Category::factory()->create();
    }

    // ─── Required Fields ───────────────────────────────────────

    public function test_product_name_is_required(): void
    {
        Livewire::actingAs($this->admin)
            ->test(ProductManager::class)
            ->set('name', '')
            ->set('category_id', $this->category->id)
            ->set('sku', 'TESTSKU1')
            ->set('price', 10000)
            ->call('save')
            ->assertHasErrors(['name' => 'required']);
    }

    public function test_product_price_is_required(): void
    {
        Livewire::actingAs($this->admin)
            ->test(ProductManager::class)
            ->set('name', 'Test Product')
            ->set('category_id', $this->category->id)
            ->set('sku', 'TESTSKU2')
            ->set('price', '')
            ->call('save')
            ->assertHasErrors(['price']);
    }

    public function test_product_category_is_required(): void
    {
        Livewire::actingAs($this->admin)
            ->test(ProductManager::class)
            ->set('name', 'Test Product')
            ->set('category_id', '')
            ->set('sku', 'TESTSKU3')
            ->set('price', 10000)
            ->call('save')
            ->assertHasErrors(['category_id']);
    }

    public function test_product_sku_must_be_unique(): void
    {
        Product::factory()->create([
            'category_id' => $this->category->id,
            'sku' => 'DUPLICATE',
        ]);

        Livewire::actingAs($this->admin)
            ->test(ProductManager::class)
            ->set('name', 'Test Product 2')
            ->set('category_id', $this->category->id)
            ->set('sku', 'DUPLICATE')
            ->set('price', 10000)
            ->call('save')
            ->assertHasErrors(['sku']);
    }

    // ─── Valid Type ─────────────────────────────────────────────

    public function test_product_type_must_be_valid(): void
    {
        Livewire::actingAs($this->admin)
            ->test(ProductManager::class)
            ->set('type', 'invalid')
            ->set('name', 'Test')
            ->set('category_id', $this->category->id)
            ->set('sku', 'TESTSKU4')
            ->set('price', 10000)
            ->call('save')
            ->assertHasErrors(['type']);
    }

    // ─── Successful Creation ───────────────────────────────────

    public function test_product_created_successfully(): void
    {
        Livewire::actingAs($this->admin)
            ->test(ProductManager::class)
            ->set('type', 'product')
            ->set('name', 'Nasi Goreng')
            ->set('category_id', $this->category->id)
            ->set('sku', 'NASGOR01')
            ->set('price', 25000)
            ->set('cost_price', 15000)
            ->set('stock', 20)
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('products', [
            'name' => 'Nasi Goreng',
            'sku' => 'NASGOR01',
            'price' => 25000,
        ]);
    }

    // ─── Service Product Has No Stock ──────────────────────────

    public function test_service_product_has_null_stock(): void
    {
        Livewire::actingAs($this->admin)
            ->test(ProductManager::class)
            ->set('type', 'service')
            ->set('name', 'Potong Rambut')
            ->set('category_id', $this->category->id)
            ->set('sku', 'HAIRCUT1')
            ->set('price', 50000)
            ->set('stock', 99)
            ->call('save')
            ->assertHasNoErrors();

        $product = Product::where('sku', 'HAIRCUT1')->first();
        $this->assertNotNull($product);
        $this->assertNull($product->stock);
    }
}
