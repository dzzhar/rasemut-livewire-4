<?php

use App\Models\Permission;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Validate;
use App\Services\CheckerService;
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

        $employee = Auth::user()?->employee->id;
        $checker = app(CheckerService::class)->setEmployee($employee);

        // cek apakah telah mengajukan izin hari ini
        if ($checker->hasPermissionToday(now())) {
            $this->dispatch('show-feedback', title: 'Gagal Mengajukan Izin', message: 'Anda telah mengajukan izin hari ini. Jika terjadi kesalahan, silakan hubungi Admin.', type: 'danger');
            return;
        }

        // cek apakah ada cuti di periode ini
        if ($checker->hasLeaveToday(now())) {
            $this->dispatch('show-feedback', title: 'Gagal Mengajukan Izin', message: 'Anda sedang dalam periode cuti hari ini, sehingga tidak dapat melakukan presensi.', type: 'warning');
            return;
        }

        // cek apakah telah melakukan presensi hari ini
        if ($checker->hasAttendanceToday(now())) {
            $this->dispatch('show-feedback', title: 'Gagal Mengajukan Izin', message: 'Anda telah melakukan presensi hari ini, sehingga tidak dapat mengajukan izin.', type: 'warning');
            return;
        }

        // jika belum izin hari ini
        DB::transaction(function () use ($employee) {
            Permission::create([
                'employee_id' => $employee,
                'permission_date' => now(),
                'permission_type' => $this->permission_type,
                'description' => $this->description,
            ]);
        });

        $this->dispatch('show-feedback', title: 'Izin Diajukan!', message: 'Pengajuan izin Anda hari ini berhasil dilakukan.');

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
