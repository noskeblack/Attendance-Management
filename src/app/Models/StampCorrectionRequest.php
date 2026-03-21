<?php

namespace App\Models;

use App\Enums\CorrectionRequestStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StampCorrectionRequest extends Model
{
    protected $fillable = [
        'user_id',
        'attendance_id',
        'status',
        'remark',
        'requested_clock_in_at',
        'requested_clock_out_at',
        'requested_breaks',
        'approved_at',
    ];

    protected function casts(): array
    {
        return [
            'requested_clock_in_at' => 'datetime',
            'requested_clock_out_at' => 'datetime',
            'requested_breaks' => 'array',
            'approved_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function attendance(): BelongsTo
    {
        return $this->belongsTo(Attendance::class);
    }

    public function isPending(): bool
    {
        return $this->status === CorrectionRequestStatus::Pending->value;
    }
}
