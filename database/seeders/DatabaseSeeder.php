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
        // =========================
        // MASTER DATA
        // =========================
        Position::factory(5)->create();
        AttendanceSetting::factory()->create();

        // =========================
        // USERS
        // =========================
        $admin = $this->createEmployeeUser(
            'Admin Satu',
            'admin@gmail.com',
            'admin'
        );

        $employee1 = $this->createEmployeeUser(
            'Karyawan Satu',
            'employee@gmail.com',
            'employee'
        );

        $employee2 = $this->createEmployeeUser(
            'Karyawan Dua',
            'employee2@gmail.com',
            'employee'
        );

        // =========================
        // ATTENDANCE (APRIL)
        // =========================
        $this->generateAttendance($admin, 4);
        $this->generateAttendance($employee1, 4);
        $this->generateAttendance($employee2, 4);

        // =========================
        // EMPLOYEE 1 (SEDIKIT DATA)
        // =========================
        Permission::factory()->count(10)->create([
            'employee_id' => $employee1->id,
        ]);

        Leave::factory()->count(5)->create([
            'employee_id' => $employee1->id,
        ]);


        Permission::factory()->count(800)->create([
            'employee_id' => $employee2->id,
        ]);

        Leave::factory()->count(800)->create([
            'employee_id' => $employee2->id,
        ]);
    }

    private function createEmployeeUser($name, $email, $role)
    {
        $user = User::factory()->create([
            'email' => $email,
            'role' => $role,
            'password' => Hash::make('password'),
        ]);

        return Employee::factory()->create([
            'user_id' => $user->id,
            'fullname' => $name,
            'is_active' => true,
            'position_id' => Position::inRandomOrder()->first()->id,
        ]);
    }

    private function generateAttendance($employee, $month = 4)
    {
        $startOfMonth = now()->setMonth($month)->startOfMonth();
        $daysInMonth = $startOfMonth->daysInMonth;

        for ($i = 0; $i < $daysInMonth; $i++) {
            $date = $startOfMonth->copy()->addDays($i);

            Attendance::factory()->create([
                'employee_id' => $employee->id,
                'attendance_date' => $date->toDateString(),
            ]);
        }
    }
}
