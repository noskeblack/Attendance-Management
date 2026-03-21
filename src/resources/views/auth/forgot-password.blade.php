@extends('layouts.guest')

@section('title', 'パスワード再設定')

@section('content')
    <div class="ct-card">
        <h1 class="ct-title">パスワード再設定</h1>
        <form method="POST" action="{{ url('/forgot-password') }}" class="ct-form">
            @csrf
            <div class="ct-field">
                <label class="ct-label" for="email">メールアドレス</label>
                <input class="ct-input" id="email" type="email" name="email" value="{{ old('email') }}" required>
                @error('email')<p class="ct-error">{{ $message }}</p>@enderror
            </div>
            <button type="submit" class="ct-btn ct-btn--primary">再設定メールを送る</button>
        </form>
    </div>
@endsection
