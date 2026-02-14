<flux:sidebar sticky collapsible="mobile"
    class="lg:hidden bg-zinc-50 dark:bg-zinc-900 border-r border-zinc-200 dark:border-zinc-700">
    <flux:sidebar.header>
        <flux:sidebar.brand logo="{{ asset('images/logo.svg') }}" name="SISemut" />

        <flux:sidebar.collapse
            class="in-data-flux-sidebar-on-desktop:not-in-data-flux-sidebar-collapsed-desktop:-mr-2" />
    </flux:sidebar.header>

    <flux:sidebar.nav>
        @foreach ($menuItems as $item)
            <flux:sidebar.item icon="{{ $item['icon'] }}" href="{{ $item['href'] }}" wire:navigate
                :current="request()->is($item['match'])">{{ $item['label'] }}
            </flux:sidebar.item>
        @endforeach
    </flux:sidebar.nav>
</flux:sidebar>
