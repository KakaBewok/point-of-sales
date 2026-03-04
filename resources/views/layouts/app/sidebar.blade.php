<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-white dark:bg-zinc-800">
        <flux:sidebar sticky collapsible="mobile" class="z-50 border-e border-zinc-200 bg-zinc-100 dark:border-zinc-700 dark:bg-zinc-950">
            <flux:sidebar.header>
                <a href="{{ route('dashboard') }}" wire:navigate class="flex items-center gap-2 px-1">
                    @php $storeLogo = App\Models\Setting::get('store_logo', ''); @endphp
                    @if($storeLogo)
                        <img src="{{ Illuminate\Support\Facades\Storage::url($storeLogo) }}" class="h-8 w-8 rounded-lg object-contain" alt="Logo" />
                    @else
                        <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-indigo-600 text-white font-bold text-sm">
                            POS
                        </div>
                    @endif
                    <span class="text-sm font-semibold text-zinc-800 dark:text-white truncate">{{ App\Models\Setting::getStoreName() }}</span>
                </a>
                <flux:sidebar.collapse class="lg:hidden" />
            </flux:sidebar.header>

            <flux:sidebar.nav>
                <flux:navlist variant="outline">
                    {{-- Main Navigation --}}
                    @if(auth()->user()->hasPermission('dashboard') || auth()->user()->hasPermission('pos'))
                    <flux:navlist.group expandable :heading="__('Utama')" :expanded="request()->routeIs('dashboard') || request()->routeIs('pos.*')">
                        @if(auth()->user()->hasPermission('dashboard'))
                            <flux:navlist.item icon="home" :href="route('dashboard')" :current="request()->routeIs('dashboard')" wire:navigate>
                                {{ __('Dashboard') }}
                            </flux:navlist.item>
                        @endif

                        @if(auth()->user()->hasPermission('pos'))
                            <flux:navlist.item icon="shopping-cart" :href="route('pos.index')" :current="request()->routeIs('pos.*')" wire:navigate>
                                {{ __('Kasir (POS)') }}
                            </flux:navlist.item>
                        @endif
                    </flux:navlist.group>
                    @endif

                    {{-- Management --}}
                    @if(auth()->user()->hasPermission('products') || auth()->user()->hasPermission('categories') || auth()->user()->hasPermission('stock') || auth()->user()->hasPermission('vouchers'))
                    <flux:navlist.group expandable :heading="__('Manajemen')" :expanded="request()->routeIs('products.*') || request()->routeIs('categories.*') || request()->routeIs('stock.*') || request()->routeIs('vouchers.*')">
                        @if(auth()->user()->hasPermission('products'))
                            <flux:navlist.item icon="cube" :href="route('products.index')" :current="request()->routeIs('products.*')" wire:navigate>
                                {{ __('Produk') }}
                            </flux:navlist.item>
                        @endif

                        @if(auth()->user()->hasPermission('categories'))
                            <flux:navlist.item icon="tag" :href="route('categories.index')" :current="request()->routeIs('categories.*')" wire:navigate>
                                {{ __('Kategori') }}
                            </flux:navlist.item>
                        @endif

                        @if(auth()->user()->hasPermission('stock'))
                            <flux:navlist.item icon="archive-box" :href="route('stock.index')" :current="request()->routeIs('stock.*')" wire:navigate>
                                {{ __('Stok') }}
                            </flux:navlist.item>
                        @endif

                        @if(auth()->user()->hasPermission('vouchers'))
                            <flux:navlist.item icon="ticket" :href="route('vouchers.index')" :current="request()->routeIs('vouchers.*')" wire:navigate>
                                {{ __('Voucher') }}
                            </flux:navlist.item>
                        @endif
                    </flux:navlist.group>
                    @endif

                    {{-- Reports --}}
                    @if(auth()->user()->hasPermission('reports'))
                    <flux:navlist.group expandable :heading="__('Laporan')" :expanded="request()->routeIs('reports.*')">
                        <flux:navlist.item icon="chart-bar" :href="route('reports.index')" :current="request()->routeIs('reports.*')" wire:navigate>
                            {{ __('Laporan Penjualan') }}
                        </flux:navlist.item>
                    </flux:navlist.group>
                    @endif

                    {{-- Admin and OwnerOnly --}}
                    @if(auth()->user()->canAccessAdminMenu())
                    <flux:navlist.group expandable :heading="__('Sistem')" :expanded="request()->routeIs('admin.*')">
                        <flux:navlist.item icon="users" :href="route('admin.users.index')" :current="request()->routeIs('admin.users.*')" wire:navigate>
                            {{ __('Pengguna') }}
                        </flux:navlist.item>

                        <flux:navlist.item icon="cog-6-tooth" :href="route('admin.settings.index')" :current="request()->routeIs('admin.settings.*')" wire:navigate>
                            {{ __('Pengaturan') }}
                        </flux:navlist.item>
                    </flux:navlist.group>
                    @endif
                </flux:navlist>
            </flux:sidebar.nav>

            <flux:spacer />

            <x-desktop-user-menu class="hidden lg:block" :name="auth()->user()->name" />
        </flux:sidebar>

        {{-- Mobile Header --}}
        <flux:header class="lg:hidden">
            <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left" />

            <flux:spacer />

            <flux:dropdown position="top" align="end">
                <flux:profile
                    :initials="auth()->user()->initials()"
                    icon-trailing="chevron-down"
                />
                <flux:menu>
                    <flux:menu.radio.group>
                        <div class="p-0 text-sm font-normal">
                            <div class="flex items-center gap-2 px-1 py-1.5 text-start text-sm">
                                <flux:avatar :name="auth()->user()->name" :initials="auth()->user()->initials()" />
                                <div class="grid flex-1 text-start text-sm leading-tight">
                                    <flux:heading class="truncate">{{ auth()->user()->name }}</flux:heading>
                                    <flux:text class="truncate">{{ auth()->user()->email }}</flux:text>
                                </div>
                            </div>
                        </div>
                    </flux:menu.radio.group>

                    <flux:menu.separator />

                    <flux:menu.radio.group>
                        <flux:menu.item :href="route('profile.edit')" icon="cog" wire:navigate>
                            {{ __('Settings') }}
                        </flux:menu.item>
                    </flux:menu.radio.group>

                    <flux:menu.separator />

                    <form method="POST" action="{{ route('logout') }}" class="w-full">
                        @csrf
                        <flux:menu.item as="button" type="submit" icon="arrow-right-start-on-rectangle" class="w-full cursor-pointer">
                            {{ __('Log Out') }}
                        </flux:menu.item>
                    </form>
                </flux:menu>
            </flux:dropdown>
        </flux:header>

        {{ $slot }}

        @fluxScripts
    </body>
</html>
