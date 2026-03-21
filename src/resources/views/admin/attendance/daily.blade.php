@extends('layouts.admin')

@section('title', '日次勤怠一覧')

@section('content')
    <div class="card">
        <h1>日次勤怠一覧</h1>
        <p class="muted">{{ $date->locale('ja')->isoFormat('YYYY年M月D日(ddd)') }}</p>
        <div style="display:flex; gap:12px; margin-bottom:16px;">
            <a class="btn" href="{{ route('admin.attendance.daily', $prevQuery) }}">前日</a>
            <a class="btn" href="{{ route('admin.attendance.daily', $nextQuery) }}">翌日</a>
        </div>

        <div style="overflow:auto;">
            <table>
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
