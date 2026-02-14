<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Leave extends Model
{
    protected $table = 'leaves';
    protected $fillable = [
        'request_date',
        'leave_code',
        'start_date',
        'end_date',
        'status',
        'description',
        'employee_id',
    ];
    protected $casts = [
        'request_date' => 'datetime',
        'start_date' => 'datetime',
        'end_date' => 'datetime'
    ];

    public function getHistoryTypeAttribute()
    {
        return $this->leave_code;
    }

    public function getHistoryDateAttribute()
    {
        return $this->request_date;
    }

    public function getBadgeColorAttribute(): string
    {
        return match ($this->status) {
            'pending' => 'yellow',
            'disetujui' => 'green',
            'ditolak' => 'red',
        };
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }
}
