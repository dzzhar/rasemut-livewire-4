<?php

use App\Models\Permission;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Validate;
use Livewire\Component;

new class extends Component {
    public $showForm = false;
    public bool $isWorkingDay = true;

    public function mount()
    {
        $this->isWorkingDay = now()->isWeekday();
    }

    public function types()
    {
        return [
            'sakit' => 'Sakit',
            'izin' => 'Izin',
            'lainnya' => 'Lainnya',
        ];
    }

    #[Validate('required')]
    public $permission_type = '';
    #[Validate('required')]
    public $description;

    public function save()
    {
        $this->validate();

        $employee = Auth::user()?->employee;

        // cek apakah telah izin hari ini
        $alreadyExists = Permission::whereBelongsTo($employee)->whereDate('permission_date', today())->exists();

        if ($alreadyExists) {
            $this->dispatch('show-feedback', title: 'Izin Sudah Diajukan', message: 'Anda sudah mengajukan izin untuk hari ini. Apabila terjadi kesalahan, silakan hubungi Operator.', type: 'warning');
            return;
        }

        // jika belum izin hari ini
        DB::transaction(function () {
            Permission::create([
                'employee_id' => $employee->id,
                'permission_date' => now(),
                'permission_type' => $this->permission_type,
                'description' => $this->description,
            ]);
        });

        $this->dispatch('show-feedback', title: 'Izin Berhasil Diajukan', message: 'Izin Anda hari ini telah berhasil diajukan.');

        $this->reset();
        $this->dispatch('refresh-history');
    }
};
?>


<div>
    @if ($isWorkingDay)
        <flux:card size="md" class="bg-white dark:bg-zinc-900" x-cloak>
            <div x-on:click="$wire.showForm = !$wire.showForm" class="cursor-pointer">
                <flux:fieldset class="flex items-center justify-between">
                    <flux:text size="xl" level="2" class="text-zinc-800 dark:text-white font-medium">
                        Formulir Perizinan
                    </flux:text>

                    <flux:button size="sm" icon="chevron-down" variant="ghost" />
                </flux:fieldset>
                <flux:separator variant="subtle" class="mt-6" wire:show="showForm" x-transition.duration.500ms />
            </div>

            <form class="space-y-6 mt-4" wire:show="showForm" x-transition.duration.500ms wire:submit.prevent="save">
                <flux:select label="Jenis Izin" wire:model="permission_type" placeholder="Pilih jenis izin...">
                    @foreach ($this->types() as $label)
                        <flux:select.option>{{ $label }}</flux:select.option>
                    @endforeach
                </flux:select>

                <flux:textarea wire:model="description" label="Keterangan" placeholder="Keterangan izin anda..."
                    resize="none" />

                <flux:button variant="primary" color="blue" class="w-full" type="submit">Kirim
                </flux:button>
            </form>
        </flux:card>
    @endif
</div>
