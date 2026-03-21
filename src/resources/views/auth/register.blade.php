@extends('layouts.guest')

@section('title', '会員登録')

@section('content')
    <div class="card">
        <h1>会員登録</h1>
        <form method="POST" action="{{ route('register') }}">
            @csrf
            <div class="field">
                <label for="name">お名前</label>
                <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus>
                @error('name')<div class="error">{{ $message }}</div>@enderror
            </div>
            <div class="field">
                <label for="email">メールアドレス</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required>
                @error('email')<div class="error">{{ $message }}</div>@enderror
            </div>
            <div class="field">
                <label for="password">パスワード</label>
                <input id="password" type="password" name="password" required autocomplete="new-password">
                @error('password')<div class="error">{{ $message }}</div>@enderror
            </div>
            <div class="field">
                <label for="password_confirmation">パスワード（確認）</label>
                <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password">
            </div>
            <button type="submit" class="btn btn-primary">登録する</button>
        </form>
        <p class="muted" style="margin-top:16px;">
            <a href="{{ route('login') }}">ログインはこちら</a>
        </p>
    </div>
@endsection
