<div class="px-6 py-8 md:px-8 space-y-8 max-w-7xl mx-auto flex-1 w-full">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold tracking-tight text-zinc-900 dark:text-white">Riwayat Pergerakan Stok</h1>
        <flux:button variant="ghost" icon="arrow-left" class="h-10 px-4" :href="route('stock.index')" wire:navigate>Kembali</flux:button>
    </div>

    <div class="flex flex-col gap-4 sm:flex-row">
        <div class="flex-1">
            <flux:input icon="magnifying-glass" class="h-10" wire:model.live.debounce.300ms="search" placeholder="Cari nama produk..." />
        </div>
        <div class="w-full sm:w-56">
            <flux:select wire:model.live="typeFilter" class="h-10" placeholder="Semua Tipe">
                <flux:select.option value="">Semua Tipe</flux:select.option>
                <flux:select.option value="in">Stok Masuk</flux:select.option>
                <flux:select.option value="out">Stok Keluar</flux:select.option>
                <flux:select.option value="sale">Penjualan</flux:select.option>
                <flux:select.option value="return">Retur Penjualan</flux:select.option>
                <flux:select.option value="adjustment">Penyesuaian Manual</flux:select.option>
            </flux:select>
        </div>
    </div>

    <div class="overflow-hidden rounded-lg border border-zinc-200 bg-white shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-zinc-50 border-b border-zinc-200 dark:bg-zinc-900/50 dark:border-zinc-800">
                    <tr>
                        <th class="px-6 py-4 font-semibold text-zinc-600 dark:text-zinc-400">Waktu</th>
                        <th class="px-6 py-4 font-semibold text-zinc-600 dark:text-zinc-400">Tipe</th>
                        <th class="px-6 py-4 font-semibold text-zinc-600 dark:text-zinc-400">Produk</th>
                        <th class="px-6 py-4 text-center font-semibold text-zinc-600 dark:text-zinc-400">Perubahan</th>
                        <th class="px-6 py-4 text-center font-semibold text-zinc-600 dark:text-zinc-400">Stok Akhir</th>
                        <th class="px-6 py-4 font-semibold text-zinc-600 dark:text-zinc-400">Pengguna</th>
                        <th class="px-6 py-4 font-semibold text-zinc-600 dark:text-zinc-400">Keterangan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800/60">
                    @forelse($logs as $log)
                        <tr class="transition-colors hover:bg-zinc-50 dark:hover:bg-zinc-800/50">
                            <td class="px-6 py-4 text-zinc-600 dark:text-zinc-400 whitespace-nowrap">{{ $log->created_at->format('d/m/Y H:i') }}</td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center rounded-md px-2.5 py-1 text-xs font-semibold ring-1 ring-inset {{ $log->getBadgeColor() }} {{ str_contains($log->getBadgeColor(), 'emerald') ? 'ring-emerald-600/20' : (str_contains($log->getBadgeColor(), 'red') ? 'ring-red-600/20' : (str_contains($log->getBadgeColor(), 'indigo') ? 'ring-indigo-600/20' : (str_contains($log->getBadgeColor(), 'blue') ? 'ring-blue-600/20' : 'ring-zinc-600/20'))) }}">
                                    {{ $log->getTypeLabel() }}
                                </span>
                            </td>
                            <td class="px-6 py-4 font-medium text-zinc-900 dark:text-white">{{ $log->product->name ?? '-' }}</td>
                            <td class="px-6 py-4 text-center text-base font-bold text-zinc-900 dark:text-white">
                                <span class="{{ in_array($log->type, ['in', 'return']) ? 'text-emerald-600 dark:text-emerald-400' : (in_array($log->type, ['sale', 'out']) ? 'text-red-600 dark:text-red-400' : '') }}">
                                    {{ in_array($log->type, ['in', 'return']) ? '+' : (in_array($log->type, ['sale', 'out']) ? '-' : '') }}{{ $log->quantity }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center font-semibold text-zinc-600 dark:text-zinc-300">{{ $log->stock_after }}</td>
                            <td class="px-6 py-4 text-zinc-600 dark:text-zinc-400">{{ $log->user->name ?? 'Sistem' }}</td>
                            <td class="px-6 py-4 text-zinc-500 text-sm max-w-xs truncate" title="{{ $log->notes }}">{{ $log->notes }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="py-10 text-center text-zinc-500">
                            <flux:icon name="clock" class="mx-auto mb-3 h-8 w-8 opacity-40" />
                            Tidak ada riwayat.
                        </td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="border-t border-zinc-200 bg-white p-4 dark:border-zinc-800 dark:bg-zinc-900">{{ $logs->links() }}</div>
    </div>
</div>
