<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class UpdateAttendanceStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:update-attendance-status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update attendance status for employees who have not checked in or out';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $today = now()->toDateString();
        $now = now();

        // find employees who have not checked in today
        $employees = DB::table('employees')
            ->leftJoin('attendances', function ($join) use ($today) {
                $join->on('employees.id', '=', 'attendances.employee_id')
                    ->whereDate('attendances.attendance_date', $today);
            })
            ->whereNull('attendances.id')
            ->select('employees.id')
            ->get();

        // mark them as 'tidak_hadir'
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
            DB::table('attendances')->insert($data);
        }

        // mark employees who checked in but not checked out as 'tidak_lengkap'
        DB::table('attendances')
            ->whereDate('attendance_date', $today)
            ->whereNotNull('check_in')
            ->whereNull('check_out')
            ->update(['status' => 'tidak_lengkap']);
    }
}
