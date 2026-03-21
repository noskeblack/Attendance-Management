@extends('layouts.guest')

@section('title', 'パスワード再設定')

@section('content')
    <div class="ct-card">
        <h1 class="ct-title">パスワード再設定</h1>
        <form method="POST" action="{{ url('/reset-password') }}" class="ct-form">
            @csrf
            <input type="hidden" name="token" value="{{ $request->route('token') }}">
            <div class="ct-field">
                <label class="ct-label" for="email">メールアドレス</label>
                <input class="ct-input" id="email" type="email" name="email" value="{{ old('email', $request->email) }}" required>
                @error('email')<p class="ct-error">{{ $message }}</p>@enderror
            </div>
            <div class="ct-field">
                <label class="ct-label" for="password">新しいパスワード</label>
                <input class="ct-input" id="password" type="password" name="password" required>
                @error('password')<p class="ct-error">{{ $message }}</p>@enderror
            </div>
            <div class="ct-field">
                <label class="ct-label" for="password_confirmation">新しいパスワード（確認）</label>
                <input class="ct-input" id="password_confirmation" type="password" name="password_confirmation" required>
            </div>
            <button type="submit" class="ct-btn ct-btn--primary">更新する</button>
        </form>
    </div>
@endsection
