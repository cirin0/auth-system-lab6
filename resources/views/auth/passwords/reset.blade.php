<!doctype html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Скидання пароля</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="simple-body">
<div class="card max-w-md">
    <h2 class="h1">Новий пароль</h2>
    <p class="text-center small mb-6">Введіть ваш новий пароль</p>

    @if($errors->any())
        <div class="notice" style="background:#f8d7da;border-color:#f5c6cb;margin-bottom:16px">
            <ul style="margin:0;padding-left:20px">
                @foreach($errors->all() as $error)
                    <li class="small">{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('password.update') }}">
        @csrf

        <input type="hidden" name="token" value="{{ $token }}">
        <input type="hidden" name="email" value="{{ $email }}">

        <div class="mb-6">
            <label class="label" for="email">Email</label>
            <input
                id="email"
                type="email"
                name="email"
                value="{{ $email }}"
                class="input"
                style="background:#f5f5f5;cursor:not-allowed"
                readonly
            >
        </div>

        <div class="mb-6">
            <label class="label" for="password">Новий пароль</label>
            <input
                id="password"
                type="password"
                name="password"
                class="input"
                required
                autofocus
            >
            <div class="small" style="margin-top:8px;color:#666;line-height:1.6">
                Пароль повинен містити:<br>
                • Мінімум 8 символів<br>
                • Великі та малі літери<br>
                • Цифру<br>
                • Спеціальний символ (@$!%*#?&)
            </div>
        </div>

        <div class="mb-6">
            <label class="label" for="password_confirmation">Підтвердження пароля</label>
            <input
                id="password_confirmation"
                type="password"
                name="password_confirmation"
                class="input"
                required
            >
        </div>

        <button type="submit" class="btn">
            Скинути пароль
        </button>
    </form>

    <div class="notice mt-6" style="background:#e3f2fd;border-color:#90caf9">
        <p class="small">
            ⏱️ Це посилання дійсне протягом 60 хвилин з моменту запиту.
        </p>
    </div>
</div>
</body>
</html>
