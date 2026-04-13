<?php

use Livewire\Component;
use Illuminate\Support\Str;
use App\Models\Attendance;
use App\Models\Leave;
use Carbon\Carbon;

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
    <flux:heading size="xl" class="font-bold">
        {{ $this->greeting }},
        {{ ucfirst(Str::before(auth()->user()->employee?->fullname, ' ')) }} 👋
    </flux:heading>

    <livewire:pages::attendance.create />

    <livewire:history-card headerTitle="Riwayat Presensi" model="\App\Models\Attendance" dateColumn="attendance_date"
        :select="['id', 'attendance_date', 'check_out', 'status', 'description']" lazy />
    <livewire:pages::attendance.detail />
</div>
