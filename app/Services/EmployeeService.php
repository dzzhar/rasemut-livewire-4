<?php

namespace App\Services;

use App\Models\Employee;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class EmployeeService
{
    // store data user + employee
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
                'employee_code' => $data['employee_code'],
                'position_id' => $data['position_id']
            ]);
        });
    }

    // update data user + employee
    public function update(Employee $employee, array $data): Employee
    {
        return DB::transaction(function () use ($employee, $data) {
            $user = [
                'email' => $data['user_email'],
                'role' => $data['user_role'],
            ];

            // the input field is filled by password
            if (!empty($data['user_password'])) {
                $user['password'] = Hash::make($data['user_password']);
            }

            $employee->user->update($user);
            $employee->update([
                'fullname' => $data['fullname'],
                'employee_code' => $data['employee_code'],
                'position_id' => $data['position_id'],
            ]);
            return $employee;
        });
    }

    // delete data user + employee
    public function delete(Employee $employee): void
    {
        DB::transaction(function () use ($employee) {
            $employee->user()->delete();
            $employee->delete();
        });
    }
}
