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
        'attendance_type',
        'status',
        'description',
        'employee_id'
    ];
    protected $casts = [
        'attendance_date' => 'datetime',
        'status' => AttendanceStatus::class
    ];

    public function getHistoryTypeAttribute()
    {
        return 'Absensi ' . $this->attendance_type;
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
