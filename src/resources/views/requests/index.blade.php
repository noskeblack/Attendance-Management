@extends('layouts.app')

@section('title', '申請一覧')

@section('content')
    <div class="card">
        <h1>申請一覧</h1>

        <h2>承認待ち</h2>
        <table>
            <thead>
                <tr>
                    <th>対象日</th>
                    <th>申請日時</th>
                    <th>備考</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($pending as $req)
                    <tr>
                        <td>{{ optional($req->attendance?->work_date)->locale('ja')->isoFormat('YYYY年M月D日') }}</td>
                        <td>{{ $req->created_at->format('Y-m-d H:i') }}</td>
                        <td>{{ \Illuminate\Support\Str::limit($req->remark, 40) }}</td>
                        <td><a href="{{ route('attendance.detail', $req->attendance_id) }}">詳細</a></td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="muted">承認待ちの申請はありません。</td></tr>
                @endforelse
            </tbody>
        </table>

        <h2 style="margin-top:24px;">承認済み</h2>
        <table>
            <thead>
                <tr>
                    <th>対象日</th>
                    <th>承認日時</th>
                    <th>備考</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($approved as $req)
                    <tr>
                        <td>{{ optional($req->attendance?->work_date)->locale('ja')->isoFormat('YYYY年M月D日') }}</td>
                        <td>{{ optional($req->approved_at)->format('Y-m-d H:i') }}</td>
                        <td>{{ \Illuminate\Support\Str::limit($req->remark, 40) }}</td>
                        <td><a href="{{ route('attendance.detail', $req->attendance_id) }}">詳細</a></td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="muted">承認済みの申請はありません。</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
