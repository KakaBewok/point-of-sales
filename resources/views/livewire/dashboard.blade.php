<div class="px-1 md:px-4 py-6 sm:px-6 lg:px-8 space-y-6 sm:space-y-8 max-w-7xl mx-auto flex-1 w-full">

    {{-- ═══════════════════════════════════════════════════════════
         HEADER
    ═══════════════════════════════════════════════════════════ --}}
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-2">
        <div>
            <h1 class="text-3xl sm:text-4xl font-bold tracking-tight text-zinc-900 dark:text-white">Dashboard</h1>
            <p class="mt-0.5 sm:mt-1 text-xs sm:text-sm text-zinc-500 dark:text-zinc-400">Selamat datang kembali, {{ auth()->user()->name }} !</p>
        </div>
        <div class="text-xs sm:text-sm font-medium text-zinc-500 dark:text-zinc-400 bg-zinc-100 dark:bg-zinc-700 px-3 py-1.5 rounded-lg">
            {{ now()->translatedFormat('l, d F Y') }}
        </div>
    </div>

    {{-- Subscription Banners --}}
    @if(auth()->user()->store)
        @php
            $store = auth()->user()->store;
            $now = now();
        @endphp

        @if($store->isExpired() || ($store->isTrial() && $store->isTrialExpired()) || ($store->isActive() && $store->subscription_ends_at && $store->subscription_ends_at->isPast()))
            <div class="rounded-xl bg-red-50 border border-red-200 p-4 shadow-sm dark:bg-red-900/20 dark:border-red-900/50">
                <div class="flex items-start">
                    <div class="shrink-0">
                        <flux:icon name="exclamation-triangle" class="h-5 w-5 text-red-600 dark:text-red-400" />
                    </div>
                    <div class="ml-3 text-sm text-red-800 dark:text-red-300">
                        <p class="font-medium">Langganan Anda telah berakhir.</p>
                        <p class="mt-1">Harap segera perpanjang untuk mengembalikan akses sistem Anda.</p>
                    </div>
                </div>
            </div>
        @elseif($store->isTrial())
            @php
                $daysLeft = $store->trial_ends_at ? $now->startOfDay()->diffInDays($store->trial_ends_at->startOfDay(), false) : 0;
            @endphp
            @if($daysLeft <= 2 && $daysLeft >= 0)
                <div class="rounded-xl bg-yellow-50 border border-yellow-200 p-4 shadow-sm dark:bg-yellow-900/20 dark:border-yellow-900/50">
                    <div class="flex items-start">
                        <div class="shrink-0">
                            <flux:icon name="clock" class="h-5 w-5 text-yellow-600 dark:text-yellow-400" />
                        </div>
                        <div class="ml-3 text-sm text-yellow-800 dark:text-yellow-300">
                            <p class="font-medium">Masa percobaan gratis Anda akan berakhir dalam {{ $daysLeft }} hari.</p>
                            <p class="text-xs md:text-sm mt-1 font-extralight text-yellow-600">Harap berlangganan untuk tetap bisa menggunakan sistem setelah masa percobaan.</p>
                        </div>
                    </div>
                </div>
            @endif
        @elseif($store->isActive() && $store->subscription_ends_at)
            @php
                $daysLeft = $now->startOfDay()->diffInDays($store->subscription_ends_at->startOfDay(), false);
            @endphp
            @if($daysLeft <= 3 && $daysLeft >= 0)
                <div class="rounded-xl bg-yellow-50 border border-yellow-200 p-4 shadow-sm dark:bg-yellow-900/20 dark:border-yellow-900/50">
                    <div class="flex items-start">
                        <div class="shrink-0">
                            <flux:icon name="clock" class="h-5 w-5 text-yellow-600 dark:text-yellow-400" />
                        </div>
                        <div class="ml-3 text-sm text-yellow-800 dark:text-yellow-300">
                            <p class="font-medium">Langganan Anda akan berakhir dalam {{ $daysLeft }} hari.</p>
                            <p class="text-xs md:text-sm mt-1 font-extralight text-yellow-600">Harap perpanjang untuk menghindari gangguan layanan.</p>
                        </div>
                    </div>
                </div>
            @endif
        @endif
    @endif

    {{-- ═══════════════════════════════════════════════════════════
         SECTION 1: SALES OVERVIEW
    ═══════════════════════════════════════════════════════════ --}}
    <section>
        {{-- Section Header --}}
        <div class="flex items-center gap-3 mb-5">
            <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-emerald-100 dark:bg-emerald-900/40">
                <flux:icon name="chart-bar" class="h-5 w-5 text-emerald-600 dark:text-emerald-400" />
            </div>
            <div>
                <h2 class="text-base sm:text-lg font-semibold text-zinc-900 dark:text-white">Ringkasan Penjualan</h2>
                <p class="text-xs text-zinc-500 dark:text-zinc-400">Statistik pendapatan dan transaksi</p>
            </div>
        </div>

        {{-- Sales Stat Cards --}}
        <div class="grid grid-cols-1 gap-4 sm:gap-5 sm:grid-cols-2 lg:grid-cols-4">
            {{-- Today Revenue --}}
            <div class="group rounded-xl border border-zinc-200 bg-white p-4 sm:p-5 shadow-sm hover:shadow-md transition-all duration-200 dark:border-zinc-800 dark:bg-zinc-900 dark:hover:border-zinc-700">
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-emerald-50 dark:bg-emerald-900/30 shrink-0 group-hover:scale-105 transition-transform duration-200">
                        <flux:icon name="banknotes" class="h-5 w-5 text-emerald-600 dark:text-emerald-400" />
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="text-xs font-medium text-zinc-500 dark:text-zinc-400 truncate">Pendapatan Hari Ini</p>
                        <p class="text-lg sm:text-xl font-bold tracking-tight text-zinc-900 dark:text-white mt-0.5 truncate">Rp {{ number_format($todayRevenue, 0, ',', '.') }}</p>
                    </div>
                </div>
            </div>

            {{-- Today Transactions --}}
            <div class="group rounded-xl border border-zinc-200 bg-white p-4 sm:p-5 shadow-sm hover:shadow-md transition-all duration-200 dark:border-zinc-800 dark:bg-zinc-900 dark:hover:border-zinc-700">
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-blue-50 dark:bg-blue-900/30 shrink-0 group-hover:scale-105 transition-transform duration-200">
                        <flux:icon name="shopping-cart" class="h-5 w-5 text-blue-600 dark:text-blue-400" />
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="text-xs font-medium text-zinc-500 dark:text-zinc-400 truncate">Transaksi Hari Ini</p>
                        <p class="text-lg sm:text-xl font-bold tracking-tight text-zinc-900 dark:text-white mt-0.5">{{ $todayTransactions }}</p>
                    </div>
                </div>
            </div>

            {{-- Monthly Revenue --}}
            <div class="group rounded-xl border border-zinc-200 bg-white p-4 sm:p-5 shadow-sm hover:shadow-md transition-all duration-200 dark:border-zinc-800 dark:bg-zinc-900 dark:hover:border-zinc-700">
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-violet-50 dark:bg-violet-900/30 shrink-0 group-hover:scale-105 transition-transform duration-200">
                        <flux:icon name="chart-bar" class="h-5 w-5 text-violet-600 dark:text-violet-400" />
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="text-xs font-medium text-zinc-500 dark:text-zinc-400 truncate">Pendapatan Bulan Ini</p>
                        <p class="text-lg sm:text-xl font-bold tracking-tight text-zinc-900 dark:text-white mt-0.5 truncate">Rp {{ number_format($monthlyRevenue, 0, ',', '.') }}</p>
                    </div>
                </div>
            </div>

            {{-- Total Products --}}
            <div class="group rounded-xl border border-zinc-200 bg-white p-4 sm:p-5 shadow-sm hover:shadow-md transition-all duration-200 dark:border-zinc-800 dark:bg-zinc-900 dark:hover:border-zinc-700">
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-amber-50 dark:bg-amber-900/30 shrink-0 group-hover:scale-105 transition-transform duration-200">
                        <flux:icon name="cube" class="h-5 w-5 text-amber-600 dark:text-amber-400" />
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="text-xs font-medium text-zinc-500 dark:text-zinc-400 truncate">Total Produk Aktif</p>
                        <p class="text-lg sm:text-xl font-bold tracking-tight text-zinc-900 dark:text-white mt-0.5">{{ $totalProducts }}</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Top Cashier Today Widget --}}
        @if(auth()->user()->canAccessAdminMenu())
        <div class="mt-5">
            <div class="rounded-xl border border-violet-200/70 bg-gradient-to-br from-violet-50 via-indigo-50 to-white p-5 shadow-sm dark:border-violet-800/40 dark:from-violet-900/20 dark:via-indigo-900/15 dark:to-zinc-900 relative overflow-hidden">
                {{-- Decorative bg element --}}
                <div class="absolute top-0 right-0 w-32 h-32 bg-violet-200/30 dark:bg-violet-800/10 rounded-full -translate-y-1/2 translate-x-1/2 blur-2xl"></div>
                <div class="relative z-10">
                    <div class="flex items-center gap-2 mb-3">
                        <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-amber-100 dark:bg-amber-900/40">
                            <flux:icon name="trophy" class="h-4 w-4 text-amber-600 dark:text-amber-400" />
                        </div>
                        <h3 class="text-sm font-semibold text-violet-700 dark:text-violet-300">Top Kasir Hari Ini</h3>
                    </div>
                    @if($topCashierToday)
                        <div class="flex flex-col sm:flex-row items-start sm:items-center gap-3 sm:gap-6">
                            <div class="flex items-center gap-3">
                                <div class="flex h-11 w-11 items-center justify-center rounded-full bg-gradient-to-br from-violet-500 to-indigo-600 text-white font-bold text-sm shadow-md">
                                    {{ strtoupper(substr($topCashierToday->cashier_name, 0, 2)) }}
                                </div>
                                <div>
                                    <p class="font-bold text-zinc-900 dark:text-white text-base">{{ $topCashierToday->cashier_name }}</p>
                                    <p class="text-xs text-zinc-500 dark:text-zinc-400">Kasir terbaik hari ini</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-4 sm:ml-auto">
                                <div class="text-center px-3 py-1.5 rounded-lg bg-white/70 dark:bg-zinc-800/50 border border-violet-100 dark:border-violet-800/30">
                                    <p class="text-[10px] uppercase tracking-wider font-medium text-zinc-500 dark:text-zinc-400">Pendapatan</p>
                                    <p class="text-sm font-bold text-emerald-600 dark:text-emerald-400">Rp {{ number_format($topCashierToday->total_sales, 0, ',', '.') }}</p>
                                </div>
                                <div class="text-center px-3 py-1.5 rounded-lg bg-white/70 dark:bg-zinc-800/50 border border-violet-100 dark:border-violet-800/30">
                                    <p class="text-[10px] uppercase tracking-wider font-medium text-zinc-500 dark:text-zinc-400">Transaksi</p>
                                    <p class="text-sm font-bold text-blue-600 dark:text-blue-400">{{ $topCashierToday->total_transactions }}</p>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="flex items-center gap-3 py-2">
                            <flux:icon name="clock" class="h-5 w-5 text-zinc-400 dark:text-zinc-500" />
                            <p class="text-sm text-zinc-500 dark:text-zinc-400">Belum ada transaksi hari ini.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        @endif

        {{-- Sales Charts --}}
        <div class="grid grid-cols-1 gap-4 sm:gap-5 lg:grid-cols-3 mt-5">
            {{-- Revenue Chart (7 days) --}}
            <div class="lg:col-span-2 rounded-xl border border-zinc-200 bg-white p-4 sm:p-6 shadow-sm hover:shadow-md transition-all duration-200 dark:border-zinc-800 dark:bg-zinc-900">
                <h3 class="mb-4 sm:mb-5 text-sm sm:text-base font-semibold text-zinc-900 dark:text-white flex items-center gap-2">
                    <flux:icon name="arrow-trending-up" class="h-4 w-4 text-emerald-500" />
                    Pendapatan 7 Hari Terakhir
                </h3>
                <div class="space-y-3">
                    @php $maxRevenue = $chartData->max('revenue') ?: 1; @endphp
                    @foreach($chartData as $day)
                        <div class="flex items-center gap-2 sm:gap-4">
                            <span class="w-12 sm:w-16 text-xs sm:text-sm text-zinc-500 dark:text-zinc-400 shrink-0">{{ $day['date'] }}</span>
                            <div class="flex-1">
                                <div class="h-5 sm:h-6 w-full rounded-md bg-zinc-100 dark:bg-zinc-800 overflow-hidden">
                                    <div class="h-full rounded-md bg-emerald-500 dark:bg-emerald-400 transition-all duration-500"
                                         style="width: {{ ($day['revenue'] / $maxRevenue) * 100 }}%"></div>
                                </div>
                            </div>
                            <span class="w-20 sm:w-28 text-right text-xs sm:text-sm font-medium text-zinc-700 dark:text-zinc-300 shrink-0">
                                Rp {{ number_format($day['revenue'], 0, ',', '.') }}
                            </span>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Best Selling --}}
            <div class="rounded-xl border border-zinc-200 bg-white p-4 sm:p-6 shadow-sm hover:shadow-md transition-all duration-200 dark:border-zinc-800 dark:bg-zinc-900">
                <h3 class="mb-4 sm:mb-5 text-sm sm:text-base font-semibold text-zinc-900 dark:text-white flex items-center gap-2">
                    <flux:icon name="trophy" class="h-4 w-4 text-amber-500" />
                    Produk Terlaris Bulan Ini
                </h3>
                <div class="space-y-3 sm:space-y-4">
                    @forelse($bestSelling as $index => $item)
                        <div class="flex items-center gap-3 w-full">
                            <span class="flex h-7 w-7 sm:h-8 sm:w-8 items-center justify-center rounded-lg bg-zinc-100 text-xs sm:text-sm font-bold text-zinc-700 dark:bg-zinc-800 dark:text-zinc-300 shrink-0">
                                {{ $index + 1 }}
                            </span>
                            <div class="flex-1 min-w-0">
                                <p class="truncate text-xs sm:text-sm font-medium text-zinc-900 dark:text-white">{{ $item->product_name }}</p>
                                <p class="text-[10px] sm:text-xs text-zinc-500 dark:text-zinc-400 mt-0.5">{{ $item->total_sold }} terjual</p>
                            </div>
                            <span class="text-xs sm:text-sm font-semibold text-zinc-900 dark:text-white shrink-0">
                                Rp {{ number_format($item->total_revenue, 0, ',', '.') }}
                            </span>
                        </div>
                    @empty
                        <div class="py-6 sm:py-8 text-center">
                            <flux:icon name="cube" class="mx-auto h-7 w-7 sm:h-8 sm:w-8 text-zinc-300 dark:text-zinc-600 mb-2 sm:mb-3" />
                            <p class="text-xs sm:text-sm text-zinc-500 dark:text-zinc-400">Belum ada data penjualan.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </section>

    {{-- ═══════════════════════════════════════════════════════════
         SECTION 2: EXPENSE OVERVIEW
    ═══════════════════════════════════════════════════════════ --}}
    <section>
        {{-- Section Header --}}
        <div class="flex items-center gap-3 mb-5">
            <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-red-100 dark:bg-red-900/40">
                <flux:icon name="arrow-trending-down" class="h-5 w-5 text-red-600 dark:text-red-400" />
            </div>
            <div>
                <h2 class="text-base sm:text-lg font-semibold text-zinc-900 dark:text-white">Ringkasan Pengeluaran</h2>
                <p class="text-xs text-zinc-500 dark:text-zinc-400">Statistik pengeluaran dan kategori</p>
            </div>
        </div>

        {{-- Expense Stat Cards --}}
        <div class="grid grid-cols-1 gap-4 sm:gap-5 sm:grid-cols-2">
            {{-- Today Expense --}}
            <div class="group rounded-xl border border-red-200/70 bg-linear-to-br from-red-50 to-white p-4 sm:p-5 shadow-sm hover:shadow-md transition-all duration-200 dark:border-red-800/40 dark:from-red-900/20 dark:to-zinc-900 dark:hover:border-red-700/50">
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-red-100 dark:bg-red-900/40 shrink-0 group-hover:scale-105 transition-transform duration-200">
                        <flux:icon name="arrow-trending-down" class="h-5 w-5 text-red-600 dark:text-red-400" />
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="text-xs font-medium text-red-600/80 dark:text-red-400/80 truncate">Pengeluaran Hari Ini</p>
                        <p class="text-lg sm:text-xl font-bold tracking-tight text-red-700 dark:text-red-300 mt-0.5 truncate">Rp {{ number_format($todayExpenses, 0, ',', '.') }}</p>
                    </div>
                </div>
            </div>

            {{-- Monthly Expense --}}
            <div class="group rounded-xl border border-red-200/70 bg-linear-to-br from-red-50 to-white p-4 sm:p-5 shadow-sm hover:shadow-md transition-all duration-200 dark:border-red-800/40 dark:from-red-900/20 dark:to-zinc-900 dark:hover:border-red-700/50">
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-red-100 dark:bg-red-900/40 shrink-0 group-hover:scale-105 transition-transform duration-200">
                        <flux:icon name="calculator" class="h-5 w-5 text-red-600 dark:text-red-400" />
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="text-xs font-medium text-red-600/80 dark:text-red-400/80 truncate">Pengeluaran Bulan Ini</p>
                        <p class="text-lg sm:text-xl font-bold tracking-tight text-red-700 dark:text-red-300 mt-0.5 truncate">Rp {{ number_format($monthlyExpenses, 0, ',', '.') }}</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Expense Charts --}}
        <div class="grid grid-cols-1 gap-4 sm:gap-5 lg:grid-cols-3 mt-5">
            {{-- Expense Trend (7 days) --}}
            <div class="lg:col-span-2 rounded-xl border border-zinc-200 bg-white p-4 sm:p-6 shadow-sm hover:shadow-md transition-all duration-200 dark:border-zinc-800 dark:bg-zinc-900">
                <h3 class="mb-4 sm:mb-5 text-sm sm:text-base font-semibold text-zinc-900 dark:text-white flex items-center gap-2">
                    <flux:icon name="arrow-trending-down" class="h-4 w-4 text-red-500" />
                    Pengeluaran 7 Hari Terakhir
                </h3>
                <div class="space-y-3">
                    @php $maxExpense = $expenseChartData->max('total') ?: 1; @endphp
                    @foreach($expenseChartData as $day)
                        <div class="flex items-center gap-2 sm:gap-4">
                            <span class="w-12 sm:w-16 text-xs sm:text-sm text-zinc-500 dark:text-zinc-400 shrink-0">{{ $day['date'] }}</span>
                            <div class="flex-1">
                                <div class="h-5 sm:h-6 w-full rounded-md bg-zinc-100 dark:bg-zinc-800 overflow-hidden">
                                    <div class="h-full rounded-md bg-red-500 dark:bg-red-400 transition-all duration-500"
                                         style="width: {{ ($day['total'] / $maxExpense) * 100 }}%"></div>
                                </div>
                            </div>
                            <span class="w-20 sm:w-28 text-right text-xs sm:text-sm font-medium text-zinc-700 dark:text-zinc-300 shrink-0">
                                Rp {{ number_format($day['total'], 0, ',', '.') }}
                            </span>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Expense by Category --}}
            <div class="rounded-xl border border-zinc-200 bg-white p-4 sm:p-6 shadow-sm hover:shadow-md transition-all duration-200 dark:border-zinc-800 dark:bg-zinc-900">
                <h3 class="mb-4 sm:mb-5 text-sm sm:text-base font-semibold text-zinc-900 dark:text-white flex items-center gap-2">
                    <flux:icon name="tag" class="h-4 w-4 text-red-500" />
                    Pengeluaran per Kategori
                </h3>
                <div class="space-y-4">
                    @php $maxCatExpense = $expenseByCategory->max('total') ?: 1; @endphp
                    @forelse($expenseByCategory as $index => $item)
                        <div class="space-y-1.5">
                            <div class="flex items-center justify-between">
                                <span class="text-xs sm:text-sm font-medium text-zinc-900 dark:text-white truncate">{{ $item['name'] }}</span>
                                <span class="text-xs sm:text-sm font-semibold text-red-600 dark:text-red-400 ml-2 shrink-0">Rp {{ number_format($item['total'], 0, ',', '.') }}</span>
                            </div>
                            <div class="h-2 w-full rounded-full bg-zinc-100 dark:bg-zinc-800 overflow-hidden">
                                <div class="h-full rounded-full bg-red-500 dark:bg-red-400 transition-all duration-500"
                                     style="width: {{ ($item['total'] / $maxCatExpense) * 100 }}%"></div>
                            </div>
                        </div>
                    @empty
                        <div class="py-6 sm:py-8 text-center">
                            <flux:icon name="banknotes" class="mx-auto h-7 w-7 sm:h-8 sm:w-8 text-zinc-300 dark:text-zinc-600 mb-2 sm:mb-3" />
                            <p class="text-xs sm:text-sm text-zinc-500 dark:text-zinc-400">Belum ada data pengeluaran bulan ini.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </section>

    {{-- ═══════════════════════════════════════════════════════════
         SECTION 3: ACTIVITY & ALERTS
    ═══════════════════════════════════════════════════════════ --}}
    <section>
        {{-- Section Header --}}
        <div class="flex items-center gap-3 mb-5">
            <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-blue-100 dark:bg-blue-900/40">
                <flux:icon name="bell-alert" class="h-5 w-5 text-blue-600 dark:text-blue-400" />
            </div>
            <div>
                <h2 class="text-base sm:text-lg font-semibold text-zinc-900 dark:text-white">Aktivitas & Peringatan</h2>
                <p class="text-xs text-zinc-500 dark:text-zinc-400">Transaksi terbaru dan peringatan stok</p>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-4 sm:gap-5 lg:grid-cols-2">
            {{-- Recent Transactions --}}
            <div class="rounded-xl border border-zinc-200 bg-white p-4 sm:p-6 shadow-sm hover:shadow-md transition-all duration-200 dark:border-zinc-800 dark:bg-zinc-900">
                <h3 class="mb-4 sm:mb-5 text-sm sm:text-base font-semibold text-zinc-900 dark:text-white flex items-center gap-2">
                    <flux:icon name="clock" class="h-4 w-4 text-blue-500" />
                    Transaksi Terakhir
                </h3>
                <div class="overflow-x-auto -mx-4 sm:-mx-6">
                    <div class="px-4 sm:px-6">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="border-b border-zinc-200 dark:border-zinc-800">
                                    <th class="hidden md:table-cell pb-3 text-left font-medium text-xs text-zinc-500 dark:text-zinc-400">Invoice</th>
                                    <th class="pb-3 text-left font-medium text-xs text-zinc-500 dark:text-zinc-400">Kasir</th>
                                    <th class="pb-3 text-right font-medium text-xs text-zinc-500 dark:text-zinc-400">Total</th>
                                    <th class="pb-3 text-right font-medium text-xs text-zinc-500 dark:text-zinc-400">Metode</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800/60">
                                @forelse($recentTransactions as $trx)
                                    <tr class="transition-colors hover:bg-zinc-50 dark:hover:bg-zinc-800/50">
                                        <td class="hidden md:table-cell py-2.5 sm:py-3 font-medium text-xs sm:text-sm text-zinc-900 dark:text-white">{{ $trx->invoice_number }}</td>
                                        <td class="py-2.5 sm:py-3 text-xs sm:text-sm text-zinc-600 dark:text-zinc-400">{{ $trx->user->name ?? '-' }}</td>
                                        <td class="py-2.5 sm:py-3 text-right font-medium text-xs sm:text-sm text-zinc-900 dark:text-white">Rp {{ number_format($trx->grand_total, 0, ',', '.') }}</td>
                                        <td class="py-2.5 sm:py-3 text-right">
                                            <span class="inline-flex items-center rounded-md px-2 py-0.5 text-[10px] sm:text-xs font-medium border
                                                {{ $trx->payment?->method === 'cash' ? 'border-emerald-200 bg-emerald-50 text-emerald-700 dark:border-emerald-900/50 dark:bg-emerald-900/20 dark:text-emerald-400' : '' }}
                                                {{ $trx->payment?->method === 'qris' ? 'border-blue-200 bg-blue-50 text-blue-700 dark:border-blue-900/50 dark:bg-blue-900/20 dark:text-blue-400' : '' }}
                                            ">
                                                {{ strtoupper($trx->payment?->method ?? '-') }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="4" class="py-6 sm:py-8 text-center text-xs sm:text-sm text-zinc-500 dark:text-zinc-400">Belum ada transaksi terakhir.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- Low Stock Alert --}}
            <div class="rounded-xl border border-zinc-200 bg-white p-4 sm:p-6 shadow-sm hover:shadow-md transition-all duration-200 dark:border-zinc-800 dark:bg-zinc-900">
                <div class="mb-4 sm:mb-5 flex items-center justify-between">
                    <h3 class="text-sm sm:text-base font-semibold text-zinc-900 dark:text-white flex items-center gap-2">
                        <flux:icon name="exclamation-triangle" class="h-4 w-4 text-amber-500" />
                        Peringatan Stok Rendah
                    </h3>
                    @if($lowStockProducts->count() > 0)
                        <span class="inline-flex items-center rounded-full bg-red-50 px-2.5 py-0.5 text-[10px] sm:text-xs font-semibold text-red-700 ring-1 ring-inset ring-red-600/10 dark:bg-red-900/30 dark:text-red-400 dark:ring-red-900/50">
                            {{ $lowStockProducts->count() }} produk
                        </span>
                    @endif
                </div>
                <div class="space-y-3">
                    @forelse($lowStockProducts as $product)
                        <div class="flex items-center justify-between rounded-lg border border-red-100 bg-red-50/50 p-3 sm:p-4 dark:border-red-900/30 dark:bg-red-900/10">
                            <div class="min-w-0 flex-1">
                                <p class="text-xs sm:text-sm font-semibold text-zinc-900 dark:text-white truncate">{{ $product->name }}</p>
                                <p class="text-[10px] sm:text-xs text-zinc-500 dark:text-zinc-400 mt-0.5">SKU: <span class="font-mono">{{ $product->sku }}</span></p>
                            </div>
                            <div class="text-right ml-3 shrink-0">
                                <span class="inline-flex items-center rounded-md px-2 py-0.5 text-[10px] sm:text-xs font-bold ring-1 ring-inset
                                    {{ $product->stock <= 0 ? 'bg-red-50 text-red-700 ring-red-600/20 dark:bg-red-900/30 dark:text-red-400 dark:ring-red-900/50' : 'bg-amber-50 text-amber-700 ring-amber-600/20 dark:bg-amber-900/30 dark:text-amber-400 dark:ring-amber-900/50' }}">
                                    Sisa {{ $product->stock }}
                                </span>
                            </div>
                        </div>
                    @empty
                        <div class="flex flex-col items-center justify-center py-8 sm:py-10 text-center">
                            <div class="flex h-10 w-10 sm:h-12 sm:w-12 items-center justify-center rounded-full bg-emerald-50 dark:bg-emerald-900/30">
                                <flux:icon name="check" class="h-5 w-5 sm:h-6 sm:w-6 text-emerald-600 dark:text-emerald-400" />
                            </div>
                            <p class="mt-3 sm:mt-4 text-xs sm:text-sm font-medium text-zinc-900 dark:text-white">Semua stok aman!</p>
                            <p class="mt-0.5 sm:mt-1 text-[10px] sm:text-sm text-zinc-500 dark:text-zinc-400">Tidak ada produk yang butuh restock.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </section>
</div>
