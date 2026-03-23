<?php

use App\Actions\Fortify\UpdateUserProfileInformation;
use App\Actions\Fortify\UpdateUserPassword;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('Pengaturan')] class extends Component {
    public $fullname;
    public $employee_code;

    public $current_password;
    public $password;
    public $password_confirmation;

    public function mount()
    {
        $employee = Auth::user()->employee;

        $this->fullname = $employee?->fullname;
        $this->employee_code = $employee?->employee_code;
    }

    public function updateProfile(UpdateUserProfileInformation $updater)
    {
        $updater->update(Auth::user(), [
            'fullname' => $this->fullname,
            'employee_code' => $this->employee_code,
        ]);

        $this->dispatch('show-feedback', title: 'Profil Berhasil Diperbarui', message: 'Profil Anda telah berhasil diperbarui.');

        $this->resetErrorBag();
        $this->resetValidation();
        $this->redirect(request()->header('Referer'), navigate: true);
    }

    public function updatePassword(UpdateUserPassword $updater)
    {
        $updater->update(Auth::user(), [
            'current_password' => $this->current_password,
            'password' => $this->password,
            'password_confirmation' => $this->password_confirmation,
        ]);

        $this->dispatch('show-feedback', title: 'Kata Sandi Berhasil Diperbarui', message: 'Kata sandi Anda telah berhasil diperbarui.');

        $this->resetErrorBag();
        $this->resetValidation();
    }
};
?>

<div>
    <flux:heading size="xl">Pengaturan</flux:heading>
    <flux:separator variant="subtle" class="my-8" />

    @island(name: 'profile')
        <div class="flex flex-col lg:flex-row gap-4 lg:gap-6">
            <div class="w-80">
                <flux:heading size="lg">Profil</flux:heading>
                <flux:subheading>Perubahan profil Anda akan tercatat dan dapat dilihat oleh admin.</flux:subheading>
            </div>

            <form class="flex-1 space-y-6" wire:submit.prevent="updateProfile">
                <flux:input label="Nama Lengkap" wire:model="fullname" placeholder="Masukkan nama lengkap Anda" />
                <flux:input label="Nomor Pegawai" wire:model="employee_code" placeholder="Masukkan nomor pegawai Anda" />

                <div class="flex justify-end">
                    <flux:button variant="primary" type="submit" wire:island="profile">Simpan Perubahan</flux:button>
                </div>
            </form>
        </div>
    @endisland

    <flux:separator variant="subtle" class="my-8" />


    @island(name: 'password')
        <div class="flex flex-col lg:flex-row gap-4 lg:gap-6">
            <div class="w-80">
                <flux:heading size="lg">Perbarui Kata Sandi</flux:heading>
                <flux:subheading>Masukkan kata sandi baru dan konfirmasi untuk menggantinya.</flux:subheading>
            </div>

            <form class="flex-1 space-y-6" wire:submit.prevent="updatePassword">
                <flux:input label="Kata Sandi Saat Ini" wire:model="current_password" type="password" placeholder="********"
                    viewable />
                <flux:input label="Kata Sandi Baru" wire:model="password" type="password"
                    description="Minimal 8 karakter dan mengandung setidaknya satu huruf besar, satu huruf kecil, satu angka, serta satu simbol."
                    placeholder="********" viewable />
                <flux:input label="Konfirmasi Kata Sandi Baru" wire:model="password_confirmation" type="password"
                    placeholder="********" viewable />

                <div class="flex justify-end">
                    <flux:button type="submit" variant="primary" wire:island="password">Perbarui Kata Sandi</flux:button>
                </div>
            </form>
        </div>
    @endisland
</div>
