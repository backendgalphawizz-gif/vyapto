@extends('layouts.website')

@section('title', 'Home')

@section('content')
@php
    $hero = $sections->get('hero');
    $heroBadge = $sections->get('hero_badge');
    $servicesHeader = $sections->get('services_header');
    $whyHeader = $sections->get('why_header');
    $whyCards = $sections->filter(fn ($s) => str_starts_with($s->section_key, 'why_partner_'))->sortBy('sort_order');
    $processHeader = $sections->get('process_header');
    $processSteps = $sections->filter(fn ($s) => str_starts_with($s->section_key, 'process_step_'))->sortBy('sort_order');
    $processImages = $sections->filter(fn ($s) => str_starts_with($s->section_key, 'process_image_'))
        ->sortBy('sort_order')
        ->filter(fn ($s) => filled($s->imageUrl()));
    $impactHeader = $sections->get('impact_header');
    $impactStats = $sections->filter(fn ($s) => str_starts_with($s->section_key, 'impact_') && $s->section_key !== 'impact_header')->sortBy('sort_order');
    $testimonialsHeader = $sections->get('testimonials_header');
    $testimonials = $sections->filter(fn ($s) => str_starts_with($s->section_key, 'testimonial_'))->sortBy('sort_order');
    $slideCount = $testimonials->isNotEmpty() ? $testimonials->count() : 3;
    $galleryHeader = $sections->get('gallery_header');
    $galleryImages = $sections->filter(fn ($s) => str_starts_with($s->section_key, 'gallery_') && $s->section_key !== 'gallery_header')
        ->sortBy('sort_order')
        ->filter(fn ($s) => filled($s->imageUrl()));
    $faqHeader = $sections->get('faq_header');
    $cta = $sections->get('cta');
    $stats = $sections->filter(fn ($s) => str_starts_with($s->section_key, 'stat_'))->sortBy('sort_order');
    $heroSlides = $sections->filter(fn ($s) => str_starts_with($s->section_key, 'hero_slide_'))
        ->sortBy('sort_order')
        ->filter(fn ($s) => filled($s->imageUrl()));
    if ($heroSlides->isEmpty() && $sections->get('hero_image')?->imageUrl()) {
        $heroSlides = collect([$sections->get('hero_image')]);
    }
@endphp

