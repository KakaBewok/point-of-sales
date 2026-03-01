<?php

namespace App\Livewire;

use App\Models\Setting;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Pengaturan')]
class SettingsManager extends Component
{
    public $store_name = '';
    public $store_address = '';
    public $store_phone = '';
    public $tax_enabled = false;
    public $tax_rate = 11;
    public $receipt_footer = '';

    public function mount()
    {
        $this->store_name = Setting::get('store_name', 'My POS');
        $this->store_address = Setting::get('store_address', '');
        $this->store_phone = Setting::get('store_phone', '');
        $this->tax_enabled = (bool) Setting::get('tax_enabled', '0');
        $this->tax_rate = (float) Setting::get('tax_rate', '11');
        $this->receipt_footer = Setting::get('receipt_footer', 'Terima Kasih!');
    }

    public function save()
    {
        $this->validate([
            'store_name' => 'required|string|max:255',
            'store_address' => 'nullable|string',
            'store_phone' => 'nullable|string|max:50',
            'tax_enabled' => 'boolean',
            'tax_rate' => 'required|numeric|min:0|max:100',
            'receipt_footer' => 'nullable|string',
        ]);

        Setting::set('store_name', $this->store_name, 'general');
        Setting::set('store_address', $this->store_address, 'general');
        Setting::set('store_phone', $this->store_phone, 'general');
        Setting::set('tax_enabled', $this->tax_enabled ? '1' : '0', 'tax');
        Setting::set('tax_rate', (string) $this->tax_rate, 'tax');
        Setting::set('receipt_footer', $this->receipt_footer, 'receipt');

        session()->flash('message', 'Pengaturan berhasil disimpan.');
    }

    public function render()
    {
        return view('livewire.settings-manager');
    }
}
