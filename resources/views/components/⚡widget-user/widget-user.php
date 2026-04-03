<?php

use Livewire\Component;
use App\Models\Attendance;
use App\Models\Permission;
use App\Models\Leave;
use Carbon\Carbon;

new class extends Component {
    protected function getTestDate(): Carbon
    {
        return Carbon::create(now()->year, 3); // maret
    }

    public function getTotalDaysProperty()
    {
        $date = $this->getTestDate();
        $start = $date->copy()->startOfMonth();
        $end = $date->copy()->endOfMonth();

        $weekdays = 0;
        while ($start->lte($end)) {
            if ($start->isWeekday()) {
                $weekdays++;
            }
            $start->addDay();
        }

        return $weekdays;
    }

    public function getTotalAttendanceProperty()
    {
        $date = $this->getTestDate();

        return Attendance::whereMonth('attendance_date', $date->month)->whereYear('attendance_date', $date->year)->where('status', 'hadir')->whereRaw('DAYOFWEEK(attendance_date) NOT IN (1, 7)')->count();
    }

    // Tidak lengkap: bobot 0.5
    public function getTotalPartialProperty()
    {
        $date = $this->getTestDate();

        return Attendance::whereMonth('attendance_date', $date->month)
            ->whereYear('attendance_date', $date->year)
            ->where('status', 'tidak_lengkap') // sesuaikan dengan nilai di DB
            ->whereRaw('DAYOFWEEK(attendance_date) NOT IN (1, 7)')
            ->count();
    }

    // Tidak hadir: bobot 0
    public function getTotalAbsentProperty()
    {
        $date = $this->getTestDate();

        return Attendance::whereMonth('attendance_date', $date->month)->whereYear('attendance_date', $date->year)->where('status', 'tidak_hadir')->whereRaw('DAYOFWEEK(attendance_date) NOT IN (1, 7)')->count();
    }

    // Izin + Cuti digabung
    public function getTotalIzinCutiProperty()
    {
        $date = $this->getTestDate();

        $izin = Permission::whereMonth('permission_date', $date->month)->whereYear('permission_date', $date->year)->whereRaw('DAYOFWEEK(permission_date) NOT IN (1, 7)')->count();

        $leaves = Leave::whereYear('start_date', $date->year)
            ->whereMonth('start_date', $date->month)
            ->where('status', 'disetujui')
            ->get(['start_date', 'end_date']);

        $monthStart = $date->copy()->startOfMonth();
        $monthEnd = $date->copy()->endOfMonth();

        $cuti = 0;
        foreach ($leaves as $leave) {
            $start = Carbon::parse($leave->start_date)->max($monthStart);
            $end = Carbon::parse($leave->end_date)->min($monthEnd);
            $current = $start->copy();

            while ($current->lte($end)) {
                if ($current->isWeekday()) {
                    $cuti++;
                }
                $current->addDay();
            }
        }

        return $izin + $cuti;
    }

    // Skor berbobot untuk persentase kehadiran utama
    public function getAttendanceScoreProperty()
    {
        return $this->totalAttendance * 1 + $this->totalPartial * 0.5;
    }

    public function getHadirPercentProperty()
    {
        return $this->totalDays ? round(($this->totalAttendance / $this->totalDays) * 100) : 0;
    }

    public function getTidakLengkapPercentProperty()
    {
        return $this->totalDays ? round(($this->totalPartial / $this->totalDays) * 100) : 0;
    }

    public function getTidakHadirPercentProperty()
    {
        return $this->totalDays ? round(($this->totalAbsent / $this->totalDays) * 100) : 0;
    }

    public function getIzinCutiPercentProperty()
    {
        return $this->totalDays ? round(($this->totalIzinCuti / $this->totalDays) * 100) : 0;
    }

    // Persentase kehadiran berbobot untuk header
    public function getScorePercentProperty()
    {
        return $this->totalDays ? round(($this->attendanceScore / $this->totalDays) * 100) : 0;
    }

    public function getScoreColorProperty(): array
    {
        return match (true) {
            $this->scorePercent >= 80 => [
                'class' => 'text-green-600 dark:text-green-400',
                'color' => 'green',
            ],
            $this->scorePercent >= 50 => [
                'class' => 'text-yellow-600 dark:text-yellow-400',
                'color' => 'yellow',
            ],
            default => [
                'class' => 'text-red-600 dark:text-red-400',
                'color' => 'red',
            ],
        };
    }
};
