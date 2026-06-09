<?php

use App\Models\Leave;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Services\CheckerService;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Flux\Flux;
use Carbon\Carbon;

new class extends Component {
    public $showForm = true;
    public $leave_remaining;

    #[Validate('required|after_or_equal:today')]
    public $start_date;
    #[Validate('required|after:start_date')]
    public $end_date;
    #[Validate('required')]
    public $description;

    public function mount()
    {
        $this->leave_remaining = Auth::user()->employee->leave_remaining ?? 0;
    }

    public function save()
    {
        $this->validate();

        $employee = Auth::user()?->employee->id;
        $checker = app(CheckerService::class)->setEmployee($employee);

        // cek apakah ada cuti di periode ini
        if ($checker->hasLeaveInRange($this->start_date, $this->end_date)) {
            Flux::toast(heading: 'Gagal Mengajukan Cuti', text: 'Anda sudah atau sedang mengajukan cuti pada periode ini, sehingga tidak dapat mengajukan cuti.', variant: 'danger');
            return;
        }

        // cek apakah telah melakukan izin hari ini
        if ($checker->hasPermissionInRange($this->start_date, $this->end_date)) {
            Flux::toast(heading: 'Gagal Mengajukan Cuti', text: 'Anda telah mengajukan izin hari ini. Jika terjadi kesalahan, silakan hubungi Admin.', variant: 'warning');
            return;
        }

        // cek apakah telah melakukan presensi hari ini
        if ($checker->hasAttendanceInRange($this->start_date, $this->end_date)) {
            Flux::toast(heading: 'Gagal Mengajukan Cuti', text: 'Anda telah melakukan presensi hari ini, sehingga tidak dapat mengajukan cuti.', variant: 'warning');
            return;
        }

        // jika belum, simpan
        DB::transaction(function () use ($employee) {
            Leave::create([
                'employee_id' => $employee,
                'request_date' => now(),
                'leave_code' => 'Pengajuan_' . uniqid(),
                'start_date' => $this->start_date,
                'end_date' => $this->end_date,
                'description' => $this->description,
            ]);
        });

        Flux::toast(heading: 'Cuti Diajukan!', text: 'Pengajuan cuti Anda berhasil dilakukan. Silakan menunggu konfirmasi dari Admin.', variant: 'success', duration: 3000);

        $this->reset(['start_date', 'end_date', 'description']);
        $this->dispatch('refresh-history');
    }
};
?>


<div>
    <flux:card size="md" class="bg-white dark:bg-zinc-900" x-cloak>
        <div x-on:click="$wire.showForm = !$wire.showForm" class="cursor-pointer">
            <flux:fieldset class="flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <flux:heading size="lg" class="font-semibold">Formulir Pengajuan Cuti</flux:heading>
                    <flux:tooltip content="Sisa kuota cuti Anda">
                        <flux:badge color="yellow" size="sm">{{ $this->leave_remaining }}</flux:badge>
                    </flux:tooltip>
                </div>

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

            <flux:textarea wire:model="description" label="Keterangan" placeholder="Keterangan cuti anda..."
                resize="none" />

            <flux:button variant="primary" color="blue" class="w-full" type="submit">Kirim
            </flux:button>
        </form>
    </flux:card>
</div>
