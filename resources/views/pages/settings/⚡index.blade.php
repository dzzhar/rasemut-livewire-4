<?php

use App\Actions\Fortify\UpdateUserProfileInformation;
use App\Actions\Fortify\UpdateUserPassword;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('Pengaturan')] class extends Component {
    public $fullname;

    public $current_password;
    public $password;
    public $password_confirmation;

    public function mount()
    {
        $this->fullname = Auth::user()->employee?->fullname;
    }

    public function updateProfile(UpdateUserProfileInformation $updater)
    {
        $updater->update(Auth::user(), [
            'fullname' => $this->fullname,
        ]);

        $this->dispatch('show-feedback', title: 'Profil Berhasil Diperbarui', message: 'Profil Anda telah berhasil diperbarui.');

        $this->resetErrorBag();
        $this->resetValidation();
        // $this->redirect(request()->header('Referer'), navigate: true);
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
    <div class="flex flex-col lg:flex-row gap-4 lg:gap-6">
        <div class="w-80">
            <flux:heading size="lg">Profil</flux:heading>
            <flux:subheading>Perubahan profil Anda akan tercatat dan dapat dilihat oleh admin.
            </flux:subheading>
        </div>

        @island(name: 'profile')
            <form class="flex-1 space-y-6" wire:submit.prevent="updateProfile">
                <flux:input label="Nama Lengkap" wire:model="fullname" placeholder="Masukkan nama lengkap Anda" />

                <div class="flex justify-end">
                    <flux:button variant="primary" type="submit" size="sm" wire:island="profile">Simpan Perubahan
                    </flux:button>
                </div>
            </form>
        @endisland
    </div>

    <flux:separator variant="subtle" class="my-8" />


    <div class="flex flex-col lg:flex-row gap-4 lg:gap-6">
        <div class="w-80">
            <flux:heading size="lg">Perbarui Kata Sandi</flux:heading>
            <flux:subheading>Masukkan kata sandi baru dan konfirmasi untuk menggantinya.</flux:subheading>
        </div>

        @island(name: 'password')
            <form class="flex-1 space-y-6" wire:submit.prevent="updatePassword">
                <flux:input label="Kata Sandi Saat Ini" wire:model="current_password" type="password" placeholder="********"
                    viewable />
                <flux:input label="Kata Sandi Baru" wire:model="password" type="password"
                    description="Minimal 8 karakter, mengandung satu huruf besar, satu huruf kecil, satu angka, dan satu simbol."
                    placeholder="********" viewable />
                <flux:input label="Konfirmasi Kata Sandi Baru" wire:model="password_confirmation" type="password"
                    placeholder="********" viewable />

                <div class="flex justify-end">
                    <flux:button type="submit" variant="primary" size="sm" wire:island="password">Perbarui Kata Sandi
                    </flux:button>
                </div>
            </form>
        @endisland
    </div>
</div>
