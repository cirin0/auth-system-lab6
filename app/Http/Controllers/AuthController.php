<?php /** @noinspection ALL */

namespace App\Http\Controllers;

use App\Models\User;
use App\Notifications\VerifyEmailNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function showRegister()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => [
                'required',
                'confirmed',
                'min:8',
                'regex:/[a-z]/',
                'regex:/[A-Z]/',
                'regex:/[0-9]/',
                'regex:/[@$!%*#?&]/',
            ],
            'g-recaptcha-response' => 'required|captcha',
        ], [
            'name.required' => 'Ім\'я є обов\'язковим',
            'name.max' => 'Ім\'я не може перевищувати 255 символів',
            'email.required' => 'Email є обов\'язковим',
            'email.email' => 'Введіть правильний email',
            'email.unique' => 'Цей email вже зареєстрований',
            'password.required' => 'Пароль є обов\'язковим',
            'password.confirmed' => 'Паролі не співпадають',
            'password.min' => 'Пароль повинен містити мінімум 8 символів',
            'password.regex' => 'Пароль повинен містити великі та малі літери, цифри та спеціальні символи (@$!%*#?&)',
            'g-recaptcha-response.required' => 'Будь ласка, підтвердіть, що ви не робот',
            'g-recaptcha-response.captcha' => 'Помилка перевірки captcha',
        ]);

        $user = User::query()->create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'email_verified_at' => null,
        ]);

        $token = Str::random(64);

        DB::table('email_verification_tokens')->insert([
            'email' => $request->email,
            'token' => $token,
            'created_at' => now(),
        ]);

        $user->notify(new VerifyEmailNotification($token, $request->email));

        return redirect()->route('login')
            ->with('success', 'Реєстрація успішна! Перевірте вашу пошту для активації акаунту.');
    }

    public function verifyEmail(Request $request, $token)
    {
        $email = $request->query('email');

        $verification = DB::table('email_verification_tokens')
            ->where('token', $token)
            ->where('email', $email)
            ->first();

        if (!$verification) {
            return redirect()->route('login')
                ->with('error', 'Невірне посилання для верифікації.');
        }

        $user = User::where('email', $email)->first();

        if (!$user) {
            return redirect()->route('login')
                ->with('error', 'Користувача не знайдено.');
        }

        if ($user->email_verified_at) {
            return redirect()->route('login')
                ->with('success', 'Email вже верифіковано. Можете увійти.');
        }

        $user->email_verified_at = now();
        $user->save();

        DB::table('email_verification_tokens')
            ->where('token', $token)
            ->delete();

        return redirect()->route('login')
            ->with('success', 'Email успішно підтверджено! Тепер ви можете увійти.');
    }

    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
            'g-recaptcha-response' => 'required|captcha',
        ], [
            'email.required' => 'Email є обов\'язковим',
            'email.email' => 'Введіть правильний email',
            'password.required' => 'Пароль є обов\'язковим',
            'g-recaptcha-response.required' => 'Будь ласка, підтвердіть, що ви не робот',
            'g-recaptcha-response.captcha' => 'Помилка перевірки captcha',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return redirect()->route('register')
                ->with('error', 'Користувача не знайдено. Будь ласка, зареєструйтесь.');
        }

        if (!$user->email_verified_at) {
            return back()->withErrors([
                'email' => 'Будь ласка, спочатку підтвердіть ваш email. Перевірте пошту.',
            ])->onlyInput('email');
        }

        if (!Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            return back()->withErrors([
                'email' => 'Невірний email або пароль.',
            ])->onlyInput('email');
        }

        if ($user->two_factor_enabled) {
            Auth::logout(); // Вихід тимчасово
            session(['2fa:user:id' => $user->id]);
            return redirect()->route('two-factor.verify');
        }

        $request->session()->regenerate();
        return redirect('/dashboard')->with('success', 'Вхід успішний!');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }

    public function dashboard()
    {
        return view('dashboard');
    }

    public function resendVerification(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);

        $user = User::where('email', $request->email)->first();

        if ($user->email_verified_at) {
            return back()->with('error', 'Email вже верифіковано.');
        }

        DB::table('email_verification_tokens')
            ->where('email', $request->email)
            ->delete();

        $token = Str::random(64);

        DB::table('email_verification_tokens')->insert([
            'email' => $request->email,
            'token' => $token,
            'created_at' => now(),
        ]);

        $user->notify(new VerifyEmailNotification($token, $request->email));

        return back()->with('success', 'Лист для верифікації відправлено повторно!');
    }

}
