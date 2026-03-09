<x-layouts::auth title="Akses Berakhir">
    <div class="w-full max-w-lg mx-auto bg-white p-8 rounded-xl shadow-sm border border-zinc-200 dark:bg-zinc-900 dark:border-zinc-800">
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-red-100 mb-4 dark:bg-red-900/30 ring-8 ring-red-50 dark:ring-red-900/10">
                <flux:icon name="no-symbol" class="w-8 h-8 text-red-600 dark:text-red-400" />
            </div>
            
            <h1 class="text-2xl font-bold text-zinc-900 dark:text-white">Akses Berakhir</h1>
            <p class="mt-2 text-zinc-600 dark:text-zinc-400">
                {{ session('error') ?? 'Langganan atau masa percobaan gratis Anda telah berakhir. Harap perpanjang langganan untuk lanjut menggunakan sistem.' }}
            </p>
        </div>

        <div class="space-y-6">
            <div>
                <h3 class="text-base font-semibold text-zinc-900 dark:text-white mb-4">Pilihan Paket Langganan:</h3>
                
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <!-- Monthly Plan -->
                    <div class="border border-zinc-200 rounded-lg p-4 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-800">
                        <div class="font-semibold text-zinc-900 dark:text-white">Paket Bulanan</div>
                        <div class="text-xl font-bold text-blue-600 dark:text-blue-400 mt-1">Rp 50.000<span class="text-xs text-zinc-500 font-normal"> /30 hari</span></div>
                    </div>
                    
                    <!-- Yearly Plan -->
                    <div class="border border-zinc-200 rounded-lg p-4 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-800 relative overflow-hidden">
                        <div class="absolute top-0 right-0 bg-blue-600 text-white text-[10px] uppercase font-bold px-2 py-0.5 rounded-bl-lg">Hemat</div>
                        <div class="font-semibold text-zinc-900 dark:text-white">Paket Tahunan</div>
                        <div class="text-xl font-bold text-blue-600 dark:text-blue-400 mt-1">Rp 550.000<span class="text-xs text-zinc-500 font-normal"> /365 hari</span></div>
                    </div>
                </div>
            </div>

            <div class="bg-zinc-50 rounded-lg p-5 border border-zinc-200 dark:bg-zinc-800/50 dark:border-zinc-700">
                <h3 class="text-base font-semibold text-zinc-900 dark:text-white mb-3 flex items-center gap-2">
                    <flux:icon name="credit-card" class="w-5 h-5 text-zinc-500" />
                    Instruksi Pembayaran
                </h3>
                <ol class="text-sm text-zinc-600 dark:text-zinc-300 space-y-3 list-decimal list-inside">
                    <li>Pilih paket langganan yang Anda inginkan (Bulanan / Tahunan).</li>
                    <li>Transfer sesuai nominal ke rekening berikut:<br>
                        <div class="mt-2 p-3 bg-white border border-zinc-200 rounded-md dark:bg-zinc-900 dark:border-zinc-700">
                            <span class="block text-xs uppercase tracking-wider text-zinc-500 font-semibold mb-1">Bank BCA</span>
                            <span class="block text-lg font-mono font-bold text-zinc-900 dark:text-white">123 456 7890</span>
                            <span class="block text-sm text-zinc-600 dark:text-zinc-400">a.n Admin Web POS</span>
                        </div>
                    </li>
                    <li>Konfirmasi pembayaran dengan mengirimkan bukti transfer via WhatsApp ke admin.</li>
                </ol>

                <div class="mt-5">
                    <a href="https://wa.me/6281234567890?text=Halo%20Admin,%20saya%20sudah%20transfer%20untuk%20perpanjang%20langganan%20aplikasi%20POS." target="_blank" class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-green-600 border border-transparent rounded-lg font-semibold text-white hover:bg-green-700 transition focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                        <svg class="w-5 h-5 fill-current" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51a12.8 12.8 0 0 0-.57-.01c-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 0 1-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 0 1-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 0 1 2.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0 0 12.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 0 0 5.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 0 0-3.48-8.413Z"/></svg>
                        Konfirmasi Pembayaran
                    </a>
                </div>
            </div>

            <div class="text-center">
                <a href="{{ route('login') }}" class="text-sm font-medium text-zinc-600 hover:text-zinc-900 dark:text-zinc-400 dark:hover:text-white transition">
                    &larr; Kembali ke halaman Login
                </a>
            </div>
        </div>
    </div>
</x-layouts::auth>
