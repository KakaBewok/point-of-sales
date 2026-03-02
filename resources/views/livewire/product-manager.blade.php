<div class="px-6 py-8 md:px-8 space-y-8 max-w-7xl mx-auto flex-1 w-full">
    {{-- Header --}}
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold tracking-tight text-zinc-900 dark:text-white">Manajemen Produk</h1>
        <div class="flex items-center gap-3">
            @if(count($selected) > 0)
                <flux:button variant="danger" icon="trash" class="h-10 px-4" wire:click="confirmDeleteSelected">Hapus Terpilih ({{ count($selected) }})</flux:button>
            @endif
            <flux:button variant="primary" icon="plus" class="h-10 px-4" wire:click="create">Tambah Produk</flux:button>
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

    {{-- Filters --}}
    <div class="flex flex-col gap-4 sm:flex-row">
        <div class="flex-1">
            <flux:input icon="magnifying-glass" class="h-10" wire:model.live.debounce.300ms="search" placeholder="Cari produk atau SKU..." />
        </div>
        <div class="w-full sm:w-56">
            <flux:select wire:model.live="categoryFilter" class="h-10" placeholder="Semua Kategori">
                <flux:select.option value="">Semua Kategori</flux:select.option>
                @foreach($categories as $cat)
                    <flux:select.option value="{{ $cat->id }}">{{ $cat->name }}</flux:select.option>
                @endforeach
            </flux:select>
        </div>
    </div>

    {{-- Product Table --}}
    <div class="overflow-hidden rounded-lg border border-zinc-200 bg-white shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-zinc-50 border-b border-zinc-200 dark:bg-zinc-900/50 dark:border-zinc-800">
                    <tr>
                        <th class="px-6 py-4 w-10 text-center">
                            <flux:checkbox wire:model.live="selectAll" />
                        </th>
                        <th class="px-6 py-4 text-left font-semibold text-zinc-600 dark:text-zinc-400">Produk</th>
                        <th class="px-6 py-4 text-left font-semibold text-zinc-600 dark:text-zinc-400">SKU</th>
                        <th class="px-6 py-4 text-left font-semibold text-zinc-600 dark:text-zinc-400">Kategori</th>
                        <th class="px-6 py-4 text-right font-semibold text-zinc-600 dark:text-zinc-400">Harga</th>
                        <th class="px-6 py-4 text-center font-semibold text-zinc-600 dark:text-zinc-400">Stok</th>
                        <th class="px-6 py-4 text-center font-semibold text-zinc-600 dark:text-zinc-400">Status</th>
                        <th class="px-6 py-4 text-right font-semibold text-zinc-600 dark:text-zinc-400">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800/60">
                    @forelse($products as $product)
                        <tr class="transition-colors hover:bg-zinc-50 dark:hover:bg-zinc-800/50">
                            <td class="px-6 py-4 text-center">
                                <flux:checkbox wire:model.live="selected" value="{{ $product->id }}" />
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-4">
                                    <div class="flex h-10 w-10 shrink-0 items-center justify-center overflow-hidden rounded-md bg-zinc-100 dark:bg-zinc-800">
                                        @if($product->image)
                                            <img src="{{ Storage::url($product->image) }}" class="h-full w-full object-cover" alt="">
                                        @else
                                            <flux:icon name="cube" class="h-5 w-5 text-zinc-400" />
                                        @endif
                                    </div>
                                    <span class="font-medium text-zinc-900 dark:text-white">{{ $product->name }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 font-mono text-sm text-zinc-600 dark:text-zinc-400">{{ $product->sku }}</td>
                            <td class="px-6 py-4 text-zinc-600 dark:text-zinc-400">{{ $product->category->name }}</td>
                            <td class="px-6 py-4 text-right font-medium text-zinc-900 dark:text-white">Rp {{ number_format($product->price, 0, ',', '.') }}</td>
                            <td class="px-6 py-4 text-center">
                                <span class="inline-flex items-center rounded-md px-2.5 py-1 text-xs font-semibold ring-1 ring-inset
                                    {{ $product->stock <= 0 ? 'bg-red-50 text-red-700 ring-red-600/20 dark:bg-red-900/30 dark:text-red-400 dark:ring-red-900/50' : ($product->isLowStock() ? 'bg-amber-50 text-amber-700 ring-amber-600/20 dark:bg-amber-900/30 dark:text-amber-400 dark:ring-amber-900/50' : 'bg-emerald-50 text-emerald-700 ring-emerald-600/20 dark:bg-emerald-900/30 dark:text-emerald-400 dark:ring-emerald-900/50') }}">
                                    {{ $product->stock }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <button outline wire:click="toggleActive({{ $product->id }})" class="cursor-pointer transition-opacity hover:opacity-80">
                                    <span class="inline-flex items-center rounded-md px-2.5 py-1 text-xs font-semibold ring-1 ring-inset {{ $product->is_active ? 'bg-emerald-50 text-emerald-700 ring-emerald-600/20 dark:bg-emerald-900/30 dark:text-emerald-400 dark:ring-emerald-900/50' : 'bg-zinc-50 text-zinc-600 ring-zinc-500/20 dark:bg-zinc-800/50 dark:text-zinc-400 dark:ring-zinc-700' }}">
                                        {{ $product->is_active ? 'Aktif' : 'Nonaktif' }}
                                    </span>
                                </button>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <flux:button size="sm" variant="ghost" class="h-8 w-8 px-0" icon="pencil" wire:click="edit({{ $product->id }})" />
                                    <flux:button size="sm" variant="ghost" class="h-8 w-8 px-0 text-red-600 hover:bg-red-50 hover:text-red-700 dark:hover:bg-red-900/20 dark:hover:text-red-400" icon="trash" wire:click="confirmDelete({{ $product->id }}, '{{ addslashes($product->name) }}')" />
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-10 text-center text-zinc-500 dark:text-zinc-400">
                                <flux:icon name="cube" class="mx-auto h-8 w-8 opacity-40 mb-3" />
                                Tidak ada produk ditemukan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div>{{ $products->links() }}</div>

    {{-- Modal --}}
    <flux:modal wire:model="showModal" class="max-w-lg md:max-w-xl p-0 overflow-hidden bg-white dark:bg-zinc-900 rounded-2xl shadow-2xl">
        <div class="p-6">
            <header class="border-b border-zinc-100 dark:border-zinc-800 pb-4 mb-4">
                <h2 class="text-lg font-semibold tracking-tight text-zinc-900 dark:text-white">{{ $editingId ? 'Edit Produk' : 'Tambah Produk' }}</h2>
                <p class="text-sm text-zinc-500 mt-1">Lengkapi informasi produk di bawah ini.</p>
            </header>

            <div class="max-h-[60vh] overflow-y-auto pr-2 customized-scrollbar">
                <form wire:submit="save" id="productForm" class="space-y-4">
                    <flux:field>
                        <flux:label class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Nama Produk <span class="text-red-500">*</span></flux:label>
                        <flux:input class="h-10 mt-1 rounded-lg border-zinc-300 focus:border-green-500 focus:ring-green-500" wire:model="name" />
                        <flux:error name="name" class="mt-1 text-sm text-red-500" />
                    </flux:field>

                    <div class="grid grid-cols-2 gap-4">
                        <flux:field>
                            <flux:label class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Kategori <span class="text-red-500">*</span></flux:label>
                            <flux:select class="h-10 mt-1 rounded-lg border-zinc-300 focus:border-green-500 focus:ring-green-500" wire:model="category_id">
                                <flux:select.option value="">Pilih Kategori</flux:select.option>
                                @foreach($categories as $cat)
                                    <flux:select.option value="{{ $cat->id }}">{{ $cat->name }}</flux:select.option>
                                @endforeach
                            </flux:select>
                            <flux:error name="category_id" class="mt-1 text-sm text-red-500" />
                        </flux:field>
                        
                        <flux:field>
                            <flux:label class="text-sm font-medium text-zinc-700 dark:text-zinc-300">SKU <span class="text-red-500">*</span></flux:label>
                            <flux:input class="h-10 mt-1 rounded-lg border-zinc-300 focus:border-green-500 focus:ring-green-500" wire:model="sku" />
                            <flux:error name="sku" class="mt-1 text-sm text-red-500" />
                        </flux:field>
                    </div>

                    <flux:field>
                        <flux:label class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Deskripsi</flux:label>
                        <flux:textarea wire:model="description" rows="3" class="mt-1 rounded-lg border-zinc-300 focus:border-green-500 focus:ring-green-500" />
                        <flux:error name="description" class="mt-1 text-sm text-red-500" />
                    </flux:field>

                    <div class="grid grid-cols-2 gap-4">
                        <flux:field>
                            <flux:label class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Harga Jual <span class="text-red-500">*</span></flux:label>
                            <flux:input class="h-10 mt-1 rounded-lg border-zinc-300 focus:border-green-500 focus:ring-green-500" type="number" wire:model="price" />
                            <flux:error name="price" class="mt-1 text-sm text-red-500" />
                        </flux:field>

                        <flux:field>
                            <flux:label class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Harga Modal</flux:label>
                            <flux:input class="h-10 mt-1 rounded-lg border-zinc-300 focus:border-green-500 focus:ring-green-500" type="number" wire:model="cost_price" />
                            <flux:error name="cost_price" class="mt-1 text-sm text-red-500" />
                        </flux:field>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <flux:field>
                            <flux:label class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Stok <span class="text-red-500">*</span></flux:label>
                            <flux:input class="h-10 mt-1 rounded-lg border-zinc-300 focus:border-green-500 focus:ring-green-500" type="number" wire:model="stock" />
                            <flux:error name="stock" class="mt-1 text-sm text-red-500" />
                        </flux:field>

                        <flux:field>
                            <flux:label class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Batas Stok Rendah <span class="text-red-500">*</span></flux:label>
                            <flux:input class="h-10 mt-1 rounded-lg border-zinc-300 focus:border-green-500 focus:ring-green-500" type="number" wire:model="low_stock_threshold" />
                            <flux:error name="low_stock_threshold" class="mt-1 text-sm text-red-500" />
                        </flux:field>
                    </div>

                    <flux:field>
                        <flux:label class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Gambar Produk</flux:label>
                        <div class="mt-2 flex items-center gap-4">
                            <div class="relative h-24 w-24 overflow-hidden rounded-xl border border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-800/50 shrink-0">
                                @if ($image)
                                    <img src="{{ $image->temporaryUrl() }}" class="h-full w-full object-cover">
                                @elseif ($existingImage)
                                    <img src="{{ Storage::url($existingImage) }}" class="h-full w-full object-cover">
                                @else
                                    <flux:icon name="photo" class="h-6 w-6 m-auto top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 absolute text-zinc-400" />
                                @endif
                            </div>
                            <div class="flex-1">
                                <input type="file" wire:model="image" id="image-upload-{{ $editingId ?? 'new' }}" accept="image/*" class="block w-full text-sm text-zinc-500 file:mr-3 file:rounded-md file:border-0 file:bg-zinc-100 file:px-4 file:py-2 file:text-sm file:font-medium file:text-zinc-600 hover:file:bg-zinc-200 dark:text-zinc-400 dark:file:bg-zinc-800 dark:file:text-white dark:hover:file:bg-zinc-700 transition-colors" />
                                <div class="mt-2 text-xs text-zinc-500 dark:text-zinc-400">Max ukuran 2MB. Format: JPG, PNG.</div>
                            </div>
                        </div>
                        <flux:error name="image" class="mt-1 text-sm text-red-500" />
                    </flux:field>

                    <div class="pt-2">
                        <flux:checkbox label="Produk Aktif" wire:model="is_active" />
                    </div>
                </form>
            </div>

            <footer class="mt-6 flex justify-end gap-3 pt-4 border-t border-zinc-100 dark:border-zinc-800">
                <button type="button" class="h-10 px-4 rounded-lg bg-zinc-100 text-zinc-700 hover:bg-zinc-200 dark:bg-zinc-800 dark:text-zinc-300 dark:hover:bg-zinc-700 font-medium text-sm transition-colors" wire:click="$set('showModal', false)">Batal</button>
                <button type="submit" form="productForm" class="h-10 px-6 rounded-lg bg-green-600 hover:bg-green-700 text-white font-semibold text-sm transition-colors">{{ $editingId ? 'Perbarui' : 'Simpan' }}</button>
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
                    <br>Tindakan ini tidak dapat dibatalkan.
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
