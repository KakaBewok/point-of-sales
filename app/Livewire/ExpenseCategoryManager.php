<?php

namespace App\Livewire;

use App\Models\ExpenseCategory;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use App\Services\ActivityLogger;

#[Layout('layouts.app')]
#[Title('Kategori Pengeluaran')]
class ExpenseCategoryManager extends Component
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

    public $hasExpensesWarning = false;
    public $categoriesWithExpenses = 0;

    protected $rules = [
        'name' => 'required|string|max:255',
    ];

    public function create()
    {
        $this->reset(['name', 'editingId']);
        $this->showModal = true;
    }

    public function edit(ExpenseCategory $category)
    {
        $this->editingId = $category->id;
        $this->name = $category->name;
        $this->showModal = true;
    }

    public function save()
    {
        $this->validate();

        // Unique name per store validation
        $exists = ExpenseCategory::where('name', $this->name)
            ->when($this->editingId, fn($q) => $q->where('id', '!=', $this->editingId))
            ->exists();

        if ($exists) {
            $this->addError('name', 'Nama kategori sudah digunakan.');
            return;
        }

        $data = ['name' => $this->name];

        if ($this->editingId) {
            ExpenseCategory::findOrFail($this->editingId)->update($data);
            ActivityLogger::crud('expense_category_updated', 'expense_category', $this->editingId, ['name' => $this->name]);
            session()->flash('message', 'Kategori pengeluaran berhasil diperbarui.');
        } else {
            $cat = ExpenseCategory::create($data);
            ActivityLogger::crud('expense_category_created', 'expense_category', $cat->id, ['name' => $cat->name]);
            session()->flash('message', 'Kategori pengeluaran berhasil ditambahkan.');
        }

        $this->showModal = false;
    }

    public function updatedSelectAll($value)
    {
        if ($value) {
            $this->selected = ExpenseCategory::when($this->search, fn ($q) => $q->where('name', 'like', "%{$this->search}%"))
                                      ->pluck('id')->map(fn($id) => (string) $id)->toArray();
        } else {
            $this->selected = [];
        }
    }

    // ─── Delete Methods ────────────────────────────────────────

    public function confirmDelete($id, $name = '')
    {
        $this->itemToDeleteId = $id;
        $this->itemToDeleteName = $name;
        $this->deleteType = 'single';

        $category = ExpenseCategory::withCount('expenses')->find($id);
        $this->hasExpensesWarning = $category && $category->expenses_count > 0;
        $this->categoriesWithExpenses = $this->hasExpensesWarning ? 1 : 0;

        $this->showDeleteModal = true;
    }

    public function confirmDeleteSelected()
    {
        if (empty($this->selected)) return;

        $this->itemToDeleteName = count($this->selected) . ' kategori terpilih';
        $this->deleteType = 'multiple';

        $this->categoriesWithExpenses = ExpenseCategory::whereIn('id', $this->selected)->has('expenses')->count();
        $this->hasExpensesWarning = $this->categoriesWithExpenses > 0;

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
        $this->reset(['itemToDeleteId', 'itemToDeleteName', 'deleteType', 'hasExpensesWarning', 'categoriesWithExpenses']);
    }

    public function delete($id)
    {
        $category = ExpenseCategory::findOrFail($id);
        if ($category->expenses()->count() > 0) {
            session()->flash('error', 'Kategori tidak bisa dihapus karena masih memiliki data pengeluaran.');
            return;
        }
        ActivityLogger::crud('expense_category_deleted', 'expense_category', $id, ['name' => $category->name]);
        $category->delete();
        session()->flash('message', 'Kategori pengeluaran berhasil dihapus.');
    }

    public function deleteSelected()
    {
        if (empty($this->selected)) return;

        $categoriesToDelete = ExpenseCategory::whereIn('id', $this->selected)->whereDoesntHave('expenses')->pluck('id');

        $notDeletedCount = count($this->selected) - $categoriesToDelete->count();

        if ($categoriesToDelete->count() > 0) {
            ActivityLogger::bulk('expense_category_bulk_deleted', 'expense_category', $categoriesToDelete->toArray());
            ExpenseCategory::whereIn('id', $categoriesToDelete)->delete();
        }

        $this->reset(['selected', 'selectAll']);

        if ($notDeletedCount > 0) {
            session()->flash('warning', "{$categoriesToDelete->count()} kategori dihapus. {$notDeletedCount} kategori gagal dihapus karena masih memiliki data pengeluaran.");
        } else {
            session()->flash('message', 'Kategori terpilih berhasil dihapus.');
        }
    }

    public function render()
    {
        $categories = ExpenseCategory::withCount('expenses')
            ->when($this->search, fn ($q) => $q->where('name', 'like', "%{$this->search}%"))
            ->ordered()
            ->get();

        return view('livewire.expense-category-manager', ['categories' => $categories]);
    }
}
