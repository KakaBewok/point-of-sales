<div class="px-6 py-8 md:px-8 space-y-8 max-w-3xl mx-auto flex-1 w-full">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold tracking-tight text-zinc-900 dark:text-white">Pengaturan Aplikasi</h1>
    </div>

    @if(session()->has('message'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)" x-transition.opacity.duration.500ms class="fixed top-6 right-6 z-50 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800 shadow-xl shadow-emerald-900/10 dark:border-emerald-900/50 dark:bg-emerald-900 dark:text-emerald-400 dark:shadow-black/50 flex items-center gap-3">
            <flux:icon name="check-circle" class="h-5 w-5" />
            {{ session('message') }}
            <button @click="show = false" class="ml-2 text-emerald-600 hover:text-emerald-800 dark:text-emerald-400/70 dark:hover:text-emerald-300 transition-colors">
                <flux:icon name="x-mark" class="h-4 w-4" />
            </button>
        </div>
    @endif
    @if(session()->has('error'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)" x-transition.opacity.duration.500ms class="fixed top-6 right-6 z-50 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm font-medium text-red-800 shadow-xl shadow-red-900/10 dark:border-red-900/50 dark:bg-red-900 dark:text-red-400 dark:shadow-black/50 flex items-center gap-3">
            <flux:icon name="exclamation-circle" class="h-5 w-5" />
            {{ session('error') }}
            <button @click="show = false" class="ml-2 text-red-600 hover:text-red-800 dark:text-red-400/70 dark:hover:text-red-300 transition-colors">
                <flux:icon name="x-mark" class="h-4 w-4" />
            </button>
        </div>
    @endif

    {{-- ======================================================= --}}
    {{-- Main settings form                                      --}}
    {{-- ======================================================= --}}
    <form wire:submit="save" class="space-y-8">

        {{-- General Section --}}
        <div class="rounded-lg border border-zinc-200 bg-white p-8 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
            <h3 class="mb-5 text-lg font-bold tracking-tight text-zinc-900 dark:text-white">Informasi Toko</h3>
            <div class="space-y-5">
                <flux:input label="Nama Toko" class="h-10" wire:model="store_name" required />
                <flux:textarea label="Alamat Toko" wire:model="store_address" rows="3" />
                <flux:input label="Nomor Telepon" class="h-10" wire:model="store_phone" />
            </div>
        </div>

        {{-- Logo Section --}}
        <div class="rounded-lg border border-zinc-200 bg-white p-8 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
            <h3 class="mb-5 text-lg font-bold tracking-tight text-zinc-900 dark:text-white">Logo Toko</h3>
            <p class="text-sm text-zinc-500 dark:text-zinc-400 mb-4">Logo akan ditampilkan di sidebar dan struk. Maks. 1MB (PNG, JPG, SVG).</p>
            
            <div class="flex items-start gap-6">
                {{-- Preview --}}
                <div class="shrink-0">
                    @if($logo)
                        <div class="h-24 w-24 rounded-xl border-2 border-green-300 bg-zinc-100 dark:bg-zinc-800 overflow-hidden flex items-center justify-center">
                            <img src="{{ $logo->temporaryUrl() }}" class="h-full w-full object-contain p-2" alt="Preview" />
                        </div>
                        <p class="text-xs text-green-600 font-medium mt-1 text-center">Preview</p>
                    @elseif($currentLogo)
                        <div class="h-24 w-24 rounded-xl border-2 border-zinc-200 bg-zinc-100 dark:bg-zinc-800 dark:border-zinc-700 overflow-hidden flex items-center justify-center">
                            <img src="{{ Illuminate\Support\Facades\Storage::url($currentLogo) }}" class="h-full w-full object-contain p-2" alt="Current Logo" />
                        </div>
                        <p class="text-xs text-zinc-500 font-medium mt-1 text-center">Logo saat ini</p>
                    @else
                        <div class="h-24 w-24 rounded-xl border-2 border-dashed border-zinc-300 bg-zinc-50 dark:bg-zinc-800 dark:border-zinc-700 flex items-center justify-center">
                            <flux:icon name="photo" class="h-8 w-8 text-zinc-300 dark:text-zinc-600" />
                        </div>
                    @endif
                </div>

                <div class="flex-1 space-y-3">
                    <flux:input type="file" wire:model="logo" accept="image/png,image/jpeg,image/svg+xml" class="h-10" />
                    @error('logo') <p class="text-sm text-red-500">{{ $message }}</p> @enderror
                    
                    @if($currentLogo)
                        <button type="button" wire:click="removeLogo" class="text-sm text-red-600 hover:text-red-700 font-medium flex items-center gap-1.5">
                            <flux:icon name="trash" class="h-3.5 w-3.5" />
                            Hapus Logo
                        </button>
                    @endif
                </div>
            </div>
        </div>

        {{-- Tax Section --}}
        <div class="rounded-lg border border-zinc-200 bg-white p-8 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
            <h3 class="mb-5 text-lg font-bold tracking-tight text-zinc-900 dark:text-white">Pengaturan Pajak</h3>
            <div class="space-y-5">
                <flux:checkbox label="Aktifkan Pajak (PPN)" wire:model.live="tax_enabled" description="Jika diaktifkan, pajak akan dihitung pada setiap transaksi." />
                
                @if($tax_enabled)
                    <div class="pl-7 mt-2">
                        <flux:input label="Persentase Pajak (%)" class="h-10 max-w-xs" type="number" step="0.1" wire:model="tax_rate" required />
                    </div>
                @endif
            </div>
        </div>

        {{-- Receipt Section --}}
        <div class="rounded-lg border border-zinc-200 bg-white p-8 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
            <h3 class="mb-5 text-lg font-bold tracking-tight text-zinc-900 dark:text-white">Struk / Resi</h3>
            <div class="space-y-5">
                <flux:input label="Catatan Kaki (Footer)" class="h-10" wire:model="receipt_footer" placeholder="Terima kasih atas kunjungan Anda!" />
            </div>
        </div>

        {{-- Social Media Section --}}
        <div class="rounded-lg border border-zinc-200 bg-white p-8 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
            <h3 class="mb-2 text-lg font-bold tracking-tight text-zinc-900 dark:text-white">Media Sosial</h3>
            <p class="text-sm text-zinc-500 dark:text-zinc-400 mb-5">Opsional. Masukkan URL lengkap profil media sosial toko Anda.</p>
            <div class="space-y-5">
                <flux:input label="Instagram" class="h-10" wire:model="social_instagram" placeholder="https://instagram.com/tokoanda" />
                @error('social_instagram') <p class="text-sm text-red-500 -mt-3">{{ $message }}</p> @enderror
                
                <flux:input label="TikTok" class="h-10" wire:model="social_tiktok" placeholder="https://tiktok.com/@tokoanda" />
                @error('social_tiktok') <p class="text-sm text-red-500 -mt-3">{{ $message }}</p> @enderror
                
                <flux:input label="Facebook" class="h-10" wire:model="social_facebook" placeholder="https://facebook.com/tokoanda" />
                @error('social_facebook') <p class="text-sm text-red-500 -mt-3">{{ $message }}</p> @enderror
                
                <flux:input label="YouTube" class="h-10" wire:model="social_youtube" placeholder="https://youtube.com/@tokoanda" />
                @error('social_youtube') <p class="text-sm text-red-500 -mt-3">{{ $message }}</p> @enderror
            </div>
        </div>

        <div class="flex justify-end pt-4">
            <flux:button type="submit" variant="primary" class="h-10 px-6">Simpan Pengaturan</flux:button>
        </div>
    </form>

    {{-- ======================================================= --}}
    {{-- QRIS Section — separate block, uses saveQris() action   --}}
    {{-- ======================================================= --}}
    <div class="rounded-lg border border-zinc-200 bg-white p-8 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
        {{-- Header --}}
        <div class="flex items-start justify-between mb-2">
            <div>
                <h3 class="text-lg font-bold tracking-tight text-zinc-900 dark:text-white flex items-center gap-2">
                    <div class="h-7 w-7 rounded-lg bg-indigo-100 dark:bg-indigo-900/40 flex items-center justify-center">
                        <flux:icon name="qr-code" class="h-4 w-4 text-indigo-600 dark:text-indigo-400" />
                    </div>
                    QRIS Pembayaran
                </h3>
                <p class="text-sm text-zinc-500 dark:text-zinc-400 mt-1.5 leading-relaxed">
                    Upload gambar QRIS statis dari bank/e-wallet Anda. Sistem akan membaca payload QR dan menyimpannya untuk pembayaran dinamis di kasir (tanpa payment gateway).
                </p>
            </div>
            @if($currentQrisPayload)
                <span class="shrink-0 ml-4 inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-black uppercase tracking-widest bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800">
                    <flux:icon name="check-circle" class="h-3.5 w-3.5" />
                    Aktif
                </span>
            @else
                <span class="shrink-0 ml-4 inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-black uppercase tracking-widest bg-zinc-100 text-zinc-500 dark:bg-zinc-800 dark:text-zinc-400 border border-zinc-200 dark:border-zinc-700">
                    Belum dikonfigurasi
                </span>
            @endif
        </div>

        {{-- Stored QRIS preview --}}
        @if($currentQrisImage)
            <div class="flex items-start gap-5 mt-5 p-4 bg-emerald-50 dark:bg-emerald-950/20 border border-emerald-200 dark:border-emerald-800 rounded-xl">
                <div class="h-28 w-28 shrink-0 rounded-xl border-2 border-emerald-300 dark:border-emerald-700 bg-white overflow-hidden flex items-center justify-center shadow-sm">
                    <img src="{{ \Illuminate\Support\Facades\Storage::url($currentQrisImage) }}" class="h-full w-full object-contain p-1" alt="QRIS Tersimpan">
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-[10px] font-black uppercase tracking-widest text-emerald-600 dark:text-emerald-400 mb-2">QRIS Tersimpan &amp; Aktif</p>
                    <p class="text-[10px] font-mono text-zinc-500 dark:text-zinc-400 break-all line-clamp-4 leading-relaxed bg-zinc-100 dark:bg-zinc-800 p-2 rounded-lg">{{ Str::limit($currentQrisPayload, 120) }}</p>
                    <button
                        wire:click="removeQris"
                        wire:confirm="Yakin hapus QRIS? Pembayaran QRIS di kasir tidak akan tersedia sampai QRIS baru diupload."
                        type="button"
                        class="mt-3 text-xs text-red-600 hover:text-red-700 font-semibold flex items-center gap-1.5 transition-colors"
                    >
                        <flux:icon name="trash" class="h-3.5 w-3.5" />
                        Hapus QRIS
                    </button>
                </div>
            </div>
        @endif

        {{-- Upload area --}}
        <div class="mt-6 space-y-4">
            <div>
                <label class="block text-sm font-semibold text-zinc-700 dark:text-zinc-300 mb-2">
                    {{ $currentQrisImage ? 'Ganti Gambar QRIS' : 'Upload Gambar QRIS' }}
                </label>
                <flux:input type="file" wire:model="qrisImage" accept="image/png,image/jpeg" class="h-10" />
                @error('qrisImage')
                    <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Preview of newly selected file --}}
            @if($qrisImage)
                <div class="flex items-center gap-4 p-4 bg-zinc-50 dark:bg-zinc-800 rounded-xl border-2 border-dashed border-green-300 dark:border-green-700">
                    <div class="h-20 w-20 shrink-0 rounded-xl border-2 border-green-200 bg-white overflow-hidden flex items-center justify-center">
                        <img src="{{ $qrisImage->temporaryUrl() }}" class="h-full w-full object-contain p-1" alt="Preview">
                    </div>
                    <div>
                        <p class="text-xs font-bold text-zinc-700 dark:text-zinc-300">{{ $qrisImage->getClientOriginalName() }}</p>
                        <p class="text-[10px] text-zinc-400 mt-0.5">{{ number_format($qrisImage->getSize() / 1024, 1) }} KB</p>
                        <p class="text-[10px] text-green-600 dark:text-green-400 font-semibold mt-1.5 flex items-center gap-1">
                            <flux:icon name="check" class="h-3 w-3" />
                            Siap diunggah — klik tombol di bawah untuk menyimpan
                        </p>
                    </div>
                </div>
            @endif

            {{-- Info box --}}
            <div class="bg-amber-50 dark:bg-amber-950/20 border border-amber-200 dark:border-amber-800 rounded-xl p-4">
                <div class="flex gap-3">
                    <flux:icon name="information-circle" class="h-5 w-5 text-amber-500 shrink-0 mt-0.5" />
                    <div class="text-xs text-amber-700 dark:text-amber-400 leading-relaxed space-y-1">
                        <p class="font-bold text-amber-800 dark:text-amber-300">Cara mendapatkan gambar QRIS statis:</p>
                        <p>1. Buka aplikasi merchant bank/e-wallet Anda (BRI, BNI, GoPay, DANA, OVO, dll.)</p>
                        <p>2. Unduh atau screenshot gambar QRIS statis (bukan QRIS transaksi)</p>
                        <p>3. Pastikan gambar QR code terlihat jelas, tidak blur dan tidak terpotong</p>
                        <p>4. Format yang didukung: <strong>JPG / PNG</strong>, maks. 2MB</p>
                    </div>
                </div>
            </div>

            <div class="flex items-center gap-3">
                <flux:button
                    wire:click="saveQris"
                    variant="primary"
                    class="h-10 px-6"
                >
                    <flux:icon name="qr-code" class="h-4 w-4 mr-1.5" />
                    Simpan &amp; Validasi QRIS
                </flux:button>

                @if(!$qrisImage)
                    <p class="text-xs text-zinc-400">Pilih file gambar terlebih dahulu</p>
                @endif
            </div>
        </div>
    </div>

</div>
