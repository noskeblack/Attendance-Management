@extends('layouts.app')

@section('title', 'メール認証')

@section('content')
    <div class="ct-card">
        <h1 class="ct-title">メール認証</h1>
        <p class="ct-muted">登録時に入力したメールアドレス宛に認証メールを送信しました。メール内のリンクをクリックして認証を完了してください。</p>
        @php
            $verifyUrl = \Illuminate\Support\Facades\URL::temporarySignedRoute(
                'verification.verify',
                now()->addMinutes(60),
                [
                    'id' => auth()->id(),
                    'hash' => sha1(auth()->user()->getEmailForVerification()),
                ]
            );
        @endphp
        <p class="ct-block-mt">
            <a class="ct-btn ct-btn--primary" href="{{ $verifyUrl }}">認証はこちらから</a>
        </p>
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <button type="submit" class="ct-btn ct-btn--primary">認証メール再送</button>
        </form>
    </div>
@endsection
