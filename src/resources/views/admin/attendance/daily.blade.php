@extends('layouts.admin')

@section('title', '日次勤怠一覧')

@section('content')
    <div class="ct-card">
        <h1 class="ct-title">日次勤怠一覧</h1>
        <p class="ct-muted">{{ $date->locale('ja')->isoFormat('YYYY年M月D日(ddd)') }}</p>
        <div class="ct-actions" style="margin-bottom:16px;">
            <a class="ct-btn" href="{{ route('admin.attendance.daily', $prevQuery) }}">前日</a>
            <a class="ct-btn" href="{{ route('admin.attendance.daily', $nextQuery) }}">翌日</a>
        </div>

        <div class="ct-table-wrap">
            <table class="ct-table">
                <thead>
                    <tr>
                        <th>名前</th>
                        <th>出勤</th>
                        <th>退勤</th>
                        <th>休憩</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($users as $u)
                        @php $a = $attendances->get($u->id); @endphp
                        <tr>
                            <td>{{ $u->name }}</td>
                            <td>{{ $a ? optional($a->clock_in_at)->format('H:i') : '' }}</td>
                            <td>{{ $a ? optional($a->clock_out_at)->format('H:i') : '' }}</td>
                            <td>
                                @if ($a)
                                    @foreach ($a->breaks as $b)
                                        {{ optional($b->break_start_at)->format('H:i') }}-{{ optional($b->break_end_at)->format('H:i') }}<br>
                                    @endforeach
                                @endif
                            </td>
                            <td>
                                @if ($a)
                                    <a href="{{ route('admin.attendance.show', $a) }}">詳細</a>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
