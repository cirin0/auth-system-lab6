<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Статистика користувача</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="simple-body" style="display:block;padding:0;min-height:100vh;background:var(--c4)">
<header style="background:var(--c1);color:#fff;padding:20px;box-shadow:0 2px 8px rgba(0,0,0,.1)">
    <div
        style="max-width:1200px;margin:0 auto;display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:12px">
        <h2 style="font-size:24px;font-weight:700;margin:0">Статистика: {{ $email }}</h2>
        <a href="{{ route('admin.login-attempts') }}" class="btn"
           style="width:auto;padding:10px 16px;background:#fff;color:var(--c1)">
            ← Назад до логів
        </a>
    </div>
</header>

<main style="max-width:1200px;margin:24px auto;padding:0 20px">
    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(250px,1fr));gap:20px;margin-bottom:24px">
        <div class="card" style="max-width:100%">
            <p style="color:var(--c2);font-size:14px;margin:0 0 12px">Всього спроб</p>
            <p style="font-size:36px;font-weight:700;color:var(--c1);margin:0">{{ $totalAttempts }}</p>
        </div>

        <div class="card" style="max-width:100%">
            <p style="color:var(--c2);font-size:14px;margin:0 0 12px">Успішних входів</p>
            <p style="font-size:36px;font-weight:700;color:#2d8659;margin:0">{{ $successfulAttempts }}</p>
        </div>

        <div class="card" style="max-width:100%">
            <p style="color:var(--c2);font-size:14px;margin:0 0 12px">Невдалих спроб</p>
            <p style="font-size:36px;font-weight:700;color:#c53030;margin:0">{{ $failedAttempts }}</p>
        </div>
    </div>

    <div class="card" style="max-width:100%">
        <h3 style="font-size:20px;font-weight:700;color:var(--c1);margin:0 0 20px">Останні 10 спроб входу</h3>
        <div style="display:flex;flex-direction:column;gap:12px">
            @forelse($recentAttempts as $attempt)
                <div
                    style="display:flex;align-items:center;justify-content:space-between;padding:16px;border:1px solid {{ $attempt->successful ? '#c6e9d3' : '#fdbdbd' }};border-radius:8px;background:{{ $attempt->successful ? '#f0fdf4' : '#fef5f5' }}">
                    <div style="flex:1">
                        <p style="font-weight:600;color:{{ $attempt->successful ? '#2d8659' : '#c53030' }};margin:0 0 6px">
                            {{ $attempt->successful ? '✓ Успішний вхід' : '✗ Невдала спроба' }}
                        </p>
                        <p style="font-size:13px;color:var(--c2);margin:0">{{ $attempt->created_at->format('d.m.Y H:i:s') }}</p>
                        @if($attempt->error_message)
                            <p style="font-size:13px;color:#c53030;margin:6px 0 0">{{ $attempt->error_message }}</p>
                        @endif
                    </div>
                    <div style="text-align:right">
                        <p style="font-size:13px;color:var(--c2);margin:0">IP: {{ $attempt->ip_address }}</p>
                    </div>
                </div>
            @empty
                <p style="color:var(--c2);text-align:center;padding:32px 0;margin:0">Немає спроб входу</p>
            @endforelse
        </div>
    </div>
</main>
</body>
</html>
