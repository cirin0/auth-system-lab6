<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Реєстрація</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gradient-to-br to-indigo-700 flex items-center justify-center p-5">
<div class="bg-white rounded-lg shadow-2xl w-full max-w-md p-4">
    <h2 class="text-2xl font-bold text-center text-gray-800 mb-2">Реєстрація</h2>

    @if(session('success'))
        <div class="bg-green-500 text-white p-4 rounded-lg mb-6">
            {{ session('success') }}
        </div>
    @endif

    <form method="POST" action="{{ route('register') }}">
        @csrf

        <div class="mb-4">
            <label class="block text-gray-700 font-semibold mb-1" for="name">Ім'я</label>
            <input
                id="name"
                type="text"
                name="name"
                value="{{ old('name') }}"
                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-600 focus:border-transparent"
                required
            >
            @error('name')
            <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-4">
            <label class="block text-gray-700 font-semibold mb-1" for="email">Email</label>
            <input
                id="email"
                type="email"
                name="email"
                value="{{ old('email') }}"
                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-600 focus:border-transparent"
                required
            >
            @error('email')
            <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-4">
            <label class="block text-gray-700 font-semibold mb-1" for="password">Пароль</label>
            <input
                id="password"
                type="password"
                name="password"
                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-600 focus:border-transparent"
                required
            >
            <div class="text-xs text-gray-600 mt-1 leading-relaxed">
                Пароль повинен містити:<br>
                • Мінімум 8 символів<br>
                • Великі та малі літери<br>
                • Цифру<br>
                • Спеціальний символ (@$!%*#?&)
            </div>
            @error('password')
            <p class="text-red-500 text-sm">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-4">
            <label class="block text-gray-700 font-semibold mb-1" for="password_confirmation">Підтвердження
                паролю</label>
            <input
                id="password_confirmation"
                type="password"
                name="password_confirmation"
                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-600 focus:border-transparent"
                required
            >
        </div>

        <button
            type="submit"
            class="w-full bg-purple-600 hover:bg-purple-700 text-white font-bold py-3 px-4 rounded-lg transition duration-200"
        >
            Зареєструватися
        </button>
    </form>

    <div class="text-center mt-4">
        <span class="text-gray-600">Вже є акаунт?</span>
        <a href="{{ route('login') }}" class="text-purple-600 hover:text-purple-700 font-semibold ml-1">
            Увійти
        </a>
    </div>
</div>
</body>
</html>
