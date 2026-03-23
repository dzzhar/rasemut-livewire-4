<?php

namespace App\Services;

use App\Models\Attendance;
use App\Models\Leave;
use App\Models\Permission;
use Carbon\Carbon;

class CheckerService
{
    protected int $employeeId;

    public function setEmployee(int $employeeId): self
    {
        $this->employeeId = $employeeId;
        return $this;
    }

    public function hasAttendanceToday(Carbon $date): bool
    {
        return Attendance::where('employee_id', $this->employeeId)
            ->whereDate('attendance_date', $date)
            ->exists();
    }

    public function hasPermissionToday(Carbon $date): bool
    {
        return Permission::where('employee_id', $this->employeeId)
            ->whereDate('permission_date', $date)
            ->exists();
    }

    public function hasLeaveToday(Carbon $date): bool
    {
        return Leave::where('employee_id', $this->employeeId)
            ->whereDate('start_date', '<=', $date)
            ->whereDate('end_date', '>=', $date)
            ->where('status', 'disetujui')
            ->exists();
    }

    // validasi khusus untuk cuti
    public function hasLeaveInRange($start, $end): bool
    {
        return Leave::where('employee_id', $this->employeeId)
            ->where(function ($q) use ($start, $end) {
                $q->whereDate('start_date', '<=', $end)
                    ->whereDate('end_date', '>=', $start);
            })
            ->whereIn('status', ['pending', 'disetujui'])
            ->exists();
    }

    public function hasAttendanceInRange($start, $end): bool
    {
        return Attendance::where('employee_id', $this->employeeId)
            ->whereBetween('attendance_date', [$start, $end])
            ->exists();
    }

    public function hasPermissionInRange($start, $end): bool
    {
        return Permission::where('employee_id', $this->employeeId)
            ->whereBetween('permission_date', [$start, $end])
            ->exists();
    }
}