{{-- Hero --}}
<section class="hero-section" id="home">
    <div class="hero-bg-pattern"></div>
    <div class="container">
        <div class="hero-grid">
            <div class="hero-content margingtopmanage" data-reveal>
                <div class="trust-badge">
                    <span class="star">&#9733;</span>
                    {{ $heroBadge?->title ?? 'Trusted by 500+ Businesses' }}
                </div>
                <h1>
                    {{ $hero?->title ?? 'Complete Logistics Support for' }}
                    <span class="highlight">{{ $hero?->subtitle ?? 'Your Business' }}</span>
                </h1>
                @if(!empty($hero?->extra['tagline']))
                    <p class="hero-subtitle">{{ $hero->extra['tagline'] }}</p>
                @else
                    <p class="hero-subtitle">Freight, Accounting, HR & IT Support — All Under One Roof</p>
                @endif
                <p class="hero-desc">{{ $hero?->content ?? 'We empower businesses with expert logistics, accounting, IT support, and HR solutions — all from a single trusted partner.' }}</p>
                <div class="hero-actions">
                    <a href="{{ $hero?->link ?: route('website.services') }}" class="btn-secondary managebutton">Explore Our Services <i class="fa-solid fa-arrow-right"></i></a>
                    <a href="{{ route('website.contact') }}" class="btn-primary managebutton">Get in Touch</a>
                </div>

                <div class="managecontent">
                    @forelse($stats->take(3) as $stat)
                        <div class="box-forming">
                            <h5 class="managetextproper">{{ $stat->title }}</h5>
                            <p class="managetext">{{ $stat->subtitle }}</p>
                        </div>
                    @empty
                        <div class="box-forming">
                            <h5 class="managetextproper">1,000+</h5>
                            <p class="managetext">Professionals Supporting Operations</p>
                        </div>
                        <div class="box-forming">
                            <h5 class="managetextproper">24/7</h5>
                            <p class="managetext">Dedicated Business Support</p>
                        </div>
                        <div class="box-forming">
                            <h5 class="managetextproper">50+</h5>
                            <p class="managetext">Operational Specialists Across Departments</p>
                        </div>
                    @endforelse
                </div>
            </div>

            <div class="hero-visual" data-reveal="right">
                <div class="hero-image-wrap">
                    <div id="heroCarousel" class="custom-carousel">
                        <div class="carousel-inner">
                            @forelse($heroSlides as $slide)
                                <div class="carousel-item {{ $loop->first ? 'active' : '' }}">
                                    <img src="{{ $slide->imageUrl() }}" alt="{{ $slide->title ?? 'Hero image' }}">
                                </div>
                            @empty
                                <div class="carousel-item active">
                                    <img src="{{ asset('images/4slider.avif') }}" alt="Hero image">
                                </div>
                                <div class="carousel-item">
                                    <img src="{{ asset('images/5slider.avif') }}" alt="Hero image">
                                </div>
                                <div class="carousel-item">
                                    <img src="{{ asset('images/3slider.avif') }}" alt="Hero image">
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- Services --}}
@if($services->isNotEmpty())
<section class="section" id="services">
    <div class="container">
        <div class="section-header" data-reveal>
            <h2>{{ $servicesHeader?->title ?? 'Comprehensive Solutions Built for Growth' }}</h2>
            <div class="w-24 h-1 bg-gradient-to-r from-brand to-cyan-400 rounded-full mx-auto mb-6"></div>
            <p>{{ $servicesHeader?->content ?? 'Our integrated platform streamlines every aspect of your operations, from logistics to workforce management.' }}</p>
        </div>
        <div class="services-grid">
            @php
                $defaultImages = [
                    asset('images/6slider.avif'),
                    asset('images/9slider.avif'),
                    asset('images/7slider.avif'),
                    asset('images/8slider.avif'),
                ];
            @endphp
            @foreach($services as $service)
                <div class="service-card cursor-hover" data-reveal data-reveal-delay="{{ $loop->index * 0.1 }}">
                    <div class="service-card-image">
                        @if($service->imageUrl())
                            <img src="{{ $service->imageUrl() }}" alt="{{ $service->title }}">
                        @else
                            <img src="{{ $defaultImages[$loop->index % count($defaultImages)] }}" alt="{{ $service->title }}">
                        @endif
                    </div>
                    <div class="service-card-body">
                        <h3>{{ $service->title }}</h3>
                        <p>{{ $service->description }}</p>
                        <div class="service-card-actions">
                            <a href="{{ route('website.services.show', $service->slug) }}" class="learn-more">Learn More</a>
                            <a href="{{ route('website.services.show', $service->slug) }}" class="explore">Explore <i class="fa-solid fa-arrow-right"></i></a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        <div style="text-align:center;margin-top:40px;" data-reveal>
            <a href="{{ route('website.services') }}" class="btn-secondary">View All Services</a>
        </div>
    </div>
</section>
@endif

{{-- Why Partner --}}
<section class="section section-alt" id="why">
    <div class="container">
        <div class="section-header" data-reveal>
            <h2>{{ $whyHeader?->title ?? 'Why Partner With Us?' }}</h2>
            <div class="w-24 h-1 bg-gradient-to-r from-brand to-cyan-400 rounded-full mx-auto mb-6"></div>
            <p>{{ $whyHeader?->content ?? 'We deliver strategic advantages that directly impact your bottom line.' }}</p>
        </div>
        <div class="why-grid">
            @forelse($whyCards as $card)
                <div class="why-card" data-reveal>
                    <span class="why-icon">
                        @if($card->imageUrl())
                            <img src="{{ $card->imageUrl() }}" alt="" style="width:40px;height:40px;object-fit:contain;">
                        @elseif($card->icon && str_starts_with($card->icon, 'fa-'))
                            <i class="fa-solid {{ $card->icon }}"></i>
                        @else
                            {{ $card->icon ?: '⚡' }}
                        @endif
                    </span>
                    <h3>{{ $card->title }}</h3>
                    <p>{{ $card->content }}</p>
                </div>
            @empty
                @foreach([
                    ['⚡', 'Industry Expertise', 'Years of experience serving the logistics industry with deep operational knowledge and proven best practices.'],
                    ['🌎', 'Nationwide Coverage', 'Supporting businesses across all regions with scalable logistics solutions and a strong network.'],
                    ['✅', 'Proven Results', 'Trusted by hundreds of companies who have improved efficiency, reduced costs, and scaled operations.'],
                ] as $w)
                    <div class="why-card" data-reveal>
                        <span class="why-icon">{{ $w[0] }}</span>
                        <h3>{{ $w[1] }}</h3>
                        <p>{{ $w[2] }}</p>
                    </div>
                @endforeach
            @endforelse
        </div>
    </div>
