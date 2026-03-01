<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\Dashboard;
use App\Livewire\PosScreen;
use App\Livewire\ProductManager;
use App\Livewire\CategoryManager;
use App\Livewire\StockManager;
use App\Livewire\StockLogViewer;
use App\Livewire\VoucherManager;
use App\Livewire\ReportManager;
use App\Livewire\UserManager;
use App\Livewire\SettingsManager;

// Redirect home to dashboard
Route::get('/', function () {
    return redirect()->route('dashboard');
})->name('home');

// Cashier & Admin routes (POS)
Route::middleware(['auth', 'verified', 'role:admin,cashier'])->group(function () {
    Route::get('/dashboard', Dashboard::class)->name('dashboard')->middleware('permission:dashboard');
    Route::get('/pos', PosScreen::class)->name('pos.index')->middleware('permission:pos');
    Route::get('/products', ProductManager::class)->name('products.index')->middleware('permission:products');
    Route::get('/categories', CategoryManager::class)->name('categories.index')->middleware('permission:categories');
    Route::get('/stock', StockManager::class)->name('stock.index')->middleware('permission:stock');
    Route::get('/stock/logs', StockLogViewer::class)->name('stock.logs')->middleware('permission:stock');
    Route::get('/vouchers', VoucherManager::class)->name('vouchers.index')->middleware('permission:vouchers');
    Route::get('/reports', ReportManager::class)->name('reports.index')->middleware('permission:reports');
    
    // Print Receipt
    Route::get('/print/receipt/{transaction}', function (\App\Models\Transaction $transaction) {
        $transaction->load(['items.product', 'payment', 'user']);
        return view('print.receipt', compact('transaction'));
    })->name('receipt.print')->middleware('permission:pos');
});

// Admin only routes
Route::middleware(['auth', 'verified', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/users', UserManager::class)->name('users.index');
    Route::get('/settings', SettingsManager::class)->name('settings.index');
});

require __DIR__.'/settings.php';

