<?php

namespace Tests\Feature;

use App\Models\Attendance;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminDailyAttendanceNavigationTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_daily_attendance_list(): void
    {
        $admin = User::factory()->admin()->create([
            'email_verified_at' => now(),
        ]);
        $staff = User::factory()->create([
            'email_verified_at' => now(),
            'is_admin' => false,
        ]);

        Attendance::create([
            'user_id' => $staff->id,
            'work_date' => now()->toDateString(),
            'clock_in_at' => now()->startOfDay()->addHours(9),
            'clock_out_at' => now()->startOfDay()->addHours(18),
            'status' => 'completed',
            'note' => null,
        ]);

        $this->actingAs($admin)
            ->get('/admin/attendance/list')
            ->assertOk()
            ->assertSee('日次勤怠一覧');
    }

    public function test_admin_can_move_previous_and_next_day(): void
    {
        $admin = User::factory()->admin()->create([
            'email_verified_at' => now(),
        ]);

        $yesterday = now()->subDay()->toDateString();
        $tomorrow = now()->addDay()->toDateString();

        $this->actingAs($admin)
            ->get('/admin/attendance/list?date='.$yesterday)
            ->assertOk();

        $this->actingAs($admin)
            ->get('/admin/attendance/list?date='.$tomorrow)
            ->assertOk();
    }
}
