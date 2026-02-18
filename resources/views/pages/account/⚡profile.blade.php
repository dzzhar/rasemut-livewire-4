<?php

use App\Models\Employee;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Title;
use Livewire\Attributes\Validate;
use Livewire\Component;

new #[Title('Akun')] class extends Component {
    #[Validate('required|string|max:50')]
    public $fullname;
    #[Validate('required|string|max:50')]
    public $employee_code;
    public $position;
    public $status;

    public function saveProfile()
    {
        $this->validate();

        $user = Auth::user();

        if (!$user) {
            return;
        }

        $employee = $user->employee;

        if ($employee) {
            $employee->update([
                'fullname' => ucwords(strtolower($this->fullname)),
                'employee_code' => $this->employee_code,
            ]);
        } else {
            $user->employee()->create([
                'fullname' => ucwords(strtolower($this->fullname)),
                'employee_code' => $this->employee_code,
            ]);
        }

        // otomatis reload halaman SPA
        $this->redirect(request()->header('Referer'), navigate: true);
    }

    public function mount()
    {
        $employee = Auth::user()?->employee;

        if ($employee) {
            $this->fullname = $employee->fullname;
            $this->employee_code = $employee->employee_code;
            $this->position = $employee->position->position_name;
            $this->status = $employee->user->is_active ? 'Nonaktif' : 'Aktif';
        }
    }
};
?>


<div>
    <flux:heading size="xl">Pengaturan Profil</flux:heading>

    <flux:separator variant="subtle" class="my-8" />

    <div class="flex flex-col lg:flex-row gap-4 lg:gap-6">
        <div class="w-80">
            <flux:heading size="lg">Profil</flux:heading>
            <flux:subheading>Perubahan profil Anda akan tercatat dan dapat dilihat oleh admin.</flux:subheading>
        </div>

        <form class="flex-1 space-y-6" wire:submit.prevent="saveProfile">
            <flux:input label="Nama Lengkap" placeholder="Zharifah Dzikra Purnomo" badge="required"
                wire:model="fullname" />
            <flux:input label="Nomor Pegawai" placeholder="21093012931" badge="required" wire:model="employee_code" />

            {{-- readonly --}}
            <flux:input label="Jabatan Pegawai" wire:model="position" readonly variant="filled" />
            <flux:input label="Status Pegawai" wire:model="status" readonly variant="filled" />

            <div class="flex justify-end">
                <flux:button variant="primary" type="submit">Simpan Perubahan</flux:button>
            </div>
        </form>
    </div>
</div>
