<?php

namespace Tests\Feature;

use App\Models\Attendance;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StampCorrectionRequestExtendedTest extends TestCase
{
    use RefreshDatabase;

    public function test_correction_request_appears_on_user_and_admin_lists_and_detail_links_work(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-05-10 10:00:00'));

        $user = User::factory()->create([
            'email_verified_at' => now(),
            'is_admin' => false,
        ]);

        $admin = User::factory()->admin()->create([
            'email_verified_at' => now(),
        ]);

        $attendance = Attendance::create([
            'user_id' => $user->id,
            'work_date' => '2026-05-10',
            'clock_in_at' => Carbon::parse('2026-05-10 09:00:00'),
            'clock_out_at' => Carbon::parse('2026-05-10 18:00:00'),
            'status' => 'completed',
            'note' => null,
        ]);

        $this->actingAs($user)
            ->post('/attendance/detail/'.$attendance->id, [
                'clock_in' => '09:00',
                'clock_out' => '18:00',
                'remark' => '修正依頼',
                'breaks' => [],
            ])
            ->assertRedirect('/stamp_correction_request/list');

        $this->actingAs($user)
            ->get('/stamp_correction_request/list')
            ->assertOk()
            ->assertSee('承認待ち')
            ->assertSee('修正依頼');

        $this->actingAs($admin)
            ->get('/stamp_correction_request/list')
            ->assertOk()
            ->assertSee($user->name)
            ->assertSee('修正依頼');

        $this->actingAs($user)
            ->get('/stamp_correction_request/list')
            ->assertOk();

        // ユーザー側「詳細」は勤怠詳細へ
        $this->actingAs($user)
            ->get('/stamp_correction_request/list')
            ->assertSee(route('attendance.detail', $attendance->id), false);

        // 管理者側「詳細」は承認画面へ（ルートは1件だけ存在する想定で抽出）
        $approveUrl = route('stamp_correction_request.approve', \App\Models\StampCorrectionRequest::query()->first());

        $this->actingAs($admin)
            ->get('/stamp_correction_request/list')
            ->assertSee($approveUrl, false);
    }
}
