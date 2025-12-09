<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Логи входу</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="simple-body" style="display:block;padding:0;min-height:100vh;background:var(--c4)">
<header style="background:var(--c1);color:#fff;padding:20px;box-shadow:0 2px 8px rgba(0,0,0,.1)">
    <div
        style="max-width:1200px;margin:0 auto;display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:12px">
        <h2 style="font-size:24px;font-weight:700;margin:0">Логи входу</h2>
        <div style="display:flex;gap:12px">
            <a href="{{ route('dashboard') }}" class="btn"
               style="width:auto;padding:10px 16px;background:#fff;color:var(--c1)">
                Dashboard
            </a>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="btn" style="width:auto;padding:10px 16px;background:var(--c3)">
                    Вийти
                </button>
            </form>
        </div>
    </div>
</header>

<main style="max-width:1200px;margin:24px auto;padding:0 20px">
    <div class="card" style="max-width:100%;margin-bottom:24px">
        <h3 style="font-size:18px;font-weight:700;color:var(--c1);margin-bottom:16px">Фільтри</h3>
        <form method="GET" action="{{ route('admin.login-attempts') }}" style="display:flex;gap:12px;flex-wrap:wrap">
            <div style="flex:1;min-width:200px">
                <label class="label" for="name">Email</label>
                <input
                    type="text"
                    name="email"
                    id="name"
                    value="{{ request('email') }}"
                    class="input"
                    placeholder="Шукати по email"
                >
            </div>
            <div style="flex:1;min-width:200px">
                <label class="label" for="select">Статус</label>
                <select name="successful" class="input" id="select">
                    <option value="">Всі</option>
                    <option value="1" {{ request('successful') === '1' ? 'selected' : '' }}>Успішні</option>
                    <option value="0" {{ request('successful') === '0' ? 'selected' : '' }}>Невдалі</option>
                </select>
            </div>
            <div style="display:flex;align-items:flex-end">
                <button type="submit" class="btn" style="width:auto;padding:12px 24px">
                    Фільтрувати
                </button>
            </div>
        </form>
    </div>

    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(250px,1fr));gap:20px;margin-bottom:24px">
        <div class="card" style="max-width:100%">
            <div style="display:flex;align-items:center;justify-content:space-between">
                <div>
                    <p style="color:var(--c2);font-size:14px;margin:0 0 8px">Всього спроб</p>
                    <p style="font-size:32px;font-weight:700;color:var(--c1);margin:0">{{ $attempts->total() }}</p>
                </div>
                <div
                    style="background:#e8f4f8;border-radius:50%;width:56px;height:56px;display:flex;align-items:center;justify-content:center">
                    <svg style="width:28px;height:28px;color:#4a90a4" fill="none" stroke="currentColor"
                         viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="card" style="max-width:100%">
            <div style="display:flex;align-items:center;justify-content:space-between">
                <div>
                    <p style="color:var(--c2);font-size:14px;margin:0 0 8px">Успішних</p>
                    <p style="font-size:32px;font-weight:700;color:#2d8659;margin:0">{{ $attempts->where('successful', true)->count() }}</p>
                </div>
                <div
                    style="background:#e8f5e9;border-radius:50%;width:56px;height:56px;display:flex;align-items:center;justify-content:center">
                    <svg style="width:28px;height:28px;color:#2d8659" fill="none" stroke="currentColor"
                         viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="card" style="max-width:100%">
            <div style="display:flex;align-items:center;justify-content:space-between">
                <div>
                    <p style="color:var(--c2);font-size:14px;margin:0 0 8px">Невдалих</p>
                    <p style="font-size:32px;font-weight:700;color:#c53030;margin:0">{{ $attempts->where('successful', false)->count() }}</p>
                </div>
                <div
                    style="background:#fee;border-radius:50%;width:56px;height:56px;display:flex;align-items:center;justify-content:center">
                    <svg style="width:28px;height:28px;color:#c53030" fill="none" stroke="currentColor"
                         viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <div class="card" style="max-width:100%;padding:0;overflow:hidden">
        <div style="overflow-x:auto">
            <table style="width:100%;border-collapse:collapse">
                <thead style="background:var(--c4)">
                <tr>
                    <th style="padding:14px 16px;text-align:left;font-size:12px;font-weight:600;color:var(--c2);text-transform:uppercase;letter-spacing:0.5px">
                        Дата/Час
                    </th>
                    <th style="padding:14px 16px;text-align:left;font-size:12px;font-weight:600;color:var(--c2);text-transform:uppercase;letter-spacing:0.5px">
                        Email
                    </th>
                    <th style="padding:14px 16px;text-align:left;font-size:12px;font-weight:600;color:var(--c2);text-transform:uppercase;letter-spacing:0.5px">
                        IP адреса
                    </th>
                    <th style="padding:14px 16px;text-align:left;font-size:12px;font-weight:600;color:var(--c2);text-transform:uppercase;letter-spacing:0.5px">
                        Статус
                    </th>
                    <th style="padding:14px 16px;text-align:left;font-size:12px;font-weight:600;color:var(--c2);text-transform:uppercase;letter-spacing:0.5px">
                        Помилка
                    </th>
                    <th style="padding:14px 16px;text-align:left;font-size:12px;font-weight:600;color:var(--c2);text-transform:uppercase;letter-spacing:0.5px">
                        Дії
                    </th>
                </tr>
                </thead>
                <tbody style="background:#fff">
                @forelse($attempts as $attempt)
                    <tr style="border-bottom:1px solid #e5e7eb">
                        <td style="padding:14px 16px;font-size:14px;color:var(--c1);white-space:nowrap">
                            {{ $attempt->created_at->format('d.m.Y H:i:s') }}
                        </td>
                        <td style="padding:14px 16px;font-size:14px;white-space:nowrap">
                            <a href="{{ route('admin.user-stats', $attempt->email) }}" class="link">
                                {{ $attempt->email }}
                            </a>
                        </td>
                        <td style="padding:14px 16px;font-size:14px;color:var(--c2)">
                            {{ $attempt->ip_address }}
                        </td>
                        <td style="padding:14px 16px;white-space:nowrap">
                            @if($attempt->successful)
                                <span
                                    style="padding:4px 10px;font-size:12px;font-weight:600;border-radius:12px;background:#e8f5e9;color:#2d8659">
                                            Успішно
                                        </span>
                            @else
                                <span
                                    style="padding:4px 10px;font-size:12px;font-weight:600;border-radius:12px;background:#fee;color:#c53030">
                                            Невдало
                                        </span>
                            @endif
                        </td>
                        <td style="padding:14px 16px;font-size:14px;color:var(--c2)">
                            {{ $attempt->error_message ?? '-' }}
                        </td>
                        <td style="padding:14px 16px;font-size:14px;white-space:nowrap">
                            <a href="{{ route('admin.user-stats', $attempt->email) }}" class="link">
                                Детальніше →
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" style="padding:32px;text-align:center;color:var(--c2)">
                            Немає логів
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        <div style="padding:16px 20px;background:var(--c4);border-top:1px solid #e5e7eb">
            {{ $attempts->appends(request()->query())->links() }}
        </div>
    </div>
</main>
</body>
</html>
