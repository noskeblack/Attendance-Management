@extends('layouts.admin')

@section('title', '修正申請承認')

@section('content')
    @php
        /** @var \App\Models\StampCorrectionRequest $requestModel */
        $a = $requestModel->attendance;
    @endphp
    <div class="card">
        <h1>修正申請承認</h1>
        <p><strong>スタッフ</strong>：{{ $requestModel->user->name }}</p>
        <p><strong>対象日</strong>：{{ $a->work_date->locale('ja')->isoFormat('YYYY年M月D日(ddd)') }}</p>

        <h2>申請内容</h2>
        <p><strong>出勤</strong>：{{ optional($requestModel->requested_clock_in_at)->format('H:i') }}</p>
        <p><strong>退勤</strong>：{{ optional($requestModel->requested_clock_out_at)->format('H:i') }}</p>
        <p><strong>備考</strong>：{{ $requestModel->remark }}</p>
        <p><strong>休憩</strong></p>
        <ul>
            @foreach ($requestModel->requested_breaks ?? [] as $row)
                <li>
                    {{ isset($row['break_start_at']) ? \Carbon\Carbon::parse($row['break_start_at'])->format('H:i') : '' }}
                    〜
                    {{ isset($row['break_end_at']) ? \Carbon\Carbon::parse($row['break_end_at'])->format('H:i') : '' }}
                </li>
            @endforeach
        </ul>

        <form method="POST" action="{{ route('stamp_correction_request.approve.store', $requestModel) }}" style="margin-top:16px;">
            @csrf
            <button type="submit" class="btn btn-primary">承認する</button>
        </form>
    </div>
@endsection
