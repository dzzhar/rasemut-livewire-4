<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class UpdateAttendanceStatus extends Command
{
    protected $signature = 'app:update-attendance-status';
    protected $description = 'Update attendance status for employees who have not checked in or out';

    public function handle()
    {
        $today = Carbon::today()->toDateString();
        $now = Carbon::now();

        DB::transaction(function () use ($today, $now) {
            // find employees who have not checked in today
            $employees = DB::table('employees')
                ->leftJoin('attendances', function ($join) use ($today) {
                    $join->on('employees.id', '=', 'attendances.employee_id')
                        ->whereDate('attendances.attendance_date', $today);
                })
                ->leftJoin('permissions', function ($join) use ($today) {
                    $join->on('employees.id', '=', 'permissions.employee_id')
                        ->whereDate('permissions.permission_date', $today);
                })
                ->leftJoin('leaves', function ($join) use ($today) {
                    $join->on('employees.id', '=', 'leaves.employee_id')
                        ->where('leaves.status', 'disetujui')
                        ->whereDate('leaves.start_date', '<=', $today)
                        ->whereDate('leaves.end_date', '>=', $today);
                })
                ->whereNull('attendances.id')
                ->whereNull('permissions.id')
                ->whereNull('leaves.id')
                ->select('employees.id')
                ->get();

            // mark as 'tidak_hadir'
            $data = $employees->map(function ($emp) use ($today, $now) {
                return [
                    'employee_id'     => $emp->id,
                    'attendance_date' => $today,
                    'status'          => 'tidak_hadir',
                    'created_at'      => $now,
                    'updated_at'      => $now,
                ];
            })->toArray();

            if (!empty($data)) {
                DB::table('attendances')->insertOrIgnore($data);
            }

            // mark incomplete (tidak_lengkap) attendance
            DB::table('attendances')
                ->whereDate('attendance_date', $today)
                ->whereNotNull('check_in')
                ->whereNull('check_out')
                ->update(['status' => 'tidak_lengkap']);
        });

        $this->info('Attendance status updated successfully.');
        return Command::SUCCESS;
    }
}
