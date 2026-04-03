<?php

namespace App\Models;

use App\Enums\AttendanceStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Attendance extends Model
{
    use HasFactory;

    protected $table = 'attendances';
    protected $fillable = [
        'attendance_date',
        'check_in',
        'check_out',
        'late_minutes',
        'overtime_minutes',
        'early_leave_minutes',
        'status',
        'description',
        'employee_id'
    ];
    protected $casts = [
        'attendance_date' => 'date',
        'status' => AttendanceStatus::class
    ];

    // history type and date for history log
    public function getHistoryTypeAttribute()
    {
        return $this->check_out !== null
            ? 'Check Out'
            : 'Check In';
    }

    public function getHistoryDateAttribute()
    {
        return $this->attendance_date;
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }
}
