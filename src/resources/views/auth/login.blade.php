@extends('layouts.guest')

@section('title', 'ログイン')

@section('content')
    <div class="ct-card">
        <h1 class="ct-title">ログイン（一般）</h1>
        <form method="POST" action="{{ route('login') }}" class="ct-form">
            @csrf
            <div class="ct-field">
                <label class="ct-label" for="email">メールアドレス</label>
                <input class="ct-input" id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username">
                @error('email')<p class="ct-error">{{ $message }}</p>@enderror
            </div>
            <div class="ct-field">
                <label class="ct-label" for="password">パスワード</label>
                <input class="ct-input" id="password" type="password" name="password" required autocomplete="current-password">
                @error('password')<p class="ct-error">{{ $message }}</p>@enderror
            </div>
            <div class="ct-field">
                <label class="ct-label"><input type="checkbox" name="remember"> ログイン状態を保持する</label>
            </div>
            <button type="submit" class="ct-btn ct-btn--primary">ログイン</button>
        </form>
        <p class="ct-muted" style="margin-top:16px;">
            <a href="{{ route('register') }}">会員登録はこちら</a>
        </p>
    </div>
@endsection
