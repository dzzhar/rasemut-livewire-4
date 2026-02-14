<flux:header container sticky class="bg-zinc-50 dark:bg-zinc-900 border-b border-zinc-200 dark:border-zinc-700">
    <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left" />

    <flux:brand logo="{{ asset('images/logo.svg') }}" name="SISemut" alt="Logo RA Semut" class="max-lg:hidden" />

    <flux:navbar class="-mb-px max-lg:hidden ml-10">
        @foreach ($menuItems as $item)
            <flux:navbar.item icon="{{ $item['icon'] }}" href="{{ $item['href'] }}" wire:navigate
                :current="request()->is($item['match'])">
                {{ $item['label'] }}
            </flux:navbar.item>
        @endforeach
    </flux:navbar>

    <flux:spacer />

    <flux:navbar class="me-4">
        <flux:button x-data x-on:click="$flux.dark = ! $flux.dark" icon="moon" variant="subtle"
            aria-label="Toggle dark mode" />
    </flux:navbar>

    <flux:dropdown align="end">
        <flux:profile avatar:name="Zharifah Dzikra Purnomo" />

        <flux:navmenu class="max-w-48">
            <div class="px-2 py-1.5">
                <flux:text size="sm">Signed in as</flux:text>
                <flux:heading class="mt-1! truncate">caleb@example.com</flux:heading>
            </div>

            <flux:navmenu.separator />
            <flux:navmenu.item href="/account" icon="user" class="text-zinc-800 dark:text-white">Account
            </flux:navmenu.item>

            <flux:navmenu.separator />
            <flux:navmenu.item href="/logout" icon="arrow-right-start-on-rectangle"
                class="text-zinc-800 dark:text-white">Logout</flux:navmenu.item>
        </flux:navmenu>
    </flux:dropdown>
</flux:header>
