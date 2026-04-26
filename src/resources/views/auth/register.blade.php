@extends('layouts.guest')

@section('title', '会員登録')

@section('content')
    <div class="ct-card">
        <h1 class="ct-title">会員登録</h1>
        <form method="POST" action="{{ route('register') }}" class="ct-form" novalidate>
            @csrf
            <div class="ct-field">
                <label class="ct-label" for="name">お名前</label>
                <input class="ct-input" id="name" type="text" name="name" value="{{ old('name') }}" required autofocus>
                @error('name')<p class="ct-error">{{ $message }}</p>@enderror
            </div>
            <div class="ct-field">
                <label class="ct-label" for="email">メールアドレス</label>
                <input class="ct-input" id="email" type="email" name="email" value="{{ old('email') }}" required>
                @error('email')<p class="ct-error">{{ $message }}</p>@enderror
            </div>
            <div class="ct-field">
                <label class="ct-label" for="password">パスワード</label>
                <input class="ct-input" id="password" type="password" name="password" required autocomplete="new-password">
                @error('password')<p class="ct-error">{{ $message }}</p>@enderror
            </div>
            <div class="ct-field">
                <label class="ct-label" for="password_confirmation">パスワード（確認）</label>
                <input class="ct-input" id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password">
            </div>
            <button type="submit" class="ct-btn ct-btn--primary">登録する</button>
        </form>
        <p class="ct-muted ct-block-mt">
            <a href="{{ route('login') }}">ログインはこちら</a>
        </p>
    </div>
@endsection
