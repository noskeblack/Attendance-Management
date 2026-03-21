@extends('layouts.app')

@section('title', 'メール認証')

@section('content')
    <div class="card">
        <h1>メール認証</h1>
        <p>登録時に入力したメールアドレス宛に認証メールを送信しました。メール内のリンクをクリックして認証を完了してください。</p>
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <button type="submit" class="btn btn-primary">認証メールを再送する</button>
        </form>
    </div>
@endsection
