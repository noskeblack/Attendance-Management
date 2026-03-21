<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', config('app.name'))</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <main class="ct-container" style="max-width: 720px;">
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
