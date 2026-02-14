<?php

use Livewire\Component;

new class extends Component {
    public $title = null;
    public $time = null;
    public $status = null;
};
?>


<flux:card size="md"
    class="flex items-center justify-between bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 hover:border-emerald-300 dark:hover:border-emerald-500/40 ">

    <div class="flex items-center gap-4">
        <div class="flex items-center justify-center w-14 h-14 rounded-xl bg-emerald-500/10 dark:bg-emerald-500/20">
            <flux:icon name="check-circle" class="size-7 text-emerald-600 dark:text-emerald-400" />
        </div>

        <div>
            <flux:text>{{ $title }}</flux:text>
            <flux:heading size="xl" level="1" class="mt-1 font-semibold">
                {{ filled($time) ? "$time WIB" : '--:--:--' }}
            </flux:heading>
        </div>
    </div>

    <div class="hidden md:block">
        <flux:badge rounded color="red" size="sm">
            {{ $status ?? 'belum absen' }}
        </flux:badge>
    </div>
</flux:card>
