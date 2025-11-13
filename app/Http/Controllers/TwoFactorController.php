<?php

namespace App\Http\Controllers;

use App\Models\User;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use PragmaRX\Google2FA\Google2FA;

class TwoFactorController extends Controller
{
    protected $google2fa;

    public function __construct()
    {
        $this->google2fa = new Google2FA();
    }

    public function show()
    {
        return view('auth.two-factor');
    }

    public function enable(Request $request)
    {
        $user = Auth::user();

        $secret = $this->google2fa->generateSecretKey();

        $user->two_factor_secret = encrypt($secret);
        $user->save();

        $qrCodeUrl = $this->google2fa->getQRCodeUrl(
            config('app.name'),
            $user->email,
            $secret
        );

        $renderer = new ImageRenderer(
            new RendererStyle(200),
            new SvgImageBackEnd()
        );

        $writer = new Writer($renderer);
        $qrCodeSvg = $writer->writeString($qrCodeUrl);

        $recoveryCodes = $this->generateRecoveryCodes();

        return view('auth.two-factor-setup', [
            'qrCode' => $qrCodeSvg,
            'secret' => $secret,
            'recoveryCodes' => $recoveryCodes,
        ]);
    }

    public function confirm(Request $request)
    {
        $request->validate([
            'code' => 'required|numeric|digits:6',
        ]);

        $user = Auth::user();
        $secret = decrypt($user->two_factor_secret);

        $valid = $this->google2fa->verifyKey($secret, $request->code);

        if (!$valid) {
            return back()->withErrors(['code' => 'Невірний код. Спробуйте ще раз.']);
        }

        $user->two_factor_enabled = true;
        $recoveryCodes = $this->generateRecoveryCodes();
        $user->two_factor_recovery_codes = encrypt(json_encode($recoveryCodes));
        $user->save();

        return redirect()->route('two-factor.show')
            ->with('success', '2FA успішно активовано!');
    }

    public function disable(Request $request)
    {
        $request->validate([
            'password' => 'required',
        ]);

        $user = Auth::user();

        if (!password_verify($request->password, $user->password)) {
            return back()->withErrors(['password' => 'Невірний пароль.']);
        }

        $user->two_factor_enabled = false;
        $user->two_factor_secret = null;
        $user->two_factor_recovery_codes = null;
        $user->save();

        return back()->with('success', '2FA вимкнено.');
    }

    public function showVerify()
    {
        if (!session('2fa:user:id')) {
            return redirect()->route('login');
        }

        return view('auth.two-factor-verify');
    }

    public function verify(Request $request)
    {
        $request->validate([
            'code' => 'required|string',
        ]);

        $userId = session('2fa:user:id');
        $user = User::query()->find($userId);

        if (!$user) {
            return redirect()->route('login')->with('error', 'Сесія закінчилась.');
        }

        $secret = decrypt($user->two_factor_secret);

        // Перевірка коду або recovery коду
        if ($this->google2fa->verifyKey($secret, $request->code)) {
            Auth::login($user);
            session()->forget('2fa:user:id');
            $request->session()->regenerate();

            return redirect()->intended('/dashboard');
        }

        $recoveryCodes = json_decode(decrypt($user->two_factor_recovery_codes), true);

        if (in_array($request->code, $recoveryCodes)) {
            $recoveryCodes = array_diff($recoveryCodes, [$request->code]);
            $user->two_factor_recovery_codes = encrypt(json_encode(array_values($recoveryCodes)));
            $user->save();

            Auth::login($user);
            session()->forget('2fa:user:id');
            $request->session()->regenerate();

            return redirect()->intended('/dashboard')
                ->with('warning', 'Ви використали recovery код. Залишилось: ' . count($recoveryCodes));
        }

        return back()->withErrors(['code' => 'Невірний код.']);
    }

    protected function generateRecoveryCodes()
    {
        $codes = [];
        for ($i = 0; $i < 8; $i++) {
            $codes[] = strtoupper(substr(bin2hex(random_bytes(5)), 0, 10));
        }
        return $codes;
    }
}
