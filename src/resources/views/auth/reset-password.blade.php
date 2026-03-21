@extends('layouts.guest')

@section('title', 'パスワード再設定')

@section('content')
    <div class="card">
        <h1>パスワード再設定</h1>
        <form method="POST" action="{{ url('/reset-password') }}">
            @csrf
            <input type="hidden" name="token" value="{{ $request->route('token') }}">
            <div class="field">
                <label for="email">メールアドレス</label>
                <input id="email" type="email" name="email" value="{{ old('email', $request->email) }}" required>
                @error('email')<div class="error">{{ $message }}</div>@enderror
            </div>
            <div class="field">
                <label for="password">新しいパスワード</label>
                <input id="password" type="password" name="password" required>
                @error('password')<div class="error">{{ $message }}</div>@enderror
            </div>
            <div class="field">
                <label for="password_confirmation">新しいパスワード（確認）</label>
                <input id="password_confirmation" type="password" name="password_confirmation" required>
            </div>
            <button type="submit" class="btn btn-primary">更新する</button>
        </form>
    </div>
@endsection
