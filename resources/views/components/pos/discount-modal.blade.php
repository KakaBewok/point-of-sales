@props([
    'tempDiscountType',
    'tempDiscountValue',
    'enableVirtualKeypad'
])

<flux:modal wire:model="showDiscountModal" {{ $attributes->merge(['class' => 'max-w-xs md:max-w-sm p-0 overflow-hidden bg-white dark:bg-zinc-900 rounded-lg w-full']) }}>
    <div class="p-3 space-y-5">
        <header class="text-center pb-4 border-b border-zinc-100 dark:border-zinc-800">
            <h2 class="text-lg font-black uppercase tracking-wide text-zinc-900 dark:text-white leading-none">Input Diskon</h2>
        </header>

        <div class="space-y-4">
            <div class="grid grid-cols-2 gap-2">
                <button wire:click="$set('tempDiscountType', 'percentage')" class="cursor-pointer py-3 rounded-xl border-2 text-xs font-black uppercase transition-all {{ $tempDiscountType === 'percentage' ? 'border-amber-500 bg-amber-50 text-amber-700' : 'border-zinc-200 bg-zinc-50 text-zinc-500' }}">Persen (%)</button>
                <button wire:click="$set('tempDiscountType', 'fixed')" class="cursor-pointer py-3 rounded-xl border-2 text-xs font-black uppercase transition-all {{ $tempDiscountType === 'fixed' ? 'border-amber-500 bg-amber-50 text-amber-700' : 'border-zinc-200 bg-zinc-50 text-zinc-500' }}">Nominal (Rp)</button>
            </div>

            <div class="relative">
                @if($enableVirtualKeypad)
                    <input type="text" readonly value="{{ $tempDiscountValue ? number_format((float)$tempDiscountValue, 0, ',', '.') : '' }}" class="w-full h-16 text-center text-3xl font-black bg-white dark:bg-zinc-950 border-2 border-zinc-200 dark:border-zinc-800 rounded-2xl text-zinc-900 dark:text-white" placeholder="0">
                @else
                    <input type="number" wire:model="tempDiscountValue" class="w-full h-16 text-center text-3xl font-black bg-white dark:bg-zinc-950 border-2 border-zinc-200 dark:border-zinc-800 rounded-2xl text-zinc-900 dark:text-white" placeholder="0" autofocus>
                @endif
                <div class="absolute left-4 top-1/2 -translate-y-1/2 text-zinc-400 font-bold text-lg select-none pointer-events-none">{{ $tempDiscountType === 'fixed' ? 'Rp' : '%' }}</div>
            </div>

            @if($enableVirtualKeypad)
                <div class="grid grid-cols-3 gap-2">
                    @foreach(['1', '2', '3', '4', '5', '6', '7', '8', '9', '000', '00', '0'] as $key)
                        <button type="button" wire:click="appendDiscountKeypad('{{ $key }}')" class="cursor-pointer h-14 bg-zinc-100 hover:bg-zinc-200 dark:bg-zinc-800 dark:hover:bg-zinc-700 text-zinc-900 dark:text-white rounded-xl font-bold text-xl transition-colors active:scale-95 shadow-sm">
                            {{ $key }}
                        </button>
                    @endforeach
                    <button type="button" wire:click="clearDiscountKeypad" class="cursor-pointer col-span-1 h-12 bg-zinc-200 hover:bg-zinc-300 dark:bg-zinc-800 dark:hover:bg-zinc-700 text-zinc-600 dark:text-zinc-400 rounded-xl font-black text-xs uppercase active:scale-95 shadow-sm">Clear</button>
                    <button type="button" wire:click="removeDiscountKeypad" class="cursor-pointer col-span-2 h-12 bg-red-50 hover:bg-red-100 text-red-600 dark:bg-red-900/20 dark:hover:bg-red-900/40 rounded-xl flex items-center justify-center transition-colors active:scale-95 shadow-sm">
                        <flux:icon name="backspace" class="h-6 w-6" />
                    </button>
                </div>
            @endif
        </div>

        <div class="flex gap-2 pt-2">
            <button type="button" wire:click="$set('showDiscountModal', false)" class="cursor-pointer flex-1 h-12 py-2 rounded-xl border border-zinc-200 dark:border-zinc-700 text-xs font-black uppercase text-zinc-500 hover:bg-zinc-50 dark:hover:bg-zinc-800 transition-colors">Batal</button>
            <button type="button" wire:click="applyDiscountAction" class="cursor-pointer flex-1 h-12 py-2 rounded-xl bg-amber-500 hover:bg-amber-600 text-white text-xs font-black uppercase shadow-lg shadow-amber-500/30 transition-all">Terapkan</button>
        </div>
    </div>
</flux:modal>
