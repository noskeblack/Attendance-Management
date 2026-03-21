<?php

namespace App\Models;

use App\Enums\AttendanceStatus;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'is_admin',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_admin' => 'boolean',
        ];
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }

    public function stampCorrectionRequests(): HasMany
    {
        return $this->hasMany(StampCorrectionRequest::class);
    }

    public function todayAttendance(): ?Attendance
    {
        $today = now()->toDateString();

        return $this->attendances()->whereDate('work_date', $today)->first();
    }

    /**
     * 打刻画面用の表示ステータス（DBのレコードが無い場合は勤務外）
     */
    public function currentAttendanceStatus(): AttendanceStatus
    {
        $att = $this->todayAttendance();
        if (! $att || ! $att->clock_in_at) {
            return AttendanceStatus::OffDuty;
        }

        return AttendanceStatus::from($att->status);
    }
}
