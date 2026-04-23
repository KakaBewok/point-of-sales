@props(['categories', 'selectedCategory'])

<nav {{ $attributes->merge(['class' => 'flex items-center gap-2 p-3 bg-white dark:bg-zinc-900 border-b border-zinc-200 dark:border-zinc-800 overflow-x-auto scrollbar-hide shrink-0']) }}>
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
