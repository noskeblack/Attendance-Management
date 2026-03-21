<?php

namespace App\Http\Controllers;

use App\Enums\CorrectionRequestStatus;
use App\Http\Requests\AttendanceCorrectionRequest;
use App\Models\Attendance;
use App\Models\StampCorrectionRequest;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AttendanceDetailController extends Controller
{
    public function show(Request $request, Attendance $attendance): View
    {
        $this->authorizeUserAttendance($request, $attendance);

        $attendance->load(['breaks' => fn ($q) => $q->orderBy('break_start_at')]);

        $pending = $attendance->pendingCorrectionRequest();

        return view('attendance.detail', [
            'attendance' => $attendance,
            'pending' => $pending,
            'readOnly' => (bool) $pending,
        ]);
    }

    public function submitCorrection(AttendanceCorrectionRequest $request, Attendance $attendance): RedirectResponse
    {
        $this->authorizeUserAttendance($request, $attendance);

        if ($attendance->pendingCorrectionRequest()) {
            return back()->withErrors(['request' => '承認待ちのため修正はできません。']);
        }

        $base = Carbon::parse($attendance->work_date->format('Y-m-d'));
        $clockInAt = $base->copy()->setTimeFromTimeString($request->input('clock_in'));
        $clockOutAt = $base->copy()->setTimeFromTimeString($request->input('clock_out'));

        $breakPayload = [];
        foreach ($request->input('breaks', []) as $row) {
            $start = $row['start'] ?? null;
            $end = $row['end'] ?? null;
            if (! $start || ! $end) {
                continue;
            }
            $breakPayload[] = [
                'break_start_at' => $base->copy()->setTimeFromTimeString($start)->toIso8601String(),
                'break_end_at' => $base->copy()->setTimeFromTimeString($end)->toIso8601String(),
            ];
        }

        StampCorrectionRequest::create([
            'user_id' => $request->user()->id,
            'attendance_id' => $attendance->id,
            'status' => CorrectionRequestStatus::Pending->value,
            'remark' => $request->input('remark'),
            'requested_clock_in_at' => $clockInAt,
            'requested_clock_out_at' => $clockOutAt,
            'requested_breaks' => $breakPayload,
        ]);

        return redirect()->route('stamp_correction_request.index')
            ->with('status', '修正申請を受け付けました。');
    }

    protected function authorizeUserAttendance(Request $request, Attendance $attendance): void
    {
        abort_if($request->user()->id !== $attendance->user_id, 403);
    }
}
