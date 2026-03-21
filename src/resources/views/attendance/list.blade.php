@extends('layouts.app')

@section('title', '勤怠一覧')

@section('content')
    <div class="card">
        <h1>勤怠一覧</h1>
        <p class="muted">{{ $current->isoFormat('YYYY年M月') }}</p>
        <div style="display:flex; gap:12px; margin-bottom:16px;">
            <a class="btn" href="{{ route('attendance.list', $prevQuery) }}">前月</a>
            <a class="btn" href="{{ route('attendance.list', $nextQuery) }}">翌月</a>
        </div>

        <div style="overflow:auto;">
            <table>
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
                            <td><a href="{{ route('attendance.detail', $attendance) }}">詳細</a></td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="muted">この月の勤怠はありません。</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
