<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Create a default store for existing data
        $defaultStoreId = DB::table('stores')->insertGetId([
            'name' => 'Default Store',
            'slug' => 'default-store',
            'subscription_status' => 'active',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 2. Add store_id to users
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('store_id')->nullable()->after('id')->constrained('stores')->cascadeOnDelete();
            $table->index('store_id');
        });
        DB::table('users')->whereNull('store_id')->update(['store_id' => $defaultStoreId]);

        // 3. Add store_id to categories
        Schema::table('categories', function (Blueprint $table) {
            $table->foreignId('store_id')->nullable()->after('id')->constrained('stores')->cascadeOnDelete();
            $table->index('store_id');
        });
        DB::table('categories')->whereNull('store_id')->update(['store_id' => $defaultStoreId]);

        // 4. Add store_id to products
        Schema::table('products', function (Blueprint $table) {
            $table->foreignId('store_id')->nullable()->after('id')->constrained('stores')->cascadeOnDelete();
            $table->index('store_id');
        });
        DB::table('products')->whereNull('store_id')->update(['store_id' => $defaultStoreId]);

        // 5. Add store_id to vouchers
        Schema::table('vouchers', function (Blueprint $table) {
            $table->foreignId('store_id')->nullable()->after('id')->constrained('stores')->cascadeOnDelete();
            $table->index('store_id');
        });
        DB::table('vouchers')->whereNull('store_id')->update(['store_id' => $defaultStoreId]);

        // 6. Add store_id to transactions
        Schema::table('transactions', function (Blueprint $table) {
            $table->foreignId('store_id')->nullable()->after('id')->constrained('stores')->cascadeOnDelete();
            $table->index(['store_id', 'created_at']);
        });
        DB::table('transactions')->whereNull('store_id')->update(['store_id' => $defaultStoreId]);

        // 7. Add store_id to transaction_items
        Schema::table('transaction_items', function (Blueprint $table) {
            $table->foreignId('store_id')->nullable()->after('id')->constrained('stores')->cascadeOnDelete();
            $table->index('store_id');
        });
        DB::table('transaction_items')->whereNull('store_id')->update(['store_id' => $defaultStoreId]);

        // 8. Add store_id to payments
        Schema::table('payments', function (Blueprint $table) {
            $table->foreignId('store_id')->nullable()->after('id')->constrained('stores')->cascadeOnDelete();
            $table->index('store_id');
        });
        DB::table('payments')->whereNull('store_id')->update(['store_id' => $defaultStoreId]);

        // 9. Add store_id to settings (with unique composite key)
        Schema::table('settings', function (Blueprint $table) {
            $table->foreignId('store_id')->nullable()->after('id')->constrained('stores')->cascadeOnDelete();
            $table->unique(['store_id', 'key']);
        });
        DB::table('settings')->whereNull('store_id')->update(['store_id' => $defaultStoreId]);

        // 10. Add store_id to stock_logs
        Schema::table('stock_logs', function (Blueprint $table) {
            $table->foreignId('store_id')->nullable()->after('id')->constrained('stores')->cascadeOnDelete();
            $table->index('store_id');
        });
        DB::table('stock_logs')->whereNull('store_id')->update(['store_id' => $defaultStoreId]);
    }

    public function down(): void
    {
        $tables = [
            'users', 'categories', 'products', 'vouchers',
            'transactions', 'transaction_items', 'payments',
            'settings', 'stock_logs',
        ];

        foreach ($tables as $tableName) {
            Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                // Drop foreign key and column
                $table->dropForeign([$tableName === 'settings' ? 'store_id' : 'store_id']);
                $table->dropColumn('store_id');
            });
        }

        // Remove the default store
        DB::table('stores')->where('slug', 'default-store')->delete();
    }
};
