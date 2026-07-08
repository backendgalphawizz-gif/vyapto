<div class="top-bar">
    <div class="container">
        <div class="top-bar-info">
            @if(!empty($companyPhone))
                <a href="tel:{{ $companyPhone }}"><i class="fa-solid fa-phone"></i> {{ $companyPhone }}</a>
            @endif
            @if(!empty($companyEmail))
                <a href="mailto:{{ $companyEmail }}"><i class="fa-solid fa-envelope"></i> {{ $companyEmail }}</a>
            @endif
            @if(!empty($companyAddress))
                <span><i class="fa-solid fa-location-dot"></i> {{ $companyAddress }}</span>
            @endif
        </div>
    </div>
</div>

<header class="site-header">
    <div class="container">
        <div class="navbar">
            <a href="{{ route('website.home') }}" class="logo">
                <img src="{{ $siteLogoDesktop }}" alt="{{ $companyName ?? 'VYAPTO' }}" class="desktop-logo">
                <img src="{{ $siteLogoMobile }}" alt="{{ $companyName ?? 'VYAPTO' }}" class="mobile-logo">
            </a>

            <nav class="nav-links">
                <a href="{{ route('website.home') }}" class="cursor-hover {{ request()->routeIs('website.home') ? 'active' : '' }}">Home</a>
                <a href="{{ route('website.about') }}" class="cursor-hover {{ request()->routeIs('website.about') ? 'active' : '' }}">About</a>

                <div class="nav-dropdown">
                    <button type="button" class="nav-dropdown-trigger {{ request()->routeIs('website.services*') ? 'active' : '' }}">
                        Services <i class="fa-solid fa-chevron-down" style="font-size:10px;"></i>
                    </button>
                    <div class="nav-dropdown-menu">
                        <a href="{{ route('website.services') }}"><i class="fa-solid fa-grid-2"></i> All Services</a>
                        @foreach(($navServices ?? collect()) as $svc)
                            <a href="{{ route('website.services.show', $svc->slug) }}">
                                <i class="fa-solid {{ $svc->icon ?? 'fa-truck' }}"></i> {{ $svc->title }}
                            </a>
                        @endforeach
                    </div>
                </div>

                <a href="{{ route('website.products') }}" class="cursor-hover {{ request()->routeIs('website.products*') ? 'active' : '' }}">Products</a>
                <a href="{{ route('website.blogs') }}" class="cursor-hover {{ request()->routeIs('website.blogs*') ? 'active' : '' }}">Blog</a>
                <a href="{{ route('website.careers') }}" class="cursor-hover {{ request()->routeIs('website.careers') ? 'active' : '' }}">Careers</a>
                <a href="{{ route('website.faq') }}" class="cursor-hover {{ request()->routeIs('website.faq') ? 'active' : '' }}">FAQ</a>
            </nav>

            <div class="header-actions">
                {{-- <a href="{{ route('portal.login') }}" class="btn-outline cursor-hover">Login</a> --}}
                <a href="{{ route('website.contact') }}" class="btn-primary cursor-hover">Get in Touch</a>
            </div>

            <button class="menu-toggle" id="menuToggle" type="button" aria-label="Menu">
                <i class="fa-solid fa-bars"></i>
            </button>
        </div>
    </div>
</header>

<div class="mobile-menu" id="mobileMenu">
    <a href="{{ route('website.home') }}">Home</a>
    <a href="{{ route('website.about') }}">About</a>
    <a href="{{ route('website.services') }}">Services</a>
    <div class="mobile-submenu">
        @foreach(($navServices ?? collect()) as $svc)
            <a href="{{ route('website.services.show', $svc->slug) }}">{{ $svc->title }}</a>
        @endforeach
    </div>
    <a href="{{ route('website.products') }}">Products</a>
    <a href="{{ route('website.blogs') }}">Blog</a>
    <a href="{{ route('website.careers') }}">Careers</a>
    <a href="{{ route('website.faq') }}">FAQ</a>
    <a href="{{ route('website.contact') }}">Contact</a>
    <a href="{{ route('portal.login') }}">Employee Login</a>
</div>
