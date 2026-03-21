<?php

namespace App\Http\Controllers;

use App\Enums\AttendanceStatus;
use App\Services\AttendanceService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AttendanceController extends Controller
{
    public function __construct(
        protected AttendanceService $attendanceService
    ) {}

    public function index(Request $request): View
    {
        $user = $request->user();
        $status = $user->currentAttendanceStatus();

        return view('attendance.index', [
            'status' => $status,
            'statusLabel' => $status->label(),
            'nowFormatted' => now()->locale('ja')->isoFormat('YYYY年M月D日(ddd) HH:mm'),
        ]);
    }

    public function clockIn(Request $request): RedirectResponse
    {
        try {
            $this->attendanceService->clockIn($request->user());
        } catch (\Throwable) {
            return back()->withErrors(['action' => '出勤処理に失敗しました。']);
        }

        return redirect()->route('attendance.index');
    }

    public function clockOut(Request $request): RedirectResponse
    {
        try {
            $this->attendanceService->clockOut($request->user());
        } catch (\Throwable) {
            return back()->withErrors(['action' => '退勤処理に失敗しました。']);
        }

        return redirect()->route('attendance.index')->with('clock_out_message', 'お疲れ様でした。');
    }

    public function breakStart(Request $request): RedirectResponse
    {
        try {
            $this->attendanceService->breakStart($request->user());
        } catch (\Throwable) {
            return back()->withErrors(['action' => '休憩の開始に失敗しました。']);
        }

        return redirect()->route('attendance.index');
    }

    public function breakEnd(Request $request): RedirectResponse
    {
        try {
            $this->attendanceService->breakEnd($request->user());
        } catch (\Throwable) {
            return back()->withErrors(['action' => '休憩の終了に失敗しました。']);
        }

        return redirect()->route('attendance.index');
    }
}
