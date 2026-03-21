<?php

namespace Tests\Feature;

use App\Models\Attendance;
use App\Models\AttendanceBreak;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AttendanceScreenTest extends TestCase
{
    use RefreshDatabase;

    public function test_attendance_screen_shows_current_datetime_in_expected_format(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-03-15 13:05:00'));

        $user = User::factory()->create([
            'email_verified_at' => now(),
            'is_admin' => false,
        ]);

        $this->actingAs($user)
            ->get('/attendance')
            ->assertOk()
            ->assertSee('現在の日時：2026年3月15日(日) 13:05');
    }

    public function test_attendance_screen_shows_status_labels(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-03-15 10:00:00'));

        $user = User::factory()->create([
            'email_verified_at' => now(),
            'is_admin' => false,
        ]);

        $this->actingAs($user)
            ->get('/attendance')
            ->assertOk()
            ->assertSee('ステータス：', false)
            ->assertSee('勤務外');

        Attendance::create([
            'user_id' => $user->id,
            'work_date' => '2026-03-15',
            'clock_in_at' => Carbon::parse('2026-03-15 09:00:00'),
            'clock_out_at' => null,
            'status' => 'working',
            'note' => null,
        ]);

        $this->actingAs($user)
            ->get('/attendance')
            ->assertOk()
            ->assertSee('出勤中');

        Attendance::query()->where('user_id', $user->id)->delete();

        $attendance = Attendance::create([
            'user_id' => $user->id,
            'work_date' => '2026-03-15',
            'clock_in_at' => Carbon::parse('2026-03-15 09:00:00'),
            'clock_out_at' => null,
            'status' => 'on_break',
            'note' => null,
        ]);

        AttendanceBreak::create([
            'attendance_id' => $attendance->id,
            'break_start_at' => Carbon::parse('2026-03-15 12:00:00'),
            'break_end_at' => null,
        ]);

        $this->actingAs($user)
            ->get('/attendance')
            ->assertOk()
            ->assertSee('休憩中');

        Attendance::query()->where('user_id', $user->id)->delete();

        Attendance::create([
            'user_id' => $user->id,
            'work_date' => '2026-03-15',
            'clock_in_at' => Carbon::parse('2026-03-15 09:00:00'),
            'clock_out_at' => Carbon::parse('2026-03-15 18:00:00'),
            'status' => 'completed',
            'note' => null,
        ]);

        $this->actingAs($user)
            ->get('/attendance')
            ->assertOk()
            ->assertSee('退勤済');
    }
}
