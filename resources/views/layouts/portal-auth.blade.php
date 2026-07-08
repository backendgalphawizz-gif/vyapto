<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login | VYAPTO Employee Portal</title>
    @php
        $logoUrl = \App\Support\BrandAssets::siteLogoDesktop();
        $companyName = \App\Support\BrandAssets::companyName();
    @endphp
    <link rel="icon" href="{{ $logoUrl }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="{{ asset('assets/portal/css/style.css') }}" rel="stylesheet">
</head>
<body class="portal-auth-body">
    @yield('content')
    <script>
        document.querySelectorAll('[data-auth-tab]').forEach(function (button) {
            button.addEventListener('click', function () {
                const target = button.dataset.authTab;
                document.querySelectorAll('[data-auth-tab]').forEach(function (el) {
                    el.classList.toggle('active', el === button);
                });
                document.querySelectorAll('[data-auth-panel]').forEach(function (panel) {
                    panel.classList.toggle('active', panel.dataset.authPanel === target);
                });
            });
        });
    </script>
    <script src="{{ asset('assets/portal/js/password-toggle.js') }}"></script>
    @stack('scripts')
</body>
</html>
