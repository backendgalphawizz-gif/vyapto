<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <title>{{ config('app.name', 'Laravel') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net" />
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')

    
</head>
@php
    $company_web_logo = \App\Models\Setting::where('type', 'company_web_logo')->first();
@endphp
<body class="min-h-screen flex items-center justify-center font-sans text-white antialiased bg-gradient-to-r from-gray-900 via-gray-800 to-gray-900 px-4">
     <div class="w-full sm:max-w-md mt-6 px-6 py-8 glass rounded-3xl shadow-lg" style="background-color: #ffff;">
        <div class="flex justify-center  mt-4">
            <img src="{{ $company_web_logo ? asset('storage/company/'.$company_web_logo->value) : '/images/developer.png' }}" alt="LEMS" class="h-20 w-20 logo-animation" onerror="this.src='{{ asset('/assets/admin/images/company_logo.png') }}';" />
        </div>
        {{ $slot }}
    </div> 
</body>
</html>
