<?php

namespace Tests\Feature;

use App\Models\Attendance;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AttendanceStampTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_clock_in_break_and_clock_out(): void
    {
        $user = User::factory()->create([
            'email_verified_at' => now(),
            'is_admin' => false,
        ]);

        $this->actingAs($user)
            ->post(route('attendance.clock_in'))
            ->assertRedirect(route('attendance.index'));

        $this->actingAs($user)
            ->post(route('attendance.break_start'))
            ->assertRedirect(route('attendance.index'));

        $this->actingAs($user)
            ->post(route('attendance.break_end'))
            ->assertRedirect(route('attendance.index'));

        $this->actingAs($user)
            ->post(route('attendance.clock_out'))
            ->assertRedirect(route('attendance.index'));

        $this->assertDatabaseHas('attendances', [
            'user_id' => $user->id,
            'status' => 'completed',
        ]);
    }

    public function test_user_cannot_clock_in_twice_in_a_day(): void
    {
        $user = User::factory()->create([
            'email_verified_at' => now(),
            'is_admin' => false,
        ]);

        $this->actingAs($user)->post(route('attendance.clock_in'));
        $response = $this->actingAs($user)->post(route('attendance.clock_in'));

        $response->assertSessionHasErrors('action');
        $this->assertEquals(1, Attendance::where('user_id', $user->id)->count());
    }
}
