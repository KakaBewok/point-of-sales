<div class="px-6 py-8 md:px-8 space-y-8 max-w-7xl mx-auto flex-1 w-full">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold tracking-tight text-zinc-900 dark:text-white">Manajemen Voucher</h1>
        <div class="flex items-center gap-3">
            @if(!empty($selected) && is_array($selected))
                <flux:button variant="danger" icon="trash" class="h-10 px-4" wire:click="confirmDeleteSelected">Hapus Terpilih ({{ count($selected) }})</flux:button>
            @endif
            <flux:button variant="primary" icon="plus" class="h-10 px-4" wire:click="create">Tambah Voucher</flux:button>
        </div>
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

    <div class="flex items-center gap-4">
        <div class="flex items-center gap-2 max-w-sm w-full">
            <flux:checkbox wire:model.live="selectAll" label="Pilih Semua" class="mr-2 whitespace-nowrap" />
            <flux:input icon="magnifying-glass" class="h-10 w-full" wire:model.live.debounce.300ms="search" placeholder="Cari kode voucher..." />
        </div>
    </div>

    <div class="overflow-hidden rounded-lg border border-zinc-200 bg-white shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-zinc-50 border-b border-zinc-200 dark:bg-zinc-900/50 dark:border-zinc-800">
                    <tr>
                        <th class="px-4 py-4 text-center w-12">
                            <flux:checkbox wire:model.live="selectAll" />
                        </th>
                        <th class="px-6 py-4 text-left font-semibold text-zinc-600 dark:text-zinc-400">Kode</th>
                        <th class="px-6 py-4 text-right font-semibold text-zinc-600 dark:text-zinc-400">Diskon</th>
                        <th class="px-6 py-4 text-right font-semibold text-zinc-600 dark:text-zinc-400">Min. Transaksi</th>
                        <th class="px-6 py-4 text-center font-semibold text-zinc-600 dark:text-zinc-400">Pemakaian</th>
                        <th class="px-6 py-4 text-left font-semibold text-zinc-600 dark:text-zinc-400">Masa Berlaku</th>
                        <th class="px-6 py-4 text-center font-semibold text-zinc-600 dark:text-zinc-400">Status</th>
                        <th class="px-6 py-4 text-right font-semibold text-zinc-600 dark:text-zinc-400">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800/60">
                    @forelse($vouchers as $voucher)
                        <tr class="transition-colors hover:bg-zinc-50 dark:hover:bg-zinc-800/50 {{ in_array((string)$voucher->id, $selected) ? 'bg-blue-50 dark:bg-blue-900/10' : '' }}">
                            <td class="px-4 py-4 text-center">
                                <flux:checkbox wire:model.live="selected" value="{{ $voucher->id }}" />
                            </td>
                            <td class="px-6 py-4 font-mono font-bold text-zinc-900 dark:text-white">{{ $voucher->code }}</td>
                            <td class="px-6 py-4 text-right text-zinc-900 dark:text-white">
                                @if($voucher->discount_type === 'percentage')
                                    <span class="font-bold border border-zinc-200 bg-zinc-50 rounded-md px-2 py-0.5 dark:border-zinc-700 dark:bg-zinc-800">{{ $voucher->discount_value }}%</span>
                                    @if($voucher->max_discount)
                                        <div class="text-xs text-zinc-500 mt-1">Maks Rp {{ number_format($voucher->max_discount, 0, ',', '.') }}</div>
                                    @endif
                                @else
                                    <span class="font-bold">Rp {{ number_format($voucher->discount_value, 0, ',', '.') }}</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right font-medium text-zinc-600 dark:text-zinc-300">
                                Rp {{ number_format($voucher->min_transaction, 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-4 text-center">
                                <div class="text-sm font-medium {{ ($voucher->usage_limit && $voucher->used_count >= $voucher->usage_limit) ? 'text-red-600 dark:text-red-400 font-bold' : 'text-zinc-600 dark:text-zinc-400' }}">
                                    {{ $voucher->used_count }} / {{ $voucher->usage_limit ?: '∞' }}
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm text-zinc-600 dark:text-zinc-400">
                                @if($voucher->valid_from && $voucher->valid_until)
                                    {{ $voucher->valid_from->format('d/m/y') }} - {{ $voucher->valid_until->format('d/m/y') }}
                                @elseif($voucher->valid_until)
                                    S/d {{ $voucher->valid_until->format('d/m/y') }}
                                @else
                                    <span class="text-zinc-400 italic">Selamanya</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="inline-flex items-center rounded-md px-2.5 py-1 text-xs font-semibold ring-1 ring-inset
                                    @if(!$voucher->is_active) bg-zinc-50 text-zinc-600 ring-zinc-500/20 dark:bg-zinc-800/50 dark:text-zinc-400 dark:ring-zinc-700
                                    @elseif(!$voucher->isValid()) bg-red-50 text-red-700 ring-red-600/20 dark:bg-red-900/30 dark:text-red-400 dark:ring-red-900/50
                                    @else bg-emerald-50 text-emerald-700 ring-emerald-600/20 dark:bg-emerald-900/30 dark:text-emerald-400 dark:ring-emerald-900/50
                                    @endif">
                                    {{ !$voucher->is_active ? 'Nonaktif' : (!$voucher->isValid() ? 'Kedaluwarsa' : 'Aktif') }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <flux:button size="sm" variant="ghost" class="h-8 w-8 px-0" icon="pencil" wire:click="edit({{ $voucher->id }})" />
                                    <flux:button size="sm" variant="ghost" class="h-8 w-8 px-0 text-red-600 hover:bg-red-50 hover:text-red-700 dark:hover:bg-red-900/20 dark:hover:text-red-400" icon="trash" wire:click="confirmDelete({{ $voucher->id }}, '{{ addslashes($voucher->code) }}')" />
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="px-6 py-10 text-center text-zinc-500">
                            <flux:icon name="ticket" class="mx-auto h-8 w-8 opacity-40 mb-3" />
                            Tidak ada voucher ditemukan.
                        </td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="border-t border-zinc-200 bg-white p-4 dark:border-zinc-800 dark:bg-zinc-900">{{ $vouchers->links() }}</div>
    </div>

    <flux:modal wire:model="showModal" class="max-w-lg md:max-w-xl p-0 overflow-hidden bg-white dark:bg-zinc-900 rounded-2xl shadow-2xl">
        <div class="p-6">
            <header class="border-b border-zinc-100 dark:border-zinc-800 pb-4 mb-4">
                <h2 class="text-lg font-semibold tracking-tight text-zinc-900 dark:text-white">{{ $editingId ? 'Edit Voucher' : 'Tambah Voucher' }}</h2>
                <p class="text-sm text-zinc-500 mt-1">Lengkapi informasi voucher di bawah ini.</p>
            </header>

            <div class="max-h-[60vh] overflow-y-auto pr-2 customized-scrollbar">
                <form wire:submit="save" id="voucherForm" class="space-y-4">
                    <flux:field>
                        <flux:label class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Kode Voucher <span class="text-red-500">*</span></flux:label>
                        <flux:input class="h-10 mt-1 uppercase font-mono rounded-lg border-zinc-300 focus:border-green-500 focus:ring-green-500" wire:model="code" />
                        <flux:error name="code" class="mt-1 text-sm text-red-500" />
                    </flux:field>
                    
                    <div class="grid grid-cols-2 gap-4">
                        <flux:field>
                            <flux:label class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Tipe Diskon <span class="text-red-500">*</span></flux:label>
                            <flux:select class="h-10 mt-1 rounded-lg border-zinc-300 focus:border-green-500 focus:ring-green-500" wire:model.live="discount_type">
                                <flux:select.option value="percentage">Persentase (%)</flux:select.option>
                                <flux:select.option value="fixed">Nominal (Rp)</flux:select.option>
                            </flux:select>
                            <flux:error name="discount_type" class="mt-1 text-sm text-red-500" />
                        </flux:field>
                        
                        <flux:field>
                            <flux:label class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Nilai Diskon <span class="text-red-500">*</span></flux:label>
                            <flux:input class="h-10 mt-1 rounded-lg border-zinc-300 focus:border-green-500 focus:ring-green-500" type="number" wire:model="discount_value" />
                            <flux:error name="discount_value" class="mt-1 text-sm text-red-500" />
                        </flux:field>
                    </div>

                    @if($discount_type === 'percentage')
                        <flux:field>
                            <flux:label class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Maksimal Diskon (Rp)</flux:label>
                            <flux:input class="h-10 mt-1 rounded-lg border-zinc-300 focus:border-green-500 focus:ring-green-500" type="number" wire:model="max_discount" />
                            <flux:error name="max_discount" class="mt-1 text-sm text-red-500" />
                        </flux:field>
                    @endif

                    <div class="grid grid-cols-2 gap-4">
                        <flux:field>
                            <flux:label class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Minimal Transaksi (Rp) <span class="text-red-500">*</span></flux:label>
                            <flux:input class="h-10 mt-1 rounded-lg border-zinc-300 focus:border-green-500 focus:ring-green-500" type="number" wire:model="min_transaction" />
                            <flux:error name="min_transaction" class="mt-1 text-sm text-red-500" />
                        </flux:field>
                        <flux:field>
                            <flux:label class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Batas Pemakaian</flux:label>
                            <flux:input class="h-10 mt-1 rounded-lg border-zinc-300 focus:border-green-500 focus:ring-green-500" type="number" wire:model="usage_limit" />
                            <flux:error name="usage_limit" class="mt-1 text-sm text-red-500" />
                        </flux:field>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <flux:field>
                            <flux:label class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Berlaku Dari</flux:label>
                            <flux:input class="h-10 mt-1 rounded-lg border-zinc-300 focus:border-green-500 focus:ring-green-500" type="datetime-local" wire:model="valid_from" />
                            <flux:error name="valid_from" class="mt-1 text-sm text-red-500" />
                        </flux:field>
                        <flux:field>
                            <flux:label class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Berlaku Sampai</flux:label>
                            <flux:input class="h-10 mt-1 rounded-lg border-zinc-300 focus:border-green-500 focus:ring-green-500" type="datetime-local" wire:model="valid_until" />
                            <flux:error name="valid_until" class="mt-1 text-sm text-red-500" />
                        </flux:field>
                    </div>

                    <div class="pt-2">
                        <flux:checkbox label="Voucher Aktif" wire:model="is_active" />
                    </div>
                </form>
            </div>

            <footer class="mt-6 flex justify-end gap-3 pt-4 border-t border-zinc-100 dark:border-zinc-800">
                <button type="button" class="h-10 px-4 rounded-lg bg-zinc-100 text-zinc-700 hover:bg-zinc-200 dark:bg-zinc-800 dark:text-zinc-300 dark:hover:bg-zinc-700 font-medium text-sm transition-colors" wire:click="$set('showModal', false)">Batal</button>
                <button type="submit" form="voucherForm" class="h-10 px-6 rounded-lg bg-green-600 hover:bg-green-700 text-white font-semibold text-sm transition-colors">{{ $editingId ? 'Perbarui' : 'Simpan' }}</button>
            </footer>
        </div>
    </flux:modal>

    <!-- Delete Confirmation Modal -->
    <flux:modal wire:model="showDeleteModal" class="max-w-md p-0 overflow-hidden bg-white dark:bg-zinc-900 rounded-2xl shadow-2xl transition-all">
        <div class="p-6">
            <div class="flex flex-col items-center text-center">
                <div class="flex h-12 w-12 items-center justify-center rounded-full bg-red-100 dark:bg-red-900/30 mb-4 mx-auto">
                    <flux:icon name="exclamation-triangle" class="h-6 w-6 text-red-600 dark:text-red-400" />
                </div>
                <h3 class="text-lg font-semibold text-gray-800 dark:text-white">
                    Konfirmasi Hapus
                </h3>
                <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                    Apakah Anda yakin ingin menghapus <span class="font-medium text-gray-700 dark:text-gray-300">{{ $itemToDeleteName ?: 'item ini' }}</span>?
                    <br>Data riwayat transaksi tetap aman.
                </p>
            </div>
            
            <div class="mt-6 flex justify-end gap-3 w-full">
                <button type="button" wire:click="$set('showDeleteModal', false)" class="h-10 px-4 rounded-lg bg-gray-100 text-gray-700 hover:bg-gray-200 dark:bg-zinc-800 dark:text-gray-300 dark:hover:bg-zinc-700 font-medium text-sm transition-colors">
                    Batal
                </button>
                <button type="button" wire:click="processDelete" class="h-10 px-4 rounded-lg bg-red-600 text-white hover:bg-red-700 disabled:opacity-50 disabled:cursor-not-allowed font-medium text-sm transition-colors" wire:loading.attr="disabled" wire:target="processDelete">
                    <span wire:loading.remove wire:target="processDelete">Hapus</span>
                    <span wire:loading wire:target="processDelete">Memproses...</span>
                </button>
            </div>
        </div>
    </flux:modal>
</div>
