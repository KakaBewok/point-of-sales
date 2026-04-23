<header {{ $attributes->merge(['class' => 'h-16 shrink-0 bg-white dark:bg-zinc-900 border-b border-zinc-200 dark:border-zinc-800 flex items-center px-6 justify-between shadow-sm']) }}>
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
