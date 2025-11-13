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
<body class="bg-gray-100">
<header class="bg-purple-600 text-white shadow-lg">
    <div class="max-w-7xl mx-auto px-4 py-4 flex justify-between items-center">
        <h2 class="text-2xl font-bold">Налаштування 2FA</h2>
        <a href="{{ route('dashboard') }}"
           class="bg-white text-purple-600 hover:bg-gray-100 font-semibold py-2 px-6 rounded-lg transition duration-200">
            Назад
        </a>
    </div>
</header>

<main class="max-w-4xl mx-auto mt-10 px-4">
    <div class="bg-white rounded-lg shadow-lg p-8">
        @if(session('success'))
            <div class="bg-green-500 text-white p-4 rounded-lg mb-6">
                {{ session('success') }}
            </div>
        @endif

        @if(session('warning'))
            <div class="bg-yellow-500 text-white p-4 rounded-lg mb-6">
                {{ session('warning') }}
            </div>
        @endif

        <h3 class="text-2xl font-bold text-gray-800 mb-6">Двофакторна аутентифікація (2FA)</h3>

        @if(Auth::user()->two_factor_enabled)
            <!-- 2FA УВІМКНЕНО -->
            <div class="bg-green-50 border border-green-200 rounded-lg p-6 mb-6">
                <div class="flex items-center mb-4">
                    <svg class="w-6 h-6 text-green-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <h4 class="text-lg font-semibold text-green-800">2FA активовано</h4>
                </div>
                <p class="text-green-700 mb-4">Ваш акаунт захищено двофакторною аутентифікацією.</p>
            </div>

            <!-- ФОРМА ВИМКНЕННЯ -->
            <div class="border border-gray-200 rounded-lg p-6">
                <h5 class="font-semibold text-gray-800 mb-4">Вимкнути 2FA</h5>
                <form method="POST" action="{{ route('two-factor.disable') }}">
                    @csrf
                    <div class="mb-4">
                        <label class="block text-gray-700 font-semibold mb-2">Введіть ваш пароль для
                            підтвердження</label>
                        <input
                            type="password"
                            name="password"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-600"
                            required
                        >
                        @error('password')
                        <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                        @enderror
                    </div>
                    <button
                        type="submit"
                        class="bg-red-500 hover:bg-red-600 text-white font-bold py-2 px-6 rounded-lg transition duration-200"
                    >
                        Вимкнути 2FA
                    </button>
                </form>
            </div>
        @else
            <!-- 2FA ВИМКНЕНО -->
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-6 mb-6">
                <div class="flex items-center mb-4">
                    <svg class="w-6 h-6 text-yellow-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                    <h4 class="text-lg font-semibold text-yellow-800">2FA не активовано</h4>
                </div>
                <p class="text-yellow-700">Рекомендуємо увімкнути 2FA для додаткового захисту вашого акаунту.</p>
            </div>

            <div class="mb-6">
                <h5 class="font-semibold text-gray-800 mb-2">Що таке 2FA?</h5>
                <p class="text-gray-600 mb-4">
                    Двофакторна аутентифікація додає додатковий рівень захисту. При вході вам потрібно буде ввести код з
                    додатку на телефоні.
                </p>
                <h5 class="font-semibold text-gray-800 mb-2">Що вам потрібно:</h5>
                <ul class="list-disc list-inside text-gray-600 mb-6">
                    <li>Встановити Google Authenticator, Authy або інший TOTP додаток</li>
                    <li>Відсканувати QR код</li>
                    <li>Зберегти recovery коди в безпечному місці</li>
                </ul>
            </div>

            <form method="POST" action="{{ route('two-factor.enable') }}">
                @csrf
                <button
                    type="submit"
                    class="bg-purple-600 hover:bg-purple-700 text-white font-bold py-3 px-6 rounded-lg transition duration-200"
                >
                    Увімкнути 2FA
                </button>
            </form>
        @endif
    </div>
</main>
</body>
</html>
