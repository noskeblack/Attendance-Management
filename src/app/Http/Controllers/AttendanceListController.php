<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AttendanceListController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();
        $month = $request->query('month');
        $year = $request->query('year');

        $current = Carbon::now()->startOfMonth();
        if ($year && $month) {
            $current = Carbon::createFromDate((int) $year, (int) $month, 1)->startOfMonth();
        }

        $start = $current->copy()->startOfMonth();
        $end = $current->copy()->endOfMonth();

        $attendances = Attendance::query()
            ->where('user_id', $user->id)
            ->whereBetween('work_date', [$start->toDateString(), $end->toDateString()])
            ->with(['breaks' => fn ($q) => $q->orderBy('break_start_at')])
            ->orderBy('work_date')
            ->get()
            ->keyBy(fn (Attendance $a) => $a->work_date->format('Y-m-d'));

        $prev = $current->copy()->subMonth();
        $next = $current->copy()->addMonth();

        return view('attendance.list', [
            'current' => $current,
            'attendances' => $attendances,
            'prevQuery' => ['year' => $prev->year, 'month' => $prev->month],
            'nextQuery' => ['year' => $next->year, 'month' => $next->month],
        ]);
    }
}
