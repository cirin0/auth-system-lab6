<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Дашборд</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100">
<header class="bg-purple-600 text-white shadow-lg">
    <div class="max-w-7xl mx-auto px-4 py-4 flex justify-between items-center">
        <h2 class="text-2xl font-bold">Dashboard</h2>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button
                type="submit"
                class="bg-white text-purple-600 hover:bg-gray-100 font-semibold py-2 px-6 rounded-lg transition duration-200"
            >
                Вийти
            </button>
        </form>
    </div>
</header>

<main class="max-w-4xl mx-auto mt-10 px-4">
    <div class="bg-white rounded-lg shadow-lg p-8">
        @if(session('success'))
            <div class="bg-green-500 text-white p-4 rounded-lg mb-6">
                {{ session('success') }}
            </div>
        @endif

        <h3 class="text-2xl font-bold text-gray-800 mb-4">
            Вітаємо, {{ Auth::user()->name }}! 👋
        </h3>
        <div class="space-y-2 text-gray-600">
            <p><span class="font-semibold">Email:</span> {{ Auth::user()->email }}</p>
            <p><span class="font-semibold">Дата реєстрації:</span> {{ Auth::user()->created_at->format('d.m.Y H:i') }}
            </p>
        </div>

        <div class="mt-8 p-4 bg-green-50 border border-green-200 rounded-lg">
            <p class="text-green-800">✅ Ваш акаунт успішно створено та активовано!</p>
        </div>
    </div>
</main>
</body>
</html>