</section>

{{-- Process --}}
<section class="section" id="process">
    <div class="container">
        <div class="section-header" data-reveal>
            <h2>{{ $processHeader?->title ?? 'Our Streamlined Process' }}</h2>
            <div class="w-24 h-1 bg-gradient-to-r from-brand to-cyan-400 rounded-full mx-auto mb-6"></div>
            <p>{{ $processHeader?->content ?? 'From initial consultation to ongoing support, we\'ve optimized every step for maximum efficiency.' }}</p>
        </div>

        <div class="hero-section section" style="background: var(--bg);">
            <div class="hero-bg-pattern"></div>
            <div class="container">
                <div class="hero-grid">
                    <div class="hero-visual" data-reveal="right">
                        <div class="hero-image-wrap" style="margin-top: 20px;">
                            <div id="featuresCarousel" class="custom-carousel">
                                <div class="carousel-inner">
                                    @forelse($processImages as $img)
                                        <div class="features-item {{ $loop->first ? 'active' : '' }}">
                                            <img src="{{ $img->imageUrl() }}" alt="{{ $img->title ?? 'Process' }}">
                                        </div>
                                    @empty
                                        <div class="features-item active">
                                            <img src="{{ asset('images/4slider.avif') }}" alt="Process">
                                        </div>
                                        <div class="features-item">
                                            <img src="{{ asset('images/5slider.avif') }}" alt="Process">
                                        </div>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="hero-content margingtopmanage" data-reveal>
                        <div class="features-list-wrapper">
                            @forelse($processSteps->take(4) as $step)
                                <div class="box-forming2 borde-coloe {{ $loop->first ? 'active' : '' }}">
                                    <div class="card-number">{{ $loop->iteration }}</div>
                                    <div class="card-body-content">
                                        <h5 class="managetextproper">{{ $step->title }}</h5>
                                        <p class="managetext">{{ $step->content }}</p>
                                    </div>
                                </div>
                            @empty
                                <div class="box-forming2 borde-coloe active">
                                    <div class="card-number">1</div>
                                    <div class="card-body-content">
                                        <h5 class="managetextproper">Freight Brokerage Solutions</h5>
                                        <p class="managetext">Connect with our extensive carrier network to find the perfect load matching solution.</p>
                                    </div>
                                </div>
                                <div class="box-forming2 borde-coloe">
                                    <div class="card-number">2</div>
                                    <div class="card-body-content">
                                        <h5 class="managetextproper">US Accounting Services for Trucking Companies</h5>
                                        <p class="managetext">Comprehensive bookkeeping, tax preparation, and financial reporting for trucking.</p>
                                    </div>
                                </div>
                                <div class="box-forming2 borde-coloe">
                                    <div class="card-number">3</div>
                                    <div class="card-body-content">
                                        <h5 class="managetextproper">IT & Administration Support</h5>
                                        <p class="managetext">Modern operational systems, CRM management, and technical support.</p>
                                    </div>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- Impact --}}
<section class="section section-dark" id="impact">
    <div class="container">
        <div class="section-header" data-reveal>
            <h2>{{ $impactHeader?->title ?? 'Proven Impact, Measurable Results' }}</h2>
            <div class="w-24 h-1 bg-gradient-to-r from-brand to-cyan-400 rounded-full mx-auto mb-6"></div>
            <p>{{ $impactHeader?->content ?? 'Join hundreds of companies that have transformed their operations with our solutions.' }}</p>
        </div>
        <div class="impact-grid">
            @forelse($impactStats as $impact)
                <div class="impact-card" data-reveal data-reveal-delay="{{ $loop->index * 0.08 }}">
                    <h3 @if(preg_match('/\d/', $impact->title)) data-count="{{ $impact->title }}" @endif>{{ $impact->title }}</h3>
                    <p>{{ $impact->subtitle ?? $impact->content }}</p>
                </div>
            @empty
                @foreach([['500+', 'Companies Served'], ['24/7', 'Business Support'], ['50+', 'Specialists'], ['99%', 'Client Satisfaction']] as $imp)
                    <div class="impact-card" data-reveal>
                        <h3>{{ $imp[0] }}</h3>
                        <p>{{ $imp[1] }}</p>
                    </div>
                @endforeach
            @endforelse
        </div>
    </div>
