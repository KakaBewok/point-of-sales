<div class="p-6">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-zinc-900 dark:text-white">Store Subscriptions Management</h1>
            <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">View and manually update subscription statuses for all stores.</p>
        </div>
        <div class="w-72">
            <input type="text" wire:model.live.debounce.500ms="search" placeholder="Search by store name..." 
                   class="w-full rounded-md border border-zinc-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 dark:border-zinc-700 dark:bg-zinc-900 dark:text-white">
        </div>
    </div>

    @if (session()->has('success'))
        <div class="mb-4 rounded-md bg-emerald-50 p-4 text-emerald-700 border border-emerald-200 dark:bg-emerald-900/30 dark:border-emerald-900/50 dark:text-emerald-400">
            {{ session('success') }}
        </div>
    @endif

    <div class="overflow-hidden rounded-lg border border-zinc-200 bg-white shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm whitespace-nowrap">
                <thead class="border-b border-zinc-200 bg-zinc-50 text-zinc-500 dark:border-zinc-800 dark:bg-zinc-800/50 dark:text-zinc-400">
                    <tr>
                        <th class="px-6 py-3 font-medium">Store Name</th>
                        <th class="px-6 py-3 font-medium">Phone</th>
                        <th class="px-6 py-3 font-medium">Plan</th>
                        <th class="px-6 py-3 font-medium">Status</th>
                        <th class="px-6 py-3 font-medium">Trial / Sub Ends</th>
                        <th class="px-6 py-3 font-medium text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-200 dark:divide-zinc-800">
                    @forelse ($stores as $store)
                        <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800/50">
                            <td class="px-6 py-4">
                                <div class="font-medium text-zinc-900 dark:text-white">{{ $store->name }}</div>
                                <div class="text-xs text-zinc-500">{{ $store->address ?? '' }}</div>
                            </td>
                            <td class="px-6 py-4 text-zinc-600 dark:text-zinc-400">{{ $store->phone ?? '-' }}</td>
                            <td class="px-6 py-4">
                                @if($store->subscription_plan)
                                    <span class="inline-flex rounded-full bg-blue-50 px-2 py-1 text-xs font-semibold text-blue-700 dark:bg-blue-900/30 dark:text-blue-400">
                                        {{ ucfirst($store->subscription_plan) }}
                                    </span>
                                @else
                                    <span class="text-zinc-400">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                @php
                                    $statusColor = match($store->subscription_status) {
                                        'active' => 'bg-emerald-50 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400',
                                        'trial' => 'bg-yellow-50 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400',
                                        'expired', 'suspended', 'cancelled' => 'bg-red-50 text-red-700 dark:bg-red-900/30 dark:text-red-400',
                                        default => 'bg-zinc-50 text-zinc-700 dark:bg-zinc-900/30 dark:text-zinc-400',
                                    };
                                @endphp
                                <span class="inline-flex rounded-md px-2 py-1 text-xs font-medium {{ $statusColor }}">
                                    {{ ucfirst($store->subscription_status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-zinc-600 dark:text-zinc-400">
                                @if($store->subscription_status === 'trial')
                                    {{ $store->trial_ends_at ? $store->trial_ends_at->format('d M Y') : '-' }} (Trial)
                                @else
                                    {{ $store->subscription_ends_at ? $store->subscription_ends_at->format('d M Y') : '-' }}
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right">
                                <button wire:click="editStore({{ $store->id }})" class="text-sm font-medium text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">
                                    Edit
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-zinc-500 dark:text-zinc-400">
                                No stores found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="border-t border-zinc-200 px-6 py-4 dark:border-zinc-800">
            {{ $stores->links() }}
        </div>
    </div>

    <!-- Edit Modal -->
    @if ($editStoreId)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-0">
            <div class="fixed inset-0 bg-zinc-900/80 backdrop-blur-sm transition-opacity" wire:click="cancelEdit"></div>
            
            <div class="relative w-full max-w-lg transform rounded-2xl bg-white p-6 shadow-xl transition-all dark:bg-zinc-900 sm:my-8 border border-zinc-200 dark:border-zinc-800">
                <div class="mb-5 flex items-center justify-between">
                    <h3 class="text-lg font-bold text-zinc-900 dark:text-white">Edit Subscription</h3>
                    <button wire:click="cancelEdit" class="text-zinc-400 hover:text-zinc-500 dark:hover:text-zinc-300">
                        <flux:icon name="x-mark" class="h-5 w-5" />
                    </button>
                </div>

                <form wire:submit.prevent="saveSubscription" class="space-y-4">
                    <div>
                        <label class="mb-1 block text-sm font-medium text-zinc-700 dark:text-zinc-300">Plan</label>
                        <select wire:model="editSubscriptionPlan" class="w-full rounded-md border border-zinc-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 dark:border-zinc-700 dark:bg-zinc-900 dark:text-white">
                            <option value="">None</option>
                            <option value="monthly">Monthly</option>
                            <option value="yearly">Yearly</option>
                        </select>
                        @error('editSubscriptionPlan') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="mb-1 block text-sm font-medium text-zinc-700 dark:text-zinc-300">Status</label>
                        <select wire:model="editSubscriptionStatus" class="w-full rounded-md border border-zinc-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 dark:border-zinc-700 dark:bg-zinc-900 dark:text-white">
                            <option value="active">Active</option>
                            <option value="trial">Trial</option>
                            <option value="expired">Expired</option>
                            <option value="suspended">Suspended</option>
                            <option value="cancelled">Cancelled</option>
                        </select>
                        @error('editSubscriptionStatus') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="mb-1 block text-sm font-medium text-zinc-700 dark:text-zinc-300">Starts At</label>
                            <input type="date" wire:model="editSubscriptionStartsAt" class="w-full rounded-md border border-zinc-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 dark:border-zinc-700 dark:bg-zinc-900 dark:text-white">
                            @error('editSubscriptionStartsAt') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="mb-1 block text-sm font-medium text-zinc-700 dark:text-zinc-300">Ends At</label>
                            <input type="date" wire:model="editSubscriptionEndsAt" class="w-full rounded-md border border-zinc-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 dark:border-zinc-700 dark:bg-zinc-900 dark:text-white">
                            @error('editSubscriptionEndsAt') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="mt-6 flex justify-end gap-3">
                        <button type="button" wire:click="cancelEdit" class="rounded-lg px-4 py-2 bg-white border border-zinc-300 text-sm font-medium text-zinc-700 shadow-sm hover:bg-zinc-50 dark:bg-zinc-900 dark:border-zinc-700 dark:text-zinc-300 dark:hover:bg-zinc-800">
                            Cancel
                        </button>
                        <button type="submit" class="rounded-lg bg-zinc-900 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-zinc-800 dark:bg-white dark:text-zinc-900 dark:hover:bg-zinc-200">
                            Save Changes
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
