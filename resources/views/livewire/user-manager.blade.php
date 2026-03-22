<div class="px-1 py-8 md:px-3 space-y-8 max-w-7xl mx-auto flex-1 w-full">
    <div class="flex items-center justify-between">
        <h1 class="text-lg md:text-2xl font-bold tracking-tight text-zinc-900 dark:text-white">Manajemen Pengguna</h1>
        <flux:button variant="primary" icon="plus" class="text-md cursor-pointer h-10 px-4" wire:click="create">Pengguna</flux:button>
    </div>

    @if(session()->has('message'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)" x-transition.opacity.duration.500ms class="fixed top-6 right-6 z-50 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800 shadow-xl shadow-emerald-900/10 dark:border-emerald-900/50 dark:bg-emerald-900 dark:text-emerald-400 dark:shadow-black/50 flex items-center gap-3">
            <flux:icon name="check-circle" class="text-md h-5 w-5" />
            {{ session('message') }}
            <button @click="show = false" class="ml-2 text-emerald-600 hover:text-emerald-800 dark:text-emerald-400/70 dark:hover:text-emerald-300 transition-colors">
                <flux:icon name="x-mark" class="h-4 w-4" />
            </button>
        </div>
    @endif
    @if(session()->has('error'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)" x-transition.opacity.duration.500ms class="fixed top-6 right-6 z-50 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm font-medium text-red-800 shadow-xl shadow-red-900/10 dark:border-red-900/50 dark:bg-red-900 dark:text-red-400 dark:shadow-black/50 flex items-center gap-3">
            <flux:icon name="exclamation-circle" class="text-md h-5 w-5" />
            {{ session('error') }}
            <button @click="show = false" class="ml-2 text-red-600 hover:text-red-800 dark:text-red-400/70 dark:hover:text-red-300 transition-colors">
                <flux:icon name="x-mark" class="h-4 w-4" />
            </button>
        </div>
    @endif

    <div class="flex">
        <flux:input icon="magnifying-glass" class="text-md h-10 w-full max-w-sm" wire:model.live.debounce.500ms="search" placeholder="Cari nama atau email..." />
    </div>

    <div class="overflow-hidden rounded-lg border border-zinc-200 bg-white shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-zinc-50 border-b border-zinc-200 dark:bg-zinc-900/50 dark:border-zinc-800">
                    <tr>
                        <th class="px-6 py-4 text-left font-semibold text-zinc-600 dark:text-zinc-400">Pengguna</th>
                        <th class="px-6 py-4 text-center font-semibold text-zinc-600 dark:text-zinc-400">Peran</th>
                        <th class="px-6 py-4 text-center font-semibold text-zinc-600 dark:text-zinc-400">Status</th>
                        <th class="px-6 py-4 text-right font-semibold text-zinc-600 dark:text-zinc-400">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800/60">
                    @forelse($users as $user)
                        <tr class="transition-colors hover:bg-zinc-50 dark:hover:bg-zinc-800/50">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-4">
                                    <flux:avatar :name="$user->name" :initials="$user->initials()" size="sm" class="ring-2 ring-white dark:ring-zinc-900" />
                                    <div>
                                        <div class="font-medium text-zinc-900 dark:text-white">{{ $user->name }}
                                            @if($user->id === auth()->id()) <span class="ml-1.5 text-xs font-semibold text-zinc-400 bg-zinc-100 dark:bg-zinc-800 rounded px-1.5 py-0.5">Anda</span> @endif
                                        </div>
                                        <div class="text-sm text-zinc-500 mt-0.5">{{ $user->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="inline-flex items-center rounded-md px-2.5 py-1 text-xs font-semibold ring-1 ring-inset {{ $user->isAdmin() ? 'bg-indigo-50 text-indigo-700 ring-indigo-600/20 dark:bg-indigo-900/30 dark:text-indigo-400 dark:ring-indigo-900/50' : 'bg-blue-50 text-blue-700 ring-blue-600/20 dark:bg-blue-900/30 dark:text-blue-400 dark:ring-blue-900/50' }}">
                                    {{ ucfirst($user->role) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="inline-flex items-center rounded-md px-2.5 py-1 text-xs font-semibold ring-1 ring-inset {{ $user->is_active ? 'bg-emerald-50 text-emerald-700 ring-emerald-600/20 dark:bg-emerald-900/30 dark:text-emerald-400 dark:ring-emerald-900/50' : 'bg-zinc-50 text-zinc-600 ring-zinc-500/20 dark:bg-zinc-800/50 dark:text-zinc-400 dark:ring-zinc-700' }}">
                                    {{ $user->is_active ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <flux:button size="sm" variant="ghost" class="cursor-pointer h-8 w-8 px-0" icon="pencil" wire:click="edit({{ $user->id }})" />
                                    @if($user->id !== auth()->id())
                                        <flux:button size="sm" variant="ghost" class="cursor-pointer h-8 w-8 px-0 [&_svg]:text-red-600 hover:bg-red-50 hover:text-red-700 dark:hover:bg-red-900/20 dark:hover:text-red-400" icon="trash" wire:click="confirmDelete({{ $user->id }}, '{{ addslashes($user->name) }}')" />
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="py-10 text-center text-zinc-500">
                            <flux:icon name="users" class="mx-auto mb-3 h-8 w-8 opacity-40" />
                            Tidak ada pengguna ditemukan.
                        </td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="border-t border-zinc-200 bg-white p-4 dark:border-zinc-800 dark:bg-zinc-900">{{ $users->links() }}</div>
    </div>

    <flux:modal wire:model="showModal" class="max-w-md md:max-w-lg p-0 overflow-hidden bg-white dark:bg-zinc-900 rounded-xl shadow-xl">
        <div class="p-3">
            <header class="border-b border-zinc-100 dark:border-zinc-800 pb-4 mb-4">
                <h2 class="text-lg font-semibold tracking-tight text-zinc-900 dark:text-white">{{ $editingId ? 'Edit Pengguna' : 'Tambah Pengguna' }}</h2>
                <p class="text-sm text-zinc-500 mt-1">Lengkapi informasi pengguna di bawah ini.</p>
            </header>

            <div class="max-h-[60vh] overflow-y-auto pr-2 customized-scrollbar">
                <form wire:submit="save" id="userForm" class="space-y-4 px-3">
                    <flux:field>
                        <flux:label class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Nama Lengkap <span class="text-red-500">*</span></flux:label>
                        <flux:input class="h-10 mt-1 rounded-lg border-zinc-300 focus:border-green-500 focus:ring-green-500" wire:model="name" />
                        <flux:error name="name" class="mt-1 text-sm text-red-500" />
                    </flux:field>

                    <flux:field>
                        <flux:label class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Email <span class="text-red-500">*</span></flux:label>
                        <flux:input type="email" class="h-10 mt-1 rounded-lg border-zinc-300 focus:border-green-500 focus:ring-green-500" wire:model="email" />
                        <flux:error name="email" class="mt-1 text-sm text-red-500" />
                    </flux:field>
                    
                    <flux:field>
                        <flux:label class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Peran <span class="text-red-500">*</span></flux:label>
                        <flux:select class="h-10 mt-1 rounded-lg border-zinc-300 focus:border-green-500 focus:ring-green-500" wire:model.live="role">
                            <flux:select.option value="cashier">Kasir</flux:select.option>
                            <flux:select.option value="admin">Admin</flux:select.option>
                        </flux:select>
                        <flux:error name="role" class="mt-1 text-sm text-red-500" />
                    </flux:field>

                    <flux:field>
                        <flux:label class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Password <span class="text-red-500">{{ !$editingId ? '*' : '' }}</span></flux:label>
                        <flux:input type="password" class="h-10 mt-1 rounded-lg border-zinc-300 focus:border-green-500 focus:ring-green-500" wire:model="password" />
                        @if($editingId) <div class="text-xs text-zinc-500 mt-1">Kosongkan jika tidak ingin diubah</div> @endif
                        <flux:error name="password" class="mt-1 text-sm text-red-500" />
                    </flux:field>

                    @if($role === 'cashier')
                        <div class="mt-4 border-t border-zinc-100 dark:border-zinc-800 pt-4">
                            <flux:label class="text-sm font-medium mb-3 block text-zinc-700 dark:text-zinc-300">Hak Akses Modul</flux:label>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3 mt-2 cursor-pointer">
                                <flux:checkbox label="Dashboard" wire:model="permissions" value="dashboard" />
                                <flux:checkbox label="Kasir" wire:model="permissions" value="pos" />
                                <flux:checkbox label="Produk" wire:model="permissions" value="products" />
                                <flux:checkbox label="Kategori Produk" wire:model="permissions" value="categories" />
                                <flux:checkbox label="Stok Produk" wire:model="permissions" value="stock" />
                                <flux:checkbox label="Voucher" wire:model="permissions" value="vouchers" />
                                <flux:checkbox label="Laporan Penjualan" wire:model="permissions" value="reports" />
                                <flux:checkbox label="Kategori Pengeluaran" wire:model="permissions" value="expense_categories" />
                                <flux:checkbox label="Pengeluaran" wire:model="permissions" value="expenses" />
                                <flux:checkbox label="Laporan Pengeluaran" wire:model="permissions" value="expense_reports" />
                            </div>
                        </div>
                    @endif

                    @if($editingId !== auth()->id())
                    <flux:label class="text-sm font-medium mb-3 block text-zinc-700 dark:text-zinc-300">Status Akun</flux:label>
                        <div class="pt-2 cursor-pointer">
                            <flux:checkbox label="Aktif" wire:model="is_active" />
                        </div>
                    @endif
                </form>
            </div>

            <footer class="mt-6 flex justify-end gap-3 pt-4 border-t border-zinc-100 dark:border-zinc-800">
                <button type="button" class="cursor-pointer h-10 px-4 rounded-lg bg-zinc-100 text-zinc-700 hover:bg-zinc-200 dark:bg-zinc-800 dark:text-zinc-300 dark:hover:bg-zinc-700 font-medium text-sm transition-colors" wire:click="$set('showModal', false)">Batal</button>
                <button type="submit" form="userForm" class="cursor-pointer h-10 px-6 rounded-lg bg-green-600 hover:bg-green-700 text-white font-semibold text-sm transition-colors">{{ $editingId ? 'Perbarui' : 'Simpan' }}</button>
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
                    Apakah Anda yakin ingin menghapus <span class="font-medium text-gray-700 dark:text-gray-300">{{ $itemToDeleteName ?: 'pengguna ini' }}</span>?
                    <br>Tindakan ini tidak dapat dibatalkan.
                </p>
            </div>
            
            <div class="mt-6 flex justify-end gap-3 w-full">
                <button type="button" wire:click="$set('showDeleteModal', false)" class="cursor-pointer h-10 px-4 rounded-lg bg-gray-100 text-gray-700 hover:bg-gray-200 dark:bg-zinc-800 dark:text-gray-300 dark:hover:bg-zinc-700 font-medium text-sm transition-colors">
                    Batal
                </button>
                <button type="button" wire:click="processDelete" class="cursor-pointer h-10 px-4 rounded-lg bg-red-600 text-white hover:bg-red-700 disabled:opacity-50 disabled:cursor-not-allowed font-medium text-sm transition-colors" wire:loading.attr="disabled" wire:target="processDelete">
                    <span wire:loading.remove wire:target="processDelete">Hapus</span>
                    <span wire:loading wire:target="processDelete">Memproses...</span>
                </button>
            </div>
        </div>
    </flux:modal>
</div>
