<header>
    <div class="container">
        <div class="navbar">
            <a href="{{ route('website.home') }}" class="logo">
                <img src="{{ $siteLogoDesktop }}" alt="{{ $companyName ?? 'VYAPTO' }}" class="auth-hero-logo desktop-logo">
                <img src="{{ $siteLogoMobile }}" alt="{{ $companyName ?? 'VYAPTO' }}" class="auth-hero-logo mobile-logo">
            </a>

            <nav class="nav-links">
                <a href="{{ route('website.home') }}" class="{{ request()->routeIs('website.home') ? 'active' : '' }}">Home</a>
                <a href="{{ route('website.services') }}" class="{{ request()->routeIs('website.services*') ? 'active' : '' }}">Our Services</a>
                <a href="{{ route('website.products') }}" class="{{ request()->routeIs('website.products*') ? 'active' : '' }}">Our Products</a>
                <a href="{{ route('website.careers') }}" class="{{ request()->routeIs('website.careers') ? 'active' : '' }}">Careers</a>
                <a href="{{ route('website.blogs') }}" class="{{ request()->routeIs('website.blogs*') ? 'active' : '' }}">Blogs</a>
                <a href="{{ route('website.contact') }}" class="{{ request()->routeIs('website.contact') ? 'active' : '' }}">Contact</a>
            </nav>

            <button class="menu-toggle" id="menuToggle" type="button" aria-label="Menu">
                <i class="fa-solid fa-bars"></i>
            </button>

            
        </div>
    </div>
</header>

<div class="mobile-menu" id="mobileMenu">
    <a href="{{ route('website.home') }}">Home</a>
    <a href="{{ route('website.services') }}">Our Services</a>
    <a href="{{ route('website.products') }}">Our Products</a>
    <a href="{{ route('website.careers') }}">Careers</a>
    <a href="{{ route('website.blogs') }}">Blogs</a>
    <a href="{{ route('website.contact') }}">Contact</a>
</div>
