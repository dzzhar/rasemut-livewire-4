<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttendanceSetting extends Model
{
    use HasFactory;

    protected $table = 'attendance_settings';
    protected $fillable = [
        'location',
        'check_in_setting',
        'check_out_setting',
        'leave_quota',
        'overtime_tolerance',
        'latitude',
        'longitude',
        'radius_attendance'
    ];
    public $timestamps = false;

    protected function setting(): AttendanceSetting
    {
        return AttendanceSetting::firstOrFail();
    }
}
