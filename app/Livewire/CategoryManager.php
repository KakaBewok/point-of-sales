<?php

namespace App\Livewire;

use App\Models\Category;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Illuminate\Support\Str;
use App\Services\ActivityLogger;

#[Layout('layouts.app')]
#[Title('Kategori')]
class CategoryManager extends Component
{
    public $search = '';
    public $showModal = false;
    public $editingId = null;

    public $showDeleteModal = false;
    public $itemToDeleteId = null;
    public $itemToDeleteName = null;
    public $deleteType = 'single';

    public $selected = [];
    public $selectAll = false;

    public $name = '';
    public $description = '';
    public $sort_order = 1;
    public $is_active = true;

    protected $rules = [
        'name' => 'required|string|max:255',
        'description' => 'nullable|string|max:500',
        'sort_order' => 'required|integer|min:1',
        'is_active' => 'boolean',
    ];

    public function create()
    {
        $this->reset(['name', 'description', 'sort_order', 'is_active', 'editingId']);
        $this->is_active = true;
        $this->showModal = true;
    }

    public function edit(Category $category)
    {
        $this->editingId = $category->id;
        $this->name = $category->name;
        $this->description = $category->description;
        $this->sort_order = $category->sort_order;
        $this->is_active = $category->is_active;
        $this->showModal = true;
    }

    public function save()
    {
        $this->validate();

        $data = [
            'name' => $this->name,
            'slug' => Str::slug($this->name),
            'description' => $this->description,
            'sort_order' => $this->sort_order,
            'is_active' => $this->is_active,
        ];

        if ($this->editingId) {
            Category::findOrFail($this->editingId)->update($data);
            ActivityLogger::crud('category_updated', 'category', $this->editingId, ['name' => $this->name]);
            session()->flash('message', 'Kategori berhasil diperbarui.');
        } else {
            $cat = Category::create($data);
            ActivityLogger::crud('category_created', 'category', $cat->id, ['name' => $cat->name]);
            session()->flash('message', 'Kategori berhasil ditambahkan.');
        }

        $this->showModal = false;
    }

    public function updatedSelectAll($value)
    {
        if ($value) {
            $this->selected = Category::when($this->search, fn ($q) => $q->where('name', 'like', "%{$this->search}%"))
                                      ->pluck('id')->map(fn($id) => (string) $id)->toArray();
        } else {
            $this->selected = [];
        }
    }

    public $hasProductsWarning = false;
    public $categoriesWithProducts = 0;

    public function deleteSelected()
    {
        if (empty($this->selected)) return;

        $categoriesToDelete = Category::whereIn('id', $this->selected)->whereDoesntHave('products')->pluck('id');
        
        $notDeletedCount = count($this->selected) - $categoriesToDelete->count();
        
        if ($categoriesToDelete->count() > 0) {
            Category::whereIn('id', $categoriesToDelete)->delete();
        }
        
        $this->reset(['selected', 'selectAll']);
        
        if ($notDeletedCount > 0) {
            session()->flash('warning', "{$categoriesToDelete->count()} kategori dihapus. {$notDeletedCount} kategori gagal dihapus karena masih memiliki produk.");
        } else {
            session()->flash('message', 'Kategori terpilih berhasil dihapus.');
        }
    }

    public function confirmDelete($id, $name = '')
    {
        $this->itemToDeleteId = $id;
        $this->itemToDeleteName = $name;
        $this->deleteType = 'single';

        $category = Category::withCount('products')->find($id);
        $this->hasProductsWarning = $category && $category->products_count > 0;
        $this->categoriesWithProducts = $this->hasProductsWarning ? 1 : 0;

        $this->showDeleteModal = true;
    }

    public function confirmDeleteSelected()
    {
        if (empty($this->selected)) return;

        $this->itemToDeleteName = count($this->selected) . ' kategori terpilih';
        $this->deleteType = 'multiple';

        $this->categoriesWithProducts = Category::whereIn('id', $this->selected)->has('products')->count();
        $this->hasProductsWarning = $this->categoriesWithProducts > 0;

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
        $this->reset(['itemToDeleteId', 'itemToDeleteName', 'deleteType', 'hasProductsWarning', 'categoriesWithProducts']);
    }

    public function delete($id)
    {
        $category = Category::findOrFail($id);
        if ($category->products()->count() > 0) {
            session()->flash('error', 'Kategori tidak bisa dihapus karena masih memiliki produk.');
            return;
        }
        ActivityLogger::crud('category_deleted', 'category', $id, ['name' => $category->name]);
        $category->delete();
        session()->flash('message', 'Kategori berhasil dihapus.');
    }

    public function render()
    {
        $categories = Category::withCount('products')
            ->when($this->search, fn ($q) => $q->where('name', 'like', "%{$this->search}%"))
            ->orderBy('sort_order', 'asc')
            ->get();

        return view('livewire.category-manager', ['categories' => $categories]);
    }
}
