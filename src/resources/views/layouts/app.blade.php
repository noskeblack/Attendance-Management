<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', config('app.name'))</title>
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif
    @stack('head')
</head>
<body>
    <header class="ct-header">
        <div class="ct-header-inner">
            <div class="brand">COACHTECH 勤怠管理</div>
            <nav class="ct-nav">
                @auth
                    <a href="{{ route('attendance.index') }}">勤怠打刻</a>
                    <a href="{{ route('attendance.list') }}">勤怠一覧</a>
                    <a href="{{ route('stamp_correction_request.index') }}">申請一覧</a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="ct-btn">ログアウト</button>
                    </form>
                @endauth
            </nav>
        </div>
    </header>
    <main class="ct-container">
        @if (session('status'))
            <p class="ct-success">{{ session('status') }}</p>
        @endif
        @if ($errors->any())
            <div class="ct-card" style="margin-bottom:16px;border-color:#fecaca;">
                <ul style="margin:0;padding-left:18px;">
                    @foreach ($errors->all() as $e)
                        <li class="ct-error">{{ $e }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        @yield('content')
    </main>
</body>
</html>
