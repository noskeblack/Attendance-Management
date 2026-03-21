<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', '管理者') - {{ config('app.name') }}</title>
    <style>
        :root { --fg: #1f2937; --muted: #6b7280; --border: #e5e7eb; --primary: #111827; --bg: #f9fafb; }
        * { box-sizing: border-box; }
        body { margin: 0; font-family: system-ui, -apple-system, "Segoe UI", sans-serif; color: var(--fg); background: var(--bg); }
        a { color: #2563eb; text-decoration: none; }
        .container { max-width: 1200px; margin: 0 auto; padding: 24px 16px; }
        header { background: #fff; border-bottom: 1px solid var(--border); }
        .header-inner { max-width: 1200px; margin: 0 auto; padding: 16px; display: flex; align-items: center; justify-content: space-between; gap: 16px; flex-wrap: wrap; }
        .brand { font-weight: 700; }
        nav { display: flex; gap: 16px; align-items: center; flex-wrap: wrap; }
        .card { background: #fff; border: 1px solid var(--border); border-radius: 8px; padding: 24px; }
        table { width: 100%; border-collapse: collapse; background: #fff; }
        th, td { border: 1px solid var(--border); padding: 10px; text-align: left; font-size: 14px; }
        th { background: #f3f4f6; }
        .btn { display: inline-block; padding: 10px 16px; border-radius: 6px; border: 1px solid var(--border); background: #fff; cursor: pointer; font-size: 14px; }
        .btn-primary { background: #111827; color: #fff; border-color: #111827; }
        h1 { font-size: 22px; margin: 0 0 16px; }
        .error { color: #b91c1c; font-size: 13px; }
        .success { color: #047857; margin-bottom: 12px; }
        input[type=text], input[type=email], input[type=password], input[type=time], textarea { width: 100%; max-width: 520px; padding: 10px 12px; border: 1px solid var(--border); border-radius: 6px; }
        label { display: block; font-size: 13px; margin-bottom: 6px; color: var(--muted); }
        .field { margin-bottom: 16px; }
    </style>
</head>
<body>
    <header>
        <div class="header-inner">
            <div class="brand">管理者：勤怠管理</div>
            <nav>
                <a href="{{ route('admin.attendance.daily') }}">日次勤怠</a>
                <a href="{{ route('admin.staff.index') }}">スタッフ一覧</a>
                <a href="{{ route('stamp_correction_request.index') }}">申請一覧</a>
                <form method="POST" action="{{ route('logout') }}" style="display:inline;">
                    @csrf
                    <button type="submit" class="btn">ログアウト</button>
                </form>
            </nav>
        </div>
    </header>
    <main class="container">
        @if (session('status'))
            <p class="success">{{ session('status') }}</p>
        @endif
        @if ($errors->any())
            <div class="card" style="margin-bottom:16px;border-color:#fecaca;">
                <ul style="margin:0;padding-left:18px;">
                    @foreach ($errors->all() as $e)
                        <li class="error">{{ $e }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        @yield('content')
    </main>
</body>
</html>
