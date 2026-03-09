<?php

namespace App\Livewire;

use App\Models\Setting;
use App\Services\QrisService;
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

    // QRIS
    public $qrisImage = null;           // File upload temporary
    public $currentQrisImage = '';       // Stored image path
    public $currentQrisPayload = '';     // Stored raw EMVCo payload

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

        // QRIS – loaded from the Store record
        $store = auth()->user()->store ?? null;
        if ($store) {
            $this->currentQrisImage   = $store->qris_image_path ?? '';
            $this->currentQrisPayload = $store->qris_payload ?? '';
        }
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

    // ─── QRIS ────────────────────────────────────────────────────

    public function saveQris()
    {
        $this->validate([
            'qrisImage' => 'required|image|mimes:jpg,jpeg,png|max:2048',
        ], [
            'qrisImage.required' => 'Pilih file gambar QRIS terlebih dahulu.',
            'qrisImage.image'    => 'File harus berupa gambar.',
            'qrisImage.mimes'    => 'Format gambar harus JPG atau PNG.',
            'qrisImage.max'      => 'Ukuran file maksimal 2MB.',
        ]);

        $store = auth()->user()->store ?? null;

        if (! $store) {
            session()->flash('error', 'Store tidak ditemukan. Pastikan akun Anda terhubung ke toko.');
            return;
        }

        $qrisService = app(QrisService::class);

        // Store the image to a temporary location, then read the QR
        $tmpPath    = $this->qrisImage->getRealPath();
        $storagePath = $this->qrisImage->store('qris', 'public');
        $absolutePath = Storage::disk('public')->path(str_replace('public/', '', $storagePath));
        // Use the real uploaded tmp path for reading (avoids storage symlink issues)
        $readPath = $this->qrisImage->getRealPath();

        try {
            $payload = $qrisService->extractPayloadFromImage($readPath);
            $qrisService->validateQrisPayload($payload);
        } catch (\Exception $e) {
            // Remove the already-stored image since it's invalid
            Storage::disk('public')->delete($storagePath);
            session()->flash('error', $e->getMessage());
            $this->qrisImage = null;
            return;
        }

        // Delete old QRIS image if exists
        if ($this->currentQrisImage && Storage::disk('public')->exists($this->currentQrisImage)) {
            Storage::disk('public')->delete($this->currentQrisImage);
        }

        // Save to Store record
        $store->update([
            'qris_image_path' => $storagePath,
            'qris_payload'    => $payload,
        ]);

        $this->currentQrisImage   = $storagePath;
        $this->currentQrisPayload = $payload;
        $this->qrisImage = null;

        ActivityLogger::settings('qris_updated', ['store_id' => $store->id]);

        session()->flash('message', 'QRIS berhasil disimpan dan divalidasi.');
    }

    public function removeQris()
    {
        $store = auth()->user()->store ?? null;

        if ($store) {
            if ($this->currentQrisImage && Storage::disk('public')->exists($this->currentQrisImage)) {
                Storage::disk('public')->delete($this->currentQrisImage);
            }

            $store->update([
                'qris_image_path' => null,
                'qris_payload'    => null,
            ]);

            ActivityLogger::settings('qris_removed', ['store_id' => $store->id]);
        }

        $this->currentQrisImage   = '';
        $this->currentQrisPayload = '';
        $this->qrisImage = null;

        session()->flash('message', 'QRIS berhasil dihapus.');
    }

    public function render()
    {
        return view('livewire.settings-manager');
    }
}
