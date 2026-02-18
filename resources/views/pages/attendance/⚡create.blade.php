<?php

use App\Services\AttendanceService;
use Livewire\Component;

new class extends Component {
    public $check_in;
    public $check_out;
    public $button;
    public int $employeeId;

    public function mount()
    {
        $this->employeeId = Auth::user()->id;
        $service = new AttendanceService($this->employeeId);
        $this->refreshState($service);
    }

    protected function refreshState(AttendanceService $service)
    {
        $today = $service->getTodayState();
        $this->check_in = $today['masuk'];
        $this->check_out = $today['pulang'];
        $this->setButtonStatus();
    }

    protected function setButtonStatus()
    {
        if (!$this->check_in) {
            $this->button = ['label' => 'Check In', 'color' => 'blue'];
        } elseif (!$this->check_out) {
            $this->button = ['label' => 'Check Out', 'color' => 'red'];
        } else {
            $this->button = null;
        }
    }

    public function absensiButton()
    {
        $service = new AttendanceService($this->employeeId);
        $service->handleAttendance();
        $this->refreshState($service);
        $this->dispatch('refresh-history');
    }
};
?>


<flux:card size="md" class="grid grid-cols-1 lg:grid-cols-2 gap-6 bg-white dark:bg-zinc-900">
    <livewire:attendance-card title="Check In" :time="$check_in?->attendance_date->format('H:i:s')" :status="$check_in?->status" :key="'checkin-' . $check_in?->attendance_date?->timestamp" />
    <livewire:attendance-card title="Check Out" :time="$check_out?->attendance_date->format('H:i:s')" :status="$check_out?->status" :key="'checkout-' . $check_out?->attendance_date?->timestamp" />

    <flux:button variant="primary" :color="$button['color'] ?? ''"
        class="w-full lg:col-span-2 {{ !$button ? 'hidden!' : '' }}" wire:click="absensiButton">
        {{ $button['label'] ?? '' }}
    </flux:button>
</flux:card>
