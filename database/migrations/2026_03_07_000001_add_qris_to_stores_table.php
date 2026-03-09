<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('stores', function (Blueprint $table) {
            $table->string('qris_image_path')->nullable()->after('trial_ends_at');
            $table->text('qris_payload')->nullable()->after('qris_image_path');
        });
    }

    public function down(): void
    {
        Schema::table('stores', function (Blueprint $table) {
            $table->dropColumn(['qris_image_path', 'qris_payload']);
        });
    }
};