</section>

{{-- Testimonials --}}
<section class="section section-alt" id="testimonials">
    <div class="container">
        <div class="section-header" data-reveal>
            <h2>{{ $testimonialsHeader?->title ?? 'Voices from Our Team' }}</h2>
            <div class="w-24 h-1 bg-gradient-to-r from-brand to-cyan-400 rounded-full mx-auto mb-6"></div>
            <p>{{ $testimonialsHeader?->content ?? 'Hear what our team members say about working with us.' }}</p>
        </div>
        <div class="testimonial-carousel" data-reveal>
            @forelse($testimonials as $testimonial)
                <div class="testimonial-slide">
                    <p class="testimonial-quote">"{{ $testimonial->content }}"</p>
                    <div class="textalign">
                        <div class="w-24 h-1 bg-gradient-to-r from-brand to-cyan-400 rounded-full mb-6"></div>
                    </div>
                    <div class="testimonial-author">
                        @if($testimonial->imageUrl())
                            <img src="{{ $testimonial->imageUrl() }}" alt="{{ $testimonial->title }}" class="avatar">
                        @else
                            <div class="avatar">{{ strtoupper(substr($testimonial->title ?? 'V', 0, 1)) }}</div>
                        @endif
                        <div class="author-info">
                            <h4>{{ $testimonial->title }}</h4>
                            <span>{{ $testimonial->subtitle }}</span>
                        </div>
                    </div>
                </div>
            @empty
                @foreach([
                    ['"A great place to work, known for its positive work culture. They truly appreciate their employees."', 'Ayesha Amaan', 'Customer Success Team'],
                    ['"The platform makes daily operations so much easier. Everything is in one place."', 'Rohit Sharma', 'Operations Manager'],
                    ['"Excellent support team and reliable systems. Highly recommended!"', 'Vivek Singh', 'Delivery Partner'],
                ] as $t)
                    <div class="testimonial-slide">
                        <p class="testimonial-quote">{{ $t[0] }}</p>
                        <div class="testimonial-author">
                            <div class="avatar">{{ strtoupper(substr($t[1], 0, 1)) }}</div>
                            <div class="author-info">
                                <h4>{{ $t[1] }}</h4>
                                <span>{{ $t[2] }}</span>
                            </div>
                        </div>
                    </div>
                @endforeach
            @endforelse
            <div class="carousel-controls">
                <button class="carousel-prev" aria-label="Previous"><i class="fa-solid fa-chevron-left"></i></button>
                <div class="carousel-dots">
                    @for($i = 0; $i < $slideCount; $i++)
                        <button class="carousel-dot {{ $i === 0 ? 'active' : '' }}" aria-label="Slide {{ $i + 1 }}"></button>
                    @endfor
                </div>
                <span class="carousel-counter">1 of {{ $slideCount }}</span>
                <button class="carousel-next" aria-label="Next"><i class="fa-solid fa-chevron-right"></i></button>
            </div>
        </div>
    </div>
</section>

