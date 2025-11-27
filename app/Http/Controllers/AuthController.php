<?php /** @noinspection ALL */

namespace App\Http\Controllers;

use App\Models\LoginAttempt;
use App\Models\User;
use App\Notifications\VerifyEmailNotification;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AuthController extends Controller
{

    const MAX_LOGIN_ATTEMPTS = 5;

    const LOCKOUT_TIME = 15;

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

        $email = $request->email;
        $ipAddress = $request->ip();

        if ($this->isLocked($email, $ipAddress)) {
            $remainingTime = $this->getRemainingLockoutTime($email, $ipAddress);
            $this->logLoginAttempt($email, $ipAddress, $request->userAgent(), false, "Акаунт тимчасово заблоковано");
            return back()->withErrors([
                'email' => "Забагато невдалих спроб входу. Спробуйте знову через {$remainingTime} хвилин.",
            ])->onlyInput('email');
        }

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            $this->logLoginAttempt($email, $ipAddress, $request->userAgent(), false, "Користувача не знайдено");

            return redirect()->route('register')
                ->with('error', 'Користувача не знайдено. Будь ласка, зареєструйтесь.');
        }

        if (!$user->email_verified_at) {
            $this->logLoginAttempt($email, $ipAddress, $request->userAgent(), false, "Email не верифіковано");

            return back()->withErrors([
                'email' => 'Будь ласка, спочатку підтвердіть ваш email. Перевірте пошту.',
            ])->onlyInput('email');
        }

        if (!Auth::attempt(['email' => $email, 'password' => $request->password])) {
            $this->logLoginAttempt($email, $ipAddress, $request->userAgent(), false, "Невірний пароль");

            $attempts = $this->getFailedAttempts($email, $ipAddress);
            $remaining = self::MAX_LOGIN_ATTEMPTS - $attempts;

            if ($remaining > 0) {
                return back()->withErrors([
                    'email' => "Невірний email або пароль. Залишилось спроб: {$remaining}",
                ])->onlyInput('email');
            } else {
                return back()->withErrors([
                    'email' => "Забагато невдалих спроб входу. Акаунт заблоковано на " . self::LOCKOUT_TIME . " хвилин.",
                ])->onlyInput('email');
            }
        }

        $this->logLoginAttempt($email, $ipAddress, $request->userAgent(), true, null);

        if ($user->two_factor_enabled) {
            Auth::logout();
            session(['2fa:user:id' => $user->id]);
            return redirect()->route('two-factor.verify');
        }

        $request->session()->regenerate();
        return redirect('/dashboard')->with('success', 'Вхід успішний!');
    }

    protected function logLoginAttempt($email, $ipAddress, $userAgent, $successful, $errorMessage = null)
    {
        LoginAttempt::create([
            'email' => $email,
            'ip_address' => $ipAddress,
            'user_agent' => $userAgent,
            'successful' => $successful,
            'error_message' => $errorMessage,
            'created_at' => Carbon::now(),
        ]);
    }

    protected function isLocked($email, $ipAddress)
    {
        $attempts = $this->getFailedAttempts($email, $ipAddress);
        return $attempts >= self::MAX_LOGIN_ATTEMPTS;
    }

    protected function getFailedAttempts($email, $ipAddress)
    {
        return LoginAttempt::where('email', $email)
            ->where('ip_address', $ipAddress)
            ->where('successful', false)
            ->where('created_at', '>', Carbon::now()->subMinutes(self::LOCKOUT_TIME))
            ->count();
    }

    protected function clearFailedAttempts($email, $ipAddress)
    {
        LoginAttempt::where('email', $email)
            ->where('ip_address', $ipAddress)
            ->where('successful', false)
            ->where('created_at', '>', Carbon::now()->subMinutes(self::LOCKOUT_TIME))
            ->delete();
    }

    protected function getRemainingLockoutTime($email, $ipAddress)
    {
        $firstAttempt = LoginAttempt::where('email', $email)
            ->where('ip_address', $ipAddress)
            ->where('successful', false)
            ->where('created_at', '>', Carbon::now()->subMinutes(self::LOCKOUT_TIME))
            ->orderBy('created_at', 'asc')
            ->first();

        if (!$firstAttempt) {
            return 0;
        }

        $lockoutEnds = Carbon::parse($firstAttempt->created_at)->addMinutes(self::LOCKOUT_TIME);
        return max(0, $lockoutEnds->diffInMinutes(Carbon::now()));
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
