<?php

use Livewire\Attributes\Layout;
use Livewire\Component;

new #[Layout('layouts::app')] class extends Component {
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

    public function getQuoteProperty()
    {
        $hour = now()->format('H');
        $quotes = match (true) {
            $hour < 11 => ['Mulai harimu dengan semangat pagi ☀️', 'Langkah kecil hari ini, hasil besar nanti ✨', 'Pagi ini sempurna untuk produktif 💪'],
            $hour < 15 => ['Tetap semangat jalani hari 🚀', 'Langkah kecil hari ini, hasil besar nanti 🌱', 'Pastikan aktivitasmu hari ini lancar 😊'],
            $hour < 18 => ['Sore ini hampir selesai 🌤️', 'Kerja cerdas hari ini, pulang dengan tenang 🌿', 'Tetap produktif sampai sore 🌸'],
            default => ['Terima kasih sudah berusaha 🌙', 'Istirahat cukup, besok lanjut produktif ✨', 'Semoga malam ini tenang 🌌'],
        };

        return collect($quotes)->random();
    }
};
?>

<div class="space-y-8">
    <div>
        <flux:heading size="xl" level="1"> {{ $this->greeting }}, Zharifah 👋</flux:heading>
        <flux:text class="mt-2 mb-6 text-base">{{ $this->quote }}</flux:text>
        <flux:separator variant="subtle" />
    </div>

    <livewire:pages::attendance.create />
    <livewire:history.container headerTitle="Riwayat Presensi" model="\App\Models\Attendance" dateColumn="attendance_date"
        :select="['id', 'attendance_type', 'attendance_date', 'status', 'description']" lazy />

    {{-- <livewire:pages::attendance.detail /> --}}
</div>
