<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <title>{{ config('app.name', 'Vyapto') }} — Admin Login</title>

    <link rel="preconnect" href="https://fonts.bunny.net" />
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    @if (file_exists(public_path('build/manifest.json')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @else
        <script src="https://cdn.tailwindcss.com"></script>
    @endif

    @stack('styles')
</head>
<body class="min-h-screen flex items-center justify-center font-sans text-white antialiased bg-gradient-to-r from-gray-900 via-gray-800 to-gray-900 px-4">
    @php
        $siteLogo = \App\Support\BrandAssets::siteLogoDesktop();
        $companyName = \App\Support\BrandAssets::companyName();
    @endphp
    <div class="w-full sm:max-w-md mt-6 px-6 py-8 glass rounded-3xl shadow-lg" style="background-color: #ffff;">
        <div class="flex justify-center mt-4">
            <img src="{{ $siteLogo }}" alt="{{ $companyName }}" class="h-20 w-25 logo-animation object-contain" onerror="this.src='{{ asset('images/nav-logo.png') }}';">
        </div>
        {{ $slot }}
    </div>
</body>
</html>
