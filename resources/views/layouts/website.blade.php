<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', $companyName ?? 'Vyapto') | {{ $companyName ?? 'Vyapto' }}</title>
    <link rel="icon" href="/favicon.ico" sizes="any">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('assets/website/css/landing.css') }}?v=5">
    <link rel="stylesheet" href="{{ asset('assets/website/css/pages.css') }}?v=2">
    @stack('styles')
</head>
<body>
    @include('website.partials.header')

    @if(session('success'))
        <div class="container">
            <div class="alert-success">{{ session('success') }}</div>
        </div>
    @endif

    @yield('content')

    @include('website.partials.footer')

    <script>
        const menuToggle = document.getElementById('menuToggle');
        const mobileMenu = document.getElementById('mobileMenu');
        if (menuToggle && mobileMenu) {
            menuToggle.addEventListener('click', () => {
                mobileMenu.classList.toggle('active');
                menuToggle.innerHTML = mobileMenu.classList.contains('active')
                    ? '<i class="fa-solid fa-xmark"></i>'
                    : '<i class="fa-solid fa-bars"></i>';
            });
        }
        function scrollToSection(id) {
            const element = document.getElementById(id);
            if (element) {
                element.scrollIntoView({ behavior: 'smooth', block: 'start' });
                mobileMenu?.classList.remove('active');
            }
        }
    </script>
    @stack('scripts')
</body>
</html>
