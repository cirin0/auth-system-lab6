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
<body class="bg-gray-100">
<header class="bg-purple-600 text-white shadow-lg">
    <div class="max-w-7xl mx-auto px-4 py-4">
        <h2 class="text-2xl font-bold">Налаштування 2FA</h2>
    </div>
</header>

<main class="max-w-2xl mx-auto mt-10 px-4">
    <div class="bg-white rounded-lg shadow-lg p-8">
        <h3 class="text-2xl font-bold text-gray-800 mb-6">Крок 1: Відскануйте QR код</h3>

        <div class="bg-gray-50 rounded-lg p-6 mb-6 text-center">
            <div class="inline-block bg-white p-4 rounded-lg shadow">
                {!! $qrCode !!}
            </div>
            <p class="text-sm text-gray-600 mt-4">Відскануйте цей QR код за допомогою Google Authenticator або Authy</p>
        </div>

        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
            <p class="text-sm text-blue-800 mb-2"><strong>Або введіть код вручну:</strong></p>
            <code class="bg-white px-3 py-2 rounded text-blue-600 font-mono">{{ $secret }}</code>
        </div>

        <h3 class="text-xl font-bold text-gray-800 mb-4">Крок 2: Збережіть Recovery коди</h3>

        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-6 mb-6">
            <p class="text-yellow-800 mb-4">
                <strong>⚠️ ВАЖЛИВО:</strong> Збережіть ці коди в безпечному місці! Вони потрібні, якщо ви втратите
                доступ до телефону.
            </p>
            <div class="bg-white rounded p-4 font-mono text-sm">
                @foreach($recoveryCodes as $code)
                    <div class="mb-2">{{ $code }}</div>
                @endforeach
            </div>
            <button
                onclick="copyRecoveryCodes()"
                class="mt-4 bg-yellow-500 hover:bg-yellow-600 text-white font-semibold py-2 px-4 rounded transition duration-200"
            >
                📋 Копіювати коди
            </button>
        </div>

        <h3 class="text-xl font-bold text-gray-800 mb-4">Крок 3: Підтвердіть налаштування</h3>

        <form method="POST" action="{{ route('two-factor.confirm') }}">
            @csrf
            <div class="mb-6">
                <label class="block text-gray-700 font-semibold mb-2">Введіть 6-значний код з додатку</label>
                <input
                    type="text"
                    name="code"
                    maxlength="6"
                    pattern="[0-9]{6}"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-600 text-center text-2xl font-mono tracking-widest"
                    placeholder="000000"
                    required
                    autofocus
                >
                @error('code')
                <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex gap-4">
                <button
                    type="submit"
                    class="flex-1 bg-purple-600 hover:bg-purple-700 text-white font-bold py-3 px-6 rounded-lg transition duration-200"
                >
                    Підтвердити і активувати
                </button>
                <a
                    href="{{ route('two-factor.show') }}"
                    class="flex-1 bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-3 px-6 rounded-lg text-center transition duration-200"
                >
                    Скасувати
                </a>
            </div>
        </form>
    </div>
</main>

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
