<?php

use Livewire\Component;
use Illuminate\Support\Str;
use App\Models\Attendance;
use App\Models\Permission;
use App\Models\Leave;
use Carbon\Carbon;
use App\Enums\AttendanceStatus;

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


<div class="space-y-6">
    <flux:heading size="xl" class="font-bold">
        {{ $this->greeting }},
        {{ ucfirst(Str::before(auth()->user()->employee?->fullname, ' ')) }} 👋
    </flux:heading>

    <livewire:widget-user lazy />

    <livewire:pages::attendance.create />
</div>
