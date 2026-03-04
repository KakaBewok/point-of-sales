<?php

namespace App\Livewire;

use App\Models\Category;
use App\Models\Product;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Illuminate\Support\Str;
use App\Services\ActivityLogger;

#[Layout('layouts.app')]
#[Title('Produk')]
class ProductManager extends Component
{
    use WithPagination, WithFileUploads;

    public $search = '';
    public $categoryFilter = '';
    public $showModal = false;
    public $editingId = null;

    public $showDeleteModal = false;
    public $itemToDeleteId = null;
    public $itemToDeleteName = null;
    public $deleteType = 'single';

    // Bulk Delete
    public $selected = [];
    public $selectAll = false;

    // Form fields
    public $type = 'product';
    public $name = '';
    public $category_id = '';
    public $sku = '';
    public $description = '';
    public $price = '';
    public $cost_price = '';
    public $stock = 0;
    public $low_stock_threshold = 10;
    public $is_active = true;
    public $image;
    public $existingImage = null;

    protected function rules()
    {
        $skuRule = $this->editingId
            ? "required|string|unique:products,sku,{$this->editingId}"
            : 'required|string|unique:products,sku';

        return [
            'type' => 'required|in:product,service',
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'sku' => $skuRule,
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'cost_price' => 'nullable|numeric|min:0',
            'stock' => 'nullable|integer|min:0',
            'low_stock_threshold' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
            'image' => 'nullable|image|max:2048',
        ];
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatedSelectAll($value)
    {
        if ($value) {
            $this->selected = $this->getProductsQuery()->pluck('id')->map(fn($id) => (string) $id)->toArray();
        } else {
            $this->selected = [];
        }
    }

    public function create()
    {
        $this->reset(['type', 'name', 'category_id', 'sku', 'description', 'price', 'cost_price', 'stock', 'low_stock_threshold', 'is_active', 'image', 'existingImage', 'editingId']);
        $this->sku = strtoupper(Str::random(8));
        $this->type = 'product';
        $this->is_active = true;
        $this->stock = 0;
        $this->low_stock_threshold = 10;
        $this->showModal = true;
    }

    public function edit(Product $product)
    {
        $this->editingId = $product->id;
        $this->type = $product->type ?? 'product';
        $this->name = $product->name;
        $this->category_id = $product->category_id;
        $this->sku = $product->sku;
        $this->description = $product->description;
        $this->price = $product->price;
        $this->cost_price = $product->cost_price;
        $this->stock = $product->stock;
        $this->low_stock_threshold = $product->low_stock_threshold;
        $this->is_active = $product->is_active;
        $this->existingImage = $product->image;
        $this->showModal = true;
    }

    public function save()
    {
        $validated = $this->validate();
        unset($validated['image']);
        
        // If type is service, it doesn't have stock or threshold
        if ($validated['type'] === 'service') {
            $validated['stock'] = null;
            $validated['low_stock_threshold'] = null;
        } else {
            // Provide default values if empty when strictly product
            $validated['stock'] = $validated['stock'] ?? 0;
            $validated['low_stock_threshold'] = $validated['low_stock_threshold'] ?? 0;
        }

        // Cost Price Logic: Make cost_price nullable and default to null (unknown) if empty
        // Profit calculation will handle null as unknown cost.
        if (isset($validated['cost_price']) && $validated['cost_price'] === '') {
            $validated['cost_price'] = null;
        }

        if ($this->image) {
            // Delete old image file only when a new image is uploaded 
            // and old file exists to prevent orphans.
            $this->deleteOldImage();
            
            $validated['image'] = $this->image->store('products', 'public');
            $validated['thumbnail'] = $validated['image'];
        }

        $validated['slug'] = Str::slug($validated['name']);

        if ($this->editingId) {
            $product = Product::findOrFail($this->editingId);
            $product->update($validated);
            ActivityLogger::crud('product_updated', 'product', $product->id, ['name' => $product->name]);
            session()->flash('message', 'Produk berhasil diperbarui.');
        } else {
            $product = Product::create($validated);
            ActivityLogger::crud('product_created', 'product', $product->id, ['name' => $product->name, 'type' => $product->type]);
            session()->flash('message', 'Produk berhasil ditambahkan.');
        }

        $this->showModal = false;
        $this->reset(['type', 'name', 'category_id', 'sku', 'description', 'price', 'cost_price', 'stock', 'low_stock_threshold', 'is_active', 'image', 'existingImage', 'editingId']);
    }

    private function deleteOldImage()
    {
        if ($this->editingId) {
            $product = Product::find($this->editingId);
            if ($product && $product->image && \Illuminate\Support\Facades\Storage::disk('public')->exists($product->image)) {
                // Prevent deleting default placeholders if any
                if (!str_contains($product->image, 'default')) {
                    \Illuminate\Support\Facades\Storage::disk('public')->delete($product->image);
                }
            }
        }
    }

    public function confirmDelete($id, $name = '')
    {
        $this->itemToDeleteId = $id;
        $this->itemToDeleteName = $name;
        $this->deleteType = 'single';
        $this->showDeleteModal = true;
    }

    public function confirmDeleteSelected()
    {
        $this->itemToDeleteName = count($this->selected) . ' produk terpilih';
        $this->deleteType = 'multiple';
        $this->showDeleteModal = true;
    }

    public function processDelete()
    {
        if ($this->deleteType === 'single' && $this->itemToDeleteId) {
            $this->delete($this->itemToDeleteId);
        } elseif ($this->deleteType === 'multiple') {
            $this->deleteSelected();
        }
        
        $this->showDeleteModal = false;
        $this->reset(['itemToDeleteId', 'itemToDeleteName', 'deleteType']);
    }

    public function delete($id)
    {
        $product = Product::findOrFail($id);
        ActivityLogger::crud('product_deleted', 'product', $id, ['name' => $product->name]);
        $product->delete();
        session()->flash('message', 'Produk berhasil dihapus.');
    }

    public function deleteSelected()
    {
        if (empty($this->selected)) return;
        
        ActivityLogger::bulk('product_bulk_deleted', 'product', $this->selected);
        Product::whereIn('id', $this->selected)->delete();
        $this->reset(['selected', 'selectAll']);
        $this->resetPage();
        session()->flash('message', 'Produk terpilih berhasil dihapus.');
    }

    public function toggleActive(Product $product)
    {
        $product->update(['is_active' => !$product->is_active]);
    }

    private function getProductsQuery()
    {
        return Product::with('category')
            ->when($this->search, fn ($q) => $q->where('name', 'like', "%{$this->search}%")
                ->orWhere('sku', 'like', "%{$this->search}%"))
            ->when($this->categoryFilter, fn ($q) => $q->where('category_id', $this->categoryFilter));
    }

    public function render()
    {
        $products = $this->getProductsQuery()->orderBy('name')->paginate(12);

        $categories = Category::active()->ordered()->get();

        return view('livewire.product-manager', [
            'products' => $products,
            'categories' => $categories,
        ]);
    }
}
