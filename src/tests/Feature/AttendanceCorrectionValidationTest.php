<?php

namespace Tests\Feature;

use App\Models\Attendance;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AttendanceCorrectionValidationTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_shows_required_remark_message(): void
    {
        [$user, $attendance] = $this->seedAttendance();

        $response = $this->actingAs($user)
            ->post(route('attendance.detail.submit', $attendance), [
                'clock_in' => '09:00',
                'clock_out' => '18:00',
                'remark' => '',
                'breaks' => [],
            ]);

        $response->assertSessionHasErrors('remark');
        $this->assertSame('備考を記入してください', session('errors')->first('remark'));
    }

    public function test_it_shows_invalid_clock_in_out_message(): void
    {
        [$user, $attendance] = $this->seedAttendance();

        $response = $this->actingAs($user)
            ->post(route('attendance.detail.submit', $attendance), [
                'clock_in' => '19:00',
                'clock_out' => '18:00',
                'remark' => '確認',
                'breaks' => [],
            ]);

        $response->assertSessionHasErrors('clock_in');
        $this->assertSame('出勤時間もしくは退勤時間が不適切な値です', session('errors')->first('clock_in'));
    }

    public function test_it_shows_invalid_break_start_before_clock_in_message(): void
    {
        [$user, $attendance] = $this->seedAttendance();

        $response = $this->actingAs($user)
            ->post(route('attendance.detail.submit', $attendance), [
                'clock_in' => '09:00',
                'clock_out' => '18:00',
                'remark' => '確認',
                'breaks' => [
                    ['start' => '08:00', 'end' => '08:30'],
                ],
            ]);

        $response->assertSessionHasErrors('breaks.0.start');
        $this->assertSame('休憩時間が不適切な値です', session('errors')->first('breaks.0.start'));
    }

    public function test_it_shows_invalid_break_start_message(): void
    {
        [$user, $attendance] = $this->seedAttendance();

        $response = $this->actingAs($user)
            ->post(route('attendance.detail.submit', $attendance), [
                'clock_in' => '09:00',
                'clock_out' => '18:00',
                'remark' => '確認',
                'breaks' => [
                    ['start' => '19:00', 'end' => '19:30'],
                ],
            ]);

        $response->assertSessionHasErrors('breaks.0.start');
        $this->assertSame('休憩時間が不適切な値です', session('errors')->first('breaks.0.start'));
    }

    public function test_it_shows_invalid_break_end_message(): void
    {
        [$user, $attendance] = $this->seedAttendance();

        $response = $this->actingAs($user)
            ->post(route('attendance.detail.submit', $attendance), [
                'clock_in' => '09:00',
                'clock_out' => '18:00',
                'remark' => '確認',
                'breaks' => [
                    ['start' => '17:00', 'end' => '18:30'],
                ],
            ]);

        $response->assertSessionHasErrors('breaks.0.end');
        $this->assertSame('休憩時間もしくは退勤時間が不適切な値です', session('errors')->first('breaks.0.end'));
    }

    /**
     * @return array{0: User, 1: Attendance}
     */
    private function seedAttendance(): array
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

        return [$user, $attendance];
    }
}
