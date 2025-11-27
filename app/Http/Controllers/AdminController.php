<?php

namespace App\Http\Controllers;

use App\Models\LoginAttempt;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function loginAttempts(Request $request)
    {
        $email = $request->query('email');
        $successful = $request->query('successful');
        $perPage = $request->query('per_page', 20);

        $query = LoginAttempt::query()->orderBy('created_at', 'desc');

        if ($email) {
            $query->where('email', 'like', "%{$email}%");
        }

        if ($successful !== null) {
            $query->where('successful', $successful === '1');
        }

        $attempts = $query->paginate($perPage);

        return view('admin.login-attempts', compact('attempts', 'email', 'successful'));
    }

    public function userStats($email)
    {
        $totalAttempts = LoginAttempt::query()->where('email', $email)->count();
        $successfulAttempts = LoginAttempt::query()->where('email', $email)->where('successful', true)->count();
        $failedAttempts = LoginAttempt::query()->where('email', $email)->where('successful', false)->count();

        $recentAttempts = LoginAttempt::query()->where('email', $email)
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        return view('admin.user-stats', compact('email', 'totalAttempts', 'successfulAttempts', 'failedAttempts', 'recentAttempts'));
    }
}
