<div class="px-6 py-8 md:px-8 space-y-8 max-w-7xl mx-auto flex-1 w-full">
        {{-- Header --}}
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-zinc-900 dark:text-white">Dashboard</h1>
                <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">Selamat datang kembali, {{ auth()->user()->name }}.</p>
            </div>
            <div class="text-sm font-medium text-zinc-500 dark:text-zinc-400">
                {{ now()->translatedFormat('l, d F Y') }}
            </div>
        </div>

        {{-- Summary Cards --}}
        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
            {{-- Today Revenue --}}
            <div class="rounded-lg border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
                <div class="flex items-center gap-4">
                    <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-emerald-50 dark:bg-emerald-900/30">
                        <flux:icon name="banknotes" class="h-6 w-6 text-emerald-600 dark:text-emerald-400" />
                    </div>
                    <div>
                        <p class="text-sm font-medium text-zinc-500 dark:text-zinc-400">Pendapatan Hari Ini</p>
                        <p class="text-xl font-bold tracking-tight text-zinc-900 dark:text-white mt-1">Rp {{ number_format($todayRevenue, 0, ',', '.') }}</p>
                    </div>
                </div>
            </div>

            {{-- Today Transactions --}}
            <div class="rounded-lg border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
                <div class="flex items-center gap-4">
                    <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-blue-50 dark:bg-blue-900/30">
                        <flux:icon name="shopping-cart" class="h-6 w-6 text-blue-600 dark:text-blue-400" />
                    </div>
                    <div>
                        <p class="text-sm font-medium text-zinc-500 dark:text-zinc-400">Transaksi Hari Ini</p>
                        <p class="text-xl font-bold tracking-tight text-zinc-900 dark:text-white mt-1">{{ $todayTransactions }}</p>
                    </div>
                </div>
            </div>

            {{-- Monthly Revenue --}}
            <div class="rounded-lg border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
                <div class="flex items-center gap-4">
                    <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-violet-50 dark:bg-violet-900/30">
                        <flux:icon name="chart-bar" class="h-6 w-6 text-violet-600 dark:text-violet-400" />
                    </div>
                    <div>
                        <p class="text-sm font-medium text-zinc-500 dark:text-zinc-400">Pendapatan Bulan Ini</p>
                        <p class="text-xl font-bold tracking-tight text-zinc-900 dark:text-white mt-1">Rp {{ number_format($monthlyRevenue, 0, ',', '.') }}</p>
                    </div>
                </div>
            </div>

            {{-- Total Products --}}
            <div class="rounded-lg border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
                <div class="flex items-center gap-4">
                    <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-amber-50 dark:bg-amber-900/30">
                        <flux:icon name="cube" class="h-6 w-6 text-amber-600 dark:text-amber-400" />
                    </div>
                    <div>
                        <p class="text-sm font-medium text-zinc-500 dark:text-zinc-400">Total Produk Aktif</p>
                        <p class="text-xl font-bold tracking-tight text-zinc-900 dark:text-white mt-1">{{ $totalProducts }}</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Charts & Tables --}}
        <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
            {{-- Revenue Chart (7 days) --}}
            <div class="col-span-2 rounded-lg border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
                <h3 class="mb-5 text-base font-semibold text-zinc-900 dark:text-white">Pendapatan 7 Hari Terakhir</h3>
                <div class="space-y-4">
                    @php $maxRevenue = $chartData->max('revenue') ?: 1; @endphp
                    @foreach($chartData as $day)
                        <div class="flex items-center gap-4">
                            <span class="w-16 text-sm text-zinc-500 dark:text-zinc-400">{{ $day['date'] }}</span>
                            <div class="flex-1">
                                <div class="h-6 w-full rounded-md bg-zinc-100 dark:bg-zinc-800 overflow-hidden">
                                    <div class="h-full rounded-md bg-zinc-900 dark:bg-zinc-300 transition-all duration-500"
                                         style="width: {{ ($day['revenue'] / $maxRevenue) * 100 }}%"></div>
                                </div>
                            </div>
                            <span class="w-28 text-right text-sm font-medium text-zinc-700 dark:text-zinc-300">
                                Rp {{ number_format($day['revenue'], 0, ',', '.') }}
                            </span>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Best Selling --}}
            <div class="rounded-lg border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
                <h3 class="mb-5 text-base font-semibold text-zinc-900 dark:text-white">Produk Terlaris (Bulan Ini)</h3>
                <div class="space-y-4">
                    @forelse($bestSelling as $index => $item)
                        <div class="flex items-center gap-4">
                            <span class="flex h-8 w-8 items-center justify-center rounded-md bg-zinc-100 text-sm font-bold text-zinc-700 dark:bg-zinc-800 dark:text-zinc-300">
                                {{ $index + 1 }}
                            </span>
                            <div class="flex-1 min-w-0">
                                <p class="truncate text-sm font-medium text-zinc-900 dark:text-white">{{ $item->product_name }}</p>
                                <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-0.5">{{ $item->total_sold }} terjual</p>
                            </div>
                            <span class="text-sm font-semibold text-zinc-900 dark:text-white">
                                Rp {{ number_format($item->total_revenue, 0, ',', '.') }}
                            </span>
                        </div>
                    @empty
                        <div class="py-8 text-center">
                            <flux:icon name="cube" class="mx-auto h-8 w-8 text-zinc-300 dark:text-zinc-600 mb-3" />
                            <p class="text-sm text-zinc-500 dark:text-zinc-400">Belum ada data penjualan.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- Bottom Row --}}
        <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
            {{-- Recent Transactions --}}
            <div class="rounded-lg border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
                <h3 class="mb-5 text-base font-semibold text-zinc-900 dark:text-white">Transaksi Terakhir</h3>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b border-zinc-200 dark:border-zinc-800">
                                <th class="pb-3 text-left font-medium text-zinc-500 dark:text-zinc-400">Invoice</th>
                                <th class="pb-3 text-left font-medium text-zinc-500 dark:text-zinc-400">Kasir</th>
                                <th class="pb-3 text-right font-medium text-zinc-500 dark:text-zinc-400">Total</th>
                                <th class="pb-3 text-right font-medium text-zinc-500 dark:text-zinc-400">Metode</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800/60">
                            @forelse($recentTransactions as $trx)
                                <tr class="transition-colors hover:bg-zinc-50 dark:hover:bg-zinc-800/50">
                                    <td class="py-3 font-medium text-zinc-900 dark:text-white">{{ $trx->invoice_number }}</td>
                                    <td class="py-3 text-zinc-600 dark:text-zinc-400">{{ $trx->user->name ?? '-' }}</td>
                                    <td class="py-3 text-right font-medium text-zinc-900 dark:text-white">Rp {{ number_format($trx->grand_total, 0, ',', '.') }}</td>
                                    <td class="py-3 text-right">
                                        <span class="inline-flex items-center rounded-md px-2 py-1 text-xs font-medium border
                                            {{ $trx->payment?->method === 'cash' ? 'border-emerald-200 bg-emerald-50 text-emerald-700 dark:border-emerald-900/50 dark:bg-emerald-900/20 dark:text-emerald-400' : '' }}
                                            {{ $trx->payment?->method === 'qris' ? 'border-blue-200 bg-blue-50 text-blue-700 dark:border-blue-900/50 dark:bg-blue-900/20 dark:text-blue-400' : '' }}
                                            {{ $trx->payment?->method === 'va' ? 'border-violet-200 bg-violet-50 text-violet-700 dark:border-violet-900/50 dark:bg-violet-900/20 dark:text-violet-400' : '' }}
                                        ">
                                            {{ strtoupper($trx->payment?->method ?? '-') }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="py-8 text-center text-zinc-500 dark:text-zinc-400">Belum ada transaksi terakhir.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Low Stock Alert --}}
            <div class="rounded-lg border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
                <div class="mb-5 flex items-center justify-between">
                    <h3 class="text-base font-semibold text-zinc-900 dark:text-white">Peringatan Stok Rendah</h3>
                    @if($lowStockProducts->count() > 0)
                        <span class="inline-flex items-center rounded-full bg-red-50 px-3 py-1 text-xs font-semibold text-red-700 ring-1 ring-inset ring-red-600/10 dark:bg-red-900/30 dark:text-red-400 dark:ring-red-900/50">
                            {{ $lowStockProducts->count() }} produk
                        </span>
                    @endif
                </div>
                <div class="space-y-4">
                    @forelse($lowStockProducts as $product)
                        <div class="flex items-center justify-between rounded-lg border border-red-100 bg-red-50/50 p-4 dark:border-red-900/30 dark:bg-red-900/10">
                            <div>
                                <p class="text-sm font-semibold text-zinc-900 dark:text-white">{{ $product->name }}</p>
                                <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-1">SKU: <span class="font-mono">{{ $product->sku }}</span></p>
                            </div>
                            <div class="text-right">
                                <span class="inline-flex items-center rounded-md px-2.5 py-1 text-xs font-bold ring-1 ring-inset
                                    {{ $product->stock <= 0 ? 'bg-red-50 text-red-700 ring-red-600/20 dark:bg-red-900/30 dark:text-red-400 dark:ring-red-900/50' : 'bg-amber-50 text-amber-700 ring-amber-600/20 dark:bg-amber-900/30 dark:text-amber-400 dark:ring-amber-900/50' }}">
                                    Sisa {{ $product->stock }}
                                </span>
                            </div>
                        </div>
                    @empty
                        <div class="flex flex-col items-center justify-center py-10 text-center">
                            <div class="flex h-12 w-12 items-center justify-center rounded-full bg-emerald-50 dark:bg-emerald-900/30">
                                <flux:icon name="check" class="h-6 w-6 text-emerald-600 dark:text-emerald-400" />
                            </div>
                            <p class="mt-4 text-sm font-medium text-zinc-900 dark:text-white">Semua stok aman!</p>
                            <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">Tidak ada produk yang butuh restock.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
