<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            if (!Schema::hasColumn('products', 'type')) {
                $table->string('type')->default('product')->after('id');
            }
            $table->integer('stock')->nullable()->change();
            $table->decimal('cost_price', 15, 2)->nullable()->change();
            $table->integer('low_stock_threshold')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            if (Schema::hasColumn('products', 'type')) {
                $table->dropColumn('type');
            }
            $table->integer('stock')->nullable(false)->default(0)->change();
            $table->decimal('cost_price', 15, 2)->nullable(false)->default(0)->change();
            $table->integer('low_stock_threshold')->nullable(false)->default(0)->change();
        });
    }
};
