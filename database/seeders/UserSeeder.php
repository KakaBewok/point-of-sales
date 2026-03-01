<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Admin user
        User::factory()->create([
            'name' => 'Administrator',
            'email' => 'admin@pos.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        // Cashier user
        User::factory()->create([
            'name' => 'Kasir 1',
            'email' => 'cashier@pos.com',
            'password' => Hash::make('password'),
            'role' => 'cashier',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        // Additional cashier
        User::factory()->create([
            'name' => 'Kasir 2',
            'email' => 'cashier2@pos.com',
            'password' => Hash::make('password'),
            'role' => 'cashier',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);
    }
}
