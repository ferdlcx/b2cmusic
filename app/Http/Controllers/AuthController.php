<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Cart;
use App\Models\Wishlist;
use App\Models\ActivityLog;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Mail;
use App\Mail\OtpMail;
use App\Mail\ResetPasswordOtpMail;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password as PasswordRule;

class AuthController extends Controller
{
    public function showRegister()
    {
        if (Auth::check()) {
            return redirect('/');
        }
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', PasswordRule::defaults()],
            'phone' => ['nullable', 'string', 'max:20'],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'phone' => $request->phone,
            'role' => 'customer',
            'status' => 'active',
        ]);

        // Create Cart and Wishlist for the new user based on ERD
        Cart::create(['user_id' => $user->id]);
        Wishlist::create(['user_id' => $user->id]);

        $otpCode = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
        $user->otp_code = $otpCode;
        $user->otp_expires_at = now()->addDays(5);
        $user->save();

        try {
            Mail::to($user->email)->send(new OtpMail($otpCode, $user->name));
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Gagal mengirim OTP ke ' . $user->email . ': ' . $e->getMessage());
        }

        Auth::login($user);

        try {
            ActivityLog::create([
                'user_id' => $user->id,
                'action' => 'register',
                'model_type' => User::class,
                'model_id' => $user->id,
                'description' => "Registrasi akun baru: {$user->name} ({$user->email})",
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);
        } catch (\Exception $e) {}

        return redirect()->route('customer.dashboard')->with('success', 'Registrasi berhasil! Silakan verifikasi email Anda.');
    }

    public function showLogin()
    {
        if (Auth::check()) {
            return redirect('/');
        }
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ]);

        $throttleKey = Str::transliterate(Str::lower($request->email) . '|' . $request->ip());

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);

            try {
                ActivityLog::create([
                    'user_id' => null,
                    'action' => 'login_locked_out',
                    'model_type' => User::class,
                    'model_id' => null,
                    'description' => "Lockout login karena terlalu banyak percobaan: {$request->email}",
                    'ip_address' => request()->ip(),
                    'user_agent' => request()->userAgent(),
                ]);
            } catch (\Exception $e) {}

            return back()->withErrors([
                'email' => str_replace(':seconds', $seconds, 'Terlalu banyak percobaan login. Silakan coba lagi dalam :seconds detik.'),
            ])->onlyInput('email');
        }

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            RateLimiter::clear($throttleKey);
            $request->session()->regenerate();

            $user = Auth::user();
            if ($user->status !== 'active') {
                Auth::logout();
                return back()->withErrors([
                    'email' => 'Akun Anda dinonaktifkan. Silakan hubungi admin.',
                ]);
            }

            try {
                ActivityLog::create([
                    'user_id' => $user->id,
                    'action' => 'login',
                    'model_type' => User::class,
                    'model_id' => $user->id,
                    'description' => "Berhasil login ke sistem",
                    'ip_address' => request()->ip(),
                    'user_agent' => request()->userAgent(),
                ]);
            } catch (\Exception $e) {}

            if (in_array($user->role, ['admin', 'super_admin'])) {
                return redirect()->intended('/admin/dashboard');
            }

            return redirect()->intended('/')->with('success', 'Login berhasil! Selamat datang kembali.');
        }

        RateLimiter::hit($throttleKey, 1800);

        try {
            ActivityLog::create([
                'user_id' => null,
                'action' => 'login_failed',
                'model_type' => User::class,
                'model_id' => null,
                'description' => "Gagal login dengan email: {$request->email}",
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);
        } catch (\Exception $e) {}

        return back()->withErrors([
            'email' => 'Email atau password yang Anda masukkan salah.',
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        $user = Auth::user();
        if ($user) {
            try {
                ActivityLog::create([
                    'user_id' => $user->id,
                    'action' => 'logout',
                    'model_type' => User::class,
                    'model_id' => $user->id,
                    'description' => "Keluar dari sistem",
                    'ip_address' => request()->ip(),
                    'user_agent' => request()->userAgent(),
                ]);
            } catch (\Exception $e) {}
        }

        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')->with('success', 'Anda telah berhasil logout.');
    }

    // ==================== Forgot Password ====================

    public function showForgotPassword()
    {
        return view('auth.forgot-password');
    }

    public function sendResetLink(Request $request)
    {
        $request->validate(['email' => ['required', 'email']]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return back()->withErrors(['email' => 'Email tidak terdaftar di sistem kami.']);
        }

        // Generate 6-digit OTP code for password reset
        $otpCode = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
        $user->reset_otp_code = $otpCode;
        $user->reset_otp_expires_at = now()->addDays(5);
        $user->save();

        try {
            Mail::to($user->email)->send(new ResetPasswordOtpMail($otpCode, $user->name));
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Gagal mengirim OTP Reset Password ke ' . $user->email . ': ' . $e->getMessage());
        }

        return redirect()->route('password.reset', ['email' => $request->email])
            ->with('success', 'Kode OTP untuk reset password telah dikirim ke email Anda.');
    }

    // ==================== Reset Password ====================

    public function showResetPassword(Request $request)
    {
        return view('auth.reset-password', ['email' => $request->email]);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
            'otp_code' => ['required', 'string', 'size:6'],
            'password' => ['required', 'confirmed', PasswordRule::min(8)],
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return back()->withErrors(['email' => 'Email tidak terdaftar.']);
        }

        if ($user->reset_otp_code !== $request->otp_code) {
            return back()->withErrors(['otp_code' => 'Kode OTP tidak valid.']);
        }

        if (now()->greaterThan($user->reset_otp_expires_at)) {
            return back()->withErrors(['otp_code' => 'Kode OTP sudah kedaluwarsa. Silakan minta kode baru.']);
        }

        // Reset password and clear OTP
        $user->password = Hash::make($request->password);
        $user->reset_otp_code = null;
        $user->reset_otp_expires_at = null;
        $user->save();

        try {
            ActivityLog::create([
                'user_id' => $user->id,
                'action' => 'reset_password',
                'model_type' => User::class,
                'model_id' => $user->id,
                'description' => "Melakukan reset password mandiri via OTP",
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);
        } catch (\Exception $e) {}

        return redirect()->route('login')->with('success', 'Password berhasil direset! Silakan login dengan password baru.');
    }

    // ==================== Email Verification ====================

    public function showVerifyEmail()
    {
        return view('auth.verify-email');
    }

    public function verifyOtp(Request $request)
    {
        $request->validate([
            'otp_code' => ['required', 'string', 'size:6'],
        ]);

        $user = Auth::user();

        if ($user->otp_code !== $request->otp_code) {
            return back()->withErrors(['otp_code' => 'Kode OTP tidak valid.']);
        }

        if (now()->greaterThan($user->otp_expires_at)) {
            return back()->withErrors(['otp_code' => 'Kode OTP sudah kedaluwarsa. Silakan minta kode baru.']);
        }

        $user->email_verified_at = now();
        $user->otp_code = null;
        $user->otp_expires_at = null;
        $user->save();

        return redirect('/')->with('success', 'Email berhasil diverifikasi! Selamat datang di DjudasMS.');
    }

    public function resendVerification(Request $request)
    {
        $user = $request->user();

        if ($user->hasVerifiedEmail()) {
            return redirect('/');
        }

        $otpCode = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
        $user->otp_code = $otpCode;
        $user->otp_expires_at = now()->addDays(5);
        $user->save();

        try {
            Mail::to($user->email)->send(new OtpMail($otpCode, $user->name));
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Gagal mengirim OTP (Resend) ke ' . $user->email . ': ' . $e->getMessage());
        }

        return back()->with('success', 'Kode OTP baru telah dikirim ke alamat email Anda.');
    }
}
