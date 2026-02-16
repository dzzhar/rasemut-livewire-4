<?php

use App\Models\Permission;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Validate;
use Livewire\Component;

new class extends Component {
    public $showForm = false;
    public int $employeeId = 2;

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
    #[Validate('required|min:10')]
    public $description;

    public function save()
    {
        $this->validate();

        // cek apakah telah izin hari ini
        $alreadyExists = Permission::where('employee_id', $this->employeeId)->whereDate('permission_date', today())->exists();

        if ($alreadyExists) {
            // send toast
            return;
        }

        // jika belum izin hari ini
        DB::transaction(function () {
            Permission::create([
                'employee_id' => $this->employeeId,
                'permission_date' => now(),
                'permission_type' => $this->permission_type,
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
                Formulir Perizinan
            </flux:text>

            <flux:button size="sm" icon="chevron-down" variant="ghost" />
        </flux:fieldset>
        <flux:separator variant="subtle" class="mt-6" wire:show="showForm" x-transition.duration.500ms />
    </div>

    <form class="space-y-6 mt-4" wire:show="showForm" x-transition.duration.500ms wire:submit.prevent="save">
        <flux:field>
            <flux:label>Jenis Izin</flux:label>
            <flux:select wire:model="permission_type" placeholder="Pilih jenis izin...">
                @foreach ($this->types() as $label)
                    <flux:select.option>{{ $label }}</flux:select.option>
                @endforeach
            </flux:select>
            <flux:error name="permission_type" />
        </flux:field>

        <flux:field>
            <flux:textarea wire:model="description" label="Keterangan" placeholder="Keterangan izin anda..."
                resize="none" />
        </flux:field>

        <flux:button variant="primary" color="blue" class="w-full" type="submit">Kirim
        </flux:button>
    </form>
</flux:card>
