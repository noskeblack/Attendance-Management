@extends('layouts.app')

@section('title', '勤怠詳細')

@section('content')
    <div class="card">
        <h1>勤怠詳細</h1>

        <p><strong>名前</strong>：{{ auth()->user()->name }}</p>
        <p><strong>日付</strong>：{{ $attendance->work_date->locale('ja')->isoFormat('YYYY年M月D日(ddd)') }}</p>

        @if ($readOnly)
            <p class="error">承認待ちのため修正はできません。</p>
        @endif

        <form method="POST" action="{{ route('attendance.detail.submit', $attendance) }}">
            @csrf
            <div class="field">
                <label>出勤・退勤</label>
                <div style="display:flex; gap:12px; flex-wrap:wrap; align-items:center;">
                    <input type="time" name="clock_in" value="{{ old('clock_in', optional($attendance->clock_in_at)->format('H:i')) }}" @disabled($readOnly)>
                    <span>〜</span>
                    <input type="time" name="clock_out" value="{{ old('clock_out', optional($attendance->clock_out_at)->format('H:i')) }}" @disabled($readOnly)>
                </div>
                @error('clock_in')<div class="error">{{ $message }}</div>@enderror
                @error('clock_out')<div class="error">{{ $message }}</div>@enderror
            </div>

            <div class="field">
                <label>休憩</label>
                @php
                    $rows = old('breaks', $attendance->breaks->map(fn ($b) => ['start' => optional($b->break_start_at)->format('H:i'), 'end' => optional($b->break_end_at)->format('H:i')])->values()->all());
                    if (empty($rows)) {
                        $rows = [['start' => '', 'end' => '']];
                    }
                @endphp
                @foreach ($rows as $i => $row)
                    <div style="display:flex; gap:12px; flex-wrap:wrap; align-items:center; margin-bottom:8px;">
                        <input type="time" name="breaks[{{ $i }}][start]" value="{{ $row['start'] ?? '' }}" @disabled($readOnly)>
                        <span>〜</span>
                        <input type="time" name="breaks[{{ $i }}][end]" value="{{ $row['end'] ?? '' }}" @disabled($readOnly)>
                    </div>
                @endforeach
                <div style="display:flex; gap:12px; flex-wrap:wrap; align-items:center; margin-bottom:8px;">
                    <input type="time" name="breaks[{{ count($rows) }}][start]" value="" @disabled($readOnly)>
                    <span>〜</span>
                    <input type="time" name="breaks[{{ count($rows) }}][end]" value="" @disabled($readOnly)>
                </div>
                @error('breaks')<div class="error">{{ $message }}</div>@enderror
            </div>

            <div class="field">
                <label for="remark">備考</label>
                <textarea id="remark" name="remark" rows="4" @disabled($readOnly)>{{ old('remark', $attendance->note) }}</textarea>
                @error('remark')<div class="error">{{ $message }}</div>@enderror
            </div>

            <button type="submit" class="btn btn-primary" @disabled($readOnly)>修正</button>
        </form>
    </div>
@endsection
