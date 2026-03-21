<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', config('app.name'))</title>
    <style>
        :root { --fg: #1f2937; --muted: #6b7280; --border: #e5e7eb; --primary: #2563eb; --bg: #f9fafb; }
        body { margin: 0; font-family: system-ui, -apple-system, "Segoe UI", sans-serif; color: var(--fg); background: var(--bg); }
        a { color: var(--primary); }
        .container { max-width: 640px; margin: 0 auto; padding: 32px 16px; }
        .card { background: #fff; border: 1px solid var(--border); border-radius: 8px; padding: 24px; }
        .muted { color: var(--muted); font-size: 14px; }
        .btn { display: inline-block; padding: 10px 16px; border-radius: 6px; border: 1px solid var(--border); background: #fff; cursor: pointer; font-size: 14px; }
        .btn-primary { background: var(--primary); color: #fff; border-color: var(--primary); }
        .muted { color: var(--muted); }
        .field { margin-bottom: 16px; }
        label { display: block; font-size: 13px; margin-bottom: 6px; color: var(--muted); }
        input[type=text], input[type=email], input[type=password] { width: 100%; padding: 10px 12px; border: 1px solid var(--border); border-radius: 6px; }
        .error { color: #b91c1c; font-size: 13px; margin-top: 6px; }
        h1 { font-size: 22px; margin: 0 0 16px; }
    </style>
</head>
<body>
    <div class="container">
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
    </div>
</body>
</html>
