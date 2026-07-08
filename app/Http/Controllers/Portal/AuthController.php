<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Support\BrandAssets;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            return $this->redirectAfterLogin(Auth::user());
        }

        $logoUrl = BrandAssets::siteLogoDesktop();

        return view('portal.auth.login', compact('logoUrl'));
    }

    public function showRegister()
    {
        if (Auth::check()) {
            return $this->redirectAfterLogin(Auth::user());
        }

        $logoUrl = BrandAssets::siteLogoDesktop();

        return view('portal.auth.register', compact('logoUrl'));
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'regex:/^[A-Za-z\s]+$/'],
            'email' => 'required|email|unique:users,email',
            'phone' => ['required', 'regex:/^[0-9]{10}$/', 'unique:users,phone'],
            'password' => 'required|confirmed|min:6',
        ]);

        $defaultRoleId = User::query()
            ->whereNotIn('role_id', [1, 2])
            ->whereNotNull('role_id')
            ->value('role_id') ?? 3;

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'password' => $validated['password'],
            'role_id' => $defaultRoleId,
            'status' => 0,
            'address' => '',
        ]);

        return redirect()
            ->route('portal.login')
            ->with('success', 'Registration successful. Please wait for admin approval before signing in.');
    }

    public function sendOtp(Request $request)
    {
        $request->validate(['phone' => 'required|digits:10']);

        $user = User::where('phone', $request->phone)->first();
        if (! $user) {
            return back()->withInput()->with('error', 'Invalid mobile number.');
        }

        if (in_array((int) $user->role_id, [1, 2], true)) {
            return back()->withInput()->with('error', 'Please use admin login for this account.');
        }

        $otp = rand(1000, 9999);
        $user->otp = $otp;
        $user->otp_expire_at = Carbon::now()->addMinutes(5);
        $user->save();

        session([
            'portal_login_phone' => $request->phone,
            'portal_otp_sent' => true,
        ]);

        return back()
            ->withInput()
            ->with('success', 'OTP sent successfully.')
            ->with('dev_otp', app()->environment('local') ? $otp : null);
    }

    public function verifyOtp(Request $request)
    {
        $request->validate([
            'phone' => 'required|digits:10',
            'otp' => 'required|digits:4',
        ]);

        $user = User::where('phone', $request->phone)
            ->where('otp', $request->otp)
            ->first();

        if (! $user) {
            throw ValidationException::withMessages(['otp' => 'Invalid OTP.']);
        }

        if (Carbon::now()->gt($user->otp_expire_at)) {
            throw ValidationException::withMessages(['otp' => 'OTP expired. Please request a new one.']);
        }

        $user->otp = null;
        $user->otp_expire_at = null;
        $user->save();

        Auth::login($user, true);
        $request->session()->regenerate();
        session()->forget(['portal_login_phone', 'portal_otp_sent']);

        return $this->redirectAfterLogin($user);
    }

    public function loginWithEmail(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            throw ValidationException::withMessages([
                'email' => 'Invalid email or password.',
            ]);
        }

        $request->session()->regenerate();
        $user = Auth::user();

        if (in_array((int) $user->role_id, [1, 2], true)) {
            return redirect()->route('admin.dashboard');
        }

        if ((int) $user->status !== 1) {
            Auth::logout();
            throw ValidationException::withMessages([
                'email' => 'Your account is inactive.',
            ]);
        }

        return redirect()->route('portal.dashboard');
    }

    private function redirectAfterLogin(User $user)
    {
        if (in_array((int) $user->role_id, [1, 2], true)) {
            return redirect()->route('admin.dashboard');
        }

        if ((int) $user->status !== 1) {
            Auth::logout();
            return redirect()->route('portal.login')->with('error', 'Your account is inactive.');
        }

        return redirect()->route('portal.dashboard');
    }
}
