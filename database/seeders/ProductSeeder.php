<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = Category::all()->keyBy('slug');

        $products = [
            // Makanan
            ['category' => 'makanan', 'name' => 'Nasi Goreng Spesial', 'sku' => 'MKN-001', 'price' => 25000, 'cost_price' => 12000, 'stock' => 100],
            ['category' => 'makanan', 'name' => 'Mie Goreng', 'sku' => 'MKN-002', 'price' => 22000, 'cost_price' => 10000, 'stock' => 100],
            ['category' => 'makanan', 'name' => 'Ayam Geprek', 'sku' => 'MKN-003', 'price' => 28000, 'cost_price' => 14000, 'stock' => 80],
            ['category' => 'makanan', 'name' => 'Nasi Ayam Bakar', 'sku' => 'MKN-004', 'price' => 32000, 'cost_price' => 16000, 'stock' => 60],
            ['category' => 'makanan', 'name' => 'Sate Ayam (10 tusuk)', 'sku' => 'MKN-005', 'price' => 30000, 'cost_price' => 15000, 'stock' => 50],

            // Minuman
            ['category' => 'minuman', 'name' => 'Es Teh Manis', 'sku' => 'MNM-001', 'price' => 5000, 'cost_price' => 1500, 'stock' => 200],
            ['category' => 'minuman', 'name' => 'Es Jeruk', 'sku' => 'MNM-002', 'price' => 8000, 'cost_price' => 3000, 'stock' => 150],
            ['category' => 'minuman', 'name' => 'Kopi Susu', 'sku' => 'MNM-003', 'price' => 15000, 'cost_price' => 5000, 'stock' => 100],
            ['category' => 'minuman', 'name' => 'Jus Alpukat', 'sku' => 'MNM-004', 'price' => 18000, 'cost_price' => 7000, 'stock' => 80],
            ['category' => 'minuman', 'name' => 'Air Mineral', 'sku' => 'MNM-005', 'price' => 4000, 'cost_price' => 2000, 'stock' => 300],

            // Snack
            ['category' => 'snack', 'name' => 'Kentang Goreng', 'sku' => 'SNK-001', 'price' => 15000, 'cost_price' => 6000, 'stock' => 100],
            ['category' => 'snack', 'name' => 'Pisang Goreng', 'sku' => 'SNK-002', 'price' => 12000, 'cost_price' => 5000, 'stock' => 80],
            ['category' => 'snack', 'name' => 'Onion Ring', 'sku' => 'SNK-003', 'price' => 18000, 'cost_price' => 7000, 'stock' => 60],
            ['category' => 'snack', 'name' => 'Dimsum (5 pcs)', 'sku' => 'SNK-004', 'price' => 20000, 'cost_price' => 8000, 'stock' => 50],

            // Dessert
            ['category' => 'dessert', 'name' => 'Es Krim Vanilla', 'sku' => 'DSR-001', 'price' => 12000, 'cost_price' => 4000, 'stock' => 100],
            ['category' => 'dessert', 'name' => 'Brownies', 'sku' => 'DSR-002', 'price' => 15000, 'cost_price' => 6000, 'stock' => 40],
            ['category' => 'dessert', 'name' => 'Pudding Coklat', 'sku' => 'DSR-003', 'price' => 10000, 'cost_price' => 4000, 'stock' => 60],

            // Paket
            ['category' => 'paket', 'name' => 'Paket Hemat A (Nasi + Ayam + Teh)', 'sku' => 'PKT-001', 'price' => 35000, 'cost_price' => 18000, 'stock' => 50],
            ['category' => 'paket', 'name' => 'Paket Hemat B (Mie + Snack + Jeruk)', 'sku' => 'PKT-002', 'price' => 38000, 'cost_price' => 20000, 'stock' => 50],

            // Lainnya
            ['category' => 'lainnya', 'name' => 'Tisu Basah', 'sku' => 'LN-001', 'price' => 3000, 'cost_price' => 1500, 'stock' => 500],
            ['category' => 'lainnya', 'name' => 'Plastik Bungkus', 'sku' => 'LN-002', 'price' => 1000, 'cost_price' => 500, 'stock' => 1000],
        ];

        foreach ($products as $product) {
            $categorySlug = $product['category'];
            unset($product['category']);

            Product::create(array_merge($product, [
                'category_id' => $categories[$categorySlug]->id,
                'slug' => \Illuminate\Support\Str::slug($product['name']),
                'low_stock_threshold' => 10,
                'is_active' => true,
            ]));
        }
    }
}
