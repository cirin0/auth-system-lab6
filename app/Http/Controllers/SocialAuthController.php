<?php

namespace App\Http\Controllers;

use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class SocialAuthController extends Controller
{

    const DASHBOARD = '/dashboard';

    public function redirectToGithub()
    {
        return Socialite::driver('github')->redirect();
    }

    public function handleGithubCallback()
    {
        try {
            $githubUser = Socialite::driver('github')->user();
        } catch (Exception $e) {
            return redirect()->route('login')
                ->with('error', 'Помилка авторизації через GitHub. Спробуйте ще раз.');
        }

        if (Auth::check()) {
            return $this->linkGithubToCurrentUser($githubUser);
        }

        return $this->loginOrRegisterWithGithub($githubUser);
    }

    private function linkGithubToCurrentUser($githubUser)
    {
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

    private function loginOrRegisterWithGithub($githubUser)
    {
        $user = User::where('github_id', $githubUser->getId())->first();

        if ($user) {
            return $this->loginExistingGithubUser($user, $githubUser);
        }

        $existingUser = User::where('email', $githubUser->getEmail())->first();

        if ($existingUser) {
            return $this->linkGithubToExistingUser($existingUser, $githubUser);
        }

        return $this->createNewGithubUser($githubUser);
    }

    private function loginExistingGithubUser($user, $githubUser)
    {
        $user->update([
            'github_token' => $githubUser->token,
            'github_refresh_token' => $githubUser->refreshToken,
            'avatar' => $githubUser->getAvatar(),
        ]);

        return $this->authenticateUser($user, 'Вхід через GitHub успішний!');
    }

    private function linkGithubToExistingUser($existingUser, $githubUser)
    {
        $existingUser->update([
            'github_id' => $githubUser->getId(),
            'github_token' => $githubUser->token,
            'github_refresh_token' => $githubUser->refreshToken,
            'avatar' => $githubUser->getAvatar(),
            'provider' => 'github',
            'email_verified_at' => now(),
        ]);

        return $this->authenticateUser($existingUser, 'GitHub прив\'язано до вашого акаунту!');
    }

    private function createNewGithubUser($githubUser)
    {
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
        return redirect(self::DASHBOARD)
            ->with('success', 'Реєстрація через GitHub успішна!');
    }

    private function authenticateUser($user, $successMessage)
    {
        if ($user->two_factor_enabled) {
            Auth::logout();
            session(['2fa:user:id' => $user->id]);
            return redirect()->route('two-factor.verify');
        }

        Auth::login($user);
        return redirect(self::DASHBOARD)->with('success', $successMessage);
    }

    public function unlinkGithub()
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
