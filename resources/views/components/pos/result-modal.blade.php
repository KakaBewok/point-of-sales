@props([
    'paymentResult',
    'qrisImageData'
])

<flux:modal wire:model="showResultModal" {{ $attributes->merge(['class' => 'max-w-sm md:max-w-md p-0 overflow-hidden bg-white dark:bg-zinc-900 rounded-md']) }} :closable="false">
    @if($paymentResult)
        <div class="p-8 flex flex-col items-center text-center">
            <div class="h-16 w-16 bg-green-500 text-white rounded-full flex items-center justify-center mb-6 shadow-xl shadow-green-100 border-4 border-white dark:border-zinc-800">
                <flux:icon name="check" variant="solid" class="h-8 w-8" />
            </div>
            
            <h2 class="text-xl font-black uppercase tracking-wide text-zinc-900 dark:text-white leading-none">Berhasil!</h2>
            <p class="text-[10px] font-black text-zinc-400 uppercase tracking-[0.2em] mt-2 mb-8">Invoice #{{ $paymentResult['invoice'] }}</p>

            @if($paymentResult['method'] === 'qris')
                <div class="mb-6 w-full flex flex-col items-center gap-4">
                    {{-- Generated dynamic QR code --}}
                    @if($qrisImageData)
                        <div class="p-3 bg-white border-4 border-zinc-100 rounded-3xl shadow-lg">
                            <img src="{{ $qrisImageData }}" class="h-52 w-52 object-contain" alt="QRIS">
                        </div>
                    @endif

                    <p class="text-2xl font-black text-green-600 tracking-wide leading-none">
                        Rp{{ number_format($paymentResult['grand_total'], 0, ',', '.') }}
                    </p>

                    <p class="text-[10px] font-black text-zinc-400 tracking-[0.2em] uppercase text-center">
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
                            class="w-full h-12 rounded-md bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-black tracking-wide flex items-center justify-center gap-2 transition-all active:scale-95 shadow-md"
                        >
                            <flux:icon name="check-circle" class="h-4 w-4" />
                            Konfirmasi Pembayaran Diterima
                        </button>
                    @else
                        <div class="w-full h-12 rounded-md bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400 text-sm font-black tracking-wide flex items-center justify-center gap-2">
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
                    <div class="bg-zinc-50 p-4 rounded-lg">
                        <p class="text-xs font-black text-zinc-400 mb-1">Diterima</p>
                        <p class="text-sm font-black">Rp{{ number_format($paymentResult['cash_received'], 0, ',', '.') }}</p>
                    </div>
                    <div class="bg-emerald-50 p-4 rounded-lg">
                        <p class="text-xs font-black text-emerald-400 mb-1">Kembali</p>
                        <p class="text-sm font-black text-emerald-600">Rp{{ number_format($paymentResult['change'], 0, ',', '.') }}</p>
                    </div>
                </div>
            @endif

            <div class="grid grid-cols-1 w-full gap-3 pt-2">
                <a href="{{ route('receipt.print', $paymentResult['transaction_id']) }}" target="_blank" class="h-12 w-full flex items-center justify-center gap-2 bg-zinc-100 hover:bg-zinc-200 text-zinc-900 rounded-xl text-[10px] font-black uppercase tracking-widest border border-zinc-200">
                    <flux:icon name="printer" class="h-4 w-4" /> Cetak Struk
                </a>
                <button wire:click="newTransaction" type="button" class="cursor-pointer h-12 w-full bg-green-600 hover:bg-green-700 text-white rounded-md text-[12px] font-black uppercase shadow-lg">
                    Selesai
                </button>
            </div>
        </div>
    @endif
</flux:modal>
