@extends('layouts.website')

@section('title', 'Home')

@section('content')
@php
    $hero = $sections->get('hero');
    $heroBadge = $sections->get('hero_badge');
    $about = $sections->get('about');
    $cta = $sections->get('cta');
    $features = $sections->filter(fn ($s) => str_starts_with($s->section_key, 'feature_'));
    $heroBg = $hero?->imageUrl() ?? asset('images/web-auth-bg.png');
    $heroSide = $sections->get('hero_image')?->imageUrl() ?? asset('images/app-screen-1.png');
@endphp

<div class="home-wrapper">
    <img src="{{ $heroBg }}" alt="" class="hero-bg">

    <section class="hero" id="home">
        <div class="container">
            <div class="hero-wrapper">
                <div>
                    @if($heroBadge)
                        <div class="badge">
                            @if($heroBadge->icon)<i class="fa-solid {{ $heroBadge->icon }}"></i>@endif
                            {{ $heroBadge->title }}
                        </div>
                    @else
                        <div class="badge"><i class="fa-solid fa-user-group"></i> Employee Portal</div>
                    @endif

                    <h1>
                        {{ $hero->title ?? 'Smart Delivery' }}
                        <span>{{ $hero->subtitle ?? 'Workforce Platform' }}</span>
                    </h1>

                    <p style="margin-bottom: 30px;">
                        {{ $hero->content ?? 'Manage attendance, shipments, salary tracking and field operations from one secure platform.' }}
                    </p>

                    <div class="feature-row">
                        @forelse($features as $feature)
                            <div class="feature-box">
                                @if($feature->imageUrl())
                                    <img src="{{ $feature->imageUrl() }}" alt="" style="width:55px;height:55px;object-fit:cover;border-radius:50%;margin:0 auto 10px;display:block;">
                                @elseif($feature->icon)
                                    <i class="fa-solid {{ $feature->icon }}"></i>
                                @endif
                                <h5>{{ $feature->title }}</h5>
                                @if($feature->subtitle)<p>{{ $feature->subtitle }}</p>@endif
                            </div>
                        @empty
                            <div class="feature-box"><i class="fa-solid fa-shield-halved"></i><h5>Secure Access</h5><p>OTP Based Login</p></div>
                            <div class="feature-box"><i class="fa-solid fa-location-dot"></i><h5>GPS Attendance</h5><p>Live Tracking</p></div>
                            <div class="feature-box"><i class="fa-solid fa-box"></i><h5>Shipment Tracking</h5><p>Real Time Updates</p></div>
                            <div class="feature-box"><i class="fa-solid fa-file-lines"></i><h5>Salary Reports</h5><p>Work Reports</p></div>
                        @endforelse
                    </div>

                   
                </div>

                <div class="hero-image">
                    <img src="{{ $heroSide }}" alt="Vyapto">
                </div>
            </div>

            <div class="stats">
                <div class="stats-box">
                    @foreach([
                        ['1000+', 'Active Employees'],
                        ['25K+', 'Shipments Delivered'],
                        ['50+', 'Locations'],
                        ['99.9%', 'Secure Platform'],
                    ] as $stat)
                        <div class="stat-wrapper">
                            <div><i class="fa-solid fa-users"></i></div>
                            <div class="stat-text">
                                <div class="stat">
                                    <h3>{{ $stat[0] }}</h3>
                                    <p>{{ $stat[1] }}</p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>
</div>

