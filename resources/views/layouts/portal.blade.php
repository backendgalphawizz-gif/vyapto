<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Home') | VYAPTO</title>
    @php
        $companyLogo = \App\Models\Setting::where('type', 'company_web_logo')->first();
        $logoUrl = $companyLogo
            ? asset('storage/company/'.$companyLogo->value)
            : asset('assets/admin/images/company_logo.png');
        $avatar = Auth::user()->profileImageUrl();
        $todayAttendance = \App\Models\Attendance::where('employee_id', Auth::id())->whereDate('punch_in_date', today())->first();
        $isDashboard = request()->routeIs('portal.dashboard');
        $navLinks = [
            ['route' => 'portal.dashboard', 'label' => 'Home', 'match' => 'portal.dashboard'],
            ['route' => 'portal.punch.index', 'label' => 'Punch', 'match' => 'portal.punch.*'],
            ['route' => 'portal.attendance.index', 'label' => 'Attendance', 'match' => 'portal.attendance.*'],
            ['route' => 'portal.parcels.index', 'label' => 'Shipments', 'match' => 'portal.parcels.*'],
            ['route' => 'portal.salary.index', 'label' => 'Salary', 'match' => 'portal.salary.*'],
            ['route' => 'portal.faqs.index', 'label' => 'FAQs', 'match' => 'portal.faqs.*'],
        ];
    @endphp
    <link rel="icon" href="{{ $logoUrl }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="{{ asset('assets/portal/css/style.css') }}" rel="stylesheet">
    @stack('styles')
</head>
<body class="site-body">
<header class="site-header">
    <div class="site-container site-header-inner">
        <a href="{{ route('portal.dashboard') }}" class="site-brand">
            <img src="{{ $logoUrl }}" alt="VYAPTO">
            <span>VYAPTO</span>
        </a>

        <nav class="site-nav" id="siteNav">
            @foreach($navLinks as $link)
                <a href="{{ route($link['route']) }}" class="{{ request()->routeIs($link['match']) ? 'active' : '' }}">
                    {{ $link['label'] }}
                </a>
            @endforeach
        </nav>

        <div class="site-header-actions">
            <div class="site-punch-badge d-none d-md-flex">
                <i class="bi bi-clock"></i>
                <span>Punch in: <strong>{{ optional($todayAttendance?->punch_in_time)->format('h:i A') ?? 'Not yet' }}</strong></span>
            </div>

            <div class="dropdown">
                <button class="site-user-btn dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <img src="{{ $avatar }}" alt="{{ Auth::user()->name }}">
                    <span class="d-none d-lg-inline">{{ Auth::user()->name }}</span>
                </button>
                <ul class="dropdown-menu dropdown-menu-end site-dropdown">
                    <li class="dropdown-header">{{ Auth::user()->email }}</li>
                    <li><a class="dropdown-item" href="{{ route('portal.profile.show') }}"><i class="bi bi-person me-2"></i>My Profile</a></li>
                    <li><a class="dropdown-item" href="{{ route('portal.pages.index') }}"><i class="bi bi-file-text me-2"></i>Pages</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="dropdown-item text-danger"><i class="bi bi-box-arrow-right me-2"></i>Sign out</button>
                        </form>
                    </li>
                </ul>
            </div>

            <button type="button" class="site-menu-toggle d-lg-none" id="siteMenuToggle" aria-label="Menu">
                <i class="bi bi-list"></i>
            </button>
        </div>
    </div>
</header>

@if(! $isDashboard)
<section class="page-hero">
    <div class="site-container">
        <nav class="site-breadcrumb" aria-label="Breadcrumb">
            <a href="{{ route('portal.dashboard') }}">Home</a>
            <i class="bi bi-chevron-right"></i>
            <span>@yield('title')</span>
        </nav>
        <div class="page-hero-row">
            <div>
                <h1>@yield('title')</h1>
                @hasSection('page_subtitle')
                    <p>@yield('page_subtitle')</p>
                @endif
            </div>
            @hasSection('header_actions')
                <div class="page-hero-actions">@yield('header_actions')</div>
            @endif
        </div>
    </div>
</section>
@endif

<main class="site-main">
    <div class="site-container">
        @if(session('success'))
            <div class="site-alert site-alert-success">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="site-alert site-alert-error">{{ session('error') }}</div>
        @endif

        @yield('content')
    </div>
</main>

<footer class="site-footer">
    <div class="site-container site-footer-inner">
        <div class="site-footer-brand">
            <img src="{{ $logoUrl }}" alt="VYAPTO">
            <div>
                <strong>VYAPTO Employee Portal</strong>
                <span>Manage your workday in one place.</span>
            </div>
        </div>
        <div class="site-footer-links">
            <a href="{{ route('portal.faqs.index') }}">FAQs</a>
            <a href="{{ route('portal.pages.index') }}">Policies</a>
            <a href="{{ route('portal.profile.show') }}">Profile</a>
        </div>
        <p class="site-footer-copy">&copy; {{ date('Y') }} VYAPTO. All rights reserved.</p>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
(function () {
    const toggle = document.getElementById('siteMenuToggle');
    const nav = document.getElementById('siteNav');

    toggle?.addEventListener('click', function () {
        nav.classList.toggle('open');
        document.body.classList.toggle('site-nav-open');
    });

    nav?.querySelectorAll('a').forEach(function (link) {
        link.addEventListener('click', function () {
            nav.classList.remove('open');
            document.body.classList.remove('site-nav-open');
        });
    });
})();
</script>
<script src="{{ asset('assets/portal/js/password-toggle.js') }}"></script>
@stack('scripts')
</body>
</html>
