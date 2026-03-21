<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminStaffController extends Controller
{
    public function index(): View
    {
        $staff = User::query()->where('is_admin', false)->orderBy('id')->get();

        return view('admin.staff.index', [
            'staff' => $staff,
        ]);
    }

    public function monthlyAttendance(Request $request, User $user): View
    {
        abort_if($user->is_admin, 404);

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

        return view('admin.attendance.staff_monthly', [
            'user' => $user,
            'current' => $current,
            'attendances' => $attendances,
            'prevQuery' => ['year' => $prev->year, 'month' => $prev->month],
            'nextQuery' => ['year' => $next->year, 'month' => $next->month],
        ]);
    }
}
