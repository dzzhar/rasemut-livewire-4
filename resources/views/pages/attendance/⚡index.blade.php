<?php

use Livewire\Attributes\Layout;
use Livewire\Component;

new class extends Component {
    public function getGreetingProperty()
    {
        $hour = now()->format('H');
        return match (true) {
            $hour < 11 => 'Selamat pagi',
            $hour < 15 => 'Selamat siang',
            $hour < 18 => 'Selamat sore',
            default => 'Selamat malam',
        };
    }
};
?>


<div class="space-y-8">
    <flux:heading size="xl" level="1">
        {{ $this->greeting . ', ' . ucfirst(Str::before(auth()->user()->employee?->fullname, ' ')) }} 👋
    </flux:heading>

    {{-- <flux:separator variant="subtle" class="my-8" /> --}}

    <livewire:pages::attendance.create />
    <livewire:history-card headerTitle="Riwayat Presensi" model="\App\Models\Attendance" dateColumn="attendance_date"
        :select="['id', 'attendance_type', 'attendance_date', 'status', 'description']" lazy />

    <livewire:pages::attendance.detail />
</div>
