<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            // Store info
            ['key' => 'store_name', 'value' => 'POS Store', 'group' => 'store'],
            ['key' => 'store_address', 'value' => 'Jl. Contoh No. 123, Jakarta', 'group' => 'store'],
            ['key' => 'store_phone', 'value' => '021-1234567', 'group' => 'store'],
            ['key' => 'store_email', 'value' => 'info@posstore.com', 'group' => 'store'],

            // Tax
            ['key' => 'tax_enabled', 'value' => '1', 'group' => 'tax'],
            ['key' => 'tax_rate', 'value' => '11', 'group' => 'tax'],
            ['key' => 'tax_name', 'value' => 'PPN', 'group' => 'tax'],

            // Receipt / Printer
            ['key' => 'receipt_header', 'value' => 'Terima Kasih Atas Kunjungan Anda', 'group' => 'receipt'],
            ['key' => 'receipt_footer', 'value' => 'Barang yang sudah dibeli tidak dapat dikembalikan', 'group' => 'receipt'],
            ['key' => 'printer_type', 'value' => '80mm', 'group' => 'receipt'],

            // Payment
            ['key' => 'midtrans_is_production', 'value' => '0', 'group' => 'payment'],
        ];

        foreach ($settings as $setting) {
            Setting::create($setting);
        }
    }
}
