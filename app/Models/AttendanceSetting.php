<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttendanceSetting extends Model
{
    use HasFactory;

    protected $table = 'attendance_settings';
    protected $fillable = [
        'check_in_setting',
        'check_out_setting',
        'leave_quota',
        'overtime_tolerance',
        'latitude',
        'longitude',
        'radius_attendance'
    ];

    protected $attributes = [
        'latitude' => -6.175392,
        'longitude' => 106.794952,
        'radius_attendance' => 50,
        'check_in_setting' => '08:00:00',
        'check_out_setting' => '17:00:00',
        'overtime_tolerance' => 5,
    ];

    protected function setting(): AttendanceSetting
    {
        return AttendanceSetting::firstOrFail();
    }
}
