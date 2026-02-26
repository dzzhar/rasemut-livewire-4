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
        $specialUser = User::factory()->create([
            'email' => 'coba@gmail.com',
            'password' => Hash::make('password'),
        ]);

        $employee = Employee::factory()->create([
            'user_id' => $specialUser->id,
        ]);

        Position::factory(5)->create();
        AttendanceSetting::factory()->create();

        $startOfMonth = now()->startOfMonth();

        for ($i = 0; $i < 500; $i++) {

            $tanggal = $startOfMonth->copy()
                ->addDays(rand(0, 29))
                ->setTime(rand(7, 9), rand(0, 59));

            Attendance::factory()->create([
                'employee_id' => $employee->id,
                'attendance_date' => $tanggal,
                'attendance_type' => 'masuk',
            ]);

            $tanggalPulang = $tanggal->copy()
                ->setTime(rand(16, 19), rand(0, 59));

            Attendance::factory()->create([
                'employee_id' => $employee->id,
                'attendance_date' => $tanggalPulang,
                'attendance_type' => 'pulang',
            ]);
        }

        Permission::factory(500)->create([
            'employee_id' => $employee->id,
        ]);

        Leave::factory(500)->create([
            'employee_id' => $employee->id,
        ]);
    }
}
