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
        $chartData = collect();
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $revenue = Transaction::completed()
                ->whereDate('created_at', $date)
                ->sum('grand_total');
            $chartData->push([
                'date' => $date->format('d M'),
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
