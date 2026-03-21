<div class="h-screen flex flex-col overflow-hidden bg-zinc-100 dark:bg-zinc-950 -m-6 rounded-none">
    {{-- TOP SEARCH BAR --}}
    <header class="h-16 shrink-0 bg-white dark:bg-zinc-900 border-b border-zinc-200 dark:border-zinc-800 flex items-center px-6 justify-between shadow-sm">
        <div class="flex items-center gap-4 flex-1">
            <div class="h-9 w-9 bg-green-600 text-white rounded-lg flex items-center justify-center font-black shadow-lg">POS</div>
            <div class="relative w-full max-w-lg">
                <flux:input 
                    icon="magnifying-glass" 
                    wire:model.live.debounce.300ms="search" 
                    placeholder="Cari produk atau SKU..." 
                    variant="filled" 
                    class="!rounded-xl"
                />
            </div>
        </div>
        <div class="hidden md:flex items-center gap-4 ml-4">
            <div class="text-right">
                <p class="text-[10px] font-black text-zinc-400 uppercase tracking-widest">Operator</p>
                <p class="text-xs font-bold text-zinc-900 dark:text-white">{{ auth()->user()->name }}</p>
            </div>
            <div class="h-10 w-10 rounded-full bg-zinc-100 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 flex items-center justify-center overflow-hidden">
                <flux:icon name="user" class="h-5 w-5 text-zinc-400" />
            </div>
        </div>
    </header>

    {{-- MAIN LAYOUT: RESPONSIVE FLEXBOX (Fixed for Mobile Stacking) --}}
    <div class="flex flex-1 overflow-y-auto overflow-x-hidden md:overflow-hidden flex-col md:flex-row">
        
        {{-- ====================================================================
             LEFT SIDE: PRODUCT CATALOG (w-full on mobile, 60-70% on desktop)
             ==================================================================== --}}
        <section class="flex-none md:flex-1 overflow-visible md:overflow-hidden flex flex-col min-w-0 bg-zinc-50 dark:bg-zinc-950/20 w-full md:w-[60%] lg:w-[65%] xl:w-[70%]">
            
            {{-- Category Scroller --}}
            <nav class="flex items-center gap-2 p-3 bg-white dark:bg-zinc-900 border-b border-zinc-200 dark:border-zinc-800 overflow-x-auto scrollbar-hide shrink-0">
                <button 
                    wire:click="$set('selectedCategory', '')" 
                    class="px-5 py-2 rounded-xl text-[10px] font-black uppercase tracking-widest whitespace-nowrap transition-all border-2 {{ $selectedCategory === '' ? 'bg-green-600 text-white border-green-600 shadow-md' : 'bg-transparent text-zinc-500 border-zinc-100 dark:border-zinc-800 hover:border-zinc-300 dark:text-zinc-400' }}"
                >
                    Semua Produk
                </button>
                @foreach($categories as $cat)
                    @if($cat->products_count > 0)
                        <button 
                            wire:click="$set('selectedCategory', {{ $cat->id }})" 
                            class="px-5 py-2 rounded-xl text-[10px] font-black uppercase tracking-widest whitespace-nowrap transition-all border-2 {{ (int)$selectedCategory === $cat->id ? 'bg-green-600 text-white border-green-600 shadow-md' : 'bg-transparent text-zinc-500 border-zinc-100 dark:border-zinc-800' }}"
                        >
                            {{ strtoupper($cat->name) }}
                        </button>
                    @endif
                @endforeach
            </nav>

            {{-- THE PRODUCT GRID --}}
            <main class="flex-none md:flex-1 overflow-visible md:overflow-y-auto p-4 customized-scrollbar">
                <div class="grid grid-cols-2 md:grid-cols-2 lg:grid-cols-3 gap-4 auto-rows-min">
                    @forelse($products as $product)
                        <div x-data="{ showDetail: false, detailModal: false }" class="relative" @mouseenter="showDetail = true" @mouseleave="showDetail = false">
                            <div 
                                wire:click="addToCart({{ $product->id }})" 
                                class="w-full cursor-pointer group relative flex flex-col bg-white dark:bg-zinc-900 rounded-xl border border-zinc-200 dark:border-zinc-800 overflow-hidden transition-all hover:border-green-500 hover:shadow-md active:scale-95 text-left"
                            >
                                <div class="aspect-square w-full bg-zinc-50 dark:bg-zinc-800/50 flex items-center justify-center p-4 relative border-b border-zinc-100 dark:border-zinc-800">
                                    @if($product->image)
                                        <img src="{{ Storage::url($product->image) }}" class="h-full w-full object-contain" alt="{{ $product->name }}">
                                    @else
                                        <flux:icon name="cube" class="h-8 w-8 text-zinc-300 dark:text-zinc-700 opacity-20" />
                                    @endif
                                    
                                    <div class="absolute top-2 right-2">
                                        @if($product->type === 'service')
                                            <span class="bg-blue-50 text-blue-600 dark:bg-blue-950 dark:text-blue-400 px-1.5 py-0.5 rounded-lg text-[8px] font-black uppercase tracking-wider border border-blue-200 dark:border-blue-800">Jasa</span>
                                        @elseif($product->stock <= $product->low_stock_threshold)
                                            <span class="bg-red-500 text-white px-1.5 py-0.5 rounded-lg text-[8px] font-black uppercase tracking-wider shadow-sm animate-pulse">Low: {{ $product->stock }}</span>
                                        @else
                                            <span class="bg-emerald-50 text-emerald-600 dark:bg-emerald-950 dark:text-emerald-400 px-1.5 py-0.5 rounded-lg text-[8px] font-black uppercase tracking-wider">Stok: {{ $product->stock }}</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="p-3 flex flex-col flex-1">
                                    <h3 class="text-[11px] md:text-xs font-bold text-zinc-800 dark:text-zinc-200 line-clamp-2 leading-snug mb-2 min-h-[32px]">{{ $product->name }}</h3>
                                    
                                    <div class="mt-auto flex items-center justify-between gap-1">
                                        <span class="text-sm md:text-base font-black text-zinc-950 dark:text-white truncate">Rp{{ number_format($product->price, 0, ',', '.') }}</span>
                                        
                                        <div class="flex items-center gap-1 shrink-0">
                                            <button @click.stop="detailModal = true" class="md:hidden flex h-7 w-7 items-center justify-center bg-zinc-100 text-zinc-500 hover:bg-zinc-200 dark:bg-zinc-800 dark:text-zinc-400 rounded-lg transition-all">
                                                <flux:icon name="information-circle" class="h-4 w-4" />
                                            </button>
                                            <div class="h-7 w-7 bg-zinc-900 text-white dark:bg-white dark:text-zinc-900 rounded-lg flex items-center justify-center shadow-md group-hover:bg-green-600 group-hover:text-white transition-colors">
                                                <flux:icon name="plus" class="h-4 w-4" />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Compact Tooltip for Description (Desktop) --}}
                            <div 
                                x-show="showDetail" 
                                x-transition.opacity.duration.200ms
                                style="display: none;"
                                class="absolute top-12 left-4 z-30 max-w-xs bg-gray-900 dark:bg-zinc-800 text-white text-xs px-3 py-2 rounded-md shadow-lg wrap-break-word pointer-events-none hidden md:block"
                            >
                                @if($product->description)
                                    {{ $product->description }}
                                @else
                                    <span class="italic opacity-70">Tidak ada deskripsi.</span>
                                @endif
                                
                                {{-- Small tooltip arrow --}}
                                <div class="absolute -top-1 left-3 w-2 h-2 bg-gray-900 dark:bg-zinc-800 rotate-45"></div>
                            </div>

                            {{-- Mobile Bottom Sheet/Modal --}}
                            <div x-show="detailModal" 
                                 style="display: none;"
                                 class="fixed inset-0 z-60 flex items-end md:items-center justify-center sm:p-4 pointer-events-auto"
                            >
                                <div x-show="detailModal"
                                     x-transition.opacity.duration.300ms
                                     class="fixed inset-0 bg-black/40 backdrop-blur-sm"
                                     @click="detailModal = false"
                                ></div>

                                <div x-show="detailModal"
                                     x-transition:enter="transition ease-out duration-300"
                                     x-transition:enter-start="translate-y-full md:translate-y-4 opacity-0 scale-95"
                                     x-transition:enter-end="translate-y-0 opacity-100 scale-100"
                                     x-transition:leave="transition ease-in duration-200"
                                     x-transition:leave-start="translate-y-0 opacity-100 scale-100"
                                     x-transition:leave-end="translate-y-full md:translate-y-4 opacity-0 scale-95"
                                     class="relative w-full md:max-w-md bg-white dark:bg-zinc-900 rounded-t-3xl md:rounded-2xl shadow-2xl p-6 flex flex-col gap-4 mx-auto border border-zinc-100 dark:border-zinc-800"
                                >
                                    <div class="flex justify-between items-start gap-4 border-b border-zinc-100 dark:border-zinc-800 pb-4">
                                        <div>
                                            <p class="text-[10px] font-black uppercase tracking-widest text-zinc-400 mb-1">{{ $product->sku }}</p>
                                            <h3 class="text-base font-bold tracking-tight text-zinc-900 dark:text-white leading-tight">{{ $product->name }}</h3>
                                        </div>
                                        <button @click="detailModal = false" class="h-8 w-8 bg-zinc-100 dark:bg-zinc-800 rounded-full flex items-center justify-center text-zinc-500 hover:text-zinc-900 shrink-0">
                                            <flux:icon name="x-mark" class="h-4 w-4" />
                                        </button>
                                    </div>
                                    <div class="text-sm text-zinc-600 dark:text-zinc-400 leading-relaxed max-h-[40vh] overflow-y-auto customized-scrollbar pr-2 mb-4">
                                        @if($product->description)
                                            {{ $product->description }}
                                        @else
                                            <span class="italic opacity-60">Tidak ada deskripsi rinci untuk produk ini.</span>
                                        @endif
                                    </div>
                                    <button 
                                        wire:click="addToCart({{ $product->id }})" 
                                        @click="detailModal = false"
                                        class="w-full h-12 rounded-xl bg-green-600 hover:bg-green-700 text-white font-bold flex items-center justify-center gap-2 transition-all active:scale-95"
                                    >
                                        <flux:icon name="shopping-cart" class="h-5 w-5" />
                                        <span>Tambah ke Cart - Rp{{ number_format($product->price, 0, ',', '.') }}</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-span-full py-32 flex flex-col items-center justify-center opacity-40">
                            <flux:icon name="magnifying-glass" class="h-16 w-16 mb-4 text-zinc-400" />
                            <p class="text-xs font-black uppercase tracking-widest">Produk tidak ditemukan</p>
                        </div>
                    @endforelse
                </div>
            </main>
        </section>

        {{-- ====================================================================
             RIGHT SIDE: CART & CHECKOUT
             ==================================================================== --}}
        <aside class="flex flex-col bg-white dark:bg-zinc-950 mt-6 md:mt-0 md:border-l border-t md:border-t-0 border-zinc-200 dark:border-zinc-800 shrink-0 w-full md:w-[40%] lg:w-[35%] xl:w-[30%] shadow-2xl overflow-visible md:overflow-hidden relative z-10">
            
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
                            <span class="text-xs font-bold text-amber-700 dark:text-amber-400">Rp{{ number_format($this->manualDiscountAmount, 0, ',', '.') }}</span>
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
                            <span class="text-xs font-bold text-emerald-700 dark:text-emerald-400">Rp{{ number_format($this->voucherDiscount, 0, ',', '.') }}</span>
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
                        <span class="text-zinc-600 dark:text-zinc-300 font-bold">Rp{{ number_format($this->subtotal, 0, ',', '.') }}</span>
                    </div>
                    @if($this->totalDiscount > 0)
                        <div class="flex justify-between text-[10px] font-bold text-red-500 uppercase tracking-widest leading-none">
                            <span>Diskon</span>
                            <span class="font-black">- Rp{{ number_format($this->totalDiscount, 0, ',', '.') }}</span>
                        </div>
                    @endif
                    @if($this->taxRate > 0)
                        <div class="flex justify-between text-[10px] font-bold text-zinc-400 uppercase tracking-widest">
                            <span>Pajak ({{ $this->taxRate }}%)</span>
                            <span class="text-zinc-600 dark:text-zinc-300">+ Rp{{ number_format($this->taxAmount, 0, ',', '.') }}</span>
                        </div>
                    @endif
                    
                    <div class="flex justify-between items-end pt-2 border-t border-zinc-100 dark:border-zinc-800">
                        <div class="flex flex-col">
                            <span class="text-[9px] font-black uppercase tracking-[0.2em] text-zinc-400 mb-0.5">Total Tagihan</span>
                            <span class="text-3xl font-black text-zinc-950 dark:text-white tracking-tighter leading-none">
                                Rp{{ number_format($this->grandTotal, 0, ',', '.') }}
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
    </div>

    {{-- ====================================================================
         2. MODAL REFINEMENT: PROPORTIONAL SIZE AND FONTS
         ==================================================================== --}}
    <flux:modal wire:model="showPaymentModal" class="max-w-2xl p-0 overflow-hidden bg-white dark:bg-zinc-900 rounded-3xl w-full">
        <div class="p-6 space-y-6">
            <header class="flex justify-between items-end border-b border-zinc-100 dark:border-zinc-800 pb-5">
                <div>
                    <h2 class="text-xl font-black uppercase tracking-tighter text-zinc-900 dark:text-white leading-none">Checkout</h2>
                    <p class="text-[9px] font-bold text-zinc-400 uppercase tracking-widest mt-1">Konfirmasi Pembayaran</p>
                </div>
                <div class="text-right">
                    <p class="text-[10px] font-black tracking-[0.2em] text-zinc-400 uppercase mb-1">Total Bayar</p>
                    <p class="text-3xl font-black text-green-600 tracking-tighter leading-none">Rp{{ number_format($this->grandTotal, 0, ',', '.') }}</p>
                </div>
            </header>

            <div class="flex flex-col md:flex-row gap-6">
                {{-- Left side: Payment Method & Input --}}
                <div class="flex-1 flex flex-col space-y-6">
                    {{-- Method Selection --}}
                    <div class="grid grid-cols-2 gap-4 shrink-0">
                        @foreach(['cash' => 'Tunai', 'qris' => 'QRIS'] as $val => $label)
                            <label class="relative flex flex-col items-center justify-center p-4 border-2 rounded-2xl cursor-pointer transition-all {{ $paymentMethod === $val ? 'border-green-600 bg-green-50 dark:bg-green-950/20 shadow-sm' : 'border-zinc-100 dark:border-zinc-800 bg-zinc-50 dark:bg-zinc-900/50 hover:bg-zinc-100' }}">
                                <input type="radio" wire:model.live="paymentMethod" value="{{ $val }}" class="sr-only">
                                <div class="h-8 w-8 rounded-xl flex items-center justify-center mb-2 {{ $paymentMethod === $val ? 'bg-green-600 text-white' : 'bg-white dark:bg-zinc-800 text-zinc-400' }}">
                                    <flux:icon name="{{ $val === 'cash' ? 'banknotes' : 'qr-code' }}" class="h-5 w-5" />
                                </div>
                                <span class="text-[10px] font-black uppercase tracking-widest text-center {{ $paymentMethod === $val ? 'text-green-700 dark:text-green-400' : 'text-zinc-400' }}">{{ $label }}</span>
                            </label>
                        @endforeach
                    </div>

                    {{-- Context Action Area --}}
                    <div class="bg-zinc-50 dark:bg-zinc-950 p-6 rounded-2xl border border-zinc-100 dark:border-zinc-800 min-h-[160px] flex flex-col justify-center flex-1">
                        @if($paymentMethod === 'cash')
                            <div class="space-y-4">
                                <div class="flex items-center justify-between px-1">
                                    <label class="text-[10px] font-black uppercase tracking-widest text-zinc-500">Uang Diterima</label>
                                    <button type="button" wire:click="$set('cashReceived', {{ $this->grandTotal }})" class="text-[10px] bg-green-100 text-green-700 px-2 py-1 rounded-md font-black hover:bg-green-200 transition-colors">PAS</button>
                                </div>
                                
                                <div class="relative">
                                    <input 
                                        type="number" 
                                        wire:model.live="cashReceived"
                                        class="w-full h-20 text-4xl font-black text-right pr-4 tracking-tighter bg-white dark:bg-zinc-900 border-2 border-zinc-200 dark:border-zinc-700 rounded-2xl focus:border-green-500 outline-none text-zinc-900 dark:text-white transition-all caret-green-600"
                                        placeholder="0"
                                        autofocus
                                    />
                                    <div class="absolute left-4 top-1/2 -translate-y-1/2 text-zinc-400 font-bold text-lg select-none pointer-events-none">Rp</div>
                                </div>

                                <div class="flex flex-col gap-2">
                                    <div class="flex justify-between items-center px-1 opacity-90">
                                        <span class="text-[10px] font-black text-zinc-400 uppercase tracking-widest">Total Format</span>
                                        <span class="text-sm font-bold text-zinc-900 dark:text-white">
                                            Rp {{ number_format((float)$cashReceived, 0, ',', '.') }}
                                        </span>
                                    </div>
                                    <div class="flex justify-between items-center px-1 pt-2 border-t border-zinc-200 dark:border-zinc-800">
                                        <span class="text-[10px] font-black text-zinc-400 uppercase tracking-widest">Kembalian</span>
                                        <span class="text-lg font-black {{ $this->changeAmount > 0 ? 'text-emerald-500' : ($this->cashReceived >= $this->grandTotal ? 'text-zinc-400' : 'text-red-500') }}">
                                            Rp{{ number_format($this->changeAmount, 0, ',', '.') }}
                                        </span>
                                    </div>
                                    @if($this->cashReceived < $this->grandTotal && $this->cashReceived > 0)
                                        <p class="text-[9px] font-bold text-red-500 uppercase tracking-tight text-right px-1">Kurang: Rp{{ number_format($this->grandTotal - $this->cashReceived, 0, ',', '.') }}</p>
                                    @endif
                                </div>
                            </div>
                        @else
                            {{-- QRIS Payment Panel --}}
                            @if($qrisNotConfigured)
                                {{-- External QRIS flow (Not configured in system) --}}
                                <div class="flex flex-col items-center justify-center text-center h-full gap-4">
                                    <div class="w-16 h-16 bg-blue-100 dark:bg-blue-950/40 border-2 border-blue-200 dark:border-blue-800 flex items-center justify-center rounded-2xl">
                                        <flux:icon name="qr-code" class="h-8 w-8 text-blue-500" />
                                    </div>
                                    <div>
                                        <p class="text-sm font-bold text-zinc-700 dark:text-zinc-300 mb-1">QRIS Eksternal</p>
                                        <p class="text-[10px] font-medium text-zinc-400 uppercase tracking-wide leading-loose">
                                            Toko menggunakan QRIS Eksternal.<br>
                                            Pelanggan scan QR dari EDC atau QR Cetak.
                                        </p>
                                    </div>
                                    <div class="px-3 py-1.5 bg-blue-100 dark:bg-blue-900/30 rounded-full">
                                        <p class="text-[9px] font-black text-blue-700 dark:text-blue-400 uppercase tracking-widest">Total: Rp{{ number_format($this->grandTotal, 0, ',', '.') }}</p>
                                    </div>
                                </div>
                            @else
                                {{-- QRIS ready --}}
                                <div class="flex flex-col items-center justify-center text-center h-full gap-4">
                                    <div class="w-20 h-20 bg-green-50 dark:bg-green-950/30 border-2 border-green-200 dark:border-green-800 flex items-center justify-center rounded-2xl">
                                        <flux:icon name="qr-code" class="h-10 w-10 text-green-600 dark:text-green-400" />
                                    </div>
                                    <div>
                                        <p class="text-sm font-bold text-zinc-700 dark:text-zinc-300 mb-1">QRIS Siap Digunakan</p>
                                        <p class="text-[10px] font-medium text-zinc-400 uppercase tracking-wide leading-loose">
                                            QR code dinamis akan digenerate<br>
                                            setelah konfirmasi pembayaran
                                        </p>
                                    </div>
                                    <div class="px-3 py-1.5 bg-green-100 dark:bg-green-900/30 rounded-full">
                                        <p class="text-[9px] font-black text-green-700 dark:text-green-400 uppercase tracking-widest">Total: Rp{{ number_format($this->grandTotal, 0, ',', '.') }}</p>
                                    </div>
                                </div>
                            @endif
                        @endif
                    </div>
                </div>

                {{-- Right side: Numeric Keypad (Cash only) --}}
                @if($paymentMethod === 'cash')
                    <div class="md:w-64 shrink-0 flex flex-col justify-end">
                        <div class="grid grid-cols-3 gap-3">
                            @foreach(['1', '2', '3', '4', '5', '6', '7', '8', '9', '000', '00', '0'] as $key)
                                <button type="button" wire:click="appendKeypad('{{ $key }}')" class="h-14 bg-zinc-100 hover:bg-zinc-200 dark:bg-zinc-800 dark:hover:bg-zinc-700 text-zinc-900 dark:text-white rounded-xl font-bold text-xl transition-colors select-none active:scale-95 shadow-sm">
                                    {{ $key }}
                                </button>
                            @endforeach
                            <button type="button" wire:click="removeKeypad" class="col-span-3 h-14 bg-red-50 hover:bg-red-100 text-red-600 dark:bg-red-900/20 dark:hover:bg-red-900/40 dark:text-red-400 rounded-xl flex items-center justify-center transition-colors active:scale-95 shadow-sm">
                                <flux:icon name="backspace" class="h-6 w-6" />
                            </button>
                        </div>
                    </div>
                @endif
            </div>

            <div class="flex gap-3 pt-4 border-t border-zinc-100 dark:border-zinc-800">
                <button type="button" wire:click="$set('showPaymentModal', false)" class="flex-1 h-14 rounded-xl border border-zinc-200 dark:border-zinc-700 text-[11px] font-black uppercase text-zinc-500 hover:bg-zinc-50 transition-colors">Batal</button>
                <button 
                    type="button" 
                    wire:click="processPayment" 
                    @if($paymentMethod === 'cash' && $this->cashReceived < $this->grandTotal) disabled @endif
                    class="flex-[2] h-14 rounded-xl bg-green-600 hover:bg-green-700 text-white font-black uppercase text-[12px] tracking-[0.2em] shadow-lg shadow-green-600/20 disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2 group transition-all"
                >
                    Konfirmasi Bayar
                    <flux:icon name="arrow-right" class="h-4 w-4 transition-transform group-hover:translate-x-1" />
                </button>
            </div>
        </div>
    </flux:modal>

    {{-- RESULT MODAL REFINEMENT --}}
    <flux:modal wire:model="showResultModal" class="max-w-md p-0 overflow-hidden bg-white dark:bg-zinc-900 rounded-[2.5rem]" :closable="false">
        @if($paymentResult)
            <div class="p-8 flex flex-col items-center text-center">
                <div class="h-16 w-16 bg-green-500 text-white rounded-full flex items-center justify-center mb-6 shadow-xl shadow-green-100 border-4 border-white dark:border-zinc-800">
                    <flux:icon name="check" variant="solid" class="h-8 w-8" />
                </div>
                
                <h2 class="text-xl font-black uppercase tracking-tighter text-zinc-900 dark:text-white leading-none">Berhasil!</h2>
                <p class="text-[10px] font-black text-zinc-400 uppercase tracking-[0.2em] mt-2 mb-8">Invoice #{{ $paymentResult['invoice'] }}</p>

                @if($paymentResult['method'] === 'qris')
                    <div class="mb-6 w-full flex flex-col items-center gap-4">
                        {{-- Generated dynamic QR code --}}
                        @if($qrisImageData)
                            <div class="p-3 bg-white border-4 border-zinc-100 rounded-3xl shadow-lg">
                                <img src="{{ $qrisImageData }}" class="h-52 w-52 object-contain" alt="QRIS">
                            </div>
                        @endif

                        <p class="text-2xl font-black text-green-600 tracking-tighter leading-none">
                            Rp{{ number_format($paymentResult['grand_total'], 0, ',', '.') }}
                        </p>

                        <p class="text-[10px] font-black text-zinc-400 uppercase tracking-[0.2em] text-center">
                            @if(isset($paymentResult['qris_type']) && $paymentResult['qris_type'] === 'external')
                                Pastikan pembayaran sudah diterima<br>lewat EDC atau mutasi rekening
                            @else
                                Tunjukkan QR ke pelanggan<br>untuk di-scan
                            @endif
                        </p>

                        {{-- Confirm payment received button --}}
                        @if(!($paymentResult['qris_confirmed'] ?? false))
                            <button
                                wire:click="confirmQrisPayment"
                                type="button"
                                class="w-full h-12 rounded-2xl bg-indigo-600 hover:bg-indigo-700 text-white text-[11px] font-black uppercase tracking-widest flex items-center justify-center gap-2 transition-all active:scale-95 shadow-lg"
                            >
                                <flux:icon name="check-circle" class="h-4 w-4" />
                                Konfirmasi Pembayaran Diterima
                            </button>
                        @else
                            <div class="w-full h-12 rounded-2xl bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400 text-[11px] font-black uppercase tracking-widest flex items-center justify-center gap-2">
                                <flux:icon name="check-circle" class="h-4 w-4" />
                                Pembayaran Dikonfirmasi
                            </div>
                        @endif
                    </div>
                @elseif($paymentResult['method'] === 'va')
                    <div class="mb-8 w-full bg-zinc-50 dark:bg-zinc-950 p-6 rounded-3xl border border-zinc-100 dark:border-zinc-800">
                        <p class="text-[9px] font-black text-zinc-400 uppercase tracking-widest mb-3">{{ $paymentResult['bank'] }} VA NUMBER</p>
                        <p class="text-2xl font-black tracking-[0.3em] text-zinc-900 dark:text-white select-all">{{ $paymentResult['va_number'] }}</p>
                    </div>
                @else
                    <div class="mb-8 w-full grid grid-cols-2 gap-3">
                        <div class="bg-zinc-50 p-4 rounded-2xl">
                            <p class="text-[8px] font-black text-zinc-400 uppercase mb-1">Diterima</p>
                            <p class="text-sm font-black">Rp{{ number_format($paymentResult['cash_received'], 0, ',', '.') }}</p>
                        </div>
                        <div class="bg-emerald-50 p-4 rounded-2xl">
                            <p class="text-[8px] font-black text-emerald-400 uppercase mb-1">Kembali</p>
                            <p class="text-sm font-black text-emerald-600">Rp{{ number_format($paymentResult['change'], 0, ',', '.') }}</p>
                        </div>
                    </div>
                @endif

                <div class="grid grid-cols-1 w-full gap-3 pt-2">
                    <a href="{{ route('receipt.print', $paymentResult['transaction_id']) }}" target="_blank" class="h-12 w-full flex items-center justify-center gap-2 bg-zinc-100 hover:bg-zinc-200 text-zinc-900 rounded-xl text-[10px] font-black uppercase tracking-widest border border-zinc-200">
                        <flux:icon name="printer" class="h-4 w-4" /> Cetak Struk
                    </a>
                    <button wire:click="newTransaction" type="button" class="h-12 w-full bg-green-600 hover:bg-green-700 text-white rounded-xl text-[10px] font-black uppercase shadow-lg">
                        Selesai
                    </button>
                </div>
            </div>
        @endif
    </flux:modal>

    {{-- ====================================================================
         DISCOUNT MODAL WITH NUMERIC KEYPAD
         ==================================================================== --}}
    <flux:modal wire:model="showDiscountModal" class="max-w-sm p-0 overflow-hidden bg-white dark:bg-zinc-900 rounded-3xl w-full">
        <div class="p-6 space-y-5">
            <header class="text-center pb-4 border-b border-zinc-100 dark:border-zinc-800">
                <h2 class="text-lg font-black uppercase tracking-tighter text-zinc-900 dark:text-white leading-none">Input Diskon</h2>
            </header>

            <div class="space-y-4">
                <div class="grid grid-cols-2 gap-2">
                    <button wire:click="$set('tempDiscountType', 'percentage')" class="py-3 rounded-xl border-2 text-xs font-black uppercase transition-all {{ $tempDiscountType === 'percentage' ? 'border-amber-500 bg-amber-50 text-amber-700' : 'border-zinc-200 bg-zinc-50 text-zinc-500' }}">Persen (%)</button>
                    <button wire:click="$set('tempDiscountType', 'fixed')" class="py-3 rounded-xl border-2 text-xs font-black uppercase transition-all {{ $tempDiscountType === 'fixed' ? 'border-amber-500 bg-amber-50 text-amber-700' : 'border-zinc-200 bg-zinc-50 text-zinc-500' }}">Nominal (Rp)</button>
                </div>

                <div class="relative">
                    <input type="text" readonly value="{{ $tempDiscountValue ? number_format((float)$tempDiscountValue, 0, ',', '.') : '' }}" class="w-full h-16 text-center text-3xl font-black bg-white dark:bg-zinc-950 border-2 border-zinc-200 dark:border-zinc-800 rounded-2xl text-zinc-900 dark:text-white" placeholder="0">
                    <div class="absolute left-4 top-1/2 -translate-y-1/2 text-zinc-400 font-bold text-lg select-none pointer-events-none">{{ $tempDiscountType === 'fixed' ? 'Rp' : '%' }}</div>
                </div>

                <div class="grid grid-cols-3 gap-2">
                    @foreach(['1', '2', '3', '4', '5', '6', '7', '8', '9', '000', '00', '0'] as $key)
                        <button type="button" wire:click="appendDiscountKeypad('{{ $key }}')" class="h-14 bg-zinc-100 hover:bg-zinc-200 dark:bg-zinc-800 dark:hover:bg-zinc-700 text-zinc-900 dark:text-white rounded-xl font-bold text-xl transition-colors active:scale-95 shadow-sm">
                            {{ $key }}
                        </button>
                    @endforeach
                    <button type="button" wire:click="clearDiscountKeypad" class="col-span-1 h-12 bg-zinc-200 hover:bg-zinc-300 dark:bg-zinc-800 dark:hover:bg-zinc-700 text-zinc-600 dark:text-zinc-400 rounded-xl font-black text-xs uppercase active:scale-95 shadow-sm">Clear</button>
                    <button type="button" wire:click="removeDiscountKeypad" class="col-span-2 h-12 bg-red-50 hover:bg-red-100 text-red-600 dark:bg-red-900/20 dark:hover:bg-red-900/40 rounded-xl flex items-center justify-center transition-colors active:scale-95 shadow-sm">
                        <flux:icon name="backspace" class="h-6 w-6" />
                    </button>
                </div>
            </div>

            <div class="flex gap-2 pt-2">
                <button type="button" wire:click="$set('showDiscountModal', false)" class="flex-1 h-12 py-2 rounded-xl border border-zinc-200 dark:border-zinc-700 text-xs font-black uppercase text-zinc-500 hover:bg-zinc-50 dark:hover:bg-zinc-800 transition-colors">Batal</button>
                <button type="button" wire:click="applyDiscountAction" class="flex-1 h-12 py-2 rounded-xl bg-amber-500 hover:bg-amber-600 text-white text-xs font-black uppercase shadow-lg shadow-amber-500/30 transition-all">Terapkan</button>
            </div>
        </div>
    </flux:modal>

    {{-- ====================================================================
         VOUCHER MODAL WITH ON-SCREEN KEYBOARD
         ==================================================================== --}}
    <flux:modal wire:model="showVoucherModal" class="max-w-xl p-0 overflow-hidden bg-white dark:bg-zinc-900 rounded-3xl w-full">
        <div class="p-6 space-y-5">
            <header class="text-center pb-4 border-b border-zinc-100 dark:border-zinc-800">
                <h2 class="text-lg font-black uppercase tracking-tighter text-zinc-900 dark:text-white leading-none">Input Kode Voucher</h2>
            </header>

            <div class="space-y-4 relative">
                <input type="text" readonly value="{{ $tempVoucherCode }}" class="w-full h-16 text-center text-3xl font-black uppercase bg-white dark:bg-zinc-950 border-2 border-zinc-200 dark:border-zinc-800 rounded-2xl text-zinc-900 dark:text-white" placeholder="KODE VOUCHER">

                <div class="space-y-2">
                    <div class="flex justify-center gap-1.5">
                        @foreach(str_split('1234567890') as $key)
                            <button type="button" wire:click="appendVoucherKeypad('{{ $key }}')" class="h-12 w-10 flex-1 bg-zinc-100 dark:bg-zinc-800 hover:bg-zinc-200 dark:hover:bg-zinc-700 text-zinc-800 dark:text-white rounded-xl font-black text-lg shadow-sm active:scale-95">{{ $key }}</button>
                        @endforeach
                    </div>
                    <div class="flex justify-center gap-1.5">
                        @foreach(str_split('QWERTYUIOP') as $key)
                            <button type="button" wire:click="appendVoucherKeypad('{{ $key }}')" class="h-12 w-10 flex-1 bg-zinc-100 dark:bg-zinc-800 hover:bg-zinc-200 dark:hover:bg-zinc-700 text-zinc-800 dark:text-white rounded-xl font-black text-lg shadow-sm active:scale-95">{{ $key }}</button>
                        @endforeach
                    </div>
                    <div class="flex justify-center gap-1.5 px-3">
                        @foreach(str_split('ASDFGHJKL') as $key)
                            <button type="button" wire:click="appendVoucherKeypad('{{ $key }}')" class="h-12 w-10 flex-1 bg-zinc-100 dark:bg-zinc-800 hover:bg-zinc-200 dark:hover:bg-zinc-700 text-zinc-800 dark:text-white rounded-xl font-black text-lg shadow-sm active:scale-95">{{ $key }}</button>
                        @endforeach
                    </div>
                    <div class="flex justify-center gap-1.5 px-6">
                        @foreach(str_split('ZXCVBNM') as $key)
                            <button type="button" wire:click="appendVoucherKeypad('{{ $key }}')" class="h-12 w-10 flex-1 bg-zinc-100 dark:bg-zinc-800 hover:bg-zinc-200 dark:hover:bg-zinc-700 text-zinc-800 dark:text-white rounded-xl font-black text-lg shadow-sm active:scale-95">{{ $key }}</button>
                        @endforeach
                        <button type="button" wire:click="removeVoucherKeypad" class="h-12 flex-[1.5] bg-red-50 hover:bg-red-100 text-red-600 dark:bg-red-900/20 dark:hover:bg-red-900/40 rounded-xl flex items-center justify-center shadow-sm active:scale-95">
                            <flux:icon name="backspace" class="h-5 w-5" />
                        </button>
                    </div>
                </div>

                @if($voucherError)
                    <p class="text-[11px] font-bold text-red-500 text-center uppercase mt-2">{{ $voucherError }}</p>
                @endif
            </div>

            <div class="flex gap-2 pt-2 border-t border-zinc-100 dark:border-zinc-800 mt-4">
                <button type="button" wire:click="$set('showVoucherModal', false)" class="flex-[0.5] h-12 rounded-xl border border-zinc-200 dark:border-zinc-700 text-xs font-black uppercase text-zinc-500 hover:bg-zinc-50 dark:hover:bg-zinc-800 transition-colors">Batal</button>
                <button type="button" wire:click="applyVoucherAction" class="flex-1 h-12 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-black uppercase shadow-lg shadow-emerald-600/30 transition-all flex justify-center items-center gap-2">Terapkan Voucher</button>
            </div>
        </div>
    </flux:modal>

    <style>
        .scrollbar-hide::-webkit-scrollbar { display: none; }
        .scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }
        .customized-scrollbar::-webkit-scrollbar { width: 4px; }
        .customized-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
    </style>
</div>
