<div class="px-6 py-8 md:px-8 space-y-8 max-w-3xl mx-auto flex-1 w-full">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold tracking-tight text-zinc-900 dark:text-white">Pengaturan Aplikasi</h1>
    </div>

    @if(session()->has('message'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)" x-transition.opacity.duration.500ms class="fixed top-6 right-6 z-50 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800 shadow-xl shadow-emerald-900/10 dark:border-emerald-900/50 dark:bg-emerald-900 dark:text-emerald-400 dark:shadow-black/50 flex items-center gap-3">
            <flux:icon name="check-circle" class="h-5 w-5" />
            {{ session('message') }}
            <button @click="show = false" class="ml-2 text-emerald-600 hover:text-emerald-800 dark:text-emerald-400/70 dark:hover:text-emerald-300 transition-colors">
                <flux:icon name="x-mark" class="h-4 w-4" />
            </button>
        </div>
    @endif
    @if(session()->has('error'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)" x-transition.opacity.duration.500ms class="fixed top-6 right-6 z-50 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm font-medium text-red-800 shadow-xl shadow-red-900/10 dark:border-red-900/50 dark:bg-red-900 dark:text-red-400 dark:shadow-black/50 flex items-center gap-3">
            <flux:icon name="exclamation-circle" class="h-5 w-5" />
            {{ session('error') }}
            <button @click="show = false" class="ml-2 text-red-600 hover:text-red-800 dark:text-red-400/70 dark:hover:text-red-300 transition-colors">
                <flux:icon name="x-mark" class="h-4 w-4" />
            </button>
        </div>
    @endif

    <form wire:submit="save" class="space-y-8">
        {{-- General Section --}}
        <div class="rounded-lg border border-zinc-200 bg-white p-8 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
            <h3 class="mb-5 text-lg font-bold tracking-tight text-zinc-900 dark:text-white">Informasi Toko</h3>
            <div class="space-y-5">
                <flux:input label="Nama Toko" class="h-10" wire:model="store_name" required />
                <flux:textarea label="Alamat Toko" wire:model="store_address" rows="3" />
                <flux:input label="Nomor Telepon" class="h-10" wire:model="store_phone" />
            </div>
        </div>

        {{-- Logo Section --}}
        <div class="rounded-lg border border-zinc-200 bg-white p-8 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
            <h3 class="mb-5 text-lg font-bold tracking-tight text-zinc-900 dark:text-white">Logo Toko</h3>
            <p class="text-sm text-zinc-500 dark:text-zinc-400 mb-4">Logo akan ditampilkan di sidebar dan struk. Maks. 1MB (PNG, JPG, SVG).</p>
            
            <div class="flex items-start gap-6">
                {{-- Preview --}}
                <div class="shrink-0">
                    @if($logo)
                        <div class="h-24 w-24 rounded-xl border-2 border-green-300 bg-zinc-100 dark:bg-zinc-800 overflow-hidden flex items-center justify-center">
                            <img src="{{ $logo->temporaryUrl() }}" class="h-full w-full object-contain p-2" alt="Preview" />
                        </div>
                        <p class="text-xs text-green-600 font-medium mt-1 text-center">Preview</p>
                    @elseif($currentLogo)
                        <div class="h-24 w-24 rounded-xl border-2 border-zinc-200 bg-zinc-100 dark:bg-zinc-800 dark:border-zinc-700 overflow-hidden flex items-center justify-center">
                            <img src="{{ Illuminate\Support\Facades\Storage::url($currentLogo) }}" class="h-full w-full object-contain p-2" alt="Current Logo" />
                        </div>
                        <p class="text-xs text-zinc-500 font-medium mt-1 text-center">Logo saat ini</p>
                    @else
                        <div class="h-24 w-24 rounded-xl border-2 border-dashed border-zinc-300 bg-zinc-50 dark:bg-zinc-800 dark:border-zinc-700 flex items-center justify-center">
                            <flux:icon name="photo" class="h-8 w-8 text-zinc-300 dark:text-zinc-600" />
                        </div>
                    @endif
                </div>

                <div class="flex-1 space-y-3">
                    <flux:input type="file" wire:model="logo" accept="image/png,image/jpeg,image/svg+xml" class="h-10" />
                    @error('logo') <p class="text-sm text-red-500">{{ $message }}</p> @enderror
                    
                    @if($currentLogo)
                        <button type="button" wire:click="removeLogo" class="text-sm text-red-600 hover:text-red-700 font-medium flex items-center gap-1.5">
                            <flux:icon name="trash" class="h-3.5 w-3.5" />
                            Hapus Logo
                        </button>
                    @endif
                </div>
            </div>
        </div>

        {{-- Tax Section --}}
        <div class="rounded-lg border border-zinc-200 bg-white p-8 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
            <h3 class="mb-5 text-lg font-bold tracking-tight text-zinc-900 dark:text-white">Pengaturan Pajak</h3>
            <div class="space-y-5">
                <flux:checkbox label="Aktifkan Pajak (PPN)" wire:model.live="tax_enabled" description="Jika diaktifkan, pajak akan dihitung pada setiap transaksi." />
                
                @if($tax_enabled)
                    <div class="pl-7 mt-2">
                        <flux:input label="Persentase Pajak (%)" class="h-10 max-w-xs" type="number" step="0.1" wire:model="tax_rate" required />
                    </div>
                @endif
            </div>
        </div>

        {{-- Receipt Section --}}
        <div class="rounded-lg border border-zinc-200 bg-white p-8 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
            <h3 class="mb-5 text-lg font-bold tracking-tight text-zinc-900 dark:text-white">Struk / Resi</h3>
            <div class="space-y-5">
                <flux:input label="Catatan Kaki (Footer)" class="h-10" wire:model="receipt_footer" placeholder="Terima kasih atas kunjungan Anda!" />
            </div>
        </div>

        {{-- Social Media Section --}}
        <div class="rounded-lg border border-zinc-200 bg-white p-8 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
            <h3 class="mb-2 text-lg font-bold tracking-tight text-zinc-900 dark:text-white">Media Sosial</h3>
            <p class="text-sm text-zinc-500 dark:text-zinc-400 mb-5">Opsional. Masukkan URL lengkap profil media sosial toko Anda.</p>
            <div class="space-y-5">
                <flux:input label="Instagram" class="h-10" wire:model="social_instagram" placeholder="https://instagram.com/tokoanda" />
                @error('social_instagram') <p class="text-sm text-red-500 -mt-3">{{ $message }}</p> @enderror
                
                <flux:input label="TikTok" class="h-10" wire:model="social_tiktok" placeholder="https://tiktok.com/@tokoanda" />
                @error('social_tiktok') <p class="text-sm text-red-500 -mt-3">{{ $message }}</p> @enderror
                
                <flux:input label="Facebook" class="h-10" wire:model="social_facebook" placeholder="https://facebook.com/tokoanda" />
                @error('social_facebook') <p class="text-sm text-red-500 -mt-3">{{ $message }}</p> @enderror
                
                <flux:input label="YouTube" class="h-10" wire:model="social_youtube" placeholder="https://youtube.com/@tokoanda" />
                @error('social_youtube') <p class="text-sm text-red-500 -mt-3">{{ $message }}</p> @enderror
            </div>
        </div>

        <div class="flex justify-end pt-4">
            <flux:button type="submit" variant="primary" class="h-10 px-6">Simpan Pengaturan</flux:button>
        </div>
    </form>
</div>
