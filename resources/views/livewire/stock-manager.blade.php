<div class="px-6 py-8 md:px-8 space-y-8 max-w-7xl mx-auto flex-1 w-full min-h-screen">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-black tracking-tighter uppercase text-zinc-900 dark:text-white">Manajemen Stok</h1>
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

    {{-- Controls --}}
    <div class="flex flex-col gap-4 sm:flex-row">
        <div class="flex-1 w-full">
            <flux:input icon="magnifying-glass" class="h-12 !rounded-2xl bg-zinc-100 border-none dark:bg-zinc-800" wire:model.live.debounce.300ms="search" placeholder="Cari nama produk atau SKU..." />
        </div>
        <div class="w-full sm:w-64">
            <flux:select wire:model.live="filterType" class="border-zinc-300 h-12 !rounded-2xl" placeholder="Semua Produk">
                <flux:select.option value="">Semua Produk</flux:select.option>
                <flux:select.option value="active">Produk Aktif</flux:select.option>
                <flux:select.option value="low_stock">Stok Menipis</flux:select.option>
                <flux:select.option value="out_of_stock">Stok Habis</flux:select.option>
            </flux:select>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        
        {{-- Product Stock Table --}}
        <div class="col-span-1 lg:col-span-2 overflow-hidden rounded-[2rem] border border-zinc-200 bg-white shadow-xl shadow-zinc-200/50 dark:border-zinc-800 dark:bg-zinc-900 dark:shadow-none min-h-[400px] flex flex-col">
            <div class="overflow-x-auto flex-1 customized-scrollbar">
                <table class="w-full text-sm text-left whitespace-nowrap">
                    <thead class="bg-zinc-50 border-b border-zinc-200 dark:bg-zinc-950 dark:border-zinc-800">
                        <tr>
                            <th class="px-6 py-5 text-xs font-black uppercase tracking-widest text-zinc-400">P/N Produk</th>
                            <th class="px-6 py-5 text-center text-xs font-black uppercase tracking-widest text-zinc-400">Status</th>
                            <th class="px-6 py-5 text-center text-xs font-black uppercase tracking-widest text-zinc-400">Kuantitas</th>
                            <th class="px-6 py-5 text-right text-xs font-black uppercase tracking-widest text-zinc-400">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800/60">
                        @forelse($products as $product)
                            <tr class="transition-colors hover:bg-zinc-50 dark:hover:bg-zinc-800/50 group">
                                <td class="px-6 py-5">
                                    <div class="font-bold text-zinc-900 dark:text-white">{{ $product->name }}</div>
                                    <div class="text-[10px] font-black tracking-widest text-zinc-400 mt-1 uppercase">SKU: {{ $product->sku }}</div>
                                </td>
                                <td class="px-6 py-5 text-center">
                                    <span class="inline-flex items-center rounded-xl px-3 py-1.5 text-[9px] font-black uppercase tracking-widest
                                        {{ $product->stock <= 0 ? 'bg-red-50 text-red-600 dark:bg-red-900/30 dark:text-red-400' : ($product->isLowStock() ? 'bg-amber-50 text-amber-600 dark:bg-amber-900/30 dark:text-amber-400' : 'bg-emerald-50 text-emerald-600 dark:bg-emerald-900/30 dark:text-emerald-400') }}">
                                        @if($product->stock <= 0) Habis @elseif($product->isLowStock()) Menipis @else Tersedia @endif
                                    </span>
                                </td>
                                <td class="px-6 py-5 text-center">
                                    <span class="text-xl font-black {{ $product->stock <= $product->low_stock_threshold ? 'text-red-500' : 'text-zinc-900 dark:text-white' }}">{{ $product->stock }}</span>
                                </td>
                                <td class="px-6 py-5 text-right">
                                    <button wire:click="openAdjustmentModal({{ $product->id }})" class="inline-flex items-center justify-center h-10 px-4 rounded-xl text-[10px] font-black uppercase tracking-widest bg-zinc-100 hover:bg-zinc-200 text-zinc-600 dark:bg-zinc-800 dark:text-zinc-300 dark:hover:bg-zinc-700 transition-all active:scale-95 border border-zinc-200 dark:border-zinc-700">
                                        <flux:icon name="adjustments-horizontal" class="h-4 w-4 mr-2" />
                                        Atur Stok
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="py-20 text-center opacity-40">
                                    <flux:icon name="inbox" class="h-12 w-12 mx-auto mb-4 text-zinc-400" />
                                    <p class="text-[10px] font-black uppercase tracking-widest text-zinc-500">Tidak ada produk ditemukan.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="border-t border-zinc-200 p-6 dark:border-zinc-800 bg-white dark:bg-zinc-900 shrink-0">
                {{ $products->links() }}
            </div>
        </div>

        {{-- Recent Logs Panel --}}
        <div class="col-span-1 rounded-[2rem] border border-zinc-200 bg-white p-6 md:p-8 shadow-xl shadow-zinc-200/50 dark:border-zinc-800 dark:bg-zinc-900 dark:shadow-none flex flex-col min-h-[400px]">
            <h3 class="text-xs font-black uppercase tracking-widest text-zinc-900 dark:text-white mb-6 border-b border-zinc-100 dark:border-zinc-800 pb-4">Riwayat Terakhir</h3>
            <div class="space-y-6 flex-1 overflow-y-auto pr-2">
                @forelse($recentLogs as $log)
                    <div class="flex items-start gap-4">
                        <div class="mt-0.5 rounded-2xl h-10 w-10 flex items-center justify-center shrink-0 {{ $log->getBadgeColor() }}">
                            <flux:icon :name="match($log->type) { 'in' => 'arrow-down', 'sale', 'out' => 'arrow-up', 'adjustment' => 'arrows-right-left', 'return' => 'arrow-uturn-left', default => 'cube' }" class="h-5 w-5" />
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-xs font-bold text-zinc-900 dark:text-white truncate">{{ $log->product->name ?? 'Produk Dihapus' }}</p>
                            <p class="text-[10px] font-black uppercase tracking-widest text-zinc-400 mt-1">
                                {{ $log->getTypeLabel() }} &bull; <span class="{{ $log->type === 'in' ? 'text-emerald-500' : 'text-red-500' }}">{{ $log->quantity > 0 ? '+'.$log->quantity : $log->quantity }}</span>
                            </p>
                            <p class="text-[9px] font-bold text-zinc-300 mt-1 uppercase">{{ $log->created_at->diffForHumans() }}</p>
                        </div>
                    </div>
                @empty
                    <div class="py-10 text-center opacity-30">
                        <flux:icon name="clock" class="h-10 w-10 mx-auto mb-3" />
                        <p class="text-[10px] font-black uppercase tracking-widest">Belum ada riwayat</p>
                    </div>
                @endforelse
            </div>
            <div class="mt-6 pt-4 border-t border-zinc-100 dark:border-zinc-800 shrink-0">
                <a href="{{ route('stock.logs') }}" wire:navigate class="flex items-center justify-center w-full h-12 rounded-xl text-[10px] font-black uppercase tracking-widest text-zinc-600 bg-zinc-100 hover:bg-zinc-200 transition-colors">Lihat Semua Data</a>
            </div>
        </div>
    </div>

    {{-- Adjustment Modal --}}
    <flux:modal wire:model="showAdjustmentModal" class="max-w-md md:max-w-lg p-0 overflow-hidden bg-white dark:bg-zinc-900 rounded-2xl shadow-2xl">
        @if($selectedProduct)
        <div class="p-6">
            <header class="border-b border-zinc-100 dark:border-zinc-800 pb-4 mb-4">
                <h2 class="text-lg font-semibold tracking-tight text-zinc-900 dark:text-white">Ubah Stok</h2>
                <p class="text-sm text-zinc-500 mt-1">Penyesuaian stok untuk produk yang dipilih.</p>
            </header>

            <div class="max-h-[60vh] overflow-y-auto pr-2 customized-scrollbar">
                <form wire:submit="processAdjustment" id="stockForm" class="space-y-4">
                    <div class="p-4 rounded-lg bg-zinc-50 border border-zinc-100 dark:bg-zinc-800/50 dark:border-zinc-700/50 flex justify-between items-center">
                        <div>
                            <p class="text-xs font-bold text-zinc-500 uppercase tracking-wider mb-1">{{ $selectedProduct->sku }}</p>
                            <p class="text-sm font-semibold text-zinc-900 dark:text-white leading-tight">{{ $selectedProduct->name }}</p>
                        </div>
                        <div class="text-right">
                            <span class="text-xs font-medium text-zinc-500 uppercase tracking-wider">Stok Saat Ini</span>
                            <div class="text-2xl font-bold text-zinc-900 dark:text-white leading-none mt-1">{{ $selectedProduct->stock }}</div>
                        </div>
                    </div>

                    <flux:field>
                        <flux:label class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Jenis Tindakan</flux:label>
                        <div class="grid grid-cols-3 gap-3 mt-1">
                            @foreach(['add' => 'Tambah', 'reduce' => 'Kurang', 'set' => 'Ubah'] as $val => $label)
                                <label class="relative flex flex-col items-center justify-center p-3 border rounded-lg cursor-pointer transition-all {{ $adjustmentType === $val ? 'border-green-600 bg-green-50 text-green-700 dark:bg-green-900/30 dark:text-green-400 dark:border-green-500' : 'border-zinc-200 bg-white text-zinc-600 hover:border-zinc-300 dark:bg-zinc-800 dark:border-zinc-700 dark:text-zinc-400' }}">
                                    <input type="radio" wire:model.live="adjustmentType" value="{{ $val }}" class="sr-only">
                                    <span class="text-sm font-medium text-center">{{ $label }}</span>
                                </label>
                            @endforeach
                        </div>
                    </flux:field>

                    <flux:field>
                        <flux:label class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Kuantitas <span class="text-red-500">*</span></flux:label>
                        <flux:input type="number" wire:model="adjustmentQuantity" min="0" required class="h-10 mt-1 rounded-lg border-zinc-300 focus:border-green-500 focus:ring-green-500" placeholder="0" />
                        <flux:error name="adjustmentQuantity" class="mt-1 text-sm text-red-500" />
                    </flux:field>
                    
                    <flux:field>
                        <flux:label class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Catatan Opsional</flux:label>
                        <flux:textarea wire:model="adjustmentNotes" rows="2" placeholder="Keterangan..." class="mt-1 rounded-lg border-zinc-300 focus:border-green-500 focus:ring-green-500" />
                        <flux:error name="adjustmentNotes" class="mt-1 text-sm text-red-500" />
                    </flux:field>
                </form>
            </div>

            <footer class="mt-6 flex justify-end gap-3 pt-4 border-t border-zinc-100 dark:border-zinc-800">
                <button type="button" class="h-10 px-4 rounded-lg bg-zinc-100 text-zinc-700 hover:bg-zinc-200 dark:bg-zinc-800 dark:text-zinc-300 dark:hover:bg-zinc-700 font-medium text-sm transition-colors" wire:click="$set('showAdjustmentModal', false)">Batal</button>
                <button type="submit" form="stockForm" class="h-10 px-6 rounded-lg bg-green-600 hover:bg-green-700 text-white font-semibold text-sm transition-colors">Simpan</button>
            </footer>
        </div>
        @endif
    </flux:modal>

    <style>
        .customized-scrollbar::-webkit-scrollbar { height: 6px; width: 6px; }
        .customized-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
    </style>
</div>
