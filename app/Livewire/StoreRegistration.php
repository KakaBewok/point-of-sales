<?php

namespace App\Livewire;

use App\Models\Category;
use App\Models\Setting;
use App\Models\Store;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Livewire\Component;

class StoreRegistration extends Component
{
    // Store fields
    public $storeName = '';
    public $storeSlug = '';
    public $storePhone = '';
    public $storeAddress = '';

    // Owner fields
    public $ownerName = '';
    public $email = '';
    public $password = '';
    public $password_confirmation = '';

    // Auto-generate slug from store name
    public function updatedStoreName($value): void
    {
        $this->storeSlug = Str::slug($value);
    }

    protected function rules(): array
    {
        return [
            'storeName' => 'required|string|max:255',
            'storeSlug' => 'required|string|max:255|unique:stores,slug|alpha_dash',
            'storePhone' => 'nullable|string|max:50',
            'storeAddress' => 'nullable|string|max:1000',
            'ownerName' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
        ];
    }

    protected function messages(): array
    {
        return [
            'storeName.required' => 'Nama toko wajib diisi.',
            'storeSlug.required' => 'Slug toko wajib diisi.',
            'storeSlug.unique' => 'Slug ini sudah digunakan, pilih yang lain.',
            'storeSlug.alpha_dash' => 'Slug hanya boleh berisi huruf, angka, dash, dan underscore.',
            'ownerName.required' => 'Nama pemilik wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.unique' => 'Email ini sudah terdaftar.',
            'password.required' => 'Password wajib diisi.',
            'password.min' => 'Password minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
        ];
    }

    public function register()
    {
        $this->validate();

        DB::transaction(function () {
            // 1. Create store
            $store = Store::create([
                'name' => $this->storeName,
                'slug' => $this->storeSlug,
                'phone' => $this->storePhone ?: null,
                'address' => $this->storeAddress ?: null,
                'subscription_status' => 'trial',
                'trial_ends_at' => now()->addDays(7),
            ]);

            // 2. Create owner user (bypass global scope)
            $owner = User::withoutGlobalScope('store')->create([
                'store_id' => $store->id,
                'name' => $this->ownerName,
                'email' => $this->email,
                'password' => Hash::make($this->password),
                'role' => 'owner',
                'is_active' => true,
                'permissions' => null, // Owner has all permissions
                'email_verified_at' => now(),
            ]);

            // 3. Create default settings
            Setting::createDefaults($store->id, $this->storeName);

            // 4. Create default category
            Category::withoutGlobalScope('store')->create([
                'store_id' => $store->id,
                'name' => 'Umum',
                'slug' => 'umum',
                'is_active' => true,
                'sort_order' => 1,
            ]);

            // 5. Auto-login
            Auth::login($owner);
        });

        return redirect()->route('dashboard');
    }

    public function render()
    {
        return view('livewire.store-registration')
            ->layout('layouts.auth', ['title' => 'Daftarkan Toko']);
    }
}
