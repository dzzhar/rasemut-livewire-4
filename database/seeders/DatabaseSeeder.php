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
        Position::factory(5)->create();
        AttendanceSetting::factory()->create();

        // =====================
        // ADMIN
        // =====================
        $admin = $this->createEmployeeUser(
            'John Doe',
            'EMP001',
            'coba@gmail.com',
            'admin'
        );

        // =====================
        // EMPLOYEE
        // =====================
        $employee = $this->createEmployeeUser(
            'Cucut',
            'EMP002',
            'cucut@gmail.com',
            'employee'
        );

        // Attendance
        $this->generateAttendance($admin);
        $this->generateAttendance($employee);

        // Permission & Leave hanya employee
        Permission::factory(10)->create([
            'employee_id' => $employee->id,
        ]);

        Leave::factory(5)->create([
            'employee_id' => $employee->id,
        ]);
    }

    private function createEmployeeUser($name, $code, $email, $role)
    {
        $user = User::factory()->create([
            'email' => $email,
            'role' => $role,
            'password' => Hash::make('password'),
        ]);

        return Employee::factory()->create([
            'user_id' => $user->id,
            'fullname' => $name,
            'employee_code' => $code,
            'is_active' => true,
            'position_id' => Position::inRandomOrder()->first()->id,
        ]);
    }

    private function generateAttendance($employee)
    {
        $startOfMonth = now()->startOfMonth();

        for ($i = 0; $i < 30; $i++) {
            $date = $startOfMonth->copy()->addDays($i);

            Attendance::factory()->create([
                'employee_id' => $employee->id,
                'attendance_date' => $date->toDateString(),
            ]);
        }
    }
}
