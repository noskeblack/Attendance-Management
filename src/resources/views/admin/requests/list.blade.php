@extends('layouts.admin')

@section('title', '申請一覧（管理者）')

@section('content')
    <div class="ct-card">
        <h1 class="ct-title">申請一覧</h1>

        <h2 class="ct-subtitle">承認待ち</h2>
        <div class="ct-table-wrap">
            <table class="ct-table">
                <thead>
                    <tr>
                        <th>スタッフ</th>
                        <th>対象日</th>
                        <th>申請日時</th>
                        <th>備考</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($pending as $req)
                        <tr>
                            <td>{{ $req->user->name }}</td>
                            <td>{{ optional($req->attendance?->work_date)->locale('ja')->isoFormat('YYYY年M月D日') }}</td>
                            <td>{{ $req->created_at->format('Y-m-d H:i') }}</td>
                            <td>{{ \Illuminate\Support\Str::limit($req->remark, 40) }}</td>
                            <td><a href="{{ route('stamp_correction_request.approve', $req) }}">詳細</a></td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="ct-muted">承認待ちの申請はありません。</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <h2 class="ct-subtitle ct-subtitle--spaced">承認済み</h2>
        <div class="ct-table-wrap">
            <table class="ct-table">
                <thead>
                    <tr>
                        <th>スタッフ</th>
                        <th>対象日</th>
                        <th>承認日時</th>
                        <th>備考</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($approved as $req)
                        <tr>
                            <td>{{ $req->user->name }}</td>
                            <td>{{ optional($req->attendance?->work_date)->locale('ja')->isoFormat('YYYY年M月D日') }}</td>
                            <td>{{ optional($req->approved_at)->format('Y-m-d H:i') }}</td>
                            <td>{{ \Illuminate\Support\Str::limit($req->remark, 40) }}</td>
                            <td>
                                @if ($req->attendance)
                                    <a href="{{ route('admin.attendance.show', $req->attendance) }}">詳細</a>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="ct-muted">承認済みの申請はありません。</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
