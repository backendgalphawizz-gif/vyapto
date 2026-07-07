<style>
    input:focus {
    box-shadow: linear-gradient(90deg, #58b341, #0d6e56) !important;
    }
</style>
<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <div class="relative">
        <!-- X button to go back (top-right of screen) -->
        <!-- <a href="{{ url('/') }}" 
        class="absolute top-0 right-0 text-gray-400 hover:text-gray-600 text-2xl font-bold z-50"
        aria-label="Close"
        title="Back to Home">
        &times;
        </a> -->

        <form method="POST" action="/login" class="mt-5" novalidate>
            @csrf

            <!-- Email Address -->
            <div class="mt-8">
                <x-input-label for="email" :value="__('Email')" />

                <x-text-input id="email"
                    class="block mt-1 w-full {{ $errors->has('email') ? '!border-red-500 !focus:border-red-500 !focus:ring-red-500' : '' }}"
                    type="email" name="email" :value="old('email')" required autofocus autocomplete="username"
                    placeholder="Enter your email address"
                    style="color:#000; background:#fff; border:1px solid #ccc;" />

                <x-input-error :messages="$errors->get('email')" class="mt-2 text-red-500" />
            </div>

            <!-- Password -->
            <div class="mt-4">
                <x-input-label for="password" :value="__('Password')" />

                <x-text-input id="password"
                    class="block mt-1 w-full {{ $errors->has('password') ? '!border-red-500 !focus:border-red-500 !focus:ring-red-500' : '' }}"
                    type="password" name="password" required autocomplete="current-password"
                    placeholder="Enter your password" style="color:#000; background:#fff; border:1px solid #ccc;" />

                <x-input-error :messages="$errors->get('password')" class="mt-2 text-red-500" />
            </div>

            <!-- Remember Me and Forgot Password -->
            <div class="flex items-center justify-between mt-4 p-2">
                <label for="remember_me" class="inline-flex items-center" style="color:#000;">
                    <input id="remember_me" type="checkbox"
                        class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" name="remember">
                    <span class="ms-2 text-sm text-gray-700">{{ __('Remember me') }}</span>
                </label>

                <!-- @if (Route::has('password.request'))
                    <a class="underline text-sm text-gray-300 hover:text-white rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('password.request') }}">
                        {{ __('Forgot your password?') }}
                    </a>
                @endif -->
            </div>

            <!-- Actions: Back and Login Buttons -->
            <div class="flex justify-center items-center mt-4 mb-4">
                <!-- Back Button -->
                <!-- <a href="{{ url('/') }}" class="text-sm text-white hover:underline">
                    Back to Home
                </a> -->

                <!-- Login Button -->
                <x-primary-button style="background:color:#fff;  background: #FF8A08;
background: linear-gradient(90deg, rgba(255, 138, 8, 1) 0%, rgba(255, 96, 2, 1) 100%);"
                    class="px-5 py-2 rounded-full font-medium transition-all duration-200">
                    {{ __('Log in') }}
                </x-primary-button>
            </div>
        </form>
    </div>
</x-guest-layout>