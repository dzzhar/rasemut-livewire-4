@placeholder
    <flux:skeleton class="rounded-lg w-full h-[calc(100vh-160px)]" animate="shimmer" />
@endplaceholder

<flux:card size="md"
    class="flex flex-col max-h-[calc(100vh-160px)] md:max-h-[calc(100vh-120px)] bg-white dark:bg-zinc-900 space-y-4 mb-0!">

    {{-- header title n datepicker --}}
    <div>
        <flux:fieldset class="flex items-center justify-between gap-4">
            <flux:text size="xl" level="2" class="text-zinc-800 dark:text-white font-medium">
                {{ $headerTitle }}
            </flux:text>

            <flux:tooltip content="Filter berdasarkan tanggal">
                <flux:input type="date" class="w-fit!" size="sm" icon:trailing="calendar"
                    x-on:click="$el.showPicker()" wire:model.live="filterDate" :min="$this->min"
                    :max="$this->max" />
            </flux:tooltip>
        </flux:fieldset>

        <flux:separator variant="subtle" class="mt-6" />
    </div>

    {{-- history container --}}
    <div class="flex-1 overflow-y-auto no-scrollbar">
        @forelse ($this->history as $item)
            <div wire:click="showModal({{ $item->id }})"
                class="bg-white dark:bg-zinc-900 border-0 {{ !$loop->last ? 'border-b border-zinc-100 dark:border-zinc-800' : '' }} rounded-none py-6 first:pt-0 last:pb-0 cursor-pointer">
                <flux:heading size="lg" level="3"
                    class="flex items-center gap-2 font-medium text-zinc-700 dark:text-zinc-300 capitalize">
                    {{-- casts in model --}}
                    {{ $item->history_type }}

                    <flux:tooltip content="Detail" class="ml-auto">
                        <flux:icon.information-circle variant="outline"
                            class="text-zinc-400 dark:text-zinc-500 group-hover:text-zinc-600 dark:group-hover:text-zinc-300 transition size-4" />
                    </flux:tooltip>
                </flux:heading>

                <div class="flex items-end justify-between mt-0 md:mt-2 gap-10">
                    <div class="min-w-0">
                        <flux:text class="text-zinc-900 dark:text-zinc-100 font-medium">
                            {{ $item->history_date ? $item->history_date->translatedFormat('l, d F Y') : '-' }}
                        </flux:text>

                        <flux:text class="text-zinc-500 dark:text-zinc-400 text-sm hidden md:block truncate">
                            Keterangan: {{ ucfirst($item->description ?? '-') }}
                        </flux:text>
                    </div>

                    <div class="hidden md:block">
                        <flux:badge rounded size="sm"
                            color="{{ $item->status instanceof \UnitEnum ? $item->status->badgeColor() : 'green' }}">
                            {{ $item->status instanceof \UnitEnum ? $item->status->badgeLabel() : $item->status }}
                        </flux:badge>
                    </div>
                </div>
            </div>
        @empty
            <div class="flex flex-col items-center justify-center text-center py-12">
                <flux:heading size="lg" level="3"
                    class="font-medium text-zinc-700 dark:text-zinc-300 capitalize">
                    Tidak ada riwayat
                </flux:heading>
                <flux:text class="mt-2 text-zinc-500 dark:text-zinc-400 max-w-sm">
                    Belum terdapat aktivitas yang tersedia pada periode ini.
                </flux:text>
            </div>
        @endforelse

        @if ($this->history->count() < $this->totalHistory)
            <div class="flex justify-center mt-6">
                <flux:button size="sm" wire:click="loadMore">Muat Lebih Banyak...</flux:button>
            </div>
        @endif
    </div>
</flux:card>
