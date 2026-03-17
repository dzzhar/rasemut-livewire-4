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

    public function hasAttendanceToday(Carbon $now): bool
    {
        return Attendance::where('employee_id', $this->employeeId)
            ->whereDate('attendance_date', $now)
            ->exists();
    }

    public function hasPermissionToday(Carbon $now): bool
    {
        return Permission::where('employee_id', $this->employeeId)
            ->whereDate('permission_date', $now)
            ->exists();
    }

    public function hasLeaveToday(Carbon $now): bool
    {
        return Leave::where('employee_id', $this->employeeId)
            ->whereDate('start_date', '<=', $now)
            ->whereDate('end_date', '>=', $now)
            ->where('status', 'disetujui')
            ->exists();
    }
}
