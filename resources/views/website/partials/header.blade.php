<div class="top-bar">
    <div class="container">
    <div class="top-bar-info">
    @if(!empty($companyPhone))
    <a href="tel:{{ $companyPhone }}">
        <i class="fa-solid fa-phone"></i>
        {{ $companyPhone }}
    </a>
    @endif

    @if(!empty($companyEmail))
    <a href="mailto:{{ $companyEmail }}">
        <i class="fa-solid fa-envelope"></i>
        {{ $companyEmail }}
    </a>
    @endif

    @if(!empty($companyAddress))
    <span>
        <i class="fa-solid fa-location-dot"></i>
        {{ $companyAddress }}
    </span>
    @endif
</div>
    </div>
</div>

@php $L = $siteLabels ?? []; @endphp

<header class="site-header">
    <div class="container">
        <div class="navbar">
            <a href="{{ route('website.home') }}" class="logo">
                <img src="{{ $siteLogoDesktop }}" alt="{{ $companyName ?? 'VYAPTO' }}" class="desktop-logo">
                <img src="{{ $siteLogoMobile }}" alt="{{ $companyName ?? 'VYAPTO' }}" class="mobile-logo">
            </a>

            <nav class="nav-links">
                <a href="{{ route('website.home') }}" class="cursor-hover {{ request()->routeIs('website.home') ? 'active' : '' }}">{{ $L['nav_home'] ?? 'Home' }}</a>
                <a href="{{ route('website.about') }}" class="cursor-hover {{ request()->routeIs('website.about') ? 'active' : '' }}">{{ $L['nav_about'] ?? 'About' }}</a>

                <div class="nav-dropdown">
                    <button type="button" class="nav-dropdown-trigger {{ request()->routeIs('website.services*') ? 'active' : '' }}">
                        {{ $L['nav_services'] ?? 'Services' }} <i class="fa-solid fa-chevron-down" style="font-size:10px;"></i>
                    </button>
                    <div class="nav-dropdown-menu">
                        <a href="{{ route('website.services') }}"><i class="fa-solid fa-grid-2"></i> {{ $L['nav_services_all'] ?? 'All Services' }}</a>
                        @foreach(($navServices ?? collect()) as $svc)
                            <a href="{{ route('website.services.show', $svc->slug) }}">
                                <i class="fa-solid {{ $svc->icon ?? 'fa-truck' }}"></i> {{ $svc->title }}
                            </a>
                        @endforeach
                    </div>
                </div>

                <a href="{{ route('website.products') }}" class="cursor-hover {{ request()->routeIs('website.products*') ? 'active' : '' }}">{{ $L['nav_products'] ?? 'Products' }}</a>
                <a href="{{ route('website.blogs') }}" class="cursor-hover {{ request()->routeIs('website.blogs*') ? 'active' : '' }}">{{ $L['nav_blogs'] ?? 'Blog' }}</a>
                <a href="{{ route('website.careers') }}" class="cursor-hover {{ request()->routeIs('website.careers') ? 'active' : '' }}">{{ $L['nav_careers'] ?? 'Careers' }}</a>
            </nav>

            <div class="header-actions">
                <a href="{{ route('website.contact') }}" class="btn-primary cursor-hover">{{ $L['nav_cta'] ?? 'Get in Touch' }}</a>
            </div>

            <button class="menu-toggle" id="menuToggle" type="button" aria-label="Menu">
                <i class="fa-solid fa-bars"></i>
            </button>
        </div>
    </div>
</header>

<div class="mobile-menu" id="mobileMenu">
    <a href="{{ route('website.home') }}">{{ $L['nav_home'] ?? 'Home' }}</a>
    <a href="{{ route('website.about') }}">{{ $L['nav_about'] ?? 'About' }}</a>
    <a href="{{ route('website.services') }}">{{ $L['nav_services'] ?? 'Services' }}</a>
    <div class="mobile-submenu">
        @foreach(($navServices ?? collect()) as $svc)
            <a href="{{ route('website.services.show', $svc->slug) }}">{{ $svc->title }}</a>
        @endforeach
    </div>
    <a href="{{ route('website.products') }}">{{ $L['nav_products'] ?? 'Products' }}</a>
    <a href="{{ route('website.blogs') }}">{{ $L['nav_blogs'] ?? 'Blog' }}</a>
    <a href="{{ route('website.careers') }}">{{ $L['nav_careers'] ?? 'Careers' }}</a>
    <a href="{{ route('website.faq') }}">{{ $L['nav_faq'] ?? 'FAQ' }}</a>
    <a href="{{ route('website.contact') }}">{{ $L['nav_contact'] ?? 'Contact' }}</a>
    <a href="{{ route('portal.login') }}">{{ $L['nav_login'] ?? 'Employee Login' }}</a>
</div>


<script>
const topBar = document.querySelector('.top-bar');
const siteHeader = document.querySelector('.site-header');

function handleHeaderScroll() {
    const scrollTop = window.pageYOffset || document.documentElement.scrollTop;

    if (scrollTop > 50) {
        topBar && topBar.classList.add('hide');
        siteHeader && siteHeader.classList.add('scrolled');
    } else {
        topBar && topBar.classList.remove('hide');
        siteHeader && siteHeader.classList.remove('scrolled');
    }
}

window.addEventListener('scroll', handleHeaderScroll, { passive: true });
handleHeaderScroll();
</script>
