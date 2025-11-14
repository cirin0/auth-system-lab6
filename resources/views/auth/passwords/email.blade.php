<!doctype html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Відновлення пароля</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="simple-body">
<div class="card max-w-md">
    <h2 class="h1">Відновлення пароля</h2>
    <p class="text-center small mb-6">Введіть ваш email для отримання посилання</p>

    @if(session('success'))
        <div class="notice" style="background:#d1f4e0;border-color:#a3d9b1;">{{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div class="notice" style="background:#f8d7da;border-color:#f5c6cb;">{{ session('error') }}</div>
    @endif

    <form method="POST" action="{{ route('password.email') }}">
        @csrf

        <div class="mb-6">
            <label class="label" for="email">Email адреса</label>
            <input
                id="email"
                type="email"
                name="email"
                value="{{ old('email', $email ?? '') }}"
                class="input"
                placeholder="your@email.com"
                required
                autofocus
            >
            @error('email')
            <p class="small" style="color:var(--c3);margin-top:8px">{{ $message }}</p>
            @enderror
        </div>

        <button type="submit" class="btn">
            Відправити посилання
        </button>
    </form>

    <div class="text-center mt-6">
        <a href="{{ route('login') }}" class="link small">
            ← Назад до входу
        </a>
    </div>
</div>
</body>
</html>
