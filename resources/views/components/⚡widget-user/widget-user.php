<?php

use Livewire\Component;
use Livewire\Attributes\On;
use App\Models\Attendance;
use App\Models\Permission;
use App\Models\Leave;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

new class extends Component {
    public int $employeeId;

    public int $totalDays = 0;
    public int $totalAttendance = 0;
    public int $totalPartial = 0;
    public int $totalAbsent = 0;
    public int $totalIzin = 0;

    public float $attendanceScore = 0;

    public int $scorePercent = 0;
    public int $hadirPercent = 0;
    public int $tidakLengkapPercent = 0;
    public int $tidakHadirPercent = 0;
    public int $izinPercent = 0;

    public array $scoreColor = [];

    public function mount(): void
    {
        $this->employeeId = Auth::user()->employee->id;
        $this->loadStats();
    }

    #[On('refresh-widget')]
    public function refresh(): void
    {
        $this->loadStats();
    }

    protected function loadStats(): void
    {
        $date = now();
        $month = $date->month;
        $year = $date->year;

        // Hitung hari kerja bulan ini
        $start = $date->copy()->startOfMonth();
        $end = $date->copy()->endOfMonth();
        $weekdays = 0;
        while ($start->lte($end)) {
            if ($start->isWeekday()) $weekdays++;
            $start->addDay();
        }

        // Hitung cuti untuk dikurangi dari totalDays
        $leaves = Leave::where('employee_id', $this->employeeId)
            ->whereYear('start_date', $year)
            ->whereMonth('start_date', $month)
            ->where('status', 'disetujui')
            ->get(['start_date', 'end_date']);

        $monthStart = $date->copy()->startOfMonth();
        $monthEnd   = $date->copy()->endOfMonth();
        $cuti = 0;

        foreach ($leaves as $leave) {
            $current = Carbon::parse($leave->start_date)->max($monthStart);
            $endDate = Carbon::parse($leave->end_date)->min($monthEnd);
            while ($current->lte($endDate)) {
                if ($current->isWeekday()) $cuti++;
                $current->addDay();
            }
        }

        // Cuti mengurangi hari kerja efektif
        $this->totalDays = $weekdays - $cuti;

        // Ambil semua attendance sekaligus
        $attendances = Attendance::where('employee_id', $this->employeeId)
            ->whereMonth('attendance_date', $month)
            ->whereYear('attendance_date', $year)
            ->whereRaw('DAYOFWEEK(attendance_date) NOT IN (1, 7)')
            ->get(['status']);

        $this->totalAttendance = $attendances->where('status', 'hadir')->count();
        $this->totalPartial    = $attendances->where('status', 'tidak_lengkap')->count();
        $this->totalAbsent     = $attendances->where('status', 'tidak_hadir')->count();

        // Izin hanya informatif, tidak mempengaruhi score maupun totalDays
        $this->totalIzin = Permission::where('employee_id', $this->employeeId)
            ->whereMonth('permission_date', $month)
            ->whereYear('permission_date', $year)
            ->whereRaw('DAYOFWEEK(permission_date) NOT IN (1, 7)')
            ->count();

        // Score murni dari attendance
        $this->attendanceScore     = $this->totalAttendance * 1 + $this->totalPartial * 0.5;
        $this->scorePercent        = $this->totalDays ? round(($this->attendanceScore / $this->totalDays) * 100) : 0;
        $this->hadirPercent        = $this->totalDays ? round(($this->totalAttendance / $this->totalDays) * 100) : 0;
        $this->tidakLengkapPercent = $this->totalDays ? round(($this->totalPartial / $this->totalDays) * 100) : 0;
        $this->tidakHadirPercent   = $this->totalDays ? round(($this->totalAbsent / $this->totalDays) * 100) : 0;
        $this->izinPercent         = $this->totalDays ? round(($this->totalIzin / $this->totalDays) * 100) : 0;

        $this->scoreColor = match (true) {
            $this->scorePercent >= 80 => ['class' => 'text-green-600 dark:text-green-400', 'color' => 'green'],
            $this->scorePercent >= 50 => ['class' => 'text-yellow-600 dark:text-yellow-400', 'color' => 'yellow'],
            default                   => ['class' => 'text-red-600 dark:text-red-400', 'color' => 'red'],
        };
    }
};
