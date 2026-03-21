<?php

namespace Database\Seeders;

use App\Enums\AttendanceStatus;
use App\Models\Attendance;
use App\Models\AttendanceBreak;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $generalUser = User::query()->updateOrCreate(
            ['email' => 'user@example.com'],
            [
                'name' => '一般ユーザー',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'is_admin' => false,
            ]
        );

        User::query()->updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => '管理者',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'is_admin' => true,
            ]
        );

        $today = now()->startOfDay();

        // 開発プロセス要件: 出勤・退勤・休憩を含む勤怠ダミーデータ
        $attendance = Attendance::query()->updateOrCreate(
            [
                'user_id' => $generalUser->id,
                'work_date' => $today->toDateString(),
            ],
            [
                'clock_in_at' => $today->copy()->addHours(9),
                'clock_out_at' => $today->copy()->addHours(18),
                'status' => AttendanceStatus::Completed->value,
                'note' => 'Seeder generated attendance record',
            ]
        );

        AttendanceBreak::query()->updateOrCreate(
            [
                'attendance_id' => $attendance->id,
                'break_start_at' => $today->copy()->addHours(12),
            ],
            [
                'break_end_at' => $today->copy()->addHours(13),
            ]
        );
    }
}
