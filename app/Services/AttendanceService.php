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

    // retrieve todays attendance record
    public function getToday()
    {
        return Attendance::where('employee_id', $this->employeeId)
            ->whereDate('attendance_date', now())
            ->first();
    }

    // handle attendance
    public function handleAttendance(float $userLatitude, float $userLongitude): array
    {
        return DB::transaction(function () use ($userLatitude, $userLongitude) {
            $now = now();

            // if theres a leave today, block attendance
            if ($this->checker->hasLeaveToday($now)) {
                return [
                    'title' => 'Presensi Gagal',
                    'message' => 'Anda sedang cuti hari ini.',
                    'type' => 'warning',
                ];
            }

            // if theres a permission today, block attendance
            if ($this->checker->hasPermissionToday($now)) {
                return [
                    'title' => 'Presensi Gagal',
                    'message' => 'Anda telah mengajukan izin hari ini.',
                    'type' => 'warning',
                ];
            }

            // calculate distance between user location and school location
            $distance = $this->calculateDistance(
                $userLatitude,
                $userLongitude,
                $this->setting->latitude,
                $this->setting->longitude
            );

            // if distance is greater than radius, block attendance
            if ($distance > $this->setting->radius_attendance) {
                return [
                    'title' => 'Diluar Jangkauan',
                    'message' => "Jarak Anda " . round($distance) . "m dari sekolah. Maksimal radius adalah {$this->setting->radius_attendance} m.",
                    'type' => 'error',
                ];
            }

            // create or update attendance record
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

    // handle check in logic
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

    // handle check out logic
    protected function doCheckOut(Attendance $attendance, Carbon $now)
    {
        $workStart = Carbon::parse($this->setting->check_in_setting)
            ->setDate($now->year, $now->month, $now->day);

        $workEnd = Carbon::parse($this->setting->check_out_setting)
            ->setDate($now->year, $now->month, $now->day);

        $desc = $attendance->description ?? '';

        // pulang cepat logic n description
        $earlyLeaveMinutes = 0;
        if ($now->lessThan($workEnd)) {
            $earlyLeaveMinutes = $now->diffInMinutes($workEnd);
            $earlyLeaveMinutes = max(0, (int) $earlyLeaveMinutes);

            $desc .= ($desc ? '; ' : '') . "Pulang cepat {$this->formattedTime($earlyLeaveMinutes)}";
        }

        // calculate total worked minutes
        $checkIn = $attendance->check_in
            ? Carbon::parse($attendance->check_in)->setDate($now->year, $now->month, $now->day)
            : null;

        $totalWorkedMinutes = $checkIn ? $checkIn->diffInMinutes($now) : 0;
        $normalWorkMinutes = $workStart->diffInMinutes($workEnd);

        // lembur logic n description
        $overtimeMinutes = max(0, $totalWorkedMinutes - $normalWorkMinutes);

        if ($overtimeMinutes > 0) {
            $desc .= ($desc ? '; ' : '') . "Lembur {$this->formattedTime($overtimeMinutes)}";
        }

        // update attendance record
        $attendance->update([
            'check_out' => $now->toTimeString(),
            'early_leave_minutes' => $earlyLeaveMinutes,
            'overtime_minutes' => $overtimeMinutes,
            'status' => 'hadir',
            'description' => $desc,
        ]);
    }

    // haversine formula to calculate distance
    protected function calculateDistance($lat1, $lon1, $lat2, $lon2): float
    {
        // radius of the earth in meters
        $earthRadius = 6371000;

        $latFrom = deg2rad($lat1);
        $lonFrom = deg2rad($lon1);
        $latTo = deg2rad($lat2);
        $lonTo = deg2rad($lon2);

        $latDelta = $latTo - $latFrom;
        $lonDelta = $lonTo - $lonFrom;

        $angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) +
            cos($latFrom) * cos($latTo) * pow(sin($lonDelta / 2), 2)));

        return $angle * $earthRadius;
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
