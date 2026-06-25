<?php

use Livewire\Attributes\Layout;
use Livewire\Component;

new class extends Component {};
?>

<div class="flex min-h-screen justify-center items-center">
    <div class="w-80 sm:w-96 space-y-4">

        <div class="flex justify-center opacity-50">
            <a href="/" class="group flex items-center gap-3">
                <img src="{{ asset('images/logo.svg') }}" alt="Logo RA Semut" class="h-8">

                <span class="text-xl font-semibold text-zinc-800 dark:text-white">RA Semut</span>
            </a>
        </div>

        <flux:heading size="xl" class="text-center">
            Reset Kata Sandi
        </flux:heading>

        <flux:subheading class="text-center">
            Masukkan kata sandi baru dan konfirmasi untuk menggantinya
        </flux:subheading>

        <div x-data={loading:false}>
            <form method="POST" action="{{ route('password.update') }}" class="flex flex-col gap-4"
                @submit="loading = true">
                @csrf
                <input type="hidden" name="token" value="{{ request()->route('token') }}">
                <input type="hidden" name="email" value="{{ request()->email }}">

                <flux:input type="password" name="password" label="Kata Sandi Baru" placeholder="********" required
                    viewable />
                <flux:input type="password" name="password_confirmation" label="Konfirmasi Kata Sandi"
                    placeholder="********" required viewable />
                <flux:button type="submit" variant="primary" x-bind:disabled="loading">Reset Kata Sandi
                </flux:button>
            </form>
        </div>

    </div>
</div>
