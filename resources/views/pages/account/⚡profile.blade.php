<?php

use App\Models\Employee;
use App\Actions\Fortify\UpdateUserProfileInformation;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Title;
use Livewire\Attributes\Validate;
use Livewire\Component;

new #[Title('Akun')] class extends Component {
    public $fullname;
    public $employee_code;
    public $position_name;
    public $status;

    public function mount()
    {
        $employee = Auth::user()->employee;

        $this->fullname = $employee?->fullname;
        $this->employee_code = $employee?->employee_code;
        $this->position_name = $employee?->position?->position_name;
        $this->status = Auth::user()->is_active ? 'Aktif' : 'Nonaktif';
    }

    public function updateProfile(UpdateUserProfileInformation $updater)
    {
        $updater->update(Auth::user(), [
            'fullname' => $this->fullname,
            'employee_code' => $this->employee_code,
        ]);

        $this->redirect(request()->header('Referer'), navigate: true);
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

        <form class="flex-1 space-y-6" wire:submit.prevent="updateProfile">

            <flux:input label="Nama Lengkap" badge="required" wire:model="fullname"
                placeholder="Masukkan nama lengkap Anda" />
            <flux:input label="Nomor Pegawai" badge="required" wire:model="employee_code"
                placeholder="Masukkan nomor pegawai Anda" />

            {{-- readonly --}}
            <flux:input label="Jabatan Pegawai" :value="$position_name" readonly variant="filled" />
            <flux:input label="Status Pegawai" :value="$status" readonly variant="filled" />

            <div class="flex justify-end">
                <flux:button variant="primary" type="submit">Simpan Perubahan</flux:button>
            </div>
        </form>
    </div>
</div>
