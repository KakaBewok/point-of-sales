<?php

namespace App\Livewire;

use App\Exports\TransactionReportExport;
use App\Models\Category;
use App\Models\Transaction;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;
use App\Services\ActivityLogger;

#[Layout('layouts.app')]
#[Title('Laporan')]
class ReportManager extends Component
{
    use WithPagination;

    public $startDate;
    public $endDate;
    public $paymentMethod = '';
    public $categoryId = '';

    public $showDeleteModal = false;
    public $itemToDeleteId = null;
    public $itemToDeleteName = null;
    public $deleteType = 'single';

    public $selected = [];
    public $selectAll = false;

    public function mount()
    {
        // Default to this month
        $this->startDate = now()->startOfMonth()->format('Y-m-d');
        $this->endDate = now()->endOfMonth()->format('Y-m-d');
    }

    public function updatingStartDate() { $this->resetPage(); }
    public function updatingEndDate() { $this->resetPage(); }
    public function updatingPaymentMethod() { $this->resetPage(); }
    public function updatingCategoryId() { $this->resetPage(); }

    public function updatedSelectAll($value)
    {
        if ($value) {
            $this->selected = $this->buildQuery()->pluck('id')->map(fn($id) => (string) $id)->toArray();
        } else {
            $this->selected = [];
        }
    }

    public function exportExcel()
    {
        $filename = 'laporan-transaksi-' . $this->startDate . '-to-' . $this->endDate . '.xlsx';
        return \Maatwebsite\Excel\Facades\Excel::download(
            new TransactionReportExport($this->startDate, $this->endDate), 
            $filename
        );
    }

    // ─── Delete Methods ────────────────────────────────────────

    public function confirmDelete($id, $invoice = '')
    {
        $this->itemToDeleteId = $id;
        $this->itemToDeleteName = $invoice;
        $this->deleteType = 'single';
        $this->showDeleteModal = true;
    }

    public function confirmDeleteSelected()
    {
        if (empty($this->selected)) return;

        $this->itemToDeleteName = count($this->selected) . ' transaksi terpilih';
        $this->deleteType = 'multiple';
        $this->showDeleteModal = true;
    }

    public function processDelete()
    {
        if ($this->deleteType === 'single' && $this->itemToDeleteId) {
            $transaction = Transaction::find($this->itemToDeleteId);
            if ($transaction) {
                ActivityLogger::crud('transaction_deleted', 'transaction', $transaction->id, ['invoice' => $transaction->invoice_number]);
                $transaction->delete(); // Soft delete
                session()->flash('message', 'Transaksi berhasil dihapus.');
            }
        } elseif ($this->deleteType === 'multiple') {
            $this->deleteSelected();
        }

        $this->showDeleteModal = false;
        $this->reset(['itemToDeleteId', 'itemToDeleteName', 'deleteType']);
    }

    public function deleteSelected()
    {
        if (empty($this->selected)) return;

        ActivityLogger::bulk('transaction_bulk_deleted', 'transaction', $this->selected);

        // Chunk deletion for safety with large selections
        collect($this->selected)->chunk(100)->each(function ($chunk) {
            Transaction::whereIn('id', $chunk)->delete();
        });

        $deletedCount = count($this->selected);
        $this->reset(['selected', 'selectAll']);
        session()->flash('message', "{$deletedCount} transaksi berhasil dihapus.");
    }

    // ─── Query Builder ─────────────────────────────────────────

    protected function buildQuery()
    {
        $query = Transaction::query()
            ->with(['user', 'payment', 'items.product.category'])
            ->where('status', 'completed')
            ->orderByDesc('created_at');

        if ($this->startDate) {
            $query->whereDate('created_at', '>=', $this->startDate);
        }

        if ($this->endDate) {
            $query->whereDate('created_at', '<=', $this->endDate);
        }

        if ($this->paymentMethod) {
            $query->whereHas('payment', fn($q) => $q->where('method', $this->paymentMethod));
        }

        if ($this->categoryId) {
            $query->whereHas('items.product', fn($q) => $q->where('category_id', $this->categoryId));
        }

        return $query;
    }

    public function render()
    {
        $query = $this->buildQuery();

        // Summary calculations (without pagination limit)
        $summaryQuery = clone $query;
        $totalRevenue = $summaryQuery->sum('grand_total');
        $totalTransactions = $summaryQuery->count();
        $totalTax = $summaryQuery->sum('tax_amount');
        $totalDiscounts = $summaryQuery->sum('discount_amount');

        $transactions = $query->paginate(20);

        $categories = Category::active()->ordered()->get();

        return view('livewire.report-manager', [
            'transactions' => $transactions,
            'totalRevenue' => $totalRevenue,
            'totalTransactions' => $totalTransactions,
            'totalTax' => $totalTax,
            'totalDiscounts' => $totalDiscounts,
            'categories' => $categories,
        ]);
    }
}
