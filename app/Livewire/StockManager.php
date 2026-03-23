<?php

namespace App\Livewire;

use App\Models\Product;
use App\Models\StockLog;
use App\Services\StockService;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
#[Title('Stok')]
class StockManager extends Component
{
    use WithPagination;

    public $search = '';
    public $filterType = ''; // all, low_stock, out_of_stock
    
    public $showAdjustmentModal = false;
    public $selectedProduct = null;
    public $adjustmentType = 'add'; // add, reduce, set
    public $adjustmentQuantity = 0;
    public $adjustmentNotes = '';

    protected $rules = [
        'adjustmentQuantity' => 'required|integer|min:1',
        'adjustmentNotes' => 'nullable|string|max:255',
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingFilterType()
    {
        $this->resetPage();
    }

    public function openAdjustmentModal(int $productId)
    {
        $this->selectedProduct = Product::findOrFail($productId);
        $this->adjustmentType = 'add';
        $this->adjustmentQuantity = 0;
        $this->adjustmentNotes = '';
        $this->showAdjustmentModal = true;
    }

    public function processAdjustment(StockService $stockService)
    {
        $this->validate();

        try {
            if ($this->adjustmentType === 'add') {
                $stockService->addStock(
                    $this->selectedProduct,
                    $this->adjustmentQuantity,
                    'in',
                    $this->adjustmentNotes ?: 'Penambahan stok manual'
                );
            } elseif ($this->adjustmentType === 'reduce') {
                $stockService->reduceStock(
                    $this->selectedProduct,
                    $this->adjustmentQuantity,
                    $this->adjustmentNotes ?: 'Pengurangan stok manual'
                );
            } elseif ($this->adjustmentType === 'set') {
                $stockService->adjustStock(
                    $this->selectedProduct,
                    $this->adjustmentQuantity,
                    $this->adjustmentNotes ?: 'Penyesuaian stok manual'
                );
            }

            session()->flash('message', 'Stok berhasil diperbarui.');
            $this->showAdjustmentModal = false;
            
        } catch (\Exception $e) {
            session()->flash('error', $e->getMessage());
        }
    }

    public function render()
    {
        $products = Product::isProduct()
            ->when($this->search, fn ($q) => $q->where('name', 'like', "%{$this->search}%")->orWhere('sku', 'like', "%{$this->search}%"))
            ->when($this->filterType === 'low_stock', fn ($q) => $q->lowStock())
            ->when($this->filterType === 'out_of_stock', fn ($q) => $q->outOfStock())
            ->when($this->filterType === 'active', fn ($q) => $q->active())
            ->orderBy('name')
            ->paginate(15);

        // Recent logs - Filtered to only show logs for existing (non-deleted) products
        $recentLogs = StockLog::query()
            ->whereHas('product') // This automatically excludes soft-deleted products
            ->with(['product', 'user'])
            ->orderByDesc('created_at')
            ->limit(10)
            ->get();

        return view('livewire.stock-manager', [
            'products' => $products,
            'recentLogs' => $recentLogs,
        ]);
    }
}
