<!doctype html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Двофакторна аутентифікація</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="simple-body">
<div class="card max-w-xl">
    <h2 class="h1">Налаштування 2FA</h2>
    <div class="text-center mb-4">
        <a href="{{ route('dashboard') }}" class="link flex justify-center p-1 border border-b-gray-500 rounded-l">←
            Назад
            до
            дашборду</a>
    </div>
    @if(session('success'))
        <div class="notice">{{ session('success') }}</div>
    @endif

    @if(session('warning'))
        <div class="notice">{{ session('warning') }}</div>
    @endif

    <h3 class="h1" style="font-size:20px;margin-bottom:16px">Двофакторна аутентифікація (2FA)</h3>

    @if(Auth::user()->two_factor_enabled)
        <div class="notice">
            <strong>2FA активовано.</strong> Ваш акаунт захищено двофакторною аутентифікацією.
        </div>

        <div class="mb-6" style="border:1px solid #ddd;border-radius:8px;padding:16px">
            <h5 style="margin:0 0 12px;color:var(--c1);font-weight:600">Вимкнути 2FA</h5>
            <form method="POST" action="{{ route('two-factor.disable') }}">
                @csrf
                <div class="mb-4">
                    <label class="label">Введіть ваш пароль для підтвердження</label>
                    <input
                        type="password"
                        name="password"
                        class="input"
                        required
                    >
                    @error('password')
                    <p class="small" style="color:var(--c3)">{{ $message }}</p>
                    @enderror
                </div>
                <button type="submit" class="btn">Вимкнути 2FA</button>
            </form>
        </div>
    @else
        <div class="notice">
            <strong>2FA не активовано.</strong> Рекомендуємо увімкнути 2FA для додаткового захисту вашого акаунту.
        </div>

        <div class="mb-6">
            <h5 style="margin:0 0 8px;color:var(--c1);font-weight:600">Що таке 2FA?</h5>
            <p class="small" style="margin:0 0 12px;color:#555">
                Двофакторна аутентифікація додає додатковий рівень захисту. При вході вам потрібно буде ввести код з
                додатку на телефоні.
            </p>
            <h5 style="margin:0 0 8px;color:var(--c1);font-weight:600">Що вам потрібно:</h5>
            <ul class="small" style="margin:0 0 16px;color:#555;padding-left:18px;list-style:disc">
                <li>Встановити Google Authenticator, Authy або інший TOTP додаток</li>
                <li>Відсканувати QR код</li>
                <li>Зберегти recovery коди в безпечному місці</li>
            </ul>
        </div>

        <form method="POST" action="{{ route('two-factor.enable') }}">
            @csrf
            <button type="submit" class="btn">Увімкнути 2FA</button>
        </form>
    @endif
</div>
</body>
</html>
