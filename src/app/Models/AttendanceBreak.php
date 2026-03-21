<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AttendanceBreak extends Model
{
    protected $fillable = [
        'attendance_id',
        'break_start_at',
        'break_end_at',
    ];

    protected function casts(): array
    {
        return [
            'break_start_at' => 'datetime',
            'break_end_at' => 'datetime',
        ];
    }

    public function attendance(): BelongsTo
    {
        return $this->belongsTo(Attendance::class);
    }
}
