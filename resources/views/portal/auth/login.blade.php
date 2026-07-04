@extends('layouts.portal-auth')

@section('content')
<div class="auth-page">
    <section class="auth-hero">
        <img src="{{ asset('images/auth-login-bg.png') }}" alt="Background" class="auth-hero-bg">
        <img src="{{ $logoUrl }}" alt="VYAPTO" class="auth-hero-logo">
        <h1>Welcome to VYAPTO</h1>
        <p>Sign in to manage attendance, shipments, salary slips, and your daily field tasks from a simple employee website.</p>
        <ul class="auth-feature-list">
            <li><i class="bi bi-check-circle-fill"></i> Secure employee login</li>
            <li><i class="bi bi-check-circle-fill"></i> Punch in/out with GPS</li>
            <li><i class="bi bi-check-circle-fill"></i> Track work and payments online</li>
        </ul>
    </section>

    <section class="auth-panel-wrap">
        <div class="auth-card">
            <img src="{{ $logoUrl }}" alt="VYAPTO" class="auth-logo d-lg-none">

            <h1 class="auth-title">Employee Login</h1>
            <p class="auth-subtitle">Enter your details to access your account.</p>

            @if(session('success'))
                <div class="site-alert site-alert-success">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="site-alert site-alert-error">{{ session('error') }}</div>
            @endif
            @if(session('dev_otp') && app()->environment('local'))
                <div class="site-alert site-alert-success">Dev OTP: {{ session('dev_otp') }}</div>
            @endif

            <div class="auth-tabs">
                <button type="button" class="auth-tab active" data-auth-tab="mobile">Mobile</button>
                <button type="button" class="auth-tab" data-auth-tab="employee">Employee ID</button>
            </div>

            <div class="auth-panel active" data-auth-panel="mobile">
                @if(!session('portal_otp_sent'))
                    <form method="POST" action="{{ route('portal.login.otp.send') }}">
                        @csrf
                        <div class="phone-input-wrap">
                            <span>+91</span>
                            <input type="tel" name="phone" maxlength="10" placeholder="Enter mobile number" value="{{ old('phone') }}" required>
                        </div>
                        @error('phone')<div class="text-danger small mb-2">{{ $message }}</div>@enderror
                        <button type="submit" class="app-btn app-btn-green">Send OTP</button>
                    </form>
                @else
                    <form method="POST" action="{{ route('portal.login.otp.verify') }}">
                        @csrf
                        <div class="phone-input-wrap mb-3">
                            <span>+91</span>
                            <input type="tel" name="phone" maxlength="10" value="{{ old('phone', session('portal_login_phone')) }}" required>
                        </div>
                        <div class="app-input-wrap mb-3">
                            <label>Enter OTP</label>
                            <input type="text" name="otp" maxlength="4" class="app-input" placeholder="4 digit OTP" required>
                        </div>
                        @error('otp')<div class="text-danger small mb-2">{{ $message }}</div>@enderror
                        <button type="submit" class="app-btn app-btn-green mb-2">Verify & Login</button>
                    </form>
                    <form method="POST" action="{{ route('portal.login.otp.send') }}">
                        @csrf
                        <input type="hidden" name="phone" value="{{ old('phone', session('portal_login_phone')) }}">
                        <button type="submit" class="app-btn app-btn-outline">Resend OTP</button>
                    </form>
                @endif
            </div>

            <div class="auth-panel" data-auth-panel="employee">
                <form method="POST" action="{{ route('portal.login.email') }}">
                    @csrf
                    <div class="app-input-wrap">
                        <label>Email / Employee ID</label>
                        <input type="email" name="email" class="app-input" value="{{ old('email') }}" placeholder="Enter email" required>
                    </div>
                    <div class="app-input-wrap">
                        <label>Password</label>
                        <div class="password-toggle-wrap">
                            <input type="password" name="password" class="app-input" placeholder="Enter password" required>
                            <button type="button" class="password-toggle-btn" aria-label="Show password" data-password-toggle>
                                <i class="bi bi-eye"></i>
                            </button>
                        </div>
                    </div>
                    @error('email')<div class="text-danger small mb-2">{{ $message }}</div>@enderror
                    <button type="submit" class="app-btn app-btn-green">Login</button>
                </form>
            </div>

            <!-- <div class="auth-switch-link">
                Not registered? <a href="{{ route('portal.register') }}">Sign up</a>
            </div> -->
        </div>
    </section>
</div>
@endsection
