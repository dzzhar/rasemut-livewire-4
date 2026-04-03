<?php

use Livewire\Attributes\Layout;
use Livewire\Component;

new #[Layout('layouts::auth')] class extends Component {};
?>

<div class="flex min-h-screen">
    <div class="flex-1 flex justify-center items-center">
        <div class="w-96 max-w-96 space-y-6">
            <div class="flex justify-center opacity-50">
                <a href="/" class="group flex items-center gap-3">
                    <img src="{{ asset('images/logo.svg') }}" alt="Logo RA Semut" class="h-8">

                    <span class="text-xl font-semibold text-zinc-800 dark:text-white">RA Semut</span>
                </a>
            </div>

            <flux:heading class="text-center" size="xl">Masuk ke Akun Anda</flux:heading>
            <flux:separator />

            <form method="POST" action="{{ route('login') }}" class="flex flex-col gap-6">
                @csrf
                <flux:input label="Email" type="email" placeholder="emailanda@gmail.com" name="email"
                    :value="old('email')" autocomplete="email" />
                <flux:input label="Kata Sandi" type="password" placeholder="Kata sandi Anda" name="password" viewable />
                <flux:button variant="primary" class="w-full" type="submit">Log in</flux:button>
            </form>
        </div>
    </div>

    <div class="flex-1 p-4 max-lg:hidden">
        <div class="text-white relative rounded-lg h-full w-full bg-zinc-900 flex flex-col items-start justify-end p-16"
            style="background-image: url('https://picsum.photos/200/300'); background-size: cover">

        </div>
    </div>
</div>
