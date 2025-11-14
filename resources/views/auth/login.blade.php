<!doctype html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Вхід</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    {!! app('captcha')->renderJs() !!}
</head>
<body class="simple-body">
<div class="card max-w-md">
    <h2 class="h1">Вхід</h2>

    @if(session('success'))
        <div class="notice">{{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div class="notice">{{ session('error') }}</div>
    @endif

    <a href="{{ route('auth.github') }}" class="btn btn-dark mb-4">
        <svg width="20" height="20" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
            <path
                d="M12 0c-6.626 0-12 5.373-12 12 0 5.302 3.438 9.8 8.207 11.387.599.111.793-.261.793-.577v-2.234c-3.338.726-4.033-1.416-4.033-1.416-.546-1.387-1.333-1.756-1.333-1.756-1.089-.745.083-.729.083-.729 1.205.084 1.839 1.237 1.839 1.237 1.07 1.834 2.807 1.304 3.492.997.107-.775.418-1.305.762-1.604-2.665-.305-5.467-1.334-5.467-5.931 0-1.311.469-2.381 1.236-3.221-.124-.303-.535-1.524.117-3.176 0 0 1.008-.322 3.301 1.23.957-.266 1.983-.399 3.003-.404 1.02.005 2.047.138 3.006.404 2.291-1.552 3.297-1.23 3.297-1.23.653 1.653.242 2.874.118 3.176.77.84 1.235 1.911 1.235 3.221 0 4.609-2.807 5.624-5.479 5.921.43.372.823 1.102.823 2.222v3.293c0 .319.192.694.801.576 4.765-1.589 8.199-6.086 8.199-11.386 0-6.627-5.373-12-12-12z"/>
        </svg>
        Увійти через GitHub
    </a>

    <div class="divider"><span class="chip">Або</span></div>

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <div class="mb-6">
            <label class="label" for="email">Email</label>
            <input
                id="email"
                type="email"
                name="email"
                value="{{ old('email') }}"
                class="input"
                required
                autofocus
            >
            @error('email')
            <p class="small" style="color:var(--c3)">{{ $message }}</p>
            @if(str_contains($message, 'підтвердіть ваш email'))
                <div class="notice">
                    <p class="small" style="margin-bottom:8px">Не отримали лист?</p>
                    <form method="POST" action="{{ route('resend.verification') }}">
                        @csrf
                        <input type="hidden" name="email" value="{{ old('email') }}">
                        <button type="submit" class="btn" style="width:auto;padding:8px 12px">
                            Відправити повторно
                        </button>
                    </form>
                </div>
            @endif
            @enderror
        </div>

        <div class="mb-6">
            <label class="label" for="password">Пароль</label>
            <input
                id="password"
                type="password"
                name="password"
                class="input"
                required
            >
            @error('password')
            <p class="small" style="color:var(--c3)">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-6">
            {!! app('captcha')->display() !!}
        </div>
        @error('g-recaptcha-response')
        <p class="small" style="color:var(--c3);margin-top:-12px;margin-bottom:16px">{{ $message }}</p>
        @enderror

        <button type="submit" class="btn">
            Увійти
        </button>
    </form>

    <div class="text-center mt-6">
        <span class="small">Немає акаунту?</span>
        <a href="{{ route('register') }}" class="link" style="margin-left:6px">
            Зареєструватися
        </a>
    </div>
</div>
</body>
</html>
