<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminAttendanceController extends Controller
{
    public function daily(Request $request): View
    {
        $date = $request->query('date')
            ? Carbon::parse($request->query('date'))->startOfDay()
            : Carbon::today();

        $prev = $date->copy()->subDay();
        $next = $date->copy()->addDay();

        $users = User::query()->where('is_admin', false)->orderBy('id')->get();

        $attendances = Attendance::query()
            ->whereDate('work_date', $date->toDateString())
            ->with(['user', 'breaks' => fn ($q) => $q->orderBy('break_start_at')])
            ->get()
            ->keyBy('user_id');

        return view('admin.attendance.daily', [
            'date' => $date,
            'prevQuery' => ['date' => $prev->toDateString()],
            'nextQuery' => ['date' => $next->toDateString()],
            'users' => $users,
            'attendances' => $attendances,
        ]);
    }

    public function show(Attendance $attendance): View
    {
        $attendance->load(['user', 'breaks' => fn ($q) => $q->orderBy('break_start_at')]);

        return view('admin.attendance.show', [
            'attendance' => $attendance,
        ]);
    }

    public function update(Request $request, Attendance $attendance): RedirectResponse
    {
        $validated = $request->validate([
            'clock_in' => ['required', 'date_format:H:i'],
            'clock_out' => ['nullable', 'date_format:H:i'],
            'note' => ['nullable', 'string', 'max:2000'],
        ]);

        $base = Carbon::parse($attendance->work_date->format('Y-m-d'));
        $in = $base->copy()->setTimeFromTimeString($validated['clock_in']);
        $out = ! empty($validated['clock_out'])
            ? $base->copy()->setTimeFromTimeString($validated['clock_out'])
            : null;

        $attendance->update([
            'clock_in_at' => $in,
            'clock_out_at' => $out,
            'note' => $validated['note'] ?? null,
            'status' => $out ? \App\Enums\AttendanceStatus::Completed->value : \App\Enums\AttendanceStatus::Working->value,
        ]);

        return redirect()->route('admin.attendance.show', $attendance)->with('status', '保存しました。');
    }
}
