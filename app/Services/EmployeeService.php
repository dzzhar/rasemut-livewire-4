<?php

namespace App\Services;

use App\Models\AttendanceSetting;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class EmployeeService
{
    // store data user n employee
    public function create(array $data): Employee
    {
        return DB::transaction(function () use ($data) {
            $user = User::create([
                'email' => $data['user_email'],
                'password' => bcrypt($data['user_password']),
                'role' => $data['user_role']
            ]);

            return Employee::create([
                'user_id' => $user->id,
                'fullname' => $data['fullname'],
                'position_id' => $data['position_id'],
                'leave_remaining' => AttendanceSetting::first()->leave_quota
            ]);
        });
    }

    // update data user n employee
    public function update(Employee $employee, array $data): Employee
    {
        return DB::transaction(function () use ($employee, $data) {
            // lock employee row
            $employee = Employee::where('id', $employee->id)
                ->lockForUpdate()
                ->first();

            // lock user row
            $userModel = User::where('id', $employee->user_id)
                ->lockForUpdate()
                ->first();

            $user = [
                'email' => $data['user_email'],
                'role' => $data['user_role'],
            ];

            if (!empty($data['user_password'])) {
                $user['password'] = Hash::make($data['user_password']);
            }

            $userModel->update($user);

            $employee->update([
                'fullname' => $data['fullname'],
                'position_id' => $data['position_id'],
            ]);

            return $employee;
        });
    }

    // delete data user n employee
    public function delete(Employee $employee): void
    {
        DB::transaction(function () use ($employee) {
            // lock employee row
            $employee = Employee::where('id', $employee->id)
                ->lockForUpdate()
                ->first();

            // lock user row
            $user = User::where('id', $employee->user_id)
                ->lockForUpdate()
                ->first();

            $user->delete();
            $employee->delete();
        });
    }
}
