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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('transaction_id')->constrained()->cascadeOnDelete();
            $table->enum('method', ['cash', 'qris', 'va']);
            $table->decimal('amount', 14, 2);
            $table->decimal('cash_received', 14, 2)->nullable();
            $table->decimal('change_amount', 14, 2)->nullable();
            $table->enum('status', ['pending', 'success', 'failed', 'expired'])->default('pending');
            $table->string('midtrans_transaction_id')->nullable();
            $table->string('midtrans_order_id')->nullable()->index();
            $table->json('midtrans_response')->nullable();
            $table->string('va_number')->nullable();
            $table->text('qris_url')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();

            $table->index('transaction_id');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
