<?php

namespace App\Models;

use App\Enums\AttendanceStatus;
use App\Enums\CorrectionRequestStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Attendance extends Model
{
    protected $fillable = [
        'user_id',
        'work_date',
        'clock_in_at',
        'clock_out_at',
        'status',
        'note',
    ];

    protected function casts(): array
    {
        return [
            'work_date' => 'date',
            'clock_in_at' => 'datetime',
            'clock_out_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function breaks(): HasMany
    {
        return $this->hasMany(AttendanceBreak::class);
    }

    public function correctionRequests(): HasMany
    {
        return $this->hasMany(StampCorrectionRequest::class);
    }

    public function pendingCorrectionRequest(): ?StampCorrectionRequest
    {
        return $this->correctionRequests()
            ->where('status', CorrectionRequestStatus::Pending->value)
            ->latest()
            ->first();
    }

    public function statusEnum(): AttendanceStatus
    {
        return AttendanceStatus::from($this->status);
    }
}
