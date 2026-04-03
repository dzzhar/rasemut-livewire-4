<?php

use Livewire\Component;
use Illuminate\Support\Str;
use App\Models\Attendance;
use App\Models\Leave;
use Carbon\Carbon;

new class extends Component {};
?>

<div class="space-y-6">
    <livewire:history-card headerTitle="Riwayat Presensi" model="\App\Models\Attendance" dateColumn="attendance_date"
        :select="['id', 'attendance_date', 'check_out', 'status', 'description']" lazy />

    <livewire:pages::attendance.detail />
</div>
