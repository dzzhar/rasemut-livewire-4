<?php

namespace App\Services;

use App\Models\Attendance;
use App\Models\AttendanceSetting;
use Carbon\Carbon;
use Carbon\CarbonInterval;
use Illuminate\Support\Facades\DB;

class AttendanceService
{
    protected int $employeeId;
    protected AttendanceSetting $setting;
    protected CheckerService $checker;

    public function __construct(int $employeeId)
    {
        $this->employeeId = $employeeId;
        $this->setting = AttendanceSetting::firstOrFail();
        $this->checker = app(CheckerService::class)->setEmployee($employeeId);
    }

    public function getToday()
    {
        return Attendance::where('employee_id', $this->employeeId)
            ->whereDate('attendance_date', now())
            ->first();
    }

    public function handleAttendance(): array
    {
        return DB::transaction(function () {
            $now = now();

            if ($this->checker->hasLeaveToday($now)) {
                return [
                    'title' => 'Presensi Gagal',
                    'message' => 'Anda sedang cuti hari ini.',
                    'type' => 'warning',
                ];
            }

            if ($this->checker->hasPermissionToday($now)) {
                return [
                    'title' => 'Presensi Gagal',
                    'message' => 'Anda telah mengajukan izin hari ini.',
                    'type' => 'warning',
                ];
            }

            $attendance = Attendance::firstOrCreate([
                'employee_id' => $this->employeeId,
                'attendance_date' => $now->toDateString(),
            ], ['status' => 'tidak_lengkap']);

            if (!$attendance->check_in) {
                $this->doCheckIn($attendance, $now);
            } elseif (!$attendance->check_out) {
                $this->doCheckOut($attendance, $now);
            }

            return [
                'title' => 'Presensi Berhasil!',
                'message' => 'Presensi Anda berhasil dilakukan.',
                'type' => 'success'
            ];
        });
    }

    protected function doCheckIn(Attendance $attendance, Carbon $now)
    {
        $timeCheckIn = Carbon::parse($this->setting->check_in_setting)
            ->setDate($now->year, $now->month, $now->day);

        $lateMinutes = 0;
        $desc = null;

        if ($now->greaterThan($timeCheckIn)) {
            $lateMinutes = $timeCheckIn->diffInMinutes($now);
            $lateMinutes = max(0, (int) $lateMinutes);

            $desc = "Terlambat {$this->formattedTime($lateMinutes)}";
        }

        $attendance->update([
            'check_in' => $now->toTimeString(),
            'late_minutes' => $lateMinutes,
            'status' => 'hadir',
            'description' => $desc,
        ]);
    }

    protected function doCheckOut(Attendance $attendance, Carbon $now)
    {
        $workStart = Carbon::parse($this->setting->check_in_setting)
            ->setDate($now->year, $now->month, $now->day);

        $workEnd = Carbon::parse($this->setting->check_out_setting)
            ->setDate($now->year, $now->month, $now->day);

        $desc = $attendance->description ?? '';

        // pulang cepat
        $earlyLeaveMinutes = 0;
        if ($now->lessThan($workEnd)) {
            $earlyLeaveMinutes = $now->diffInMinutes($workEnd);
            $earlyLeaveMinutes = max(0, (int) $earlyLeaveMinutes);

            $desc .= ($desc ? '; ' : '') . "Pulang cepat {$this->formattedTime($earlyLeaveMinutes)}";
        }

        // total kerja
        $checkIn = $attendance->check_in
            ? Carbon::parse($attendance->check_in)->setDate($now->year, $now->month, $now->day)
            : null;

        $totalWorkedMinutes = $checkIn ? $checkIn->diffInMinutes($now) : 0;
        $normalWorkMinutes = $workStart->diffInMinutes($workEnd);

        // lembur
        $overtimeMinutes = max(0, $totalWorkedMinutes - $normalWorkMinutes);

        if ($overtimeMinutes > 0) {
            $desc .= ($desc ? '; ' : '') . "Lembur {$this->formattedTime($overtimeMinutes)} menit";
        }

        $attendance->update([
            'check_out' => $now->toTimeString(),
            'early_leave_minutes' => $earlyLeaveMinutes,
            'overtime_minutes' => $overtimeMinutes,
            'status' => 'hadir',
            'description' => $desc,
        ]);
    }

    public function isWorkingDay(Carbon $date): bool
    {
        return $date->isWeekday();
    }

    public function formattedTime(int $lateMinutes): string
    {
        return CarbonInterval::minutes($lateMinutes)->cascade();
    }
}
