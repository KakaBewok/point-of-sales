<?php

namespace App\Livewire;

use App\Exports\CashierPerformanceExport;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Performa Kasir')]
class CashierPerformance extends Component
{
    public $startDate;
    public $endDate;
    public $cashierId = '';
    public $sortField = 'total_revenue';
    public $sortDirection = 'desc';

    public function mount()
    {
        // Default to today
        $this->startDate = now()->format('Y-m-d');
        $this->endDate = now()->format('Y-m-d');
    }

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'desc';
        }
    }

    public function exportExcel()
    {
        $filename = 'performa-kasir-' . $this->startDate . '-to-' . $this->endDate . '.xlsx';
        return \Maatwebsite\Excel\Facades\Excel::download(
            new CashierPerformanceExport($this->startDate, $this->endDate, $this->cashierId),
            $filename
        );
    }

    protected function buildQuery()
    {
        $query = Transaction::query()
            ->select(
                'user_id',
                DB::raw('COUNT(*) as total_transactions'),
                DB::raw('SUM(grand_total) as total_revenue'),
                DB::raw('AVG(grand_total) as avg_transaction'),
                DB::raw('MAX(created_at) as last_transaction_at')
            )
            ->where('status', 'completed')
            ->groupBy('user_id');

        if ($this->startDate) {
            $query->whereDate('created_at', '>=', $this->startDate);
        }

        if ($this->endDate) {
            $query->whereDate('created_at', '<=', $this->endDate);
        }

        if ($this->cashierId) {
            $query->where('user_id', $this->cashierId);
        }

        // Apply sorting
        $allowedSorts = ['total_transactions', 'total_revenue', 'avg_transaction', 'last_transaction_at'];
        if (in_array($this->sortField, $allowedSorts)) {
            $query->orderBy($this->sortField, $this->sortDirection);
        } else {
            $query->orderByDesc('total_revenue');
        }

        return $query;
    }

    public function render()
    {
        $performanceData = $this->buildQuery()->get();

        // Eager load cashier names
        $cashierIds = $performanceData->pluck('user_id')->toArray();
        $cashiers = User::whereIn('id', $cashierIds)->pluck('name', 'id');

        // Map cashier names to performance data
        $performanceData = $performanceData->map(function ($item) use ($cashiers) {
            $item->cashier_name = $cashiers[$item->user_id] ?? 'Unknown';
            return $item;
        });

        // Summary stats
        $totalRevenue = $performanceData->sum('total_revenue');
        $totalTransactions = $performanceData->sum('total_transactions');

        // Available cashiers for dropdown (only those with completed transactions)
        $availableCashiers = User::whereHas('transactions', function ($q) {
            $q->where('status', 'completed');
        })->orderBy('name')->get(['id', 'name']);

        return view('livewire.cashier-performance', [
            'performanceData' => $performanceData,
            'totalRevenue' => $totalRevenue,
            'totalTransactions' => $totalTransactions,
            'availableCashiers' => $availableCashiers,
        ]);
    }
}
