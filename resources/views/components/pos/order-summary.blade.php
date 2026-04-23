@props([
    'cart',
    'manualDiscountType',
    'manualDiscountValue',
    'manualDiscountAmount',
    'voucherApplied',
    'voucherCode',
    'voucherDiscount',
    'subtotal',
    'totalDiscount',
    'taxRate',
    'taxAmount',
    'grandTotal',
])

<aside {{ $attributes->merge(['class' => 'flex flex-col bg-white dark:bg-zinc-950 mt-6 md:mt-0 md:border-l border-t md:border-t-0 border-zinc-200 dark:border-zinc-800 shrink-0 w-full md:w-[40%] lg:w-[35%] xl:w-[30%] shadow-2xl overflow-visible md:overflow-hidden relative z-10']) }}>
    
    {{-- Cart Header --}}
    <header class="p-4 bg-white dark:bg-zinc-900 border-b border-zinc-200 dark:border-zinc-800 shrink-0 flex items-center justify-between">
        <div>
            <h2 class="text-[11px] font-black uppercase tracking-widest text-zinc-950 dark:text-white">Daftar Belanja</h2>
            <p class="text-[9px] font-bold text-zinc-400 uppercase mt-0.5">{{ count($cart) }} Items</p>
        </div>
        @if(count($cart) > 0)
            <button wire:click="clearCart" class="text-[9px] font-black text-red-500 hover:bg-red-50 dark:hover:bg-red-950/30 px-2 py-1 rounded-lg transition-all uppercase tracking-tighter">Batal</button>
        @endif
    </header>

    {{-- Cart Content: Priority Area --}}
    <div class="flex-none md:flex-1 overflow-visible md:overflow-y-auto customized-scrollbar p-3 space-y-2">
        @forelse($cart as $key => $item)
            <div class="bg-zinc-50 dark:bg-zinc-900/50 p-3 rounded-2xl border border-zinc-100 dark:border-zinc-800 flex flex-col gap-3 group transition-all hover:border-green-300">
                <div class="flex justify-between items-start gap-3">
                    <div class="flex-1">
                        <h4 class="text-[11px] font-black text-zinc-800 dark:text-zinc-100 uppercase truncate leading-tight flex items-center gap-1">
                            {{ $item['name'] }}
                            @if(($item['type'] ?? 'product') === 'service')
                                <span class="text-[7px] bg-blue-100 text-blue-700 px-1 py-0.5 rounded-sm dark:bg-blue-900 dark:text-blue-300">JASA</span>
                            @endif
                        </h4>
                        <p class="text-[9px] font-bold text-zinc-400 mt-0.5">@ Rp{{ number_format($item['price'], 0, ',', '.') }}</p>
                    </div>
                    <button wire:click="removeFromCart('{{ $key }}')" class="h-6 w-6 text-zinc-300 hover:text-red-500 transition-colors flex items-center justify-center">
                        <flux:icon name="trash" class="h-3 w-3" />
                    </button>
                </div>
                <div class="flex items-center justify-between">
                    <div class="flex items-center bg-white dark:bg-zinc-800 rounded-lg p-0.5 border border-zinc-100 dark:border-zinc-700 shadow-sm">
                        <button wire:click="decrementQuantity('{{ $key }}')" class="h-7 w-7 flex items-center justify-center text-zinc-400 hover:text-red-500"><flux:icon name="minus" class="h-3 w-3" /></button>
                        <span class="w-8 text-center text-[11px] font-black dark:text-white">{{ $item['quantity'] }}</span>
                        <button wire:click="incrementQuantity('{{ $key }}')" class="h-7 w-7 flex items-center justify-center text-zinc-400 hover:text-green-500"><flux:icon name="plus" class="h-3 w-3" /></button>
                    </div>
                    <span class="text-xs font-black text-zinc-950 dark:text-white">
                        Rp{{ number_format($item['price'] * $item['quantity'], 0, ',', '.') }}
                    </span>
                </div>
            </div>
        @empty
            <div class="h-full flex flex-col items-center justify-center opacity-20 py-20 px-6">
                <flux:icon name="shopping-bag" class="h-14 w-14 mb-4" />
                <p class="text-[10px] font-black uppercase tracking-widest text-center">Pilih produk</p>
            </div>
        @endforelse
    </div>

    {{-- DISCOUNT & VOUCHER BUTTONS --}}
    <div class="px-3 py-3 bg-zinc-50 dark:bg-zinc-900/50 border-t border-zinc-100 dark:border-zinc-800 shrink-0 flex gap-2">
        @if($manualDiscountType && $manualDiscountValue > 0)
            <div class="flex-1 flex flex-col bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 rounded-xl overflow-hidden relative group">
                <button wire:click="openDiscountModal" class="flex-1 px-3 py-1.5 text-left hover:bg-amber-100 dark:hover:bg-amber-900/40 transition-colors">
                    <span class="text-[9px] font-black text-amber-600 dark:text-amber-500 uppercase block mb-0.5">Diskon ({{ $manualDiscountType === 'percentage' ? $manualDiscountValue.'%' : 'Rp' }})</span>
                    <span class="text-xs font-bold text-amber-700 dark:text-amber-400">Rp{{ number_format($manualDiscountAmount, 0, ',', '.') }}</span>
                </button>
                <button wire:click="resetDiscount" class="absolute right-0 top-0 bottom-0 w-8 flex items-center justify-center text-amber-400 hover:text-amber-600 hover:bg-amber-100 dark:hover:bg-amber-900/50 transition-colors border-l border-amber-200 dark:border-amber-800/50">
                    <flux:icon name="x-mark" class="h-3 w-3" />
                </button>
            </div>
        @else
            <button wire:click="openDiscountModal" class="flex-1 py-3 px-2 bg-white dark:bg-zinc-800 hover:bg-zinc-100 dark:hover:bg-zinc-700 border-2 border-zinc-200 dark:border-zinc-700 rounded-xl text-[10px] font-black uppercase text-zinc-600 dark:text-zinc-300 shadow-sm transition-colors flex items-center justify-center gap-1.5">
                <flux:icon name="receipt-percent" class="h-4 w-4" />
                Diskon
            </button>
        @endif

        @if($voucherApplied)
            <div class="flex-1 flex flex-col bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-800 rounded-xl overflow-hidden relative group">
                <div class="flex-1 px-3 py-1.5 text-left">
                    <span class="text-[9px] font-black text-emerald-600 dark:text-emerald-500 uppercase block mb-0.5">Vcr ({{ $voucherCode }})</span>
                    <span class="text-xs font-bold text-emerald-700 dark:text-emerald-400">Rp{{ number_format($voucherDiscount, 0, ',', '.') }}</span>
                </div>
                <button wire:click="resetVoucher" class="absolute right-0 top-0 bottom-0 w-8 flex items-center justify-center text-emerald-400 hover:text-emerald-600 hover:bg-emerald-100 dark:hover:bg-emerald-900/50 transition-colors border-l border-emerald-200 dark:border-emerald-800/50">
                    <flux:icon name="x-mark" class="h-3 w-3" />
                </button>
            </div>
        @else
            <button wire:click="openVoucherModal" class="flex-1 py-3 px-2 bg-zinc-900 dark:bg-zinc-700 hover:bg-zinc-800 dark:hover:bg-zinc-600 border-2 border-zinc-900 dark:border-zinc-700 rounded-xl text-[10px] font-black uppercase text-white shadow-sm transition-colors flex items-center justify-center gap-1.5">
                <flux:icon name="ticket" class="h-4 w-4" />
                Voucher
            </button>
        @endif
    </div>

    {{-- FOOTER: SUMMARY AND CTA --}}
    <footer class="shrink-0 bg-white dark:bg-zinc-950 border-t border-zinc-100 dark:border-zinc-800 p-5 space-y-4 shadow-[0_-10px_20px_0_rgba(0,0,0,0.05)]">
        <div class="space-y-2">
            <div class="flex justify-between text-[10px] font-bold text-zinc-400 uppercase tracking-widest">
                <span>Subtotal</span>
                <span class="text-zinc-600 dark:text-zinc-300 font-bold">Rp{{ number_format($subtotal, 0, ',', '.') }}</span>
            </div>
            @if($totalDiscount > 0)
                <div class="flex justify-between text-[10px] font-bold text-red-500 uppercase tracking-widest leading-none">
                    <span>Diskon</span>
                    <span class="font-black">- Rp{{ number_format($totalDiscount, 0, ',', '.') }}</span>
                </div>
            @endif
            @if($taxRate > 0)
                <div class="flex justify-between text-[10px] font-bold text-zinc-400 uppercase tracking-widest">
                    <span>Pajak ({{ $taxRate }}%)</span>
                    <span class="text-zinc-600 dark:text-zinc-300">+ Rp{{ number_format($taxAmount, 0, ',', '.') }}</span>
                </div>
            @endif
            
            <div class="flex justify-between items-end pt-2 border-t border-zinc-100 dark:border-zinc-800">
                <div class="flex flex-col">
                    <span class="text-[9px] font-black uppercase tracking-[0.2em] text-zinc-400 mb-0.5">Total Tagihan</span>
                    <span class="text-3xl font-black text-zinc-950 dark:text-white tracking-tighter leading-none">
                        Rp{{ number_format($grandTotal, 0, ',', '.') }}
                    </span>
                </div>
            </div>
        </div>

        {{-- BIG VISIBLE CTA BUTTON --}}
        @if(count($cart) > 0)
            <button 
                wire:click="openPaymentModal" 
                class="w-full h-14 flex items-center justify-center gap-2 bg-green-600 hover:bg-green-700 text-white rounded-xl text-sm font-black uppercase tracking-[0.2em] shadow-lg shadow-green-200 dark:shadow-none transition-all active:scale-95 group overflow-hidden relative"
            >
                <div class="absolute inset-0 bg-green-600 group-hover:bg-green-700 transition-colors"></div>
                <div class="relative z-10 flex items-center gap-2">
                    <span>BAYAR SEKARANG</span>
                    <flux:icon name="arrow-right" class="h-4 w-4 transition-transform group-hover:translate-x-1" />
                </div>
            </button>
        @endif
    </footer>
</aside>
