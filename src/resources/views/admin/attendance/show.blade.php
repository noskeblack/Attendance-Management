@extends('layouts.admin')

@section('title', '勤怠詳細（管理者）')

@section('content')
    <div class="ct-card">
        <h1 class="ct-title">勤怠詳細（管理者）</h1>
        <p><strong>名前</strong>：{{ $attendance->user->name }}</p>
        <p><strong>日付</strong>：{{ $attendance->work_date->locale('ja')->isoFormat('YYYY年M月D日(ddd)') }}</p>

        @if (session('status'))
            <p class="ct-success">{{ session('status') }}</p>
        @endif
        @error('request')<p class="ct-error">{{ $message }}</p>@enderror

        @if ($readOnly)
            <p class="ct-error">承認待ちのため修正はできません。</p>
        @endif

        <form method="POST" action="{{ route('admin.attendance.update', $attendance) }}" class="ct-form">
            @csrf
            @method('PUT')
            <div class="ct-field">
                <label class="ct-label">出勤・退勤</label>
                <div class="ct-row">
                    <input class="ct-input" type="time" name="clock_in" value="{{ old('clock_in', optional($attendance->clock_in_at)->format('H:i')) }}" required @disabled($readOnly)>
                    <span>〜</span>
                    <input class="ct-input" type="time" name="clock_out" value="{{ old('clock_out', optional($attendance->clock_out_at)->format('H:i')) }}" @disabled($readOnly)>
                </div>
                @error('clock_in')<p class="ct-error">{{ $message }}</p>@enderror
                @error('clock_out')<p class="ct-error">{{ $message }}</p>@enderror
            </div>

            <div class="ct-field">
                <label class="ct-label">休憩</label>
                @php
                    $rows = old('breaks', $attendance->breaks->map(fn ($b) => ['start' => optional($b->break_start_at)->format('H:i'), 'end' => optional($b->break_end_at)->format('H:i')])->values()->all());
                    if (empty($rows)) {
                        $rows = [['start' => '', 'end' => '']];
                    }
                @endphp
                @foreach ($rows as $i => $row)
                    <div class="ct-row ct-row--mb">
                        <input class="ct-input" type="time" name="breaks[{{ $i }}][start]" value="{{ $row['start'] ?? '' }}" @disabled($readOnly)>
                        <span>〜</span>
                        <input class="ct-input" type="time" name="breaks[{{ $i }}][end]" value="{{ $row['end'] ?? '' }}" @disabled($readOnly)>
                    </div>
                @endforeach
                <div class="ct-row ct-row--mb">
                    <input class="ct-input" type="time" name="breaks[{{ count($rows) }}][start]" value="" @disabled($readOnly)>
                    <span>〜</span>
                    <input class="ct-input" type="time" name="breaks[{{ count($rows) }}][end]" value="" @disabled($readOnly)>
                </div>
                @error('breaks')<p class="ct-error">{{ $message }}</p>@enderror
                @foreach ($rows as $i => $row)
                    @error('breaks.'.$i.'.start')<p class="ct-error">{{ $message }}</p>@enderror
                    @error('breaks.'.$i.'.end')<p class="ct-error">{{ $message }}</p>@enderror
                @endforeach
            </div>

            <div class="ct-field">
                <label class="ct-label" for="remark">備考</label>
                <textarea class="ct-textarea" id="remark" name="remark" rows="4" @disabled($readOnly)>{{ old('remark', $attendance->note) }}</textarea>
                @error('remark')<p class="ct-error">{{ $message }}</p>@enderror
            </div>
            <button type="submit" class="ct-btn ct-btn--primary" @disabled($readOnly)>修正</button>
        </form>
    </div>
@endsection
