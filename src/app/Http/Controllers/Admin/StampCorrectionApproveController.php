<?php

namespace App\Http\Controllers\Admin;

use App\Enums\AttendanceStatus;
use App\Enums\CorrectionRequestStatus;
use App\Http\Controllers\Controller;
use App\Models\AttendanceBreak;
use App\Models\StampCorrectionRequest;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StampCorrectionApproveController extends Controller
{
    public function show(StampCorrectionRequest $stampCorrectionRequest): View
    {
        $stampCorrectionRequest->load(['user', 'attendance.breaks']);

        return view('admin.requests.approve', [
            'requestModel' => $stampCorrectionRequest,
        ]);
    }

    public function approve(Request $request, StampCorrectionRequest $stampCorrectionRequest): RedirectResponse
    {
        if ($stampCorrectionRequest->status !== CorrectionRequestStatus::Pending->value) {
            return back()->withErrors(['request' => '既に処理済みです。']);
        }

        $attendance = $stampCorrectionRequest->attendance;
        $base = Carbon::parse($attendance->work_date->format('Y-m-d'));

        $in = $stampCorrectionRequest->requested_clock_in_at
            ? Carbon::parse($stampCorrectionRequest->requested_clock_in_at)
            : null;
        $out = $stampCorrectionRequest->requested_clock_out_at
            ? Carbon::parse($stampCorrectionRequest->requested_clock_out_at)
            : null;

        $attendance->breaks()->delete();

        foreach ($stampCorrectionRequest->requested_breaks ?? [] as $row) {
            $start = isset($row['break_start_at']) ? Carbon::parse($row['break_start_at']) : null;
            $end = isset($row['break_end_at']) ? Carbon::parse($row['break_end_at']) : null;
            if (! $start || ! $end) {
                continue;
            }
            AttendanceBreak::create([
                'attendance_id' => $attendance->id,
                'break_start_at' => $start,
                'break_end_at' => $end,
            ]);
        }

        $status = $out
            ? AttendanceStatus::Completed->value
            : AttendanceStatus::Working->value;

        $attendance->update([
            'clock_in_at' => $in,
            'clock_out_at' => $out,
            'status' => $status,
            'note' => $stampCorrectionRequest->remark,
        ]);

        $stampCorrectionRequest->update([
            'status' => CorrectionRequestStatus::Approved->value,
            'approved_at' => now(),
        ]);

        return redirect()->route('stamp_correction_request.index')->with('status', '承認しました。');
    }
}
