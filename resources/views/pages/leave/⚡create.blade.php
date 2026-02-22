<?php

use App\Models\Leave;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Validate;
use Livewire\Component;

new class extends Component {
    public $showForm = false;

    #[Validate('required|after_or_equal:today')]
    public $start_date;
    #[Validate('required|after:start_date')]
    public $end_date;
    #[Validate('required|min:10')]
    public $description;

    public function save()
    {
        $this->validate();

        $employee = Auth::user()?->employee;
        if (!$employee) {
            return collect();
        }

        // cek apakah cuti sudah dibooking
        $alreadyExists = Leave::whereBelongsTo($employee)
            ->where(function ($query) {
                $query->where('start_date', '<=', $this->end_date)->where('end_date', '>=', $this->start_date);
            })
            ->exists();

        if ($alreadyExists) {
            // send toast
            return;
        }

        // jika belum, simpan
        DB::transaction(function () {
            Leave::create([
                'employee_id' => Auth::user()->employee->id,
                'request_date' => now(),
                'leave_code' => 'Cuti ' . '#' . now()->format('dMyHis'),
                'start_date' => $this->start_date,
                'end_date' => $this->end_date,
                'description' => $this->description,
            ]);
        });

        $this->reset();
        $this->dispatch('refresh-history');
    }
};
?>


<flux:card size="md" class="bg-white dark:bg-zinc-900" x-cloak>
    <div x-on:click="$wire.showForm = !$wire.showForm" class="cursor-pointer">
        <flux:fieldset class="flex items-center justify-between">
            <flux:text size="xl" level="2" class="text-zinc-800 dark:text-white font-medium">
                Formulir Pengajuan Cuti
            </flux:text>

            <flux:button size="sm" icon="chevron-down" variant="ghost" />
        </flux:fieldset>
        <flux:separator variant="subtle" class="mt-6" wire:show="showForm" x-transition.duration.500ms />
    </div>

    <form class="pt-6 space-y-6" wire:show="showForm" x-transition.duration.500ms wire:submit.prevent="save">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <flux:input label="Tanggal Mulai" wire:model="start_date" type="date" x-on:click="$el.showPicker()"
                icon:trailing="calendar" />
            <flux:input label="Tanggal Selesai" wire:model="end_date" type="date" x-on:click="$el.showPicker()"
                icon:trailing="calendar" />
        </div>

        <flux:textarea wire:model="description" label="Keterangan" placeholder="Keterangan izin anda..."
            resize="none" />

        <flux:button variant="primary" color="blue" class="w-full" type="submit">Kirim
        </flux:button>
    </form>

</flux:card>
