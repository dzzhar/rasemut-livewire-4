<flux:card size="sm"
    class="flex items-center justify-between bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800">

    <div class="flex items-center gap-4">
        <flux:badge :color="$status?->badgeColor()" class="flex items-center justify-center w-14 h-14 rounded-xl"
            size="sm">
            <flux:icon :name="$status?->badgeIcon() ?? 'finger-print'" class="size-7 {{ $status?->badgeAccent() }}" />
        </flux:badge>

        <div>
            <flux:text>{{ $title }}</flux:text>
            <flux:heading size="xl" level="1" class="mt-1 font-semibold">
                {{ filled($time) ? "$time WIB" : '--:--:--' }}
            </flux:heading>
        </div>
    </div>

    <div class="hidden md:block">
        <flux:badge rounded :color="$status?->badgeColor()" size="sm">{{ $status ?? 'belum absen' }}
        </flux:badge>
    </div>
</flux:card>
