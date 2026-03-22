<div class="px-1 md:px-6 py-8 space-y-8 max-w-7xl mx-auto flex-1 w-full">
    <div class="flex items-center justify-between">
        <h1 class="text-lg md:text-2xl font-bold tracking-tight text-zinc-900 dark:text-white">Kategori Produk</h1>
        <div class="flex flex-col md:flex-row justify-end md:items-center gap-3">
            @if(!empty($selected) && is_array($selected))
                <flux:button variant="danger" icon="trash" class="cursor-pointer h-10 px-4" wire:click="confirmDeleteSelected">Terpilih ({{ count($selected) }})</flux:button>
            @endif
            <flux:button variant="primary" icon="plus" class="cursor-pointer text-sm md:text-md h-10 px-4" wire:click="create">Kategori</flux:button>
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

    <div class="flex flex-col md:flex-row justify-center md:justify-start md:items-center gap-5 w-full">
        <flux:checkbox wire:model.live="selectAll" label="Pilih Semua" class="whitespace-normal md:shrink-0" />
        <flux:input icon="magnifying-glass" class="h-10 max-w-md" wire:model.live.debounce.300ms="search" placeholder="Cari kategori..." />
    </div>

    <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
        @forelse($categories as $category)
            <div class="relative rounded-lg border border-zinc-200 bg-white p-3 shadow-sm dark:border-zinc-800 dark:bg-zinc-900 transition-shadow hover:shadow-md">
                <div class="absolute top-4 left-4 z-10">
                    <flux:checkbox wire:model.live="selected" value="{{ $category->id }}" class="cursor-pointer"/>
                </div>
                <div class="flex items-start justify-between pl-9">
                    <div>
                        <h3 class="text-lg font-semibold text-zinc-900 dark:text-white">{{ $category->name }}</h3>
                        <p class="mt-1.5 text-sm text-zinc-500 dark:text-zinc-400">{{ $category->description ?? 'Tidak ada deskripsi' }}</p>
                    </div>
                    <span class="inline-flex items-center rounded-md px-2.5 py-1 text-xs font-semibold ring-1 ring-inset {{ $category->is_active ? 'bg-emerald-50 text-emerald-700 ring-emerald-600/20 dark:bg-emerald-900/30 dark:text-emerald-400 dark:ring-emerald-900/50' : 'bg-zinc-50 text-zinc-600 ring-zinc-500/20 dark:bg-zinc-800/50 dark:text-zinc-400 dark:ring-zinc-700' }}">
                        {{ $category->is_active ? 'Aktif' : 'Nonaktif' }}
                    </span>
                </div>
                <div class="mt-6 flex items-center justify-between border-t border-zinc-100 pt-4 dark:border-zinc-800">
                    <span class="text-sm font-medium text-zinc-500 dark:text-zinc-400">{{ $category->products_count }} produk</span>
                    <div class="flex gap-2">
                        <flux:button size="sm" variant="ghost" class="cursor-pointer h-8 w-8 px-0" icon="pencil" wire:click="edit({{ $category->id }})" />
                        <flux:button size="sm" variant="ghost" class="cursor-pointer h-8 w-8 px-0 [&_svg]:text-red-600 hover:bg-red-50 hover:text-red-700 dark:hover:bg-red-900/20 dark:hover:text-red-400" icon="trash" wire:click="confirmDelete({{ $category->id }}, '{{ addslashes($category->name) }}')" />
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full py-12 text-center text-zinc-500 dark:text-zinc-400">
                <flux:icon name="folder" class="mx-auto mb-3 h-8 w-8 opacity-40" />
                Tidak ada kategori.
            </div>
        @endforelse
    </div>

    <flux:modal wire:model="showModal" class="max-w-sm md:max-w-lg p-0 overflow-hidden bg-white dark:bg-zinc-900 rounded-2xl shadow-2xl">
        <div>
            <header class="border-b border-zinc-100 dark:border-zinc-800 pb-4 mb-4">
                <h2 class="text-lg font-semibold tracking-tight text-zinc-900 dark:text-white">{{ $editingId ? 'Edit Kategori' : 'Tambah Kategori' }}</h2>
                <p class="text-sm text-zinc-500 mt-1">Lengkapi informasi kategori di bawah ini.</p>
            </header>

            <div class="max-h-[60vh] overflow-y-auto pr-2 customized-scrollbar">
                <form wire:submit="save" id="categoryForm" class="space-y-4 p-3">
                    <flux:field>
                        <flux:label class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Nama Kategori <span class="text-red-500">*</span></flux:label>
                        <flux:input class="h-10 mt-1 rounded-lg border-zinc-300 " wire:model="name" />
                        <flux:error name="name" class="mt-1 text-sm text-red-500" />
                    </flux:field>

                    <flux:field>
                        <flux:label class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Deskripsi</flux:label>
                        <flux:textarea wire:model="description" rows="3" class="mt-1 rounded-lg border-zinc-300 " />
                        <flux:error name="description" class="mt-1 text-sm text-red-500" />
                    </flux:field>

                    <!-- <flux:field>
                        <flux:label class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Urutan <span class="text-red-500">*</span></flux:label>
                        <flux:input class="h-10 mt-1 rounded-lg border-zinc-300 " type="number" wire:model="sort_order" />
                        <flux:error name="sort_order" class="mt-1 text-sm text-red-500" />
                    </flux:field> -->

                    <div class="pt-2">
                        <flux:checkbox label="Kategori Aktif" wire:model="is_active" />
                    </div>
                </form>
            </div>

            <footer class="mt-6 flex justify-end gap-3 pt-4 border-t border-zinc-100 dark:border-zinc-800">
                <button type="button" class="h-10 px-4 rounded-lg bg-zinc-100 text-zinc-700 hover:bg-zinc-200 dark:bg-zinc-800 dark:text-zinc-300 dark:hover:bg-zinc-700 font-medium text-sm transition-colors" wire:click="$set('showModal', false)">Batal</button>
                <button type="submit" form="categoryForm" class="h-10 px-6 rounded-lg bg-green-600 hover:bg-green-700 text-white font-semibold text-sm transition-colors">{{ $editingId ? 'Perbarui' : 'Simpan' }}</button>
            </footer>
        </div>
    </flux:modal>

    <!-- Delete Confirmation Modal -->
    <flux:modal wire:model="showDeleteModal" class="max-w-sm md:max-w-md p-0 overflow-hidden bg-white dark:bg-zinc-900 rounded-2xl shadow-2xl transition-all">
        <div>
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
                @if($hasProductsWarning)
                    <div class="mt-3 rounded-lg bg-amber-50 border border-amber-200 dark:bg-amber-900/20 dark:border-amber-800/50 p-3">
                        <div class="flex items-start gap-2">
                            <flux:icon name="exclamation-triangle" class="h-5 w-5 text-amber-600 dark:text-amber-400 shrink-0 mt-0.5" />
                            <p class="text-sm text-amber-800 dark:text-amber-300">
                                @if($deleteType === 'multiple')
                                    <strong>{{ $categoriesWithProducts }} kategori</strong> masih memiliki produk. Menghapusnya dapat mempengaruhi data terkait.
                                @else
                                    Kategori ini masih memiliki produk. Menghapusnya dapat mempengaruhi data terkait.
                                @endif
                            </p>
                        </div>
                    </div>
                @endif
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
