<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->dropUnique('settings_key_unique');
            // Note: unique(['store_id', 'key']) was already added in 2026_03_05_000002
        });

        Schema::table('categories', function (Blueprint $table) {
            $table->dropUnique('categories_name_unique');
            $table->dropUnique('categories_slug_unique');
            $table->unique(['store_id', 'name']);
            $table->unique(['store_id', 'slug']);
        });

        Schema::table('products', function (Blueprint $table) {
            $table->dropUnique('products_slug_unique');
            $table->dropUnique('products_sku_unique');
            $table->unique(['store_id', 'slug']);
            $table->unique(['store_id', 'sku']);
        });

        Schema::table('vouchers', function (Blueprint $table) {
            $table->dropUnique('vouchers_code_unique');
            $table->unique(['store_id', 'code']);
        });

        Schema::table('transactions', function (Blueprint $table) {
            $table->dropUnique('transactions_invoice_number_unique');
            $table->unique(['store_id', 'invoice_number']);
        });
    }

    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->unique('key');
        });

        Schema::table('categories', function (Blueprint $table) {
            $table->dropUnique(['store_id', 'name']);
            $table->dropUnique(['store_id', 'slug']);
            $table->unique('name');
            $table->unique('slug');
        });

        Schema::table('products', function (Blueprint $table) {
            $table->dropUnique(['store_id', 'slug']);
            $table->dropUnique(['store_id', 'sku']);
            $table->unique('slug');
            $table->unique('sku');
        });

        Schema::table('vouchers', function (Blueprint $table) {
            $table->dropUnique(['store_id', 'code']);
            $table->unique('code');
        });

        Schema::table('transactions', function (Blueprint $table) {
            $table->dropUnique(['store_id', 'invoice_number']);
            $table->unique('invoice_number');
        });
    }
};
