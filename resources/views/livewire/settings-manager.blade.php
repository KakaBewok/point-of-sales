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

        <div class="flex justify-end pt-4">
            <flux:button type="submit" variant="primary" class="h-10 px-6">Simpan Pengaturan</flux:button>
        </div>
    </form>
</div>
