@extends('layouts.admin')

@section('title', '勤怠詳細（管理者）')

@section('content')
    <div class="card">
        <h1>勤怠詳細（管理者）</h1>
        <p><strong>名前</strong>：{{ $attendance->user->name }}</p>
        <p><strong>日付</strong>：{{ $attendance->work_date->locale('ja')->isoFormat('YYYY年M月D日(ddd)') }}</p>

        <form method="POST" action="{{ route('admin.attendance.update', $attendance) }}">
            @csrf
            @method('PUT')
            <div class="field">
                <label>出勤・退勤</label>
                <div style="display:flex; gap:12px; flex-wrap:wrap; align-items:center;">
                    <input type="time" name="clock_in" value="{{ old('clock_in', optional($attendance->clock_in_at)->format('H:i')) }}" required>
                    <span>〜</span>
                    <input type="time" name="clock_out" value="{{ old('clock_out', optional($attendance->clock_out_at)->format('H:i')) }}">
                </div>
                @error('clock_in')<div class="error">{{ $message }}</div>@enderror
            </div>
            <div class="field">
                <label for="note">備考</label>
                <textarea id="note" name="note" rows="4">{{ old('note', $attendance->note) }}</textarea>
            </div>
            <button type="submit" class="btn btn-primary">保存</button>
        </form>
    </div>
@endsection
