<?php

namespace App\Services;

use App\Enums\AttendanceStatus;
use App\Models\Attendance;
use App\Models\AttendanceBreak;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class AttendanceService
{
    public function todayAttendance(User $user): ?Attendance
    {
        return $user->todayAttendance();
    }

    public function clockIn(User $user): Attendance
    {
        return DB::transaction(function () use ($user) {
            $today = Carbon::today();

            if ($user->currentAttendanceStatus() !== AttendanceStatus::OffDuty) {
                throw new RuntimeException('出勤できません');
            }

            $attendance = Attendance::create([
                'user_id' => $user->id,
                'work_date' => $today->toDateString(),
                'clock_in_at' => now(),
                'clock_out_at' => null,
                'status' => AttendanceStatus::Working->value,
                'note' => null,
            ]);

            return $attendance;
        });
    }

    public function clockOut(User $user): Attendance
    {
        return DB::transaction(function () use ($user) {
            $attendance = $this->requireTodayAttendance($user);

            if ($attendance->statusEnum() !== AttendanceStatus::Working) {
                throw new RuntimeException('退勤できません');
            }

            $attendance->update([
                'clock_out_at' => now(),
                'status' => AttendanceStatus::Completed->value,
            ]);

            return $attendance->fresh();
        });
    }

    public function breakStart(User $user): Attendance
    {
        return DB::transaction(function () use ($user) {
            $attendance = $this->requireTodayAttendance($user);

            if ($attendance->statusEnum() !== AttendanceStatus::Working) {
                throw new RuntimeException('休憩に入れません');
            }

            AttendanceBreak::create([
                'attendance_id' => $attendance->id,
                'break_start_at' => now(),
                'break_end_at' => null,
            ]);

            $attendance->update([
                'status' => AttendanceStatus::OnBreak->value,
            ]);

            return $attendance->fresh();
        });
    }

    public function breakEnd(User $user): Attendance
    {
        return DB::transaction(function () use ($user) {
            $attendance = $this->requireTodayAttendance($user);

            if ($attendance->statusEnum() !== AttendanceStatus::OnBreak) {
                throw new RuntimeException('休憩から戻れません');
            }

            $open = $attendance->breaks()
                ->whereNull('break_end_at')
                ->latest('break_start_at')
                ->first();

            if (! $open) {
                throw new RuntimeException('休憩レコードがありません');
            }

            $open->update(['break_end_at' => now()]);

            $attendance->update([
                'status' => AttendanceStatus::Working->value,
            ]);

            return $attendance->fresh();
        });
    }

    protected function requireTodayAttendance(User $user): Attendance
    {
        $attendance = $this->todayAttendance($user);

        if (! $attendance) {
            throw new RuntimeException('勤怠がありません');
        }

        return $attendance;
    }
}
