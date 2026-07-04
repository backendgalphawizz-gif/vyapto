@extends('layouts.portal-auth')

@section('content')
<div class="auth-page">
    <section class="auth-hero">
        <img src="{{ $logoUrl }}" alt="VYAPTO" class="auth-hero-logo">
        <h1>Join VYAPTO</h1>
        <p>Create your employee account to access attendance, shipments, salary slips, and daily field tasks.</p>
        <ul class="auth-feature-list">
            <li><i class="bi bi-check-circle-fill"></i> Quick employee registration</li>
            <li><i class="bi bi-check-circle-fill"></i> Secure login with mobile OTP</li>
            <li><i class="bi bi-check-circle-fill"></i> Admin approval before first login</li>
        </ul>
    </section>

    <section class="auth-panel-wrap">
        <div class="auth-card">
            <img src="{{ $logoUrl }}" alt="VYAPTO" class="auth-logo d-lg-none">

            <h1 class="auth-title">Employee Sign Up</h1>
            <p class="auth-subtitle">Fill in your details to create an account.</p>

            @if($errors->any())
                <div class="site-alert site-alert-error">
                    <ul class="mb-0 ps-3">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('portal.register.submit') }}">
                @csrf

                <div class="app-input-wrap">
                    <label>Full Name</label>
                    <input type="text" name="name" class="app-input" value="{{ old('name') }}"
                        pattern="[A-Za-z\s]+" oninput="this.value = this.value.replace(/[^A-Za-z\s]/g, '');" maxlength="255" required>
                </div>

                <div class="app-input-wrap">
                    <label>Email</label>
                    <input type="email" name="email" class="app-input" value="{{ old('email') }}" placeholder="Enter email" required>
                </div>

                <div class="app-input-wrap">
                    <label>Mobile</label>
                    <div class="phone-input-wrap">
                        <span>+91</span>
                        <input type="tel" name="phone" maxlength="10" placeholder="Enter mobile number"
                            value="{{ old('phone') }}" oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 10);" required>
                    </div>
                </div>

                <div class="app-input-wrap">
                    <label>Password</label>
                    <div class="password-toggle-wrap">
                        <input type="password" name="password" class="app-input" placeholder="Create password" required>
                        <button type="button" class="password-toggle-btn" aria-label="Show password" data-password-toggle>
                            <i class="bi bi-eye"></i>
                        </button>
                    </div>
                </div>

                <div class="app-input-wrap">
                    <label>Confirm Password</label>
                    <div class="password-toggle-wrap">
                        <input type="password" name="password_confirmation" class="app-input" placeholder="Confirm password" required>
                        <button type="button" class="password-toggle-btn" aria-label="Show password" data-password-toggle>
                            <i class="bi bi-eye"></i>
                        </button>
                    </div>
                </div>

                <button type="submit" class="app-btn app-btn-green">Create Account</button>
            </form>

            <div class="auth-switch-link">
                Already have an account? <a href="{{ route('portal.login') }}">Sign in</a>
            </div>
        </div>
    </section>
</div>
@endsection
