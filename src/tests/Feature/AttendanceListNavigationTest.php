<?php

namespace Tests\Feature;

use App\Models\Attendance;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AttendanceListNavigationTest extends TestCase
{
    use RefreshDatabase;

    public function test_attendance_list_shows_current_month(): void
    {
        $user = User::factory()->create([
            'email_verified_at' => now(),
            'is_admin' => false,
        ]);

        Attendance::create([
            'user_id' => $user->id,
            'work_date' => now()->toDateString(),
            'clock_in_at' => now()->startOfDay()->addHours(9),
            'clock_out_at' => now()->startOfDay()->addHours(18),
            'status' => 'completed',
            'note' => null,
        ]);

        $this->actingAs($user)
            ->get('/attendance/list')
            ->assertOk()
            ->assertSee('勤怠一覧');
    }

    public function test_attendance_list_can_move_previous_and_next_month(): void
    {
        $user = User::factory()->create([
            'email_verified_at' => now(),
            'is_admin' => false,
        ]);

        $base = now()->startOfMonth();
        $prev = $base->copy()->subMonth();
        $next = $base->copy()->addMonth();

        Attendance::create([
            'user_id' => $user->id,
            'work_date' => $prev->copy()->addDay()->toDateString(),
            'clock_in_at' => $prev->copy()->addDay()->setTime(9, 0),
            'clock_out_at' => $prev->copy()->addDay()->setTime(18, 0),
            'status' => 'completed',
            'note' => null,
        ]);

        Attendance::create([
            'user_id' => $user->id,
            'work_date' => $next->copy()->addDay()->toDateString(),
            'clock_in_at' => $next->copy()->addDay()->setTime(9, 0),
            'clock_out_at' => $next->copy()->addDay()->setTime(18, 0),
            'status' => 'completed',
            'note' => null,
        ]);

        $this->actingAs($user)
            ->get('/attendance/list?year='.$prev->year.'&month='.$prev->month)
            ->assertOk()
            ->assertSee('勤怠一覧');

        $this->actingAs($user)
            ->get('/attendance/list?year='.$next->year.'&month='.$next->month)
            ->assertOk()
            ->assertSee('勤怠一覧');
    }
}
