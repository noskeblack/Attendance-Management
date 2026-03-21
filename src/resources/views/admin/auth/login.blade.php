@extends('layouts.guest')

@section('title', '管理者ログイン')

@section('content')
    <div class="ct-card">
        <h1 class="ct-title">管理者ログイン</h1>
        <form method="POST" action="{{ route('admin.login.store') }}" class="ct-form">
            @csrf
            <div class="ct-field">
                <label class="ct-label" for="email">メールアドレス</label>
                <input class="ct-input" id="email" type="email" name="email" value="{{ old('email') }}" required autofocus>
                @error('email')<p class="ct-error">{{ $message }}</p>@enderror
            </div>
            <div class="ct-field">
                <label class="ct-label" for="password">パスワード</label>
                <input class="ct-input" id="password" type="password" name="password" required>
                @error('password')<p class="ct-error">{{ $message }}</p>@enderror
            </div>
            <div class="ct-field">
                <label class="ct-label"><input type="checkbox" name="remember"> ログイン状態を保持する</label>
            </div>
            <button type="submit" class="ct-btn ct-btn--primary">ログイン</button>
        </form>
    </div>
@endsection
