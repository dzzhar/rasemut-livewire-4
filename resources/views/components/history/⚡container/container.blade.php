@placeholder
    <flux:skeleton class="rounded-lg w-full h-[calc(100vh-160px)]" animate="shimmer" />
@endplaceholder

<flux:card size="md"
    class="flex flex-col max-h-[calc(100vh-160px)] md:max-h-[calc(100vh-120px)] bg-white dark:bg-zinc-900 space-y-4">
    <div>
        <flux:fieldset class="flex items-center justify-between gap-4">
            <flux:text size="xl" level="2" class="text-zinc-800 dark:text-white font-medium">
                {{ $headerTitle }}
            </flux:text>

            <flux:input type="date" class="w-fit!" size="sm" icon:trailing="calendar"
                x-on:click="$el.showPicker()" wire:model.live="filterDate" :min="$this->min"
                :max="$this->max" />
        </flux:fieldset>
        <flux:separator variant="subtle" class="mt-6" />
    </div>

    <div class="flex-1 overflow-y-auto no-scrollbar">
        @forelse ($this->history as $item)
            <flux:modal.trigger wire:click="showDetail({{ $item->id }})" class="cursor-pointer">
                <div
                    class="bg-white dark:bg-zinc-900 border-0 border-b border-zinc-100 dark:border-zinc-800 rounded-none py-6 last:border-0">
                    <flux:heading size="lg" level="3"
                        class="flex items-center gap-2 font-medium text-zinc-700 dark:text-zinc-300 capitalize">
                        {{ $item->history_type }}
                        <flux:tooltip content="Detail" class="ml-auto">
                            <flux:icon.information-circle variant="outline"
                                class="text-zinc-400 dark:text-zinc-500 group-hover:text-zinc-600 dark:group-hover:text-zinc-300 transition size-4" />
                        </flux:tooltip>
                    </flux:heading>

                    <div class="flex items-end justify-between mt-0 md:mt-4">
                        <div>
                            <flux:text class="text-zinc-900 dark:text-zinc-100 font-medium">
                                {{ $item->history_date ? $item->history_date->translatedFormat('l, d F Y • H:i:s') . ' WIB' : '-' }}
                            </flux:text>

                            <flux:text class="mt-0.5 text-zinc-500 dark:text-zinc-400 text-sm hidden md:block">
                                Keterangan: {{ $item->description ?? '-' }}
                            </flux:text>
                        </div>

                        <div class="hidden md:block">
                            <flux:badge rounded size="sm" :color="$item->badge_color"> {{ $item->status }}
                            </flux:badge>
                        </div>
                    </div>
                </div>
            </flux:modal.trigger>
        @empty
            <div class="flex flex-col items-center justify-center text-center py-12">
                <flux:icon.face-frown class="size-12 text-zinc-400 mb-4" />
                <flux:heading size="lg" level="3"
                    class="font-medium text-zinc-700 dark:text-zinc-300 capitalize">
                    Tidak ada riwayat
                </flux:heading>
                <flux:text class="mt-2 text-zinc-500 dark:text-zinc-400 max-w-sm">
                    Belum terdapat aktivitas yang tersedia pada periode ini.
                </flux:text>
            </div>
        @endforelse
    </div>
</flux:card>
