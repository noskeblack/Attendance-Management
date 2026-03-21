@php
    use App\Enums\AttendanceStatus;
@endphp

@extends('layouts.app')

@section('title', '勤怠打刻')

@section('content')
    <div class="ct-card">
        <h1 class="ct-title">勤怠打刻</h1>
        <p class="ct-muted">現在の日時：{{ $nowFormatted }}</p>
        <p><strong>ステータス：</strong>{{ $statusLabel }}</p>

        @if (session('clock_out_message'))
            <p class="ct-success">{{ session('clock_out_message') }}</p>
        @endif

        <div class="ct-actions" style="margin-top:16px;">
            @if ($status === AttendanceStatus::OffDuty)
                <form method="POST" action="{{ route('attendance.clock_in') }}">
                    @csrf
                    <button type="submit" class="ct-btn ct-btn--primary">出勤</button>
                </form>
            @endif

            @if ($status === AttendanceStatus::Working)
                <form method="POST" action="{{ route('attendance.break_start') }}">
                    @csrf
                    <button type="submit" class="ct-btn ct-btn--primary">休憩入</button>
                </form>
                <form method="POST" action="{{ route('attendance.clock_out') }}">
                    @csrf
                    <button type="submit" class="ct-btn">退勤</button>
                </form>
            @endif

            @if ($status === AttendanceStatus::OnBreak)
                <form method="POST" action="{{ route('attendance.break_end') }}">
                    @csrf
                    <button type="submit" class="ct-btn ct-btn--primary">休憩戻</button>
                </form>
            @endif

            @if ($status === AttendanceStatus::Completed)
                <p class="ct-muted">本日の勤務は終了しています。</p>
            @endif
        </div>
    </div>
@endsection
