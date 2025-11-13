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
<body class="bg-gray-300 min-h-screen flex items-center justify-center p-5">
<div class="bg-white rounded-lg shadow-2xl w-full max-w-md p-8">
    <h2 class="text-3xl font-bold text-center text-gray-800 mb-8">Вхід</h2>

    @if(session('success'))
        <div class="bg-green-500 text-white p-4 rounded-lg mb-6">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-500 text-white p-4 rounded-lg mb-6">
            {{ session('error') }}
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <div class="mb-6">
            <label class="block text-gray-700 font-semibold mb-2" for="email">Email</label>
            <input
                id="email"
                type="email"
                name="email"
                value="{{ old('email') }}"
                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-600 focus:border-transparent"
                required
                autofocus
            >
            @error('email')
            <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
            @if(str_contains($message, 'підтвердіть ваш email'))
                <div class="mt-4 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                    <p class="text-sm text-yellow-800 mb-2">Не отримали лист?</p>
                    <form method="POST" action="{{ route('resend.verification') }}">
                        @csrf
                        <input type="hidden" name="email" value="{{ old('email') }}">
                        <button
                            type="submit"
                            class="text-sm bg-yellow-500 hover:bg-yellow-600 text-white font-semibold py-2 px-4 rounded transition duration-200"
                        >
                            Відправити повторно
                        </button>
                    </form>
                </div>
            @endif
            @enderror
        </div>

        <div class="mb-6">
            <label class="block text-gray-700 font-semibold mb-2" for="password">Пароль</label>
            <input
                id="password"
                type="password"
                name="password"
                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-600 focus:border-transparent"
                required
            >
            @error('password')
            <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-6 flex items-center flex-col">
            {!! app('captcha')->display() !!}
            @error('g-recaptcha-response')
            <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
            @enderror
        </div>

        <button
            type="submit"
            class="w-full bg-purple-600 hover:bg-purple-700 text-white font-bold py-3 px-4 rounded-lg transition duration-200"
        >
            Увійти
        </button>
    </form>

    <div class="text-center mt-6">
        <span class="text-gray-600">Немає акаунту?</span>
        <a href="{{ route('register') }}" class="text-purple-600 hover:text-purple-700 font-semibold ml-1">
            Зареєструватися
        </a>
    </div>
</div>
</body>
</html>
