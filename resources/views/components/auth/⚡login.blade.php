<?php

use Livewire\Attributes\Layout;
use Livewire\Component;

new #[Layout('layouts::auth')] class extends Component {};
?>

<div class="flex min-h-screen">
    <div class="flex-1 flex justify-center items-center">
        <div class="w-80 sm:w-96 max-w-96 space-y-4">

            <div class="flex justify-center opacity-50">
                <a href="/" class="group flex items-center gap-3">
                    <img src="{{ asset('images/logo.svg') }}" alt="Logo RA Semut" class="h-8">

                    <span class="text-xl font-semibold text-zinc-800 dark:text-white">RA Semut</span>
                </a>
            </div>

            <flux:heading class="text-center" size="xl">Masuk ke Akun Anda</flux:heading>
            <flux:separator />

            <div x-data={loading:false}>
                <form method="POST" action="{{ route('login') }}" class="flex flex-col gap-6" @submit="loading = true">
                    @csrf
                    <flux:input label="Email" type="email" placeholder="emailanda@gmail.com" name="email"
                        :value="old('email')" autocomplete="email" />

                    <div class="space-y-2">
                        <flux:input label="Kata Sandi" type="password" placeholder="Kata sandi Anda" name="password"
                            autocomplete="current-password" viewable />

                        <div class="flex justify-end">
                            <flux:button as="a" variant="ghost" size="xs" inset
                                href="{{ route('password.request') }}">
                                Lupa Kata Sandi?
                            </flux:button>
                        </div>
                    </div>

                    <flux:button variant="primary" class="w-full" type="submit" x-bind:disabled="loading">Masuk
                    </flux:button>
                </form>
            </div>
        </div>
    </div>

    <div class="flex-1 p-4 max-lg:hidden">
        <div
            class="text-white relative rounded-lg overflow-hidden h-full w-full bg-zinc-900 flex flex-col items-start justify-end p-16">
            <img src="{{ asset('images/login.jpg') }}" alt="Ilustrasi Login"
                class="absolute inset-0 w-full h-full object-cover opacity-40">
        </div>
    </div>
</div>
