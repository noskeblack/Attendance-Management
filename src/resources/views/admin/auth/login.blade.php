@extends('layouts.guest')

@section('title', '管理者ログイン')

@section('content')
    <div class="card">
        <h1>管理者ログイン</h1>
        <form method="POST" action="{{ route('admin.login.store') }}">
            @csrf
            <div class="field">
                <label for="email">メールアドレス</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus>
                @error('email')<div class="error">{{ $message }}</div>@enderror
            </div>
            <div class="field">
                <label for="password">パスワード</label>
                <input id="password" type="password" name="password" required>
                @error('password')<div class="error">{{ $message }}</div>@enderror
            </div>
            <div class="field">
                <label><input type="checkbox" name="remember"> ログイン状態を保持する</label>
            </div>
            <button type="submit" class="btn btn-primary">ログイン</button>
        </form>
    </div>
@endsection
