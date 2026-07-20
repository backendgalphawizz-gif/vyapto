<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', $companyName ?? 'Vyapto') | {{ $companyName ?? 'Vyapto' }}</title>
    <meta name="description" content="@yield('meta_description', 'Professional logistics and workforce solutions.')">
    <link rel="icon" href="/favicon.ico" sizes="any">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('assets/website/css/srp-theme.css') }}?v=24">
    @stack('styles')
</head>
<body>
    @include('website.partials.header')

    @if(session('success'))
        <div class="alert-success">{{ session('success') }}</div>
    @endif

    @yield('content')

    @include('website.partials.footer')

    <script src="{{ asset('assets/website/js/website.js') }}?v=10"></script>
    @stack('scripts')
</body>
</html>
