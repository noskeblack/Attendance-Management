<?php

namespace Tests\Feature;

use App\Enums\CorrectionRequestStatus;
use App\Models\Attendance;
use App\Models\StampCorrectionRequest;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StampCorrectionRequestListCoverageTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_pending_tab_lists_all_own_pending_requests(): void
    {
        $user = User::factory()->create([
            'email_verified_at' => now(),
            'is_admin' => false,
        ]);

        $remarks = ['申請甲', '申請乙', '申請丙'];

        foreach ([1, 2, 3] as $i) {
            $day = sprintf('2026-04-%02d', $i);
            $attendance = Attendance::create([
                'user_id' => $user->id,
                'work_date' => $day,
                'clock_in_at' => Carbon::parse($day.' 09:00:00'),
                'clock_out_at' => Carbon::parse($day.' 18:00:00'),
                'status' => 'completed',
                'note' => null,
            ]);

            StampCorrectionRequest::create([
                'user_id' => $user->id,
                'attendance_id' => $attendance->id,
                'status' => CorrectionRequestStatus::Pending->value,
                'remark' => $remarks[$i - 1],
                'requested_clock_in_at' => Carbon::parse($day.' 08:30:00'),
                'requested_clock_out_at' => Carbon::parse($day.' 18:00:00'),
                'requested_breaks' => [],
            ]);
        }

        $html = $this->actingAs($user)
            ->get('/stamp_correction_request/list')
            ->assertOk()
            ->assertSee('承認待ち')
            ->getContent();

        foreach ($remarks as $r) {
            $this->assertStringContainsString($r, $html);
        }
    }

    public function test_user_approved_tab_lists_all_own_approved_requests(): void
    {
        $user = User::factory()->create([
            'email_verified_at' => now(),
            'is_admin' => false,
        ]);

        $remarks = ['承認済A', '承認済B'];

        foreach ([1, 2] as $i) {
            $day = sprintf('2026-05-%02d', $i + 10);
            $attendance = Attendance::create([
                'user_id' => $user->id,
                'work_date' => $day,
                'clock_in_at' => Carbon::parse($day.' 09:00:00'),
                'clock_out_at' => Carbon::parse($day.' 18:00:00'),
                'status' => 'completed',
                'note' => null,
            ]);

            StampCorrectionRequest::create([
                'user_id' => $user->id,
                'attendance_id' => $attendance->id,
                'status' => CorrectionRequestStatus::Approved->value,
                'remark' => $remarks[$i - 1],
                'requested_clock_in_at' => Carbon::parse($day.' 09:00:00'),
                'requested_clock_out_at' => Carbon::parse($day.' 18:00:00'),
                'requested_breaks' => [],
                'approved_at' => now(),
            ]);
        }

        $html = $this->actingAs($user)
            ->get('/stamp_correction_request/list')
            ->assertOk()
            ->assertSee('承認済み')
            ->getContent();

        foreach ($remarks as $r) {
            $this->assertStringContainsString($r, $html);
        }
    }

    public function test_admin_sees_pending_requests_from_all_users(): void
    {
        $admin = User::factory()->admin()->create([
            'email_verified_at' => now(),
        ]);

        $alice = User::factory()->create(['name' => '利用者アリス', 'email_verified_at' => now(), 'is_admin' => false]);
        $bob = User::factory()->create(['name' => '利用者ボブ', 'email_verified_at' => now(), 'is_admin' => false]);

        foreach ([[$alice, '2026-06-01'], [$bob, '2026-06-02']] as [$u, $day]) {
            $attendance = Attendance::create([
                'user_id' => $u->id,
                'work_date' => $day,
                'clock_in_at' => Carbon::parse($day.' 09:00:00'),
                'clock_out_at' => Carbon::parse($day.' 18:00:00'),
                'status' => 'completed',
                'note' => null,
            ]);

            StampCorrectionRequest::create([
                'user_id' => $u->id,
                'attendance_id' => $attendance->id,
                'status' => CorrectionRequestStatus::Pending->value,
                'remark' => '管理者一覧テスト',
                'requested_clock_in_at' => Carbon::parse($day.' 09:00:00'),
                'requested_clock_out_at' => Carbon::parse($day.' 18:00:00'),
                'requested_breaks' => [],
            ]);
        }

        $html = $this->actingAs($admin)
            ->get('/stamp_correction_request/list')
            ->assertOk()
            ->assertSee('承認待ち')
            ->getContent();

        $this->assertStringContainsString('利用者アリス', $html);
        $this->assertStringContainsString('利用者ボブ', $html);
    }

    public function test_admin_sees_approved_requests_from_all_users(): void
    {
        $admin = User::factory()->admin()->create([
            'email_verified_at' => now(),
        ]);

        $alice = User::factory()->create(['name' => '承認済アリス', 'email_verified_at' => now(), 'is_admin' => false]);
        $bob = User::factory()->create(['name' => '承認済ボブ', 'email_verified_at' => now(), 'is_admin' => false]);

        foreach ([[$alice, '2026-07-01'], [$bob, '2026-07-02']] as [$u, $day]) {
            $attendance = Attendance::create([
                'user_id' => $u->id,
                'work_date' => $day,
                'clock_in_at' => Carbon::parse($day.' 09:00:00'),
                'clock_out_at' => Carbon::parse($day.' 18:00:00'),
                'status' => 'completed',
                'note' => null,
            ]);

            StampCorrectionRequest::create([
                'user_id' => $u->id,
                'attendance_id' => $attendance->id,
                'status' => CorrectionRequestStatus::Approved->value,
                'remark' => '承認済み確認',
                'requested_clock_in_at' => Carbon::parse($day.' 09:00:00'),
                'requested_clock_out_at' => Carbon::parse($day.' 18:00:00'),
                'requested_breaks' => [],
                'approved_at' => now(),
            ]);
        }

        $html = $this->actingAs($admin)
            ->get('/stamp_correction_request/list')
            ->assertOk()
            ->assertSee('承認済み')
            ->getContent();

        $this->assertStringContainsString('承認済アリス', $html);
        $this->assertStringContainsString('承認済ボブ', $html);
    }

    public function test_user_request_detail_links_point_to_attendance_detail(): void
    {
        $user = User::factory()->create([
            'email_verified_at' => now(),
            'is_admin' => false,
        ]);

        $attendance = Attendance::create([
            'user_id' => $user->id,
            'work_date' => '2026-08-15',
            'clock_in_at' => Carbon::parse('2026-08-15 09:00:00'),
            'clock_out_at' => Carbon::parse('2026-08-15 18:00:00'),
            'status' => 'completed',
            'note' => null,
        ]);

        StampCorrectionRequest::create([
            'user_id' => $user->id,
            'attendance_id' => $attendance->id,
            'status' => CorrectionRequestStatus::Pending->value,
            'remark' => 'リンク確認',
            'requested_clock_in_at' => Carbon::parse('2026-08-15 09:00:00'),
            'requested_clock_out_at' => Carbon::parse('2026-08-15 18:00:00'),
            'requested_breaks' => [],
        ]);

        $expected = route('attendance.detail', $attendance->id);

        $this->actingAs($user)
            ->get('/stamp_correction_request/list')
            ->assertOk()
            ->assertSee('href="'.$expected.'"', false);
    }

    public function test_admin_approve_screen_displays_request_payload(): void
    {
        $admin = User::factory()->admin()->create([
            'email_verified_at' => now(),
        ]);

        $user = User::factory()->create([
            'name' => 'ペイロードユーザー',
            'email_verified_at' => now(),
            'is_admin' => false,
        ]);

        $attendance = Attendance::create([
            'user_id' => $user->id,
            'work_date' => '2026-09-20',
            'clock_in_at' => Carbon::parse('2026-09-20 09:00:00'),
            'clock_out_at' => Carbon::parse('2026-09-20 18:00:00'),
            'status' => 'completed',
            'note' => null,
        ]);

        $req = StampCorrectionRequest::create([
            'user_id' => $user->id,
            'attendance_id' => $attendance->id,
            'status' => CorrectionRequestStatus::Pending->value,
            'remark' => '詳細表示確認用の備考',
            'requested_clock_in_at' => Carbon::parse('2026-09-20 08:00:00'),
            'requested_clock_out_at' => Carbon::parse('2026-09-20 19:00:00'),
            'requested_breaks' => [],
        ]);

        $this->actingAs($admin)
            ->get(route('stamp_correction_request.approve', $req))
            ->assertOk()
            ->assertSee('修正申請承認')
            ->assertSee('ペイロードユーザー')
            ->assertSee('申請内容')
            ->assertSee('詳細表示確認用の備考')
            ->assertSee('08:00', false)
            ->assertSee('19:00', false);
    }
}
