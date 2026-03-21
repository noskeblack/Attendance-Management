<?php

namespace Tests\Feature;

use App\Models\Attendance;
use App\Models\AttendanceBreak;
use App\Models\StampCorrectionRequest;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminScreensTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_daily_list_shows_all_users_and_accurate_times_and_date_navigation(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-06-01 12:00:00'));

        $admin = User::factory()->admin()->create([
            'email_verified_at' => now(),
        ]);

        $u1 = User::factory()->create(['name' => 'スタッフA', 'email_verified_at' => now(), 'is_admin' => false]);
        $u2 = User::factory()->create(['name' => 'スタッフB', 'email_verified_at' => now(), 'is_admin' => false]);

        $a1 = Attendance::create([
            'user_id' => $u1->id,
            'work_date' => '2026-06-01',
            'clock_in_at' => Carbon::parse('2026-06-01 09:00:00'),
            'clock_out_at' => Carbon::parse('2026-06-01 18:00:00'),
            'status' => 'completed',
            'note' => null,
        ]);

        AttendanceBreak::create([
            'attendance_id' => $a1->id,
            'break_start_at' => Carbon::parse('2026-06-01 12:00:00'),
            'break_end_at' => Carbon::parse('2026-06-01 13:00:00'),
        ]);

        Attendance::create([
            'user_id' => $u2->id,
            'work_date' => '2026-06-01',
            'clock_in_at' => Carbon::parse('2026-06-01 10:15:00'),
            'clock_out_at' => Carbon::parse('2026-06-01 19:30:00'),
            'status' => 'completed',
            'note' => null,
        ]);

        $this->actingAs($admin)
            ->get('/admin/attendance/list')
            ->assertOk()
            ->assertSee('2026年6月1日', false)
            ->assertSee('スタッフA')
            ->assertSee('スタッフB')
            ->assertSee('09:00')
            ->assertSee('10:15')
            ->assertSee('12:00-13:00');

        $yesterday = Carbon::parse('2026-06-01')->subDay()->toDateString();
        $tomorrow = Carbon::parse('2026-06-01')->addDay()->toDateString();

        $this->actingAs($admin)
            ->get('/admin/attendance/list?date='.$yesterday)
            ->assertOk();

        $this->actingAs($admin)
            ->get('/admin/attendance/list?date='.$tomorrow)
            ->assertOk();
    }

    public function test_admin_attendance_detail_shows_selected_user_and_date(): void
    {
        $admin = User::factory()->admin()->create([
            'email_verified_at' => now(),
        ]);

        $staff = User::factory()->create([
            'name' => '対象スタッフ',
            'email_verified_at' => now(),
            'is_admin' => false,
        ]);

        $attendance = Attendance::create([
            'user_id' => $staff->id,
            'work_date' => '2026-07-10',
            'clock_in_at' => Carbon::parse('2026-07-10 09:00:00'),
            'clock_out_at' => Carbon::parse('2026-07-10 18:00:00'),
            'status' => 'completed',
            'note' => 'メモ',
        ]);

        $this->actingAs($admin)
            ->get('/admin/attendance/'.$attendance->id)
            ->assertOk()
            ->assertSee('対象スタッフ')
            ->assertSee($attendance->work_date->locale('ja')->isoFormat('YYYY年M月D日(ddd)'), false);
    }

    public function test_admin_staff_list_shows_names_and_emails_and_monthly_navigation(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-08-10 10:00:00'));

        $admin = User::factory()->admin()->create([
            'email_verified_at' => now(),
        ]);

        $staff = User::factory()->create([
            'name' => '一覧スタッフ',
            'email' => 'staff_list@example.com',
            'email_verified_at' => now(),
            'is_admin' => false,
        ]);

        $prevMonthDate = Carbon::parse('2026-08-10')->subMonth()->startOfMonth()->addDay();
        $nextMonthDate = Carbon::parse('2026-08-10')->addMonth()->startOfMonth()->addDay();

        $staffPrevAttendance = Attendance::create([
            'user_id' => $staff->id,
            'work_date' => $prevMonthDate->toDateString(),
            'clock_in_at' => $prevMonthDate->copy()->setTime(9, 0),
            'clock_out_at' => $prevMonthDate->copy()->setTime(18, 0),
            'status' => 'completed',
            'note' => null,
        ]);

        Attendance::create([
            'user_id' => $staff->id,
            'work_date' => $nextMonthDate->toDateString(),
            'clock_in_at' => $nextMonthDate->copy()->setTime(9, 0),
            'clock_out_at' => $nextMonthDate->copy()->setTime(18, 0),
            'status' => 'completed',
            'note' => null,
        ]);

        $this->actingAs($admin)
            ->get('/admin/staff/list')
            ->assertOk()
            ->assertSee('一覧スタッフ')
            ->assertSee('staff_list@example.com');

        $detailUrl = route('admin.attendance.show', $staffPrevAttendance);

        $this->actingAs($admin)
            ->get('/admin/attendance/staff/'.$staff->id.'?year='.$prevMonthDate->year.'&month='.$prevMonthDate->month)
            ->assertOk()
            ->assertSee($prevMonthDate->year.'年'.$prevMonthDate->month.'月')
            ->assertSee('href="'.$detailUrl.'"', false);

        $this->actingAs($admin)
            ->get('/admin/attendance/staff/'.$staff->id.'?year='.$nextMonthDate->year.'&month='.$nextMonthDate->month)
            ->assertOk()
            ->assertSee($nextMonthDate->year.'年'.$nextMonthDate->month.'月');
    }

    public function test_admin_correction_request_lists_and_approve_updates_attendance(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-09-01 10:00:00'));

        $admin = User::factory()->admin()->create([
            'email_verified_at' => now(),
        ]);

        $user = User::factory()->create([
            'email_verified_at' => now(),
            'is_admin' => false,
        ]);

        $attendance = Attendance::create([
            'user_id' => $user->id,
            'work_date' => '2026-09-01',
            'clock_in_at' => Carbon::parse('2026-09-01 09:00:00'),
            'clock_out_at' => Carbon::parse('2026-09-01 18:00:00'),
            'status' => 'completed',
            'note' => null,
        ]);

        $req = StampCorrectionRequest::create([
            'user_id' => $user->id,
            'attendance_id' => $attendance->id,
            'status' => 'pending',
            'remark' => '承認テスト',
            'requested_clock_in_at' => Carbon::parse('2026-09-01 08:30:00'),
            'requested_clock_out_at' => Carbon::parse('2026-09-01 17:30:00'),
            'requested_breaks' => [],
        ]);

        $this->actingAs($admin)
            ->get('/stamp_correction_request/list')
            ->assertOk()
            ->assertSee('承認待ち')
            ->assertSee('承認テスト');

        $this->actingAs($admin)
            ->get(route('stamp_correction_request.approve', $req))
            ->assertOk()
            ->assertSee('承認テスト');

        $this->actingAs($admin)
            ->post(route('stamp_correction_request.approve.store', $req), [])
            ->assertRedirect('/stamp_correction_request/list');

        $attendance->refresh();
        $this->assertSame('08:30', $attendance->clock_in_at->format('H:i'));
        $this->assertSame('17:30', $attendance->clock_out_at->format('H:i'));
    }
}