<section id="feature" class="features-section">
    <div class="container">
        <div class="section-title">
            <span class="section-badge"><i class="fa-solid fa-layer-group"></i> FEATURES</span>
            <h2>Everything You Need, In One Platform</h2>
            <p>Built for delivery associates to simplify daily operations and maximize efficiency.</p>
        </div>
        <div class="features-grid">
            @foreach([
                ['feature-icon1.png', 'Easy Attendance', 'Punch in/out with GPS location and selfie verification for accurate attendance.'],
                ['feature-icon2.png', 'Manage Deliveries', 'Get assigned shipments and update delivery status in real time.'],
                ['feature-icon3.png', 'Real-Time Tracking', 'Live tracking of deliveries and routes to ensure complete visibility.'],
                ['feature-icon4.png', 'Salary on Track', 'Access your salary slips, earnings and work reports anytime.'],
                ['feature-icon5.png', 'Performance Insights', 'Track your performance and delivery statistics with detailed insights.'],
                ['feature-icon6.png', 'Secure & Trusted', 'Your data is protected with industry-standard security and privacy.'],
            ] as $f)
                <div class="feature-card">
                    <div class="feature-icon">
                        <img src="{{ asset('images/' . $f[0]) }}" alt="" class="feature-icons">
                    </div>
                    <div class="feature-content">
                        <h4>{{ $f[1] }}</h4>
                        <p>{{ $f[2] }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>

<section class="mobile-app-section">
    <div class="container">
        <div class="mobile-app-wrapper">
            <div class="mobile-app-content">
                <span class="mobile-badge"><i class="fa-solid fa-mobile-screen"></i> MOBILE APP</span>
                <h2>Your Work, <br> On The Go</h2>
                <p>Our Android app helps you stay connected, manage deliveries, mark attendance and track earnings — anytime, anywhere.</p>
                <div class="divider"></div>
                <a href="#" class="playstore-btn"><img src="{{ asset('images/play-store.png') }}" alt="Play Store"></a>
            </div>
            <div class="mobile-app-image">
                <div class="bg-gradient"></div>
                <div class="dot-pattern dot-left"></div>
                <div class="dot-pattern dot-right"></div>
                <img src="{{ asset('images/app-screen-1.png') }}" alt="" class="phone phone-left">
                <img src="{{ asset('images/app-screen-2.png') }}" alt="" class="phone phone-right">
            </div>
        </div>
    </div>
</section>

<section class="why-vyapto" id="about">
    <div class="container">
        <div class="content">
            <span class="badge"><i class="fa-solid fa-users"></i> WHY CHOOSE VYAPTO</span>
            <h2>@if($about?->title){!! nl2br(e($about->title)) !!}@else Built for Smarter<br>Delivery Operations @endif</h2>
            <p>{{ $about?->content ? strip_tags($about->content) : 'We empower delivery teams and operations managers with the tools they need to work smarter and deliver better.' }}</p>
            @if($about && $about->imageUrl())
                <img src="{{ $about->imageUrl() }}" alt="{{ $about->title }}" style="max-width:100%;border-radius:16px;margin-top:20px;">
            @endif
            <ul>
                <li><i class="fa-solid fa-circle-check"></i> Dedicated support for employees and delivery partners</li>
                <li><i class="fa-solid fa-circle-check"></i> Reliable & accurate tracking for all operations</li>
                <li><i class="fa-solid fa-circle-check"></i> Designed for speed, simplicity and productivity</li>
            </ul>
        </div>
        <div class="promise-card">
            <div class="icon"><i class="fa-solid fa-shield-halved"></i></div>
            <div>
                <h4>Our Promise</h4>
                <p>To provide a secure, reliable and user-friendly platform that helps every delivery associate succeed.</p>
            </div>
        </div>
    </div>
</section>

@if($services->isNotEmpty())
<section class="features-section" style="padding-top:40px;">
    <div class="container">
        <div class="section-title">
            <span class="section-badge"><i class="fa-solid fa-truck"></i> OUR SERVICES</span>
            <h2>What We Offer</h2>
            <p>End-to-end logistics and workforce solutions for growing businesses.</p>
        </div>
        <div class="features-grid">
            @foreach($services as $service)
                <div class="feature-card">
                    <div class="feature-icon">
                        @if($service->imageUrl())
                            <img src="{{ $service->imageUrl() }}" alt="" class="feature-icons" style="width:60px;height:60px;object-fit:cover;border-radius:12px;">
                        @else
                            <i class="fa-solid {{ $service->icon ?? 'fa-truck' }}" style="font-size:28px;color:#FF6002;"></i>
                        @endif
                    </div>
                    <div class="feature-content">
                        <h4>{{ $service->title }}</h4>
                        <p>{{ $service->description }}</p>
                        <a href="{{ route('website.services.show', $service->slug) }}" style="color:#FF6002;font-weight:600;text-decoration:none;">Learn more &rarr;</a>
                    </div>
                </div>
            @endforeach
        </div>
        <div style="text-align:center;margin-top:30px;">
            <a href="{{ route('website.services') }}" class="cta-btn">View All Services</a>
        </div>
    </div>
</section>
@endif

<section class="testimonials-section">
    <div class="container">
        <div class="section-heading">
            <span class="section-tag"><i class="fa-solid fa-user-group"></i> TESTIMONIALS</span>
            <h2>Loved by Delivery Partners</h2>
            <p>See what our employees have to say about Vyapto.</p>
        </div>
        <div class="testimonial-grid">
            @foreach([
                ['"Vyapto app makes my work so easy. Punch in, get deliveries and track earnings — everything in one place."', 'Aman Kumar', 'Delivery Associate', 12],
                ['"The GPS attendance is accurate and the app is very simple to use."', 'Rohit Paswan', 'Delivery Associate', 15],
                ['"I can track my salary and download payslips anytime. Very helpful!"', 'Vivek Singh', 'Delivery Associate', 18],
            ] as $t)
                <div class="testimonial-card">
                    <div class="rating">★★★★★</div>
                    <p class="testimonial-text">{!! $t[0] !!}</p>
                    <div class="testimonial-user">
                        <img src="https://i.pravatar.cc/60?img={{ $t[3] }}" alt="">
                        <div><h5>{{ $t[1] }}</h5><span>{{ $t[2] }}</span></div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>

<div class="container">
    <div class="cta-banner cta-banner--centered">
        <div>
            <h2>{{ $cta->title ?? 'Ready To Get Started?' }}</h2>
            <p>{{ $cta->content ?? 'Join thousands of delivery associates using Vyapto.' }}</p>
        </div>
    </div>
</div>
@endsection
