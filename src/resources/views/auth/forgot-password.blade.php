@extends('layouts.guest')

@section('title', 'パスワード再設定')

@section('content')
    <div class="card">
        <h1>パスワード再設定</h1>
        <form method="POST" action="{{ url('/forgot-password') }}">
            @csrf
            <div class="field">
                <label for="email">メールアドレス</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required>
                @error('email')<div class="error">{{ $message }}</div>@enderror
            </div>
            <button type="submit" class="btn btn-primary">再設定メールを送る</button>
        </form>
    </div>
@endsection
