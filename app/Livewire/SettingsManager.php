<?php

namespace App\Livewire;

use App\Models\Setting;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;
use App\Services\ActivityLogger;

#[Layout('layouts.app')]
#[Title('Pengaturan')]
class SettingsManager extends Component
{
    use WithFileUploads;

    public $store_name = '';
    public $store_address = '';
    public $store_phone = '';
    public $tax_enabled = false;
    public $tax_rate = 11;
    public $receipt_footer = '';

    // Social Media
    public $social_instagram = '';
    public $social_tiktok = '';
    public $social_facebook = '';
    public $social_youtube = '';

    // Logo
    public $logo = null;       // File upload temporary
    public $currentLogo = '';   // Existing logo path

    public function mount()
    {
        $this->store_name = Setting::get('store_name', 'My POS');
        $this->store_address = Setting::get('store_address', '');
        $this->store_phone = Setting::get('store_phone', '');
        $this->tax_enabled = (bool) Setting::get('tax_enabled', '0');
        $this->tax_rate = (float) Setting::get('tax_rate', '11');
        $this->receipt_footer = Setting::get('receipt_footer', 'Terima Kasih!');

        // Social Media
        $this->social_instagram = Setting::get('social_instagram', '');
        $this->social_tiktok = Setting::get('social_tiktok', '');
        $this->social_facebook = Setting::get('social_facebook', '');
        $this->social_youtube = Setting::get('social_youtube', '');

        // Logo
        $this->currentLogo = Setting::get('store_logo', '');
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
            'social_instagram' => 'nullable|url|max:500',
            'social_tiktok' => 'nullable|url|max:500',
            'social_facebook' => 'nullable|url|max:500',
            'social_youtube' => 'nullable|url|max:500',
            'logo' => 'nullable|image|mimes:png,jpg,jpeg,svg|max:1024',
        ]);

        Setting::set('store_name', $this->store_name, 'general');
        Setting::set('store_address', $this->store_address, 'general');
        Setting::set('store_phone', $this->store_phone, 'general');
        Setting::set('tax_enabled', $this->tax_enabled ? '1' : '0', 'tax');
        Setting::set('tax_rate', (string) $this->tax_rate, 'tax');
        Setting::set('receipt_footer', $this->receipt_footer, 'receipt');

        // Social Media
        Setting::set('social_instagram', $this->social_instagram ?? '', 'social');
        Setting::set('social_tiktok', $this->social_tiktok ?? '', 'social');
        Setting::set('social_facebook', $this->social_facebook ?? '', 'social');
        Setting::set('social_youtube', $this->social_youtube ?? '', 'social');

        // Logo Upload
        if ($this->logo) {
            // Delete old logo
            if ($this->currentLogo && Storage::disk('public')->exists($this->currentLogo)) {
                Storage::disk('public')->delete($this->currentLogo);
            }

            $path = $this->logo->store('logos', 'public');
            Setting::set('store_logo', $path, 'general');
            $this->currentLogo = $path;
            $this->logo = null;
            ActivityLogger::settings('logo_updated', ['path' => $path]);
        }

        ActivityLogger::settings('settings_updated', [
            'store_name' => $this->store_name,
            'tax_enabled' => $this->tax_enabled,
        ]);

        session()->flash('message', 'Pengaturan berhasil disimpan.');
    }

    public function removeLogo()
    {
        if ($this->currentLogo && Storage::disk('public')->exists($this->currentLogo)) {
            Storage::disk('public')->delete($this->currentLogo);
        }

        Setting::set('store_logo', '', 'general');
        $this->currentLogo = '';
        $this->logo = null;

        ActivityLogger::settings('logo_removed');
        
        session()->flash('message', 'Logo berhasil dihapus.');
    }

    public function render()
    {
        return view('livewire.settings-manager');
    }
}
