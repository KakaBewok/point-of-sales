<div class="px-6 py-8 md:px-8 space-y-8 max-w-5xl mx-auto flex-1 w-full">
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-zinc-900 dark:text-white">Detail Transaksi</h1>
            <p class="text-sm text-zinc-500 mt-1">Informasi lengkap transaksi #{{ $transaction->invoice_number }}</p>
        </div>
        <div class="flex gap-3">
            <flux:button variant="ghost" icon="arrow-left" class="h-10 px-4" :href="route('reports.index')" wire:navigate>Kembali</flux:button>
            <flux:button variant="primary" icon="printer" class="h-10 px-4" as="a" href="{{ route('receipt.print', $transaction->id) }}" target="_blank">Cetak Struk</flux:button>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        {{-- Main Detail Card --}}
        <div class="lg:col-span-2 space-y-8">
            <div class="rounded-2xl border border-zinc-200 bg-white shadow-sm dark:border-zinc-800 dark:bg-zinc-900 overflow-hidden">
                <div class="bg-zinc-50 px-6 py-4 border-b border-zinc-200 dark:bg-zinc-900/50 dark:border-zinc-800">
                    <h3 class="font-semibold text-zinc-900 dark:text-white">Item Transaksi</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-zinc-50/50 border-b border-zinc-100 dark:bg-zinc-800/30 dark:border-zinc-800">
                            <tr>
                                <th class="px-6 py-3 text-left font-semibold text-zinc-600 dark:text-zinc-400">Produk</th>
                                <th class="px-6 py-3 text-center font-semibold text-zinc-600 dark:text-zinc-400">Qty</th>
                                <th class="px-6 py-3 text-right font-semibold text-zinc-600 dark:text-zinc-400">Harga</th>
                                <th class="px-6 py-3 text-right font-semibold text-zinc-600 dark:text-zinc-400">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800/60">
                            @foreach($transaction->items as $item)
                                <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800/40 transition-colors">
                                    <td class="px-6 py-4">
                                        <div class="font-medium text-zinc-900 dark:text-white">{{ $item->product_name }}</div>
                                        <div class="text-[10px] text-zinc-500 uppercase tracking-wider mt-0.5">SKU: {{ $item->product?->sku ?? '-' }}</div>
                                    </td>
                                    <td class="px-6 py-4 text-center font-medium">{{ $item->quantity }}</td>
                                    <td class="px-6 py-4 text-right">Rp {{ number_format($item->product_price, 0, ',', '.') }}</td>
                                    <td class="px-6 py-4 text-right font-semibold text-zinc-900 dark:text-white">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Summary Breakdown --}}
            <div class="rounded-2xl border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
                <div class="space-y-3">
                    <div class="flex justify-between items-center text-zinc-600 dark:text-zinc-400">
                        <span>Subtotal</span>
                        <span class="font-medium">Rp {{ number_format($transaction->subtotal, 0, ',', '.') }}</span>
                    </div>
                    
                    @if($transaction->discount_amount > 0)
                        <div class="flex justify-between items-center text-emerald-600 dark:text-emerald-400">
                            <div class="flex items-center gap-1.5">
                                <flux:icon name="receipt-percent" class="h-4 w-4" />
                                <span>Diskon {{ $transaction->voucher ? '('.$transaction->voucher->code.')' : '' }}</span>
                            </div>
                            <span class="font-medium">-Rp {{ number_format($transaction->discount_amount, 0, ',', '.') }}</span>
                        </div>
                    @endif

                    <div class="flex justify-between items-center text-zinc-600 dark:text-zinc-400">
                        <span>Pajak (PPN 11%)</span>
                        <span class="font-medium">Rp {{ number_format($transaction->tax_amount, 0, ',', '.') }}</span>
                    </div>

                    <div class="pt-4 mt-2 border-t border-zinc-100 dark:border-zinc-800 flex justify-between items-center">
                        <span class="text-lg font-bold text-zinc-900 dark:text-white uppercase tracking-tight">Total Akhir</span>
                        <span class="text-2xl font-black text-zinc-900 dark:text-white">Rp {{ number_format($transaction->grand_total, 0, ',', '.') }}</span>
                    </div>

                    <div class="pt-4 space-y-2 text-sm">
                        <div class="flex justify-between text-zinc-500">
                            <span>Dibayar</span>
                            <span class="font-medium text-zinc-700 dark:text-zinc-300">Rp {{ number_format($transaction->payment?->cash_received ?? $transaction->grand_total, 0, ',', '.') }}</span>
                        </div>
                        @if($transaction->payment?->isCash())
                            <div class="flex justify-between text-zinc-500">
                                <span>Kembalian</span>
                                <span class="font-medium text-zinc-700 dark:text-zinc-300">Rp {{ number_format($transaction->payment->change_amount, 0, ',', '.') }}</span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- Sidebar Info --}}
        <div class="space-y-6">
            <div class="rounded-2xl border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
                <h4 class="text-xs font-black text-zinc-400 uppercase tracking-widest mb-4">Informasi Transaksi</h4>
                <div class="space-y-5">
                    <div>
                        <div class="text-[10px] text-zinc-500 uppercase tracking-widest font-black mb-1">Metode Pembayaran</div>
                        <div class="flex items-center gap-2">
                            <span class="inline-flex items-center rounded-lg px-3 py-1 text-xs font-bold ring-1 ring-inset {{ match($transaction->payment?->method) { 
                                'cash' => 'bg-emerald-50 text-emerald-700 ring-emerald-600/20 dark:bg-emerald-900/30 dark:text-emerald-400', 
                                'qris' => 'bg-blue-50 text-blue-700 ring-blue-600/20 dark:bg-blue-900/30 dark:text-blue-400', 
                                'va' => 'bg-violet-50 text-violet-700 ring-violet-600/20 dark:bg-violet-900/30 dark:text-violet-400', 
                                default => 'bg-zinc-50 text-zinc-600 ring-zinc-500/20 dark:bg-zinc-800/50 dark:text-zinc-400' 
                            } }}">
                                {{ strtoupper($transaction->payment?->method ?? 'UNKNOWN') }}
                            </span>
                            <span class="text-xs font-medium text-emerald-600">{{ $transaction->payment?->status === 'success' ? 'LUNAS' : '' }}</span>
                        </div>
                    </div>

                    <div>
                        <div class="text-[10px] text-zinc-500 uppercase tracking-widest font-black mb-1">Kasir</div>
                        <div class="flex items-center gap-2">
                            <flux:avatar :name="$transaction->user->name" size="xs" class="ring-2 ring-white dark:ring-zinc-800" />
                            <span class="text-sm font-semibold text-zinc-900 dark:text-white">{{ $transaction->user->name ?? 'System' }}</span>
                        </div>
                    </div>

                    <div>
                        <div class="text-[10px] text-zinc-500 uppercase tracking-widest font-black mb-1">Waktu Transaksi</div>
                        <div class="text-sm font-medium text-zinc-700 dark:text-zinc-300">
                            {{ $transaction->created_at->format('d F Y') }}<br>
                            <span class="text-zinc-400">{{ $transaction->created_at->format('H:i:s') }}</span>
                        </div>
                    </div>

                    <div>
                        <div class="text-[10px] text-zinc-500 uppercase tracking-widest font-black mb-1">Status</div>
                        <span class="inline-flex items-center rounded-lg px-3 py-1 text-xs font-bold ring-1 ring-inset bg-emerald-50 text-emerald-700 ring-emerald-600/20 dark:bg-emerald-900/30 dark:text-emerald-400 capitalize">
                            {{ $transaction->status }}
                        </span>
                    </div>
                </div>
            </div>

            @if($transaction->notes)
                <div class="rounded-2xl border border-zinc-200 bg-amber-50/50 p-6 shadow-sm dark:border-amber-900/30 dark:bg-amber-900/10">
                    <h4 class="text-xs font-black text-amber-600 dark:text-amber-500 uppercase tracking-widest mb-2">Catatan</h4>
                    <p class="text-sm text-amber-800 dark:text-amber-400 leading-relaxed italic">"{{ $transaction->notes }}"</p>
                </div>
            @endif
        </div>
    </div>
</div>
