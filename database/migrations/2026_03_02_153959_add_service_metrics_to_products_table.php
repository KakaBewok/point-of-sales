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
            if (!Schema::hasColumn('products', 'service_duration')) {
                $table->integer('service_duration')->nullable()->comment('Duration in minutes');
            }
            if (!Schema::hasColumn('products', 'is_appointment_ready')) {
                $table->boolean('is_appointment_ready')->default(false);
            }
            if (!Schema::hasColumn('products', 'assigned_staff_id')) {
                $table->foreignId('assigned_staff_id')->nullable()->constrained('users')->nullOnDelete();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropForeign(['assigned_staff_id']);
            $table->dropColumn(['service_duration', 'is_appointment_ready', 'assigned_staff_id']);
        });
    }
};