{{-- Gallery --}}
<section class="section gallery-section" id="gallery">
    <div class="container">
        <div class="section-header" data-reveal>
            <h2>{{ $galleryHeader?->title ?? 'Operations in Motion' }}</h2>
            <p>{{ $galleryHeader?->content ?? 'Real logistics operations and networks powering supply chains.' }}</p>
        </div>
    </div>
    @if($galleryImages->isNotEmpty())
        <div class="gallery-marquee" data-reveal>
            <div class="gallery-track">
                @foreach($galleryImages as $img)
                    <div class="gallery-item cursor-hover">
                        <img src="{{ $img->imageUrl() }}" alt="{{ $img->title ?? 'Operations' }}" loading="lazy">
                    </div>
                @endforeach
                {{-- Duplicate for seamless marquee --}}
                @foreach($galleryImages as $img)
                    <div class="gallery-item cursor-hover" aria-hidden="true">
                        <img src="{{ $img->imageUrl() }}" alt="" loading="lazy">
                    </div>
                @endforeach
            </div>
        </div>
    @else
        <div class="container">
            <div class="gallery-grid" data-reveal>
                @foreach([
                    asset('images/1slider.avif'),
                    asset('images/2slider.avif'),
                    asset('images/6slider.avif'),
                    asset('images/7slider.avif'),
                    asset('images/8slider.avif'),
                    asset('images/9slider.avif'),
                    asset('images/3slider.avif'),
                    asset('images/4slider.avif'),
                ] as $i => $src)
                    <div class="gallery-item">
                        <img src="{{ $src }}" alt="Logistics operations {{ $i + 1 }}">
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</section>

{{-- FAQ --}}
<section class="section section-alt" id="faq">
    <div class="container">
        <div class="section-header" data-reveal>
            <h2>{{ $faqHeader?->title ?? 'Frequently Asked Questions' }}</h2>
            <div class="w-24 h-1 bg-gradient-to-r from-brand to-cyan-400 rounded-full mx-auto mb-6"></div>
            <p>{{ $faqHeader?->content ?? 'Find answers to common questions about our services and solutions.' }}</p>
        </div>
        <div class="faq-list" data-reveal>
            @forelse($faqs as $faq)
                <div class="faq-item">
                    <button type="button" class="faq-question">
                        {{ $faq->title }}
                        <i class="fa-solid fa-chevron-down"></i>
                    </button>
                    <div class="faq-answer">
                        <div class="faq-answer-inner">{{ $faq->description }}</div>
                    </div>
                </div>
            @empty
                @foreach([
                    ['What services do you offer?', 'We provide comprehensive solutions including logistics, accounting services, IT support, and HR management tailored for businesses.'],
                    ['How can your services help my business?', 'Our extensive network and smart systems connect you with the best solutions. We handle operations so you can focus on growth.'],
                    ['Do you provide support across regions?', 'Yes! We serve businesses across multiple regions with nationwide support and dedicated teams.'],
                    ['Can you help with system setup?', 'Absolutely. We set up modern operational systems and provide ongoing technical support for your operations.'],
                    ['What areas do you serve?', 'We serve businesses across the entire region with comprehensive logistics and support services.'],
                ] as $f)
                    <div class="faq-item">
                        <button type="button" class="faq-question">{{ $f[0] }} <i class="fa-solid fa-chevron-down"></i></button>
                        <div class="faq-answer">
                            <div class="faq-answer-inner">{{ $f[1] }}</div>
                        </div>
                    </div>
                @endforeach
            @endforelse
        </div>
        <div style="text-align:center;margin-top:32px;" data-reveal>
            <a href="{{ route('website.faq') }}" class="btn-outline">View All FAQs</a>
        </div>
    </div>
</section>

{{-- CTA --}}
<div class="container">
    <div class="cta-banner" data-reveal>
        <h2>{{ $cta?->title ?? 'Ready to Transform Your Operations?' }}</h2>
        <p>{{ $cta?->content ?? 'Partner with us today and experience the difference.' }}</p>
        <a href="{{ route('website.contact') }}" class="btn-primary cursor-hover">Get in Touch <i class="fa-solid fa-arrow-right"></i></a>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    function initVanillaCarousel(carouselId, itemSelector, intervalTime) {
        var carousel = document.getElementById(carouselId);
        if (!carousel) return;
        var slides = carousel.querySelectorAll(itemSelector);
        if (slides.length <= 1) return;
        var currentSlide = 0;
        setInterval(function () {
            slides[currentSlide].classList.remove('active');
            currentSlide = (currentSlide + 1) % slides.length;
            slides[currentSlide].classList.add('active');
        }, intervalTime);
    }

    initVanillaCarousel('heroCarousel', '.carousel-item', 3000);
    initVanillaCarousel('featuresCarousel', '.features-item', 3000);
});
</script>
@endsection
