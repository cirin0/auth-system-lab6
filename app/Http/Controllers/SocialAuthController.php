<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class SocialAuthController extends Controller
{
    public function redirectToGithub()
    {
        return Socialite::driver('github')->redirect();
    }

    public function handleGithubCallback()
    {
        try {
            $githubUser = Socialite::driver('github')->user();
        } catch (\Exception $e) {
            return redirect()->route('login')
                ->with('error', 'Помилка авторизації через GitHub. Спробуйте ще раз.');
        }

        if (Auth::check()) {
            $currentUser = Auth::user();

            $existingUserWithGithub = User::where('github_id', $githubUser->getId())
                ->where('id', '!=', $currentUser->id)
                ->first();

            if ($existingUserWithGithub) {
                return redirect()->route('dashboard')
                    ->with('error', 'Цей GitHub акаунт вже прив\'язаний до іншого користувача.');
            }

            $currentUser->update([
                'github_id' => $githubUser->getId(),
                'github_token' => $githubUser->token,
                'github_refresh_token' => $githubUser->refreshToken,
                'avatar' => $githubUser->getAvatar(),
            ]);


            return redirect()->route('dashboard')
                ->with('success', 'GitHub успішно підключено до вашого акаунту!');
        }

        $user = User::where('github_id', $githubUser->getId())->first();

        if ($user) {
            $user->update([
                'github_token' => $githubUser->token,
                'github_refresh_token' => $githubUser->refreshToken,
                'avatar' => $githubUser->getAvatar(),
            ]);

            if ($user->two_factor_enabled) {
                Auth::logout();
                session(['2fa:user:id' => $user->id]);
                return redirect()->route('two-factor.verify');
            }

            Auth::login($user);
            return redirect('/dashboard')->with('success', 'Вхід через GitHub успішний!');
        }

        $existingUser = User::where('email', $githubUser->getEmail())->first();

        if ($existingUser) {

            $existingUser->update([
                'github_id' => $githubUser->getId(),
                'github_token' => $githubUser->token,
                'github_refresh_token' => $githubUser->refreshToken,
                'avatar' => $githubUser->getAvatar(),
                'provider' => 'github',
                'email_verified_at' => now(),
            ]);

            if ($existingUser->two_factor_enabled) {
                Auth::logout();
                session(['2fa:user:id' => $existingUser->id]);
                return redirect()->route('two-factor.verify');
            }

            Auth::login($existingUser);
            return redirect('/dashboard')
                ->with('success', 'GitHub прив\'язано до вашого акаунту!');
        }

        $newUser = User::create([
            'name' => $githubUser->getName() ?? $githubUser->getNickname(),
            'email' => $githubUser->getEmail(),
            'github_id' => $githubUser->getId(),
            'github_token' => $githubUser->token,
            'github_refresh_token' => $githubUser->refreshToken,
            'avatar' => $githubUser->getAvatar(),
            'provider' => 'github',
            'password' => Hash::make(Str::random(24)),
            'email_verified_at' => now(),
        ]);

        Auth::login($newUser);
        return redirect('/dashboard')
            ->with('success', 'Реєстрація через GitHub успішна!');
    }

    public function unlinkGithub(Request $request)
    {
        $user = Auth::user();

        if (!$user->password && $user->provider === 'github') {
            return back()->with('error', 'Неможливо від\'єднати GitHub. Спочатку встановіть пароль.');
        }

        $user->update([
            'github_id' => null,
            'github_token' => null,
            'github_refresh_token' => null,
            'provider' => null,
        ]);

        return back()->with('success', 'GitHub від\'єднано від акаунту.');
    }
}
