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

    private const SESSION_RECOVERY_CODES = '2fa:setup:recovery_codes';
    private const SESSION_USER_ID = '2fa:user:id';

    protected $google2fa;

    public function __construct()
    {
        $this->google2fa = new Google2FA();
    }

    public function show()
    {
        return view('auth.two-factor');
    }

    public function enable()
    {
        $user = Auth::user();

        $secret = $this->google2fa->generateSecretKey();

        $user->two_factor_secret = encrypt($secret);
        $user->save();

        $recoveryCodes = $this->generateRecoveryCodes();
        session(['2fa:setup:recovery_codes' => $recoveryCodes]);

        return redirect()->route('two-factor.setup');
    }

    public function showSetup()
    {
        $user = Auth::user();

        if (!$user->two_factor_secret) {
            return redirect()->route('two-factor.show');
        }

        $secret = decrypt($user->two_factor_secret);

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

        $recoveryCodes = session(self::SESSION_RECOVERY_CODES, $this->generateRecoveryCodes());

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
        $recoveryCodes = session(self::SESSION_RECOVERY_CODES, $this->generateRecoveryCodes());
        $user->two_factor_recovery_codes = encrypt(json_encode($recoveryCodes));
        $user->save();

        session()->forget(self::SESSION_RECOVERY_CODES);

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
        if (!session(self::SESSION_USER_ID)) {
            return redirect()->route('login');
        }

        return view('auth.two-factor-verify');
    }

    public function verify(Request $request)
    {
        $request->validate([
            'code' => 'required|string',
        ]);

        $userId = session(self::SESSION_USER_ID);
        $user = User::query()->find($userId);

        if (!$user) {
            return redirect()->route('login')->with('error', 'Сесія закінчилась.');
        }

        $secret = decrypt($user->two_factor_secret);
        $isValidCode = $this->google2fa->verifyKey($secret, $request->code);
        $warning = null;

        if (!$isValidCode) {
            $recoveryCodes = json_decode(decrypt($user->two_factor_recovery_codes), true);

            if (!in_array($request->code, $recoveryCodes)) {
                return back()->withErrors(['code' => 'Невірний код.']);
            }

            $recoveryCodes = array_diff($recoveryCodes, [$request->code]);
            $user->two_factor_recovery_codes = encrypt(json_encode(array_values($recoveryCodes)));
            $user->save();
            $warning = 'Ви використали recovery код. Залишилось: ' . count($recoveryCodes);
        }

        Auth::login($user);
        session()->forget(self::SESSION_USER_ID);
        $request->session()->regenerate();

        $redirect = redirect()->intended('/dashboard');
        return $warning ? $redirect->with('warning', $warning) : $redirect;
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
