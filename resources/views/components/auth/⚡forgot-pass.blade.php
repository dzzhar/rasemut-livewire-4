<?php

use Livewire\Attributes\Layout;
use Livewire\Component;
use Flux\Flux;

new class extends Component {
    public function mount()
    {
        if (session()->has('status')) {
            $status = session('status');
            Flux::toast(text: $status, variant: str_contains($status, 'berhasil') ? 'success' : 'warning');
        }
    }
};
?>

<div class="flex min-h-screen justify-center items-center">
    <div class="w-80 sm:w-96 space-y-4">

        <div class="flex justify-center opacity-50">
            <a href="/" class="group flex items-center gap-3">
                <img src="{{ asset('images/logo.svg') }}" alt="Logo RA Semut" class="h-8">

                <span class="text-xl font-semibold text-zinc-800 dark:text-white">RA Semut</span>
            </a>
        </div>

        <flux:heading class="text-center" size="xl">
            Lupa Kata Sandi
        </flux:heading>

        <flux:subheading class="text-center">
            Masukkan email untuk menerima link reset kata sandi
        </flux:subheading>

        <div x-data={loading:false}>
            <form method="POST" action="{{ route('password.email') }}" class="flex flex-col gap-4"
                @submit="loading = true">
                @csrf

                <flux:input label="Email" type="email" name="email" placeholder="emailanda@gmail.com" required />

                <flux:button type="submit" variant="primary" class="w-full" x-bind:disabled="loading">
                    Kirim Link Reset
                </flux:button>
            </form>
        </div>

    </div>
</div>
