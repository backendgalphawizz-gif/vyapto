@php $L = $siteLabels ?? []; @endphp
<footer class="site-footer">
    <div class="container">
        <div class="footer-grid">
            <div class="footer-brand">
                <img src="{{ $siteLogoFooter }}" alt="{{ $companyName ?? 'VYAPTO' }}">
                <p>{{ $footerTagline }}</p>
                <div class="footer-social">
                    @if(!empty($socialInstagram))
                        <a href="{{ $socialInstagram }}" target="_blank" rel="noopener" aria-label="Instagram"><i class="fa-brands fa-instagram"></i></a>
                    @endif
                    @if(!empty($socialLinkedin))
                        <a href="{{ $socialLinkedin }}" target="_blank" rel="noopener" aria-label="LinkedIn"><i class="fa-brands fa-linkedin-in"></i></a>
                    @endif
                    @if(!empty($socialFacebook))
                        <a href="{{ $socialFacebook }}" target="_blank" rel="noopener" aria-label="Facebook"><i class="fa-brands fa-facebook-f"></i></a>
                    @endif
                </div>
            </div>

            <div class="footer-col">
                <h4>{{ $L['footer_col_services'] ?? 'Services' }}</h4>
                <ul>
                    <li><a href="{{ route('website.services') }}">{{ $L['nav_services_all'] ?? 'All Services' }}</a></li>
                    @foreach(($navServices ?? collect())->take(4) as $svc)
                        <li><a href="{{ route('website.services.show', $svc->slug) }}">{{ $svc->title }}</a></li>
                    @endforeach
                </ul>
            </div>

            <div class="footer-col">
                <h4>{{ $L['footer_col_company'] ?? 'Company' }}</h4>
                <ul>
                    <li><a href="{{ route('website.about') }}">{{ $L['footer_link_about'] ?? 'About Us' }}</a></li>
                    <li><a href="{{ route('website.blogs') }}">{{ $L['nav_blogs'] ?? 'Blog' }}</a></li>
                    <li><a href="{{ route('website.careers') }}">{{ $L['nav_careers'] ?? 'Careers' }}</a></li>
                    <li><a href="{{ route('website.faq') }}">{{ $L['nav_faq'] ?? 'FAQ' }}</a></li>
                </ul>
            </div>

            <div class="footer-col">
                <h4>{{ $L['footer_col_support'] ?? 'Support' }}</h4>
                <ul>
                    <li><a href="{{ route('website.contact') }}">{{ $L['footer_link_contact'] ?? 'Contact Us' }}</a></li>
                    <li><a href="{{ route('website.faq') }}">{{ $L['nav_faq'] ?? 'FAQ' }}</a></li>
                    <li><a href="{{ route('portal.login') }}">{{ $L['nav_login'] ?? 'Employee Login' }}</a></li>
                </ul>
                @if(!empty($companyPhone))
                <div class="footer-contact-item" style="margin-top:16px;">
                    <i class="fa-solid fa-phone"></i>
                    <div><a href="tel:{{ $companyPhone }}">{{ $companyPhone }}</a></div>
                </div>
                @endif
                @if(!empty($companyEmail))
                <div class="footer-contact-item">
                    <i class="fa-solid fa-envelope"></i>
                    <div><a href="mailto:{{ $companyEmail }}">{{ $companyEmail }}</a></div>
                </div>
                @endif
            </div>
        </div>

        <div class="footer-bottom">
            <span>&copy; {{ date('Y') }} {{ $companyName ?? 'Vyapto' }}. {{ $L['footer_copyright'] ?? 'All rights reserved.' }}</span>
            <div>
                <a href="{{ route('website.contact') }}">{{ $L['footer_privacy'] ?? 'Privacy Policy' }}</a>
                <a href="{{ route('website.contact') }}">{{ $L['footer_terms'] ?? 'Terms of Service' }}</a>
            </div>
        </div>
    </div>
</footer>
