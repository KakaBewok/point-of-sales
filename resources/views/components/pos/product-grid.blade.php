@props(['products'])

<main {{ $attributes->merge(['class' => 'flex-none md:flex-1 overflow-visible md:overflow-y-auto p-4 customized-scrollbar']) }}>
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
