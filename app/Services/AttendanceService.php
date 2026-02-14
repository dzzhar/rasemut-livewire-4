<?php

namespace App\Services;

use App\Models\Attendance;
use App\Models\AttendanceSetting;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AttendanceService
{
    protected int $employeeId;
    protected AttendanceSetting $setting;

    public function __construct(int $employeeId)
    {
        $this->employeeId = $employeeId;
        $this->setting = AttendanceSetting::firstOrFail();
    }

    public function getTodayAttendance(): array
    {
        $today = Attendance::where('employee_id', $this->employeeId)
            ->whereDate('attendance_date', now())
            ->get()
            ->keyBy('attendance_type');

        return [
            'masuk' => $today->get('masuk'),
            'pulang' => $today->get('pulang'),
        ];
    }

    public function handleAttendance(): void
    {
        DB::transaction(function () {
            $now = now();
            $this->handleYesterdayCheckout();
            $today = $this->getTodayAttendance();

            if (!$today['masuk']) {
                $this->doMasuk($now);
            } elseif (!$today['pulang']) {
                $this->doPulang($now);
            } else {
                return null;
            }
        });
    }

    // logic untuk masuk (button check in)
    protected function doMasuk(Carbon $now)
    {
        $jamMasuk = Carbon::createFromTimeString($this->setting->check_in_setting);
        $status = $now->gt($jamMasuk) ? 'terlambat' : 'tepat waktu';
        $desc = $status === 'terlambat' ? 'Terlambat ' . $jamMasuk->diffForHumans($now, ['parts' => 1, 'syntax' => Carbon::DIFF_ABSOLUTE]) : null;

        Attendance::create([
            'employee_id' => $this->employeeId,
            'attendance_date' => now(),
            'attendance_type' => 'masuk',
            'status' => $status,
            'description' => $desc
        ]);
    }

    // logic untuk pulang (button check out)
    protected function doPulang(Carbon $now)
    {
        $jamPulang = Carbon::createFromTimeString($this->setting->check_out_setting);
        $batasLembur = $jamPulang->copy()->addMinutes($this->setting->overtime_tolerance);

        $status = match (true) {
            $now->lt($jamPulang) => 'pulang cepat',
            $now->between($jamPulang, $batasLembur) => 'akhir shift',
            $now->gt($batasLembur) => 'lembur',
            default => 'tidak absen'
        };

        $desc = $status === 'lembur' ? 'Lembur ' . $batasLembur->diffForHumans($now, ['parts' => 1, 'syntax' => Carbon::DIFF_ABSOLUTE]) : null;

        Attendance::create([
            'employee_id' => $this->employeeId,
            'attendance_date' => now(),
            'attendance_type' => 'pulang',
            'status' => $status,
            'description' => $desc
        ]);
    }

    protected function handleYesterdayCheckout()
    {
        $yesterday = now()->subDay();

        $hasMasuk = Attendance::where('employee_id', $this->employeeId)
            ->whereDate('attendance_date', $yesterday)
            ->where('attendance_type', 'masuk')
            ->exists();

        $hasPulang = Attendance::where('employee_id', $this->employeeId)
            ->whereDate('attendance_date', $yesterday)
            ->where('attendance_type', 'pulang')
            ->exists();

        // jika ada presensi masuk namun pulang tidak
        if ($hasMasuk && !$hasPulang) {
            Attendance::create([
                'employee_id' => $this->employeeId,
                'attendance_date' => $yesterday,
                'attendance_type' => 'pulang',
                'status' => 'tidak absen',
                'description' => 'Tidak melakukan presensi kepulangan'
            ]);
        }
    }
}
