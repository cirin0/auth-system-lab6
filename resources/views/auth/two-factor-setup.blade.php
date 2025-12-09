<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Налаштування 2FA</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="simple-body">
<div class="card max-w-2xl">
    <h2 class="h1">Налаштування 2FA</h2>

    <h3 class="h1" style="font-size:20px;margin-bottom:12px">Крок 1: Відскануйте QR код</h3>

    <div class="mb-6 text-center" style="border:1px solid #ddd;border-radius:8px;padding:16px;background:#fff">
        <div class="inline-block"
             style="background:#fff;padding:12px;border-radius:8px;box-shadow:0 2px 8px rgba(0,0,0,.06)">
            {!! $qrCode !!}
        </div>
        <p class="small mt-2" style="color:#555">Відскануйте цей QR код за допомогою Google Authenticator або Authy</p>
    </div>

    <div class="notice" style="background:#fff">
        <p class="small" style="margin:0 0 8px"><strong>Або введіть код вручну:</strong></p>
        <code
            style="background:#f7f7f7;padding:6px 10px;border-radius:6px;color:var(--c3);font-family:ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, 'Liberation Mono', 'Courier New', monospace">{{ $secret }}</code>
    </div>

    <h3 class="h1" style="font-size:20px;margin-bottom:8px">Крок 2: Збережіть Recovery коди</h3>

    <div class="mb-6" style="border:1px solid #ddd;border-radius:8px;padding:16px">
        <p class="small" style="margin:0 0 12px;color:#555">
            <strong>ВАЖЛИВО:</strong> Збережіть ці коди в безпечному місці! Вони потрібні, якщо ви втратите доступ до
            телефону.
        </p>
        <div class="mt-2"
             style="background:#fff;border:1px solid #eee;border-radius:6px;padding:12px;font-family:ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, 'Liberation Mono', 'Courier New', monospace;font-size:12px">
            @foreach($recoveryCodes as $code)
                <div class="mb-4" style="margin-bottom:8px">{{ $code }}</div>
            @endforeach
        </div>
        <button onclick="copyRecoveryCodes()" class="btn mt-3" style="width:auto">📋 Копіювати коди</button>
    </div>

    <h3 class="h1" style="font-size:20px;margin-bottom:8px">Крок 3: Підтвердіть налаштування</h3>

    <form method="POST" action="{{ route('two-factor.confirm') }}">
        @csrf
        <div class="mb-6">
            <label class="label" for="code">Введіть 6-значний код з додатку</label>
            <input
                type="text"
                name="code"
                id="code"
                maxlength="6"
                pattern="[0-9]{6}"
                class="input"
                placeholder="000000"
                required
                autofocus
            >
            @error('code')
            <p class="small" style="color:var(--c3)">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <button type="submit" class="btn">Підтвердити і активувати</button>
            <div class="text-center" style="margin-top:12px">
                <a href="{{ route('two-factor.show') }}" class="link">Скасувати</a>
            </div>
        </div>
    </form>
</div>

<script>
    function copyRecoveryCodes() {
        const codes = {!! json_encode($recoveryCodes) !!};
        const text = codes.join('\n');
        navigator.clipboard.writeText(text).then(() => {
            alert('Recovery коди скопійовано!');
        });
    }
</script>
</body>
</html>
