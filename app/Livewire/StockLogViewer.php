<?php

namespace App\Livewire;

use App\Models\StockLog;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
#[Title('Riwayat Stok')]
class StockLogViewer extends Component
{
    use WithPagination;

    public $search = '';
    public $typeFilter = '';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingTypeFilter()
    {
        $this->resetPage();
    }

    public function render()
    {
        $logs = StockLog::with('product', 'user')
            ->whereHas('product', fn ($q) => $q->where('type', 'product'))
            ->when($this->search, fn ($q) => $q->whereHas('product', fn ($q2) => $q2->where('name', 'like', "%{$this->search}%")))
            ->when($this->typeFilter, fn ($q) => $q->where('type', $this->typeFilter))
            ->orderByDesc('created_at')
            ->paginate(20);

        return view('livewire.stock-log-viewer', ['logs' => $logs]);
    }
}
