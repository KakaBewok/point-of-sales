<?php

namespace App\Livewire;

use App\Models\Payment;
use App\Models\Product;
use App\Models\Transaction;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Dashboard')]
class Dashboard extends Component
{
    public function render()
    {
        // Today's stats
        $todayRevenue = Transaction::completed()->today()
            ->sum('grand_total');

        $todayTransactions = Transaction::completed()->today()->count();

        // Monthly stats
        $monthlyRevenue = Transaction::completed()->thisMonth()
            ->sum('grand_total');

        $monthlyTransactions = Transaction::completed()->thisMonth()->count();

        // Product stats
        $totalProducts = Product::active()->count();
        $lowStockProducts = Product::active()->lowStock()->get();
        $outOfStockProducts = Product::active()->outOfStock()->count();

        // Best selling products (this month)
        $bestSelling = \App\Models\TransactionItem::query()
            ->select('product_id', 'product_name')
            ->selectRaw('SUM(quantity) as total_sold')
            ->selectRaw('SUM(subtotal) as total_revenue')
            ->whereHas('transaction', function ($q) {
                $q->completed()->thisMonth();
            })
            ->groupBy('product_id', 'product_name')
            ->orderByDesc('total_sold')
            ->limit(5)
            ->get();

        // Recent transactions
        $recentTransactions = Transaction::with(['user', 'payment'])
            ->completed()
            ->orderByDesc('created_at')
            ->limit(10)
            ->get();

        // Revenue chart data (last 7 days)
        // $chartData = collect();
        // for ($i = 6; $i >= 0; $i--) {
        //     $date = now()->subDays($i);
        //     $revenue = Transaction::completed()
        //         ->whereDate('created_at', $date)
        //         ->sum('grand_total');
        //     $chartData->push([
        //         'date' => $date->format('d M'),
        //         'revenue' => (float) $revenue,
        //     ]);
        // }

        // 1. Get data from 7 days ago to now in one query
        $rawChartData = Transaction::completed()
            ->where('created_at', '>=', now()->subDays(6)->startOfDay())
            ->selectRaw('DATE(created_at) as date, SUM(grand_total) as revenue')
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->keyBy('date'); // Change index to date for easier lookup

        // 2. Prepare final result (to ensure empty dates are still displayed)
        $chartData = collect();

        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $displayDate = now()->subDays($i)->format('d M');

            // Check if there is data for this date in the query result
            $revenue = $rawChartData->has($date) ? $rawChartData[$date]->revenue : 0;

            $chartData->push([
                'date' => $displayDate,
                'revenue' => (float) $revenue,
            ]);
        }

        return view('livewire.dashboard', [
            'todayRevenue' => $todayRevenue,
            'todayTransactions' => $todayTransactions,
            'monthlyRevenue' => $monthlyRevenue,
            'monthlyTransactions' => $monthlyTransactions,
            'totalProducts' => $totalProducts,
            'lowStockProducts' => $lowStockProducts,
            'outOfStockProducts' => $outOfStockProducts,
            'bestSelling' => $bestSelling,
            'recentTransactions' => $recentTransactions,
            'chartData' => $chartData,
        ]);
    }
}
