@extends('layouts.admin')

@section('title', 'スタッフ一覧')

@section('content')
    <div class="card">
        <h1>スタッフ一覧</h1>
        <table>
            <thead>
                <tr>
                    <th>名前</th>
                    <th>メールアドレス</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($staff as $s)
                    <tr>
                        <td>{{ $s->name }}</td>
                        <td>{{ $s->email }}</td>
                        <td><a href="{{ route('admin.staff.attendance', $s) }}">勤怠一覧</a></td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
