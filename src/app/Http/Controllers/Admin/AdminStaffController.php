<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;
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

    /**
     * FN045: スタッフ別月次勤怠の CSV ダウンロード
     */
    public function exportMonthlyCsv(Request $request, User $user): StreamedResponse
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
            ->get();

        $filename = sprintf('attendance_%d_%04d%02d.csv', $user->id, $current->year, $current->month);

        return response()->streamDownload(function () use ($attendances) {
            $handle = fopen('php://output', 'w');
            fwrite($handle, "\xEF\xBB\xBF");
            fputcsv($handle, ['日付', '出勤', '退勤', '休憩', '備考']);
            foreach ($attendances as $a) {
                $breakStr = $a->breaks
                    ->map(fn ($b) => optional($b->break_start_at)->format('H:i').'-'.optional($b->break_end_at)->format('H:i'))
                    ->join(' / ');
                fputcsv($handle, [
                    $a->work_date->format('Y-m-d'),
                    optional($a->clock_in_at)->format('H:i'),
                    optional($a->clock_out_at)->format('H:i'),
                    $breakStr,
                    $a->note ?? '',
                ]);
            }
            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }
}
