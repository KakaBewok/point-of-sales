<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            ['name' => 'Makanan', 'slug' => 'makanan', 'description' => 'Aneka makanan dan hidangan', 'sort_order' => 1],
            ['name' => 'Minuman', 'slug' => 'minuman', 'description' => 'Aneka minuman dan beverage', 'sort_order' => 2],
            ['name' => 'Snack', 'slug' => 'snack', 'description' => 'Camilan dan makanan ringan', 'sort_order' => 3],
            ['name' => 'Dessert', 'slug' => 'dessert', 'description' => 'Hidangan penutup dan kue', 'sort_order' => 4],
            ['name' => 'Paket', 'slug' => 'paket', 'description' => 'Paket bundling hemat', 'sort_order' => 5],
            ['name' => 'Lainnya', 'slug' => 'lainnya', 'description' => 'Produk dan jasa lainnya', 'sort_order' => 6],
        ];

        foreach ($categories as $category) {
            Category::create(array_merge($category, ['is_active' => true]));
        }
    }
}
