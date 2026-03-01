<?php

namespace App\Livewire;

use App\Exports\TransactionReportExport;
use App\Models\Transaction;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
#[Title('Laporan')]
class ReportManager extends Component
{
    use WithPagination;

    public $startDate;
    public $endDate;
    public $paymentMethod = '';

    public function mount()
    {
        // Default to this month
        $this->startDate = now()->startOfMonth()->format('Y-m-d');
        $this->endDate = now()->endOfMonth()->format('Y-m-d');
    }

    public function updatingStartDate() { $this->resetPage(); }
    public function updatingEndDate() { $this->resetPage(); }
    public function updatingPaymentMethod() { $this->resetPage(); }

    public function exportExcel()
    {
        $filename = 'laporan-transaksi-' . $this->startDate . '-to-' . $this->endDate . '.xlsx';
        return \Maatwebsite\Excel\Facades\Excel::download(
            new TransactionReportExport($this->startDate, $this->endDate), 
            $filename
        );
    }

    public function render()
    {
        $query = Transaction::query()
            ->with(['user', 'payment', 'items'])
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

        // Summary calculations (without pagination limit)
        $summaryQuery = clone $query;
        $totalRevenue = $summaryQuery->sum('grand_total');
        $totalTransactions = $summaryQuery->count();
        $totalTax = $summaryQuery->sum('tax_amount');
        $totalDiscounts = $summaryQuery->sum('discount_amount');

        $transactions = $query->paginate(20);

        return view('livewire.report-manager', [
            'transactions' => $transactions,
            'totalRevenue' => $totalRevenue,
            'totalTransactions' => $totalTransactions,
            'totalTax' => $totalTax,
            'totalDiscounts' => $totalDiscounts,
        ]);
    }
}
