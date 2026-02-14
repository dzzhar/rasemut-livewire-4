<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property \Carbon\Carbon|null $attendance_date
 * @property string|null $status
 * @property string|null $description
 */
class Attendance extends Model
{
    protected $table = 'attendances';
    protected $fillable = [
        'attendance_date',
        'attendance_type',
        'status',
        'description',
        'employee_id'
    ];
    protected $casts = [
        'attendance_date' => 'datetime'
    ];

    public function getHistoryTypeAttribute()
    {
        return 'Absensi ' . $this->attendance_type;
    }

    public function getHistoryDateAttribute()
    {
        return $this->attendance_date;
    }

    public function getBadgeColorAttribute(): string
    {
        return match ($this->status) {
            'tepat waktu' => 'green',
            'terlambat' => 'yellow',
            'akhir shift' => 'green',
            'pulang cepat' => 'orange',
            'lembur' => 'blue',
            'tidak absen' => 'red',
        };
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }
}
