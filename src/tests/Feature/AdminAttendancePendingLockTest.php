<?php

namespace Tests\Feature;

use App\Enums\CorrectionRequestStatus;
use App\Models\Attendance;
use App\Models\StampCorrectionRequest;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminAttendancePendingLockTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_cannot_edit_attendance_when_correction_request_is_pending(): void
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
            'work_date' => '2026-11-01',
            'clock_in_at' => Carbon::parse('2026-11-01 09:00:00'),
            'clock_out_at' => Carbon::parse('2026-11-01 18:00:00'),
            'status' => 'completed',
            'note' => 'メモ',
        ]);

        StampCorrectionRequest::create([
            'user_id' => $staff->id,
            'attendance_id' => $attendance->id,
            'status' => CorrectionRequestStatus::Pending->value,
            'remark' => '承認待ち',
            'requested_clock_in_at' => Carbon::parse('2026-11-01 08:00:00'),
            'requested_clock_out_at' => Carbon::parse('2026-11-01 19:00:00'),
            'requested_breaks' => [],
        ]);

        $this->actingAs($admin)
            ->get('/admin/attendance/'.$attendance->id)
            ->assertOk()
            ->assertSee('承認待ちのため修正はできません。');

        $this->actingAs($admin)
            ->put('/admin/attendance/'.$attendance->id, [
                'clock_in' => '10:00',
                'clock_out' => '18:00',
                'remark' => '不正更新',
                'breaks' => [],
            ])
            ->assertSessionHasErrors('request');

        $this->assertSame(
            '承認待ちのため修正はできません。',
            session('errors')->first('request')
        );

        $attendance->refresh();
        $this->assertSame('09:00', $attendance->clock_in_at->format('H:i'));
    }
}
