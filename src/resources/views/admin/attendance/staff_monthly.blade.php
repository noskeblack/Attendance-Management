@extends('layouts.admin')

@section('title', 'スタッフ別勤怠一覧')

@section('content')
    <div class="ct-card">
        <h1 class="ct-title">スタッフ別勤怠一覧（{{ $user->name }}）</h1>
        <p class="ct-muted">{{ $current->isoFormat('YYYY年M月') }}</p>
        <div class="ct-actions ct-actions--mb">
            <a class="ct-btn" href="{{ route('admin.staff.attendance', array_merge(['user' => $user->id], $prevQuery)) }}">前月</a>
            <a class="ct-btn" href="{{ route('admin.staff.attendance', array_merge(['user' => $user->id], $nextQuery)) }}">翌月</a>
            <a class="ct-btn ct-btn--primary" href="{{ route('admin.staff.attendance.export', ['user' => $user->id, 'year' => $current->year, 'month' => $current->month]) }}">CSV出力</a>
        </div>

        <div class="ct-table-wrap">
            <table class="ct-table">
                <thead>
                    <tr>
                        <th>日付</th>
                        <th>出勤</th>
                        <th>退勤</th>
                        <th>休憩</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($attendances as $day => $attendance)
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($day)->locale('ja')->isoFormat('M/D(ddd)') }}</td>
                            <td>{{ optional($attendance->clock_in_at)->format('H:i') }}</td>
                            <td>{{ optional($attendance->clock_out_at)->format('H:i') }}</td>
                            <td>
                                @foreach ($attendance->breaks as $b)
                                    {{ optional($b->break_start_at)->format('H:i') }}-{{ optional($b->break_end_at)->format('H:i') }}<br>
                                @endforeach
                            </td>
                            <td><a href="{{ route('admin.attendance.show', $attendance) }}">詳細</a></td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="ct-muted">勤怠はありません。</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
