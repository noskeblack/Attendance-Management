<?php

namespace Tests\Feature;

use App\Models\Attendance;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AttendanceStampExtendedTest extends TestCase
{
    use RefreshDatabase;

    public function test_clock_in_changes_status_to_working_and_shows_clock_in_button_only_once(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-03-15 09:00:00'));

        $user = User::factory()->create([
            'email_verified_at' => now(),
            'is_admin' => false,
        ]);

        $this->actingAs($user)
            ->post('/attendance/clock-in')
            ->assertRedirect('/attendance');

        $this->actingAs($user)
            ->get('/attendance')
            ->assertOk()
            ->assertSee('出勤中')
            ->assertDontSee('>出勤</button>', false);

        // 退勤済ユーザーは出勤ボタンが出ない（シート要件: 出勤は一日一回）
        Attendance::query()->where('user_id', $user->id)->update([
            'clock_out_at' => Carbon::parse('2026-03-15 18:00:00'),
            'status' => 'completed',
        ]);

        $this->actingAs($user)
            ->get('/attendance')
            ->assertOk()
            ->assertDontSee('>出勤</button>', false);
    }

    public function test_break_flow_and_list_times(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-03-15 09:00:00'));

        $user = User::factory()->create([
            'email_verified_at' => now(),
            'is_admin' => false,
        ]);

        $this->actingAs($user)->post('/attendance/clock-in')->assertRedirect('/attendance');

        Carbon::setTestNow(Carbon::parse('2026-03-15 12:00:00'));
        $this->actingAs($user)->post('/attendance/break-start')->assertRedirect('/attendance');

        $this->actingAs($user)
            ->get('/attendance')
            ->assertOk()
            ->assertSee('休憩中')
            ->assertSee('休憩戻');

        Carbon::setTestNow(Carbon::parse('2026-03-15 13:00:00'));
        $this->actingAs($user)->post('/attendance/break-end')->assertRedirect('/attendance');

        $this->actingAs($user)
            ->get('/attendance')
            ->assertOk()
            ->assertSee('出勤中')
            ->assertSee('休憩入');

        Carbon::setTestNow(Carbon::parse('2026-03-15 12:30:00'));
        $this->actingAs($user)->post('/attendance/break-start')->assertRedirect('/attendance');

        Carbon::setTestNow(Carbon::parse('2026-03-15 12:45:00'));
        $this->actingAs($user)->post('/attendance/break-end')->assertRedirect('/attendance');

        Carbon::setTestNow(Carbon::parse('2026-03-15 18:00:00'));
        $this->actingAs($user)->post('/attendance/clock-out')->assertRedirect('/attendance');

        $this->actingAs($user)
            ->get('/attendance/list')
            ->assertOk()
            ->assertSee('09:00')
            ->assertSee('18:00')
            ->assertSee('12:00-13:00')
            ->assertSee('12:30-12:45');
    }

    public function test_clock_out_changes_status_to_completed_and_list_shows_clock_out(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-03-20 09:00:00'));

        $user = User::factory()->create([
            'email_verified_at' => now(),
            'is_admin' => false,
        ]);

        $this->actingAs($user)->post('/attendance/clock-in')->assertRedirect('/attendance');

        Carbon::setTestNow(Carbon::parse('2026-03-20 18:00:00'));
        $this->actingAs($user)->post('/attendance/clock-out')->assertRedirect('/attendance');

        $this->actingAs($user)
            ->get('/attendance')
            ->assertOk()
            ->assertSee('退勤済');

        $this->actingAs($user)
            ->get('/attendance/list')
            ->assertOk()
            ->assertSee('18:00');
    }
}
