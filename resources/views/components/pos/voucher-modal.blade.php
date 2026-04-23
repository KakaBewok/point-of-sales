@props([
    'tempVoucherCode',
    'voucherError'
])

<flux:modal wire:model="showVoucherModal" {{ $attributes->merge(['class' => 'max-w-xl p-0 overflow-hidden bg-white dark:bg-zinc-900 rounded-3xl w-full']) }}>
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
