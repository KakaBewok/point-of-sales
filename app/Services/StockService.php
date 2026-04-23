<?php

namespace App\Services;

use App\Models\Product;
use App\Models\StockLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StockService
{
    /**
     * Reduce stock after a sale.
     */
    public function reduceStock(Product $product, int $quantity, string $reference = ''): StockLog
    {
        return DB::transaction(function () use ($product, $quantity, $reference) {
            // Lock the product row to prevent race conditions
            $product = Product::lockForUpdate()->find($product->id);

            $stockBefore = $product->stock;
            $stockAfter = $stockBefore - $quantity;

            if ($stockAfter < 0) {
                throw new \Exception("Stock {$product->name} is not enough. Available: {$stockBefore}, requested: {$quantity}");
            }

            $product->update(['stock' => $stockAfter]);

            return StockLog::create([
                'product_id' => $product->id,
                'user_id' => Auth::id(),
                'type' => 'sale',
                'quantity' => $quantity,
                'stock_before' => $stockBefore,
                'stock_after' => $stockAfter,
                'reference' => $reference,
                'notes' => "Penjualan: {$quantity} unit/pcs",
            ]);
        });
    }

    /**
     * Add stock (restock / return).
     */
    public function addStock(Product $product, int $quantity, string $type = 'in', string $notes = ''): StockLog
    {
        return DB::transaction(function () use ($product, $quantity, $type, $notes) {
            $product = Product::lockForUpdate()->find($product->id);

            $stockBefore = $product->stock;
            $stockAfter = $stockBefore + $quantity;

            $product->update(['stock' => $stockAfter]);

            return StockLog::create([
                'product_id' => $product->id,
                'user_id' => Auth::id(),
                'type' => $type,
                'quantity' => $quantity,
                'stock_before' => $stockBefore,
                'stock_after' => $stockAfter,
                'notes' => $notes ?: "Stok masuk: {$quantity} unit",
            ]);
        });
    }

    /**
     * Manual stock adjustment (set stock to specific value).
     */
    public function adjustStock(Product $product, int $newStock, string $notes = ''): StockLog
    {
        return DB::transaction(function () use ($product, $newStock, $notes) {
            $product = Product::lockForUpdate()->find($product->id);

            $stockBefore = $product->stock;
            $quantity = abs($newStock - $stockBefore);

            $product->update(['stock' => $newStock]);

            return StockLog::create([
                'product_id' => $product->id,
                'user_id' => Auth::id(),
                'type' => 'adjustment',
                'quantity' => $quantity,
                'stock_before' => $stockBefore,
                'stock_after' => $newStock,
                'notes' => $notes ?: "Penyesuaian stok: {$stockBefore} → {$newStock}",
            ]);
        });
    }

    /**
     * Return stock from a cancelled transaction.
     */
    public function returnStock(Product $product, int $quantity, string $reference = ''): StockLog
    {
        return $this->addStock($product, $quantity, 'return', "Retur: {$quantity} unit. Ref: {$reference}");
    }

    /**
     * Get products with low stock.
     */
    public function getLowStockProducts()
    {
        return Product::active()
            ->whereColumn('stock', '<=', 'low_stock_threshold')
            ->orderBy('stock')
            ->get();
    }

    /**
     * Get products that are out of stock.
     */
    public function getOutOfStockProducts()
    {
        return Product::active()
            ->where('stock', '<=', 0)
            ->get();
    }
}
