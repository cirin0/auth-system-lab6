<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Дашборд</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="simple-body">
<div class="card max-w-3xl">
    <div class="flex items-center justify-between mb-4">
        <div class="flex items-center gap-3">
            @if(Auth::user()->avatar)
                <img src="{{ Auth::user()->avatar }}" alt="Avatar"
                     style="width:40px;height:40px;border-radius:50%;object-fit:cover;border:2px solid #fff;box-shadow:0 1px 3px rgba(0,0,0,.1)">
            @endif
            <h2 class="h1" style="margin:0;font-size:22px;text-align:left">Dashboard</h2>
        </div>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="btn" style="width:auto;padding:10px 14px">Вийти</button>
        </form>
    </div>

    <div class="mb-6">
        @if(session('success'))
            <div class="notice">{{ session('success') }}</div>
        @endif

        @if(session('warning'))
            <div class="notice">{{ session('warning') }}</div>
        @endif

        @if(session('error'))
            <div class="notice">{{ session('error') }}</div>
        @endif
    </div>

    <h3 class="h1" style="font-size:20px;margin-bottom:12px;text-align:left">Вітаємо, {{ Auth::user()->name }}! 👋</h3>
    <div class="mb-6" style="color:#555">
        <p><strong>Email:</strong> {{ Auth::user()->email }}</p>
        <p><strong>Дата реєстрації:</strong> {{ Auth::user()->created_at->format('d.m.Y H:i') }}</p>
        <p>
            <strong>2FA:</strong>
            @if(Auth::user()->two_factor_enabled)
                <span style="color:green;font-weight:600">✓ Увімкнено</span>
            @else
                <span style="color:#a67f00;font-weight:600">✗ Вимкнено</span>
            @endif
        </p>
        <p>
            <strong>GitHub:</strong>
            @if(Auth::user()->github_id)
                <span style="color:green;font-weight:600">✓ Підключено</span>
            @else
                <span class="small">✗ Не підключено</span>
            @endif
        </p>
    </div>

    <div class="flex gap-3 flex-wrap mt-4">
        <a href="{{ route('two-factor.show') }}" class="btn" style="width:auto">🔐 Налаштування 2FA</a>

        @if(!Auth::user()->github_id)
            <a href="{{ route('auth.github') }}" class="btn btn-dark inline-flex items-center gap-2" style="width:auto">
                <svg width="20" height="20" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path
                        d="M12 0c-6.626 0-12 5.373-12 12 0 5.302 3.438 9.8 8.207 11.387.599.111.793-.261.793-.577v-2.234c-3.338.726-4.033-1.416-4.033-1.416-.546-1.387-1.333-1.756-1.333-1.756-1.089-.745.083-.729.083-.729 1.205.084 1.839 1.237 1.839 1.237 1.07 1.834 2.807 1.304 3.492.997.107-.775.418-1.305.762-1.604-2.665-.305-5.467-1.334-5.467-5.931 0-1.311.469-2.381 1.236-3.221-.124-.303-.535-1.524.117-3.176 0 0 1.008-.322 3.301 1.23.957-.266 1.983-.399 3.003-.404 1.02.005 2.047.138 3.006.404 2.291-1.552 3.297-1.23 3.297-1.23.653 1.653.242 2.874.118 3.176.77.84 1.235 1.911 1.235 3.221 0 4.609-2.807 5.624-5.479 5.921.43.372.823 1.102.823 2.222v3.293c0 .319.192.694.801.576 4.765-1.589 8.199-6.086 8.199-11.386 0-6.627-5.373-12-12-12z"/>
                </svg>
                Підключити GitHub
            </a>
        @else
            <form method="POST" action="{{ route('auth.github.unlink') }}" class="inline">
                @csrf
                <button type="submit" onclick="return confirm('Ви впевнені, що хочете від\'єднати GitHub?')"
                        class="btn inline-flex items-center gap-2" style="width:auto">
                    <svg width="20" height="20" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path
                            d="M12 0c-6.626 0-12 5.373-12 12 0 5.302 3.438 9.8 8.207 11.387.599.111.793-.261.793-.577v-2.234c-3.338.726-4.033-1.416-4.033-1.416-.546-1.387-1.333-1.756-1.333-1.756-1.089-.745.083-.729.083-.729 1.205.084 1.839 1.237 1.839 1.237 1.07 1.834 2.807 1.304 3.492.997.107-.775.418-1.305.762-1.604-2.665-.305-5.467-1.334-5.467-5.931 0-1.311.469-2.381 1.236-3.221-.124-.303-.535-1.524.117-3.176 0 0 1.008-.322 3.301 1.23.957-.266 1.983-.399 3.003-.404 1.02.005 2.047.138 3.006.404 2.291-1.552 3.297-1.23 3.297-1.23.653 1.653.242 2.874.118 3.176.77.84 1.235 1.911 1.235 3.221 0 4.609-2.807 5.624-5.479 5.921.43.372.823 1.102.823 2.222v3.293c0 .319.192.694.801.576 4.765-1.589 8.199-6.086 8.199-11.386 0-6.627-5.373-12-12-12z"/>
                    </svg>
                    Від'єднати GitHub
                </button>
            </form>
        @endif
    </div>
</div>
</body>
</html>
