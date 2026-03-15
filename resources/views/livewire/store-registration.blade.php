<div class="flex flex-col gap-6">
    <div class="text-center">
        <h1 class="text-xl font-semibold text-zinc-900 dark:text-white">Daftarkan Toko Anda</h1>
        <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">Buat toko dan akun pemilik untuk mulai menggunakan POS</p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="text-center" :status="session('status')" />

    <form wire:submit="register" class="flex flex-col gap-6">
        {{-- Store Information --}}
        <div class="space-y-1">
            <h2 class="text-sm text-slate-700 dark:text-zinc-300 uppercase tracking-wider font-extrabold">Informasi Toko</h2>
            <div class="h-px bg-zinc-200 dark:bg-zinc-700"></div>
        </div>

        <div class="flex flex-col gap-4">
            <flux:input
                wire:model="storeName"
                label="Nama Toko"
                type="text"
                required
                placeholder="Contoh: Toko Sejahtera"
            />
            @error('storeName') <span class="text-sm text-red-500">{{ $message }}</span> @enderror

            <flux:input
                wire:model="storeSlug"
                label="Slug Toko"
                type="text"
                required
                placeholder="contoh: toko-sejahtera"
                description="URL unik untuk toko Anda (otomatis dari nama toko)"
            />
            @error('storeSlug') <span class="text-sm text-red-500">{{ $message }}</span> @enderror

            <flux:input
                wire:model="storePhone"
                label="Nomor Telepon"
                type="text"
                placeholder="08xx-xxxx-xxxx"
            />

            <flux:textarea
                wire:model="storeAddress"
                label="Alamat"
                placeholder="Alamat lengkap toko"
                rows="2"
            />
        </div>

        {{-- Owner Account --}}
        <div class="space-y-1 pt-2">
            <h2 class="text-sm font-semibold text-zinc-700 dark:text-zinc-300 uppercase tracking-wider">Akun Pemilik</h2>
            <div class="h-px bg-zinc-200 dark:bg-zinc-700"></div>
        </div>

        <div class="flex flex-col gap-4">
            <flux:input
                wire:model="ownerName"
                label="Nama Lengkap"
                type="text"
                required
                placeholder="Nama pemilik toko"
            />
            @error('ownerName') <span class="text-sm text-red-500">{{ $message }}</span> @enderror

            <flux:input
                wire:model="email"
                label="Email"
                type="email"
                required
                placeholder="email@contoh.com"
            />
            @error('email') <span class="text-sm text-red-500">{{ $message }}</span> @enderror

            <flux:input
                wire:model="password"
                label="Password"
                type="password"
                required
                placeholder="Minimal 8 karakter"
                viewable
            />
            @error('password') <span class="text-sm text-red-500">{{ $message }}</span> @enderror

            <flux:input
                wire:model="password_confirmation"
                label="Konfirmasi Password"
                type="password"
                required
                placeholder="Ulangi password"
                viewable
            />
        </div>

        <div class="flex items-center justify-end pt-2">
            <flux:button type="submit" variant="primary" class="w-full">
                <span wire:loading.remove wire:target="register">Daftarkan Toko</span>
                <span wire:loading wire:target="register">Memproses...</span>
            </flux:button>
        </div>
    </form>

    <div class="space-x-1 rtl:space-x-reverse text-center text-sm text-zinc-600 dark:text-zinc-400">
        <span>Sudah punya akun?</span>
        <flux:link :href="route('login')" wire:navigate>Masuk</flux:link>
    </div>
</div>
