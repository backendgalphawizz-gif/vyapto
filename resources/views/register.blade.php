<x-guest-layout>
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <div class="text-center mb-4">
        <h2 class="text-white font-bold text-2xl">Register</h2>
        <p class="text-gray-300">Fill out the form to create an account</p>
    </div>

    <form id="registerForm" method="POST" action="{{ route('register.submit') }}" class="mt-4">
        @csrf

        <!-- Full Name -->
        <div class="mt-4">
            <x-input-label for="name" :value="__('Full Name')" />
            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name"
                :value="old('name')" required autofocus />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <!-- Email -->
        <div class="mt-4">
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email"
                :value="old('email')" required autocomplete="email" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />
            <x-text-input id="password" class="block mt-1 w-full" type="password" name="password"
                required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('Confirm Password')" />
            <x-text-input id="password_confirmation" class="block mt-1 w-full" type="password"
                name="password_confirmation" required />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <!-- Actions -->
        <div class="flex justify-between items-center mt-6 mb-4">
            <a href="{{ url('/') }}" class="text-sm text-white hover:underline">
                Back to Home
            </a>
            <x-primary-button style="background-color: #2563eb; color: white;" class="px-5 py-2 rounded-full font-medium transition-all duration-200">
                {{ __('Register') }}
            </x-primary-button>
        </div>
    </form>

    <!-- Loader -->
    <div id="loader" class="fixed top-0 left-0 w-full h-full bg-white bg-opacity-70 flex items-center justify-center hidden z-50">
        <div class="spinner-border text-primary" role="status" style="width: 4rem; height: 4rem;">
            <span class="visually-hidden">Saving...</span>
        </div>
    </div>

    @if(session('success'))
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Registered Successfully!',
                text: '{{ session('success') }}',
                confirmButtonColor: '#3085d6',
                timer: 3000
            });
        </script>
    @endif

    @if($errors->any())
        <script>
            Swal.fire({
                icon: 'error',
                title: 'Registration Failed!',
                html: `{!! implode('<br>', $errors->all()) !!}`,
                timer: 4000,
                confirmButtonColor: '#d33'
            });
        </script>
    @endif

    <script>
        const loader = document.getElementById('loader');
        const form = document.getElementById('registerForm');
        form.addEventListener('submit', () => {
            loader.style.display = 'flex';
        });
        window.addEventListener('load', () => {
            loader.style.display = 'none';
        });
    </script>
</x-guest-layout>

<!-- {{-- This is the registration form view for the Laravel application. It includes a form for users to register with their name, email, and password. Upon successful registration, a success message is displayed using SweetAlert2. The form also includes error handling for validation errors. --}}

{{-- Note: Ensure that you have the necessary routes and controllers set up in your Laravel application to handle the registration process. --}}
{{-- The form action should point to the route that processes the registration. --}}

{{-- This code is a complete HTML document with embedded PHP for a registration form in a Laravel application. It includes Bootstrap for styling and SweetAlert2 for notifications. --}}
{{-- The form captures user input for name, email, and password, and displays success or error messages based on the registration outcome. --}}

{{-- The loader overlay is displayed when the form is submitted to indicate that processing is taking place. --}}

{{-- Make sure to include the necessary scripts and styles in your Laravel project for this code to work correctly. --}}
{{-- The form uses CSRF protection provided by Laravel and includes validation error handling. --}}

{{-- This code is designed to be user-friendly and visually appealing, with a clean layout and responsive design. --}}
{{-- The use of icons and colors enhances the user experience, making it easy to understand and navigate. --}}

{{-- The registration form is a crucial part of the application, allowing users to create accounts and access features. --}}
{{-- Ensure that you have the necessary backend logic to handle user registration securely and efficiently. --}}
{{-- This includes hashing passwords, validating input, and sending confirmation emails if required. --}}

{{-- The form is designed to be easily customizable, allowing you to modify styles, texts, and functionalities as needed. --}}
{{-- You can also extend the form with additional fields or features based on your application's requirements. --}}
{{-- The use of Bootstrap ensures that the form is responsive and looks good on various devices, including desktops, tablets, and smartphones. --}}
{{-- This is important for providing a seamless user experience across different platforms. --}}

{{-- Overall, this code serves as a solid foundation for a registration form in a Laravel application, with best practices in mind. --}} -->