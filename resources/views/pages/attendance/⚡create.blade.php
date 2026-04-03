<?php

use App\Services\AttendanceService;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;
use Carbon\Carbon;
use App\Models\Attendance;

new class extends Component {
    public ?string $check_in = null;
    public ?string $check_out = null;
    public ?array $button = null;
    public ?Attendance $todayAttendance = null;
    public int $employeeId;
    public bool $isWorkingDay = true;

    public ?float $latitude = null;
    public ?float $longitude = null;

    public function mount()
    {
        $this->employeeId = Auth::user()->employee->id;
        $service = new AttendanceService($this->employeeId);
        $this->isWorkingDay = $service->isWorkingDay(now());
        $this->refreshState($service);
    }

    protected function refreshState(AttendanceService $service)
    {
        $this->todayAttendance = $service->getToday();
        $this->check_in = $this->todayAttendance?->check_in;
        $this->check_out = $this->todayAttendance?->check_out;
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

    // $this->todayAttendance = $service->getToday();

    public function absensiButton()
    {
        if (is_null($this->latitude) || is_null($this->longitude)) {
            $this->dispatch('show-feedback', title: 'Lokasi belum ditemukan', message: 'Mohon tunggu GPS aktif lalu coba lagi.', type: 'warning');
            return;
        }

        $service = new AttendanceService($this->employeeId);
        $result = $service->handleAttendance($this->latitude, $this->longitude);

        $this->dispatch('show-feedback', title: $result['title'], message: $result['message'], type: $result['type']);
        $this->refreshState($service);
        $this->dispatch('refresh-history');
    }

    #[On('set-location')]
    public function setLocation($lat, $lng)
    {
        $this->latitude = $lat;
        $this->longitude = $lng;
    }
};
?>


<div>
    @if ($isWorkingDay)
        <flux:card size="md" class="bg-white dark:bg-zinc-900 antialiased">
            <flux:fieldset class="flex items-center justify-between">
                <div>
                    <flux:heading size="lg" class="font-semibold">Presensi Hari Ini</flux:heading>
                    <flux:text>{{ now()->translatedFormat('l, d F Y') }}</flux:text>
                </div>

                <flux:button size="sm" variant="primary" href="{{ route('history') }}" wire:navigate>
                    Riwayat
                </flux:button>
            </flux:fieldset>

            <div class="pt-6 space-y-6">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <livewire:attendance-card title="Check In" :time="$check_in" :key="'in-' . ($check_in ?? '0')" />
                    <livewire:attendance-card title="Check Out" :time="$check_out" :key="'out-' . ($check_out ?? '0')" />
                </div>

                <flux:button variant="primary" :color="$button['color'] ?? ''"
                    class="w-full lg:col-span-2 {{ !$button ? 'hidden!' : '' }}" wire:click="absensiButton">
                    {{ $button['label'] ?? '' }}
                </flux:button>
            </div>
        </flux:card>
    @endif
</div>
