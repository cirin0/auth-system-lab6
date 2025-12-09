<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Notifications\ResetPasswordNotification;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class PasswordResetController extends Controller
{
    public function showLinkRequestForm(Request $request)
    {
        $email = $request->query('email', '');
        return view('auth.passwords.email', ['email' => $email]);
    }

    public function sendResetLinkEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ], [
            'email.exists' => 'Користувача з таким email не знайдено.',
        ]);

        $user = User::where('email', $request->email)->first();

        DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->delete();

        $token = Str::random(64);

        DB::table('password_reset_tokens')->insert([
            'email' => $request->email,
            'token' => Hash::make($token),
            'created_at' => Carbon::now(),
        ]);

        $user->notify(new ResetPasswordNotification($token, $request->email));

        return back()->with('success', 'Посилання для відновлення пароля відправлено на вашу пошту!');
    }

    public function showResetForm(Request $request, $token)
    {
        $email = $request->query('email');

        return view('auth.passwords.reset', [
            'token' => $token,
            'email' => $email,
        ]);
    }

    public function reset(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email|exists:users,email',
            'password' => [
                'required',
                'confirmed',
                'min:8',
                'regex:/[a-z]/',
                'regex:/[A-Z]/',
                'regex:/[0-9]/',
                'regex:/[@$!%*#?&]/',
            ],
        ], [
            'password.min' => 'Пароль повинен містити мінімум 8 символів',
            'password.regex' => 'Пароль повинен містити великі та малі літери, цифри та спеціальні символи (@$!%*#?&)',
        ]);

        $passwordReset = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->first();

        $error = null;

        if (!$passwordReset) {
            $error = 'Невірне посилання для відновлення пароля.';
        } elseif (Carbon::now()->diffInMinutes(Carbon::parse($passwordReset->created_at)) > 60) {
            DB::table('password_reset_tokens')
                ->where('email', $request->email)
                ->delete();
            $error = 'Посилання для відновлення пароля застаріло. Запросіть нове.';
        } elseif (!Hash::check($request->token, $passwordReset->token)) {
            $error = 'Невірний токен.';
        }

        if ($error) {
            return back()->withErrors(['email' => $error]);
        }

        $user = User::where('email', $request->email)->first();
        $user->password = Hash::make($request->password);
        $user->save();

        DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->delete();

        return redirect()->route('login')
            ->with('success', 'Пароль успішно змінено! Тепер ви можете увійти з новим паролем.');
    }
}
