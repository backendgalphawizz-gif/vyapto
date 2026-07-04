<footer id="contact">
    <div class="container">
        <div class="footer-grid">
            <div>
                <img src="{{ asset('images/nav-logo.png') }}" alt="{{ $companyName ?? 'VYAPTO' }}" class="auth-foot-logo">
                <p>Smart Delivery Workforce Platform for attendance, shipment tracking and operations.</p>
            </div>
            <div>
                <h4>Quick Links</h4>
                <ul>
                    <li><a href="{{ route('website.home') }}">Home</a></li>
                    <li><a href="{{ route('website.services') }}">Our Services</a></li>
                    <li><a href="{{ route('website.products') }}">Our Products</a></li>
                    <li><a href="{{ route('website.blogs') }}">Blogs</a></li>
                </ul>
            </div>
            <div>
                <h4>Careers</h4>
                <ul>
                    <li><a href="{{ route('website.careers') }}">Careers & Highlights</a></li>
                    <li><a href="{{ route('portal.login') }}">Employee Login</a></li>
                    <li><a href="{{ route('portal.register') }}">Register</a></li>
                </ul>
            </div>
            <div>
                <h4>Contact</h4>
                <ul>
                    @if(!empty($companyEmail))
                        <li><a href="mailto:{{ $companyEmail }}">{{ $companyEmail }}</a></li>
                    @endif
                    @if(!empty($companyPhone))
                        <li><a href="tel:{{ $companyPhone }}">{{ $companyPhone }}</a></li>
                    @endif
                    @if(!empty($companyAddress))
                        <li>{{ $companyAddress }}</li>
                    @endif
                </ul>
            </div>
        </div>
        <div class="copyright">
            &copy; {{ date('Y') }} {{ $companyName ?? 'Vyapto' }}. All Rights Reserved.
        </div>
    </div>
</footer>
