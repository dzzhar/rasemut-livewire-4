<div>
    <flux:heading size="xl">Pengaturan Kata Sandi</flux:heading>
    <flux:separator variant="subtle" class="my-8" />

    <div class="flex flex-col lg:flex-row gap-4 lg:gap-6">
        <div class="w-80">
            <flux:heading size="lg">Perbarui Kata Sandi</flux:heading>
            <flux:subheading>Masukkan kata sandi baru dan konfirmasi untuk menggantinya.</flux:subheading>
        </div>

        <form class="flex-1 space-y-6" wire:submit.prevent="updatePassword">
            <flux:input label="Kata Sandi Saat Ini" wire:model="current_password" type="password" placeholder="********"
                viewable />
            <flux:input label="Kata Sandi Baru" wire:model="password" type="password"
                description="Kata sandi harus mengandung minimal satu huruf, kombinasi huruf besar & kecil, satu angka, dan satu simbol."
                placeholder="********" viewable />
            <flux:input label="Konfirmasi Kata Sandi Baru" wire:model="password_confirmation" type="password"
                placeholder="********" viewable />

            <div class="flex justify-end">
                <flux:button type="submit" variant="primary">Perbarui Kata Sandi</flux:button>
            </div>
        </form>
    </div>
</div>
