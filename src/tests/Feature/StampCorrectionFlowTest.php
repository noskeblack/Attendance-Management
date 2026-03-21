<?php

namespace Tests\Feature;

use App\Models\Attendance;
use App\Models\StampCorrectionRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StampCorrectionFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_submit_correction_request(): void
    {
        $user = User::factory()->create([
            'email_verified_at' => now(),
            'is_admin' => false,
        ]);

        $attendance = Attendance::create([
            'user_id' => $user->id,
            'work_date' => now()->toDateString(),
            'clock_in_at' => now()->startOfDay()->addHours(9),
            'clock_out_at' => now()->startOfDay()->addHours(18),
            'status' => 'completed',
            'note' => null,
        ]);

        $this->actingAs($user)
            ->post(route('attendance.detail.submit', $attendance), [
                'clock_in' => '09:30',
                'clock_out' => '18:00',
                'remark' => '電車遅延',
                'breaks' => [
                    ['start' => '12:00', 'end' => '13:00'],
                ],
            ])
            ->assertRedirect(route('stamp_correction_request.index'));

        $this->assertDatabaseHas('stamp_correction_requests', [
            'attendance_id' => $attendance->id,
            'status' => 'pending',
            'remark' => '電車遅延',
        ]);
    }

    public function test_admin_can_approve_correction_request(): void
    {
        $user = User::factory()->create([
            'email_verified_at' => now(),
            'is_admin' => false,
        ]);
        $admin = User::factory()->admin()->create([
            'email_verified_at' => now(),
        ]);

        $attendance = Attendance::create([
            'user_id' => $user->id,
            'work_date' => now()->toDateString(),
            'clock_in_at' => now()->startOfDay()->addHours(9),
            'clock_out_at' => now()->startOfDay()->addHours(18),
            'status' => 'completed',
            'note' => null,
        ]);

        $request = StampCorrectionRequest::create([
            'user_id' => $user->id,
            'attendance_id' => $attendance->id,
            'status' => 'pending',
            'remark' => '電車遅延',
            'requested_clock_in_at' => now()->startOfDay()->addHours(9)->addMinutes(15),
            'requested_clock_out_at' => now()->startOfDay()->addHours(18),
            'requested_breaks' => [
                [
                    'break_start_at' => now()->startOfDay()->addHours(12)->toIso8601String(),
                    'break_end_at' => now()->startOfDay()->addHours(13)->toIso8601String(),
                ],
            ],
        ]);

        $this->actingAs($admin)
            ->post(route('stamp_correction_request.approve.store', $request))
            ->assertRedirect(route('stamp_correction_request.index'));

        $this->assertDatabaseHas('stamp_correction_requests', [
            'id' => $request->id,
            'status' => 'approved',
        ]);
    }
}
