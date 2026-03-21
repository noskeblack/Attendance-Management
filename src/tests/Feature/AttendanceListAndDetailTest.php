<?php

namespace Tests\Feature;

use App\Models\Attendance;
use App\Models\AttendanceBreak;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AttendanceListAndDetailTest extends TestCase
{
    use RefreshDatabase;

    public function test_list_shows_only_own_attendance_and_supports_month_navigation_and_detail_link(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-03-15 10:00:00'));

        $user = User::factory()->create([
            'name' => '対象ユーザー',
            'email_verified_at' => now(),
            'is_admin' => false,
        ]);

        $other = User::factory()->create([
            'email_verified_at' => now(),
            'is_admin' => false,
        ]);

        $prevMonthDate = Carbon::parse('2026-03-15')->subMonth()->startOfMonth()->addDay();
        $nextMonthDate = Carbon::parse('2026-03-15')->addMonth()->startOfMonth()->addDay();

        $a1 = Attendance::create([
            'user_id' => $user->id,
            'work_date' => '2026-03-10',
            'clock_in_at' => Carbon::parse('2026-03-10 09:00:00'),
            'clock_out_at' => Carbon::parse('2026-03-10 18:00:00'),
            'status' => 'completed',
            'note' => null,
        ]);

        Attendance::create([
            'user_id' => $other->id,
            'work_date' => '2026-03-10',
            'clock_in_at' => Carbon::parse('2026-03-10 10:00:00'),
            'clock_out_at' => Carbon::parse('2026-03-10 19:00:00'),
            'status' => 'completed',
            'note' => null,
        ]);

        Attendance::create([
            'user_id' => $user->id,
            'work_date' => $prevMonthDate->toDateString(),
            'clock_in_at' => $prevMonthDate->copy()->setTime(9, 0),
            'clock_out_at' => $prevMonthDate->copy()->setTime(18, 0),
            'status' => 'completed',
            'note' => null,
        ]);

        Attendance::create([
            'user_id' => $user->id,
            'work_date' => $nextMonthDate->toDateString(),
            'clock_in_at' => $nextMonthDate->copy()->setTime(9, 0),
            'clock_out_at' => $nextMonthDate->copy()->setTime(18, 0),
            'status' => 'completed',
            'note' => null,
        ]);

        $list = $this->actingAs($user)
            ->get('/attendance/list')
            ->assertOk()
            ->assertSee('2026年3月')
            ->assertSee('09:00')
            ->assertDontSee('10:00');

        $detailUrl = route('attendance.detail', $a1);
        $list->assertSee('href="'.$detailUrl.'"', false);

        $this->actingAs($user)
            ->get('/attendance/list?year='.$prevMonthDate->year.'&month='.$prevMonthDate->month)
            ->assertOk()
            ->assertSee($prevMonthDate->year.'年'.$prevMonthDate->month.'月');

        $this->actingAs($user)
            ->get('/attendance/list?year='.$nextMonthDate->year.'&month='.$nextMonthDate->month)
            ->assertOk()
            ->assertSee($nextMonthDate->year.'年'.$nextMonthDate->month.'月');

        $this->actingAs($user)
            ->get('/attendance/detail/'.$a1->id)
            ->assertOk()
            ->assertSee('対象ユーザー')
            ->assertSee($a1->work_date->locale('ja')->isoFormat('YYYY年M月D日(ddd)'), false)
            ->assertSee('value="09:00"', false)
            ->assertSee('value="18:00"', false);
    }

    public function test_detail_shows_break_times_matching_records(): void
    {
        $user = User::factory()->create([
            'email_verified_at' => now(),
            'is_admin' => false,
        ]);

        $attendance = Attendance::create([
            'user_id' => $user->id,
            'work_date' => '2026-04-01',
            'clock_in_at' => Carbon::parse('2026-04-01 09:00:00'),
            'clock_out_at' => Carbon::parse('2026-04-01 18:00:00'),
            'status' => 'completed',
            'note' => null,
        ]);

        AttendanceBreak::create([
            'attendance_id' => $attendance->id,
            'break_start_at' => Carbon::parse('2026-04-01 12:00:00'),
            'break_end_at' => Carbon::parse('2026-04-01 13:00:00'),
        ]);

        $this->actingAs($user)
            ->get('/attendance/detail/'.$attendance->id)
            ->assertOk()
            ->assertSee('value="12:00"', false)
            ->assertSee('value="13:00"', false);
    }
}
