<?php

use Livewire\Attributes\Layout;
use Livewire\Component;

new #[Layout('layouts::app', ['title' => 'Akun'])] class extends Component {
    //
};
?>


<div>
    <flux:heading size="xl">Settings</flux:heading>

    <flux:separator variant="subtle" class="my-8" />

    <div class="flex flex-col lg:flex-row gap-4 lg:gap-6">
        <div class="w-80">
            <flux:heading size="lg">Profil</flux:heading>
            <flux:subheading>Perubahan profil Anda akan tercatat dan dapat dilihat oleh admin.</flux:subheading>
        </div>

        <div class="flex-1 space-y-6">
            <flux:input label="Nama Lengkap" placeholder="Zharifah Dzikra Purnomo" badge="Perlu Diisi" />
            <flux:input label="Nomor Pegawai" placeholder="21093012931" badge="Perlu Diisi" />

            {{-- readonly --}}
            <flux:input label="Jabatan Pegawai" value="Guru Tetap" readonly variant="filled" />
            <flux:input label="Status Pegawai" value="Guru Tetap" readonly variant="filled" />

            <div class="flex justify-end">
                <flux:button type="submit" variant="primary">Simpan Perubahan</flux:button>
            </div>
        </div>
    </div>

    <flux:separator variant="subtle" class="my-8" />

    <div class="flex flex-col lg:flex-row gap-4 lg:gap-6">
        <div class="w-80">
            <flux:heading size="lg">Perbarui Kata Sandi</flux:heading>
            <flux:subheading>Masukkan kata sandi baru dan konfirmasi untuk menggantinya.</flux:subheading>
        </div>

        <div class="flex-1 space-y-6">
            <flux:input label="Kata Sandi Baru" viewable />
            <flux:input label="Konfirmasi Kata Sandi Baru" viewable />

            <div class="flex justify-end">
                <flux:button type="submit" variant="primary">Perbarui Kata Sandi</flux:button>
            </div>
        </div>
    </div>
</div>
