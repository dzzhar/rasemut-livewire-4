<?php

namespace Database\Seeders;

use App\Models\Attendance;
use App\Models\AttendanceSetting;
use App\Models\Employee;
use App\Models\Leave;
use App\Models\Permission;
use App\Models\Position;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        Position::factory(1)->create();
        AttendanceSetting::factory()->create();

        $this->createEmployeeUser('Administrator', 'admin@gmail.com', ['employee', 'admin']);
        $this->createEmployeeUser('Karyawan', 'karyawan@gmail.com', 'employee');
    }

    private function createEmployeeUser($name, $email, array|string $roles)
    {
        $user = User::factory()->create([
            'email' => $email,
            'roles' => is_array($roles) ? $roles : [$roles],
            'is_active' => true,
            'last_activity' => now(),
            'password' => Hash::make('password'),
        ]);

        return Employee::factory()->create([
            'user_id' => $user->id,
            'fullname' => $name,
            'position_id' => Position::inRandomOrder()->first()->id,
        ]);
    }

    private function generateFullMonthData($employee, $month)
    {
        $date = now()->setMonth($month)->startOfMonth();
        $daysInMonth = $date->daysInMonth;

        for ($i = 0; $i < $daysInMonth; $i++) {
            if (!$date->isWeekend()) {
                if ($date->day <= 15) {
                    Attendance::factory()->create([
                        'employee_id' => $employee->id,
                        'attendance_date' => $date->toDateString(),
                        'check_in' => $date->copy()->setTime(8, 0, 0),
                        'check_out' => $date->copy()->setTime(17, 0, 0),
                        'status' => 'hadir',
                    ]);
                } elseif ($date->day <= 20) {
                    Permission::factory()->create([
                        'employee_id' => $employee->id,
                        'permission_date' => $date->toDateString(),
                        'permission_type' => 'sakit',
                        'description' => 'Izin keperluan keluarga',
                        'status' => 'izin',
                    ]);
                } else {
                    Leave::factory()->create([
                        'employee_id' => $employee->id,
                        'request_date' => $date->toDateString(),
                        'leave_code' => 'CT',
                        'start_date' => $date->toDateString(),
                        'end_date' => $date->toDateString(),
                        'status' => 'disetujui',
                        'description' => 'Cuti Tahunan',
                    ]);
                }
            }
            $date->addDay();
        }
    }
}
