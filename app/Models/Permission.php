<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Permission extends Model
{
    protected $table = 'permissions';
    protected $fillable = [
        'permission_date',
        'permission_type',
        'status',
        'description',
        'employee_id'
    ];
    protected $casts = [
        'permission_date' => 'datetime',
    ];

    public function getHistoryTypeAttribute()
    {
        return $this->permission_type;
    }

    public function getHistoryDateAttribute()
    {
        return $this->permission_date;
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }
}
