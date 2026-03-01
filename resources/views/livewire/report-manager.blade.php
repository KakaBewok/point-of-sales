<div class="px-6 py-8 md:px-8 space-y-8 max-w-7xl mx-auto flex-1 w-full">
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <h1 class="text-2xl font-bold tracking-tight text-zinc-900 dark:text-white">Laporan Transaksi</h1>
        <flux:button variant="primary" icon="arrow-down-tray" class="h-10 px-4" wire:click="exportExcel" wire:loading.attr="disabled">Export Excel</flux:button>
    </div>

    {{-- Filters --}}
    <div class="grid grid-cols-1 gap-5 sm:grid-cols-3 rounded-lg border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
        <flux:input type="date" label="Dari Tanggal" class="h-10" wire:model.live="startDate" />
        <flux:input type="date" label="Sampai Tanggal" class="h-10" wire:model.live="endDate" />
        <flux:select label="Metode Pembayaran" class="h-10" wire:model.live="paymentMethod">
            <flux:select.option value="">Semua Metode</flux:select.option>
            <flux:select.option value="cash">Tunai</flux:select.option>
            <flux:select.option value="qris">QRIS</flux:select.option>
            <flux:select.option value="va">Virtual Account</flux:select.option>
        </flux:select>
    </div>

    {{-- Summary Cards --}}
    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
        <div class="rounded-lg border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
            <div class="flex items-center gap-3 text-zinc-500 dark:text-zinc-400 mb-2">
                <flux:icon name="shopping-cart" class="h-5 w-5" />
                <h3 class="font-medium text-sm">Total Transaksi</h3>
            </div>
            <p class="text-2xl font-bold tracking-tight text-zinc-900 dark:text-white">{{ number_format($totalTransactions) }} <span class="text-sm font-semibold text-zinc-500">trx</span></p>
        </div>
        <div class="rounded-lg border border-emerald-200 bg-emerald-50 dark:bg-emerald-900/20 p-6 shadow-sm dark:border-emerald-800/50 relative overflow-hidden">
            <div class="flex items-center gap-3 text-emerald-600 dark:text-emerald-400 mb-2 relative z-10">
                <flux:icon name="currency-dollar" class="h-5 w-5" />
                <h3 class="font-medium text-sm">Total Pendapatan</h3>
            </div>
            <p class="text-2xl font-bold tracking-tight text-emerald-700 dark:text-emerald-300 relative z-10">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</p>
        </div>
        <div class="rounded-lg border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
            <div class="flex items-center gap-3 text-indigo-500 dark:text-indigo-400 mb-2">
                <flux:icon name="receipt-percent" class="h-5 w-5" />
                <h3 class="font-medium text-sm">Total Diskon Diberikan</h3>
            </div>
            <p class="text-2xl font-bold tracking-tight text-zinc-900 dark:text-white">Rp {{ number_format($totalDiscounts, 0, ',', '.') }}</p>
        </div>
        <div class="rounded-lg border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
            <div class="flex items-center gap-3 text-amber-500 dark:text-amber-400 mb-2">
                <flux:icon name="document-text" class="h-5 w-5" />
                <h3 class="font-medium text-sm">Total Pajak (PPN)</h3>
            </div>
            <p class="text-2xl font-bold tracking-tight text-zinc-900 dark:text-white">Rp {{ number_format($totalTax, 0, ',', '.') }}</p>
        </div>
    </div>

    {{-- Table --}}
    <div class="overflow-hidden rounded-lg border border-zinc-200 bg-white shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-zinc-50 border-b border-zinc-200 dark:bg-zinc-900/50 dark:border-zinc-800">
                    <tr>
                        <th class="px-6 py-4 text-left font-semibold text-zinc-600 dark:text-zinc-400">Invoice / Waktu</th>
                        <th class="px-6 py-4 text-center font-semibold text-zinc-600 dark:text-zinc-400">Item</th>
                        <th class="px-6 py-4 text-right font-semibold text-zinc-600 dark:text-zinc-400">Subtotal</th>
                        <th class="px-6 py-4 text-right font-semibold text-emerald-600 dark:text-emerald-500">Diskon</th>
                        <th class="px-6 py-4 text-right font-semibold text-red-600 dark:text-red-400">Pajak</th>
                        <th class="px-6 py-4 text-right font-semibold text-zinc-900 dark:text-white">Total</th>
                        <th class="px-6 py-4 text-center font-semibold text-zinc-600 dark:text-zinc-400">Metode</th>
                        <th class="px-6 py-4 text-right font-semibold text-zinc-600 dark:text-zinc-400">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800/60">
                    @forelse($transactions as $trx)
                        <tr class="transition-colors hover:bg-zinc-50 dark:hover:bg-zinc-800/50">
                            <td class="px-6 py-4">
                                <div class="font-bold font-mono text-zinc-900 dark:text-white">{{ $trx->invoice_number }}</div>
                                <div class="text-xs text-zinc-500 mt-1">{{ $trx->created_at->format('d/m/Y H:i') }} • {{ $trx->user->name ?? '-' }}</div>
                            </td>
                            <td class="px-6 py-4 text-center font-bold text-zinc-700 dark:text-zinc-300">{{ $trx->items->sum('quantity') }}</td>
                            <td class="px-6 py-4 text-right font-medium text-zinc-600 dark:text-zinc-400">Rp {{ number_format($trx->subtotal, 0, ',', '.') }}</td>
                            <td class="px-6 py-4 text-right font-medium text-emerald-600 dark:text-emerald-400">-Rp {{ number_format($trx->discount_amount, 0, ',', '.') }}</td>
                            <td class="px-6 py-4 text-right font-medium text-red-500 dark:text-red-400">+Rp {{ number_format($trx->tax_amount, 0, ',', '.') }}</td>
                            <td class="px-6 py-4 text-right text-base font-bold text-zinc-900 dark:text-white">Rp {{ number_format($trx->grand_total, 0, ',', '.') }}</td>
                            <td class="px-6 py-4 text-center">
                                <span class="inline-flex items-center rounded-md px-2.5 py-1 text-xs font-semibold ring-1 ring-inset {{ match($trx->payment?->method) { 
                                    'cash' => 'bg-emerald-50 text-emerald-700 ring-emerald-600/20 dark:bg-emerald-900/30 dark:text-emerald-400 dark:ring-emerald-900/50', 
                                    'qris' => 'bg-blue-50 text-blue-700 ring-blue-600/20 dark:bg-blue-900/30 dark:text-blue-400 dark:ring-blue-900/50', 
                                    'va' => 'bg-violet-50 text-violet-700 ring-violet-600/20 dark:bg-violet-900/30 dark:text-violet-400 dark:ring-violet-900/50', 
                                    default => 'bg-zinc-50 text-zinc-600 ring-zinc-500/20 dark:bg-zinc-800/50 dark:text-zinc-400 dark:ring-zinc-700' 
                                } }}">
                                    {{ strtoupper($trx->payment?->method ?? '-') }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <flux:button size="sm" variant="ghost" class="h-8 w-8 px-0" icon="printer" as="a" href="{{ route('receipt.print', $trx->id) }}" target="_blank" />
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="px-6 py-12 text-center text-zinc-500">
                            <flux:icon name="document-magnifying-glass" class="mx-auto mb-3 h-8 w-8 opacity-40" />
                            Tidak ada transaksi pada periode ini.
                        </td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="border-t border-zinc-200 bg-white p-4 dark:border-zinc-800 dark:bg-zinc-900">{{ $transactions->links() }}</div>
    </div>
</div>
