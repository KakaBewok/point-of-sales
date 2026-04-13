<div class="px-0 py-8 md:px-6 space-y-8 max-w-7xl mx-auto flex-1 w-full">
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-lg md:text-2xl font-bold tracking-tight text-zinc-900 dark:text-white">Performa Kasir</h1>
            <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">Analisis performa kasir berdasarkan aktivitas penjualan</p>
        </div>
        <div class="flex items-center gap-3">
            <flux:button variant="primary" icon="arrow-down-tray" class="h-10 px-4 cursor-pointer" wire:click="exportExcel" wire:loading.attr="disabled">
                <span wire:loading.remove wire:target="exportExcel">Export Excel</span>
                <span wire:loading wire:target="exportExcel">Mengunduh...</span>
            </flux:button>
        </div>
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

    {{-- Filters --}}
    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3 rounded-xl border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
        <flux:input type="date" label="Dari Tanggal" class="h-10" wire:model.live="startDate" />
        <flux:input type="date" label="Sampai Tanggal" class="h-10" wire:model.live="endDate" />
        <flux:select label="Kasir" class="h-10" wire:model.live="cashierId">
            <flux:select.option value="">Semua Kasir</flux:select.option>
            @foreach($availableCashiers as $cashier)
                <flux:select.option value="{{ $cashier->id }}">{{ $cashier->name }}</flux:select.option>
            @endforeach
        </flux:select>
    </div>

    {{-- Summary Cards --}}
    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3">
        {{-- Total Cashiers --}}
        <div class="group rounded-xl border border-zinc-200 bg-white p-5 shadow-sm hover:shadow-md transition-all duration-200 dark:border-zinc-800 dark:bg-zinc-900 dark:hover:border-zinc-700">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-violet-50 dark:bg-violet-900/30 shrink-0 group-hover:scale-105 transition-transform duration-200">
                    <flux:icon name="users" class="h-5 w-5 text-violet-600 dark:text-violet-400" />
                </div>
                <div class="min-w-0 flex-1">
                    <p class="text-xs font-medium text-zinc-500 dark:text-zinc-400 truncate">Kasir Aktif</p>
                    <p class="text-xl font-bold tracking-tight text-zinc-900 dark:text-white mt-0.5">{{ $performanceData->count() }}</p>
                </div>
            </div>
        </div>

        {{-- Total Transactions --}}
        <div class="group rounded-xl border border-zinc-200 bg-white p-5 shadow-sm hover:shadow-md transition-all duration-200 dark:border-zinc-800 dark:bg-zinc-900 dark:hover:border-zinc-700">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-blue-50 dark:bg-blue-900/30 shrink-0 group-hover:scale-105 transition-transform duration-200">
                    <flux:icon name="shopping-cart" class="h-5 w-5 text-blue-600 dark:text-blue-400" />
                </div>
                <div class="min-w-0 flex-1">
                    <p class="text-xs font-medium text-zinc-500 dark:text-zinc-400 truncate">Total Transaksi</p>
                    <p class="text-xl font-bold tracking-tight text-zinc-900 dark:text-white mt-0.5">{{ number_format($totalTransactions) }}</p>
                </div>
            </div>
        </div>

        {{-- Total Revenue --}}
        <div class="group rounded-xl border border-emerald-200/70 bg-linear-to-br from-emerald-50 to-white p-5 shadow-sm hover:shadow-md transition-all duration-200 dark:border-emerald-800/40 dark:from-emerald-900/20 dark:to-zinc-900 dark:hover:border-emerald-700/50 sm:col-span-2 lg:col-span-1">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-emerald-100 dark:bg-emerald-900/40 shrink-0 group-hover:scale-105 transition-transform duration-200">
                    <flux:icon name="banknotes" class="h-5 w-5 text-emerald-600 dark:text-emerald-400" />
                </div>
                <div class="min-w-0 flex-1">
                    <p class="text-xs font-medium text-emerald-600/80 dark:text-emerald-400/80 truncate">Total Pendapatan</p>
                    <p class="text-xl font-bold tracking-tight text-emerald-700 dark:text-emerald-300 mt-0.5 truncate">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Performance Table --}}
    <div class="overflow-hidden rounded-xl border border-zinc-200 bg-white shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-zinc-50 border-b border-zinc-200 dark:bg-zinc-900/50 dark:border-zinc-800">
                    <tr>
                        <th class="px-6 py-4 text-left font-semibold text-zinc-600 dark:text-zinc-400">
                            <span class="flex items-center gap-1">
                                <flux:icon name="user" class="h-4 w-4" />
                                Nama Kasir
                            </span>
                        </th>
                        <th class="px-6 py-4 text-center font-semibold text-zinc-600 dark:text-zinc-400 cursor-pointer hover:text-zinc-900 dark:hover:text-white transition-colors" wire:click="sortBy('total_transactions')">
                            <span class="flex items-center justify-center gap-1">
                                Total Transaksi
                                @if($sortField === 'total_transactions')
                                    <flux:icon name="{{ $sortDirection === 'asc' ? 'chevron-up' : 'chevron-down' }}" class="h-3.5 w-3.5 text-violet-500" />
                                @else
                                    <flux:icon name="chevron-up-down" class="h-3.5 w-3.5 opacity-30" />
                                @endif
                            </span>
                        </th>
                        <th class="px-6 py-4 text-right font-semibold text-zinc-600 dark:text-zinc-400 cursor-pointer hover:text-zinc-900 dark:hover:text-white transition-colors" wire:click="sortBy('total_revenue')">
                            <span class="flex items-center justify-end gap-1">
                                Total Pendapatan
                                @if($sortField === 'total_revenue')
                                    <flux:icon name="{{ $sortDirection === 'asc' ? 'chevron-up' : 'chevron-down' }}" class="h-3.5 w-3.5 text-violet-500" />
                                @else
                                    <flux:icon name="chevron-up-down" class="h-3.5 w-3.5 opacity-30" />
                                @endif
                            </span>
                        </th>
                        <th class="hidden md:table-cell px-6 py-4 text-right font-semibold text-zinc-600 dark:text-zinc-400 cursor-pointer hover:text-zinc-900 dark:hover:text-white transition-colors" wire:click="sortBy('avg_transaction')">
                            <span class="flex items-center justify-end gap-1">
                                Rata-rata
                                @if($sortField === 'avg_transaction')
                                    <flux:icon name="{{ $sortDirection === 'asc' ? 'chevron-up' : 'chevron-down' }}" class="h-3.5 w-3.5 text-violet-500" />
                                @else
                                    <flux:icon name="chevron-up-down" class="h-3.5 w-3.5 opacity-30" />
                                @endif
                            </span>
                        </th>
                        <th class="hidden lg:table-cell px-6 py-4 text-center font-semibold text-zinc-600 dark:text-zinc-400 cursor-pointer hover:text-zinc-900 dark:hover:text-white transition-colors" wire:click="sortBy('last_transaction_at')">
                            <span class="flex items-center justify-center gap-1">
                                Transaksi Terakhir
                                @if($sortField === 'last_transaction_at')
                                    <flux:icon name="{{ $sortDirection === 'asc' ? 'chevron-up' : 'chevron-down' }}" class="h-3.5 w-3.5 text-violet-500" />
                                @else
                                    <flux:icon name="chevron-up-down" class="h-3.5 w-3.5 opacity-30" />
                                @endif
                            </span>
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800/60">
                    @forelse($performanceData as $index => $data)
                        <tr class="transition-colors hover:bg-zinc-50 dark:hover:bg-zinc-800/50">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    {{-- Rank badge --}}
                                    @if($index === 0 && $performanceData->count() > 1)
                                        <span class="flex h-7 w-7 items-center justify-center rounded-lg bg-amber-50 text-xs font-bold text-amber-600 ring-1 ring-amber-200 dark:bg-amber-900/30 dark:text-amber-400 dark:ring-amber-900/50 shrink-0">
                                            <flux:icon name="trophy" class="h-3.5 w-3.5" />
                                        </span>
                                    @else
                                        <span class="flex h-7 w-7 items-center justify-center rounded-lg bg-zinc-100 text-xs font-bold text-zinc-500 dark:bg-zinc-800 dark:text-zinc-400 shrink-0">
                                            {{ $index + 1 }}
                                        </span>
                                    @endif
                                    <div class="font-medium text-zinc-900 dark:text-white">{{ $data->cashier_name }}</div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="inline-flex items-center rounded-md px-2.5 py-1 text-xs font-semibold ring-1 ring-inset bg-blue-50 text-blue-700 ring-blue-600/20 dark:bg-blue-900/30 dark:text-blue-400 dark:ring-blue-900/50">
                                    {{ number_format($data->total_transactions) }} transaksi
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <span class="font-bold text-emerald-600 dark:text-emerald-400">Rp {{ number_format($data->total_revenue, 0, ',', '.') }}</span>
                            </td>
                            <td class="hidden md:table-cell px-6 py-4 text-right">
                                <span class="text-zinc-600 dark:text-zinc-400">Rp {{ number_format($data->avg_transaction, 0, ',', '.') }}</span>
                            </td>
                            <td class="hidden lg:table-cell px-6 py-4 text-center">
                                <span class="text-xs text-zinc-500 dark:text-zinc-400">
                                    {{ $data->last_transaction_at ? \Carbon\Carbon::parse($data->last_transaction_at)->format('d/m/Y H:i') : '-' }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-16 text-center text-zinc-500">
                                <div class="flex flex-col items-center">
                                    <div class="flex h-12 w-12 items-center justify-center rounded-full bg-zinc-100 dark:bg-zinc-800 mb-4">
                                        <flux:icon name="chart-bar" class="h-6 w-6 text-zinc-400 dark:text-zinc-500" />
                                    </div>
                                    <p class="font-medium text-zinc-700 dark:text-zinc-300">Tidak ada data</p>
                                    <p class="text-sm text-zinc-500 dark:text-zinc-400 mt-1">Tidak ada transaksi kasir pada periode ini.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Revenue Distribution Bar (visual) --}}
        @if($performanceData->count() > 0)
            <div class="border-t border-zinc-200 dark:border-zinc-800 p-5">
                <h3 class="text-sm font-semibold text-zinc-700 dark:text-zinc-300 mb-4 flex items-center gap-2">
                    <flux:icon name="chart-bar" class="h-4 w-4 text-violet-500" />
                    Distribusi Pendapatan per Kasir
                </h3>
                <div class="space-y-3">
                    @php $maxRevenue = $performanceData->max('total_revenue') ?: 1; @endphp
                    @foreach($performanceData as $data)
                        <div class="flex items-center gap-3">
                            <span class="w-24 sm:w-32 text-xs sm:text-sm text-zinc-600 dark:text-zinc-400 truncate shrink-0">{{ $data->cashier_name }}</span>
                            <div class="flex-1">
                                <div class="h-5 sm:h-6 w-full rounded-md bg-zinc-100 dark:bg-zinc-800 overflow-hidden">
                                    <div class="h-full rounded-md bg-gradient-to-r from-violet-500 to-indigo-500 dark:from-violet-400 dark:to-indigo-400 transition-all duration-700"
                                         style="width: {{ ($data->total_revenue / $maxRevenue) * 100 }}%"></div>
                                </div>
                            </div>
                            <span class="w-24 sm:w-28 text-right text-xs sm:text-sm font-medium text-zinc-700 dark:text-zinc-300 shrink-0">
                                Rp {{ number_format($data->total_revenue, 0, ',', '.') }}
                            </span>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</div>
