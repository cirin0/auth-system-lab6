<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Двофакторна аутентифікація</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="simple-body">
<div class="card max-w-lg">
    <h2 class="h1">Двофакторна аутентифікація</h2>
    <p class="small text-center mb-4" style="margin-top:-8px;color:var(--c2)">Введіть код з вашого додатку</p>

    <form method="POST" action="{{ route('two-factor.verify') }}">
        @csrf

        <div class="mb-6">
            <input
                type="text"
                name="code"
                maxlength="10"
                class="input"
                placeholder="000000"
                required
                autofocus
            >
            <p class="small text-center" style="margin-top:8px;color:#555">Або використайте recovery код</p>
            @error('code')
            <p class="small text-center" style="color:var(--c3);margin-top:8px">{{ $message }}</p>
            @enderror
        </div>

        <button type="submit" class="btn">Підтвердити</button>
    </form>

    <div class="text-center mt-6">
        <a href="{{ route('login') }}" class="link small">← Назад до входу</a>
    </div>
</div>
</body>
</html>
