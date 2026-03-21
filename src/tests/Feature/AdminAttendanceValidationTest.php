<?php

namespace Tests\Feature;

use App\Models\Attendance;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminAttendanceValidationTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_update_shows_invalid_clock_in_out_message(): void
    {
        $admin = User::factory()->admin()->create([
            'email_verified_at' => now(),
        ]);

        $staff = User::factory()->create([
            'email_verified_at' => now(),
            'is_admin' => false,
        ]);

        $attendance = Attendance::create([
            'user_id' => $staff->id,
            'work_date' => '2026-10-01',
            'clock_in_at' => Carbon::parse('2026-10-01 09:00:00'),
            'clock_out_at' => Carbon::parse('2026-10-01 18:00:00'),
            'status' => 'completed',
            'note' => 'メモ',
        ]);

        $this->actingAs($admin)
            ->put('/admin/attendance/'.$attendance->id, [
                'clock_in' => '19:00',
                'clock_out' => '18:00',
                'remark' => '確認',
                'breaks' => [],
            ])
            ->assertSessionHasErrors('clock_in');

        $this->assertSame(
            '出勤時間もしくは退勤時間が不適切な値です',
            session('errors')->first('clock_in')
        );
    }

    public function test_admin_update_shows_invalid_break_start_message(): void
    {
        $admin = User::factory()->admin()->create([
            'email_verified_at' => now(),
        ]);

        $staff = User::factory()->create([
            'email_verified_at' => now(),
            'is_admin' => false,
        ]);

        $attendance = Attendance::create([
            'user_id' => $staff->id,
            'work_date' => '2026-10-02',
            'clock_in_at' => Carbon::parse('2026-10-02 09:00:00'),
            'clock_out_at' => Carbon::parse('2026-10-02 18:00:00'),
            'status' => 'completed',
            'note' => 'メモ',
        ]);

        $this->actingAs($admin)
            ->put('/admin/attendance/'.$attendance->id, [
                'clock_in' => '09:00',
                'clock_out' => '18:00',
                'remark' => '確認',
                'breaks' => [
                    ['start' => '19:00', 'end' => '19:30'],
                ],
            ])
            ->assertSessionHasErrors('breaks.0.start');

        $this->assertSame('休憩時間が不適切な値です', session('errors')->first('breaks.0.start'));
    }

    public function test_admin_update_shows_invalid_break_start_before_clock_in_message(): void
    {
        $admin = User::factory()->admin()->create([
            'email_verified_at' => now(),
        ]);

        $staff = User::factory()->create([
            'email_verified_at' => now(),
            'is_admin' => false,
        ]);

        $attendance = Attendance::create([
            'user_id' => $staff->id,
            'work_date' => '2026-10-05',
            'clock_in_at' => Carbon::parse('2026-10-05 09:00:00'),
            'clock_out_at' => Carbon::parse('2026-10-05 18:00:00'),
            'status' => 'completed',
            'note' => 'メモ',
        ]);

        $this->actingAs($admin)
            ->put('/admin/attendance/'.$attendance->id, [
                'clock_in' => '09:00',
                'clock_out' => '18:00',
                'remark' => '確認',
                'breaks' => [
                    ['start' => '08:00', 'end' => '08:30'],
                ],
            ])
            ->assertSessionHasErrors('breaks.0.start');

        $this->assertSame('休憩時間が不適切な値です', session('errors')->first('breaks.0.start'));
    }

    public function test_admin_update_shows_invalid_break_end_message(): void
    {
        $admin = User::factory()->admin()->create([
            'email_verified_at' => now(),
        ]);

        $staff = User::factory()->create([
            'email_verified_at' => now(),
            'is_admin' => false,
        ]);

        $attendance = Attendance::create([
            'user_id' => $staff->id,
            'work_date' => '2026-10-03',
            'clock_in_at' => Carbon::parse('2026-10-03 09:00:00'),
            'clock_out_at' => Carbon::parse('2026-10-03 18:00:00'),
            'status' => 'completed',
            'note' => 'メモ',
        ]);

        $this->actingAs($admin)
            ->put('/admin/attendance/'.$attendance->id, [
                'clock_in' => '09:00',
                'clock_out' => '18:00',
                'remark' => '確認',
                'breaks' => [
                    ['start' => '17:00', 'end' => '18:30'],
                ],
            ])
            ->assertSessionHasErrors('breaks.0.end');

        $this->assertSame(
            '休憩時間もしくは退勤時間が不適切な値です',
            session('errors')->first('breaks.0.end')
        );
    }

    public function test_admin_update_requires_remark(): void
    {
        $admin = User::factory()->admin()->create([
            'email_verified_at' => now(),
        ]);

        $staff = User::factory()->create([
            'email_verified_at' => now(),
            'is_admin' => false,
        ]);

        $attendance = Attendance::create([
            'user_id' => $staff->id,
            'work_date' => '2026-10-04',
            'clock_in_at' => Carbon::parse('2026-10-04 09:00:00'),
            'clock_out_at' => Carbon::parse('2026-10-04 18:00:00'),
            'status' => 'completed',
            'note' => 'メモ',
        ]);

        $this->actingAs($admin)
            ->put('/admin/attendance/'.$attendance->id, [
                'clock_in' => '09:00',
                'clock_out' => '18:00',
                'remark' => '',
                'breaks' => [],
            ])
            ->assertSessionHasErrors('remark');

        $this->assertSame('備考を記入してください', session('errors')->first('remark'));
    }
}
