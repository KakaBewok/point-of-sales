<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Modify the enum column to include 'owner'
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin', 'cashier', 'owner') DEFAULT 'cashier'");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Note: Reverting might fail if there are existing users with the 'owner' role.
        // We will just issue the command anyway. You might want to update users first in a real scenario.
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin', 'cashier') DEFAULT 'cashier'");
        }
    }
};
