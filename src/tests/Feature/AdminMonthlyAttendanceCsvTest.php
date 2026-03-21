<?php

namespace Tests\Feature;

use App\Models\Attendance;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminMonthlyAttendanceCsvTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_download_monthly_attendance_csv(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-12-10 12:00:00'));

        $admin = User::factory()->admin()->create([
            'email_verified_at' => now(),
        ]);

        $staff = User::factory()->create([
            'email_verified_at' => now(),
            'is_admin' => false,
        ]);

        Attendance::create([
            'user_id' => $staff->id,
            'work_date' => '2026-12-05',
            'clock_in_at' => Carbon::parse('2026-12-05 09:15:00'),
            'clock_out_at' => Carbon::parse('2026-12-05 18:20:00'),
            'status' => 'completed',
            'note' => '備考A',
        ]);

        $response = $this->actingAs($admin)
            ->get('/admin/attendance/staff/'.$staff->id.'/export/csv?year=2026&month=12');

        $response->assertOk();
        $response->assertHeader('content-disposition');
        $this->assertStringContainsString('attachment', $response->headers->get('content-disposition'));
        $content = $response->streamedContent();
        $this->assertStringContainsString("\xEF\xBB\xBF", $content);
        $this->assertStringContainsString('2026-12-05', $content);
        $this->assertStringContainsString('09:15', $content);
        $this->assertStringContainsString('18:20', $content);
        $this->assertStringContainsString('備考A', $content);
    }
}
