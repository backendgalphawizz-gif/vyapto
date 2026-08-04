@extends('layouts.website')

@section('title', $sections->get('hero')?->title ?? 'Home')

@section('content')
@php
    $hero = $sections->get('hero');
    $heroBadge = $sections->get('hero_badge');
    $heroTagline = $sections->get('hero_tagline');
    $heroBtnServices = $sections->get('hero_btn_services');
    $servicesHeader = $sections->get('services_header');
    $servicesViewAll = $sections->get('services_view_all');
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
    $slideCount = max(1, $testimonials->count());
    $galleryHeader = $sections->get('gallery_header');
    $galleryImages = $sections->filter(fn ($s) => str_starts_with($s->section_key, 'gallery_') && $s->section_key !== 'gallery_header')
        ->sortBy('sort_order')
        ->filter(fn ($s) => filled($s->imageUrl()));
    $faqHeader = $sections->get('faq_header');
    $faqViewAll = $sections->get('faq_view_all');
    $cta = $sections->get('cta');
    $stats = $sections->filter(fn ($s) => str_starts_with($s->section_key, 'stat_'))->sortBy('sort_order');
    $heroSlides = $sections->filter(fn ($s) => str_starts_with($s->section_key, 'hero_slide_'))
        ->sortBy('sort_order')
        ->filter(fn ($s) => filled($s->imageUrl()));
    if ($heroSlides->isEmpty() && $sections->get('hero_image')?->imageUrl()) {
        $heroSlides = collect([$sections->get('hero_image')]);
    }
    $L = $siteLabels ?? [];
    $learnMore = $sections->get('label_learn_more')?->title ?: 'Learn More';
    $exploreLabel = $sections->get('label_explore')?->title ?: 'Explore';
@endphp

{{-- Hero --}}
<section class="hero-section" id="home">
    <div class="hero-bg-pattern"></div>
    <div class="container">
        <div class="hero-grid">
            <div class="hero-content margingtopmanage" data-reveal>
                @if($heroBadge?->title)
                <div class="trust-badge">
                    <span class="star">&#9733;</span>
                    {{ $heroBadge->title }}
                </div>
                @endif

                @if($hero?->title || $hero?->subtitle)
                <h1>
                    @if($hero?->title){{ $hero->title }}@endif
                    @if($hero?->subtitle)
                    <span class="highlight">{{ $hero->subtitle }}</span>
                    @endif
                </h1>
                @endif

                @if($heroTagline?->title || $heroTagline?->content)
                    <p class="hero-subtitle">{{ $heroTagline->title ?: $heroTagline->content }}</p>
                @elseif(!empty($hero?->extra['tagline']))
                    <p class="hero-subtitle">{{ $hero->extra['tagline'] }}</p>
                @endif

                @if($hero?->content)
                <p class="hero-desc">{{ $hero->content }}</p>
                @endif

                <div class="hero-actions">
                    @if($heroBtnServices?->title || $hero?->link)
                    <a href="{{ $heroBtnServices?->link ?: ($hero?->link ?: route('website.services')) }}" class="btn-secondary managebutton">
                        {{ $heroBtnServices?->title ?: 'Explore Our Services' }}
                        <i class="fa-solid fa-arrow-right"></i>
                    </a>
                    @endif
                    <a href="{{ route('website.contact') }}" class="btn-primary managebutton">{{ $L['nav_cta'] ?? 'Get in Touch' }}</a>
                </div>

                @if($stats->isNotEmpty())
                <div class="managecontent">
                    @foreach($stats->take(3) as $stat)
                        <div class="box-forming">
                            <h5 class="managetextproper">{{ $stat->title }}</h5>
                            <p class="managetext">{{ $stat->subtitle }}</p>
                        </div>
                    @endforeach
                </div>
                @endif
            </div>

            @if($heroSlides->isNotEmpty())
            <div class="hero-visual" data-reveal="right">
                <div class="hero-image-wrap">
                    <div id="heroCarousel" class="custom-carousel">
                        <div class="carousel-inner">
                            @foreach($heroSlides as $slide)
                                <div class="carousel-item {{ $loop->first ? 'active' : '' }}">
                                    <img src="{{ $slide->imageUrl() }}" alt="{{ $slide->title ?: 'Hero' }}">
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</section>

{{-- Services --}}
@if($services->isNotEmpty())
<section class="section" id="services">
    <div class="container">
        @if($servicesHeader?->title || $servicesHeader?->content)
        <div class="section-header" data-reveal>
            @if($servicesHeader?->title)
            <h2>{{ $servicesHeader->title }}</h2>
            <div class="w-24 h-1 bg-gradient-to-r from-brand to-cyan-400 rounded-full mx-auto mb-6"></div>
            @endif
            @if($servicesHeader?->content)
            <p>{{ $servicesHeader->content }}</p>
            @endif
        </div>
        @endif
        <div class="services-grid">
            @foreach($services as $service)
                <div class="service-card cursor-hover" data-reveal data-reveal-delay="{{ $loop->index * 0.1 }}">
                    @if($service->imageUrl())
                    <div class="service-card-image">
                        <img src="{{ $service->imageUrl() }}" alt="{{ $service->title }}">
                    </div>
                    @endif
                    <div class="service-card-body">
                        <h3>{{ $service->title }}</h3>
                        <p>{{ $service->description }}</p>
                        <div class="service-card-actions">
                            <a href="{{ route('website.services.show', $service->slug) }}" class="learn-more">{{ $learnMore }}</a>
                            <a href="{{ route('website.services.show', $service->slug) }}" class="explore">{{ $exploreLabel }} <i class="fa-solid fa-arrow-right"></i></a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        <div style="text-align:center;margin-top:40px;" data-reveal>
            <a href="{{ $servicesViewAll?->link ?: route('website.services') }}" class="btn-secondary">{{ $servicesViewAll?->title ?: 'View All Services' }}</a>
        </div>
    </div>
</section>
@endif

{{-- Why Partner --}}
@if($whyHeader || $whyCards->isNotEmpty())
<section class="section section-alt" id="why">
    <div class="container">
        @if($whyHeader?->title || $whyHeader?->content)
        <div class="section-header" data-reveal>
            @if($whyHeader?->title)
            <h2>{{ $whyHeader->title }}</h2>
            <div class="w-24 h-1 bg-gradient-to-r from-brand to-cyan-400 rounded-full mx-auto mb-6"></div>
            @endif
            @if($whyHeader?->content)
            <p>{{ $whyHeader->content }}</p>
            @endif
        </div>
        @endif
        @if($whyCards->isNotEmpty())
        <div class="why-grid">
            @foreach($whyCards as $card)
                <div class="why-card" data-reveal>
                    <span class="why-icon">
                        @if($card->imageUrl())
                            <img src="{{ $card->imageUrl() }}" alt="" style="width:40px;height:40px;object-fit:contain;">
                        @elseif($card->icon && str_starts_with($card->icon, 'fa-'))
                            <i class="fa-solid {{ $card->icon }}"></i>
                        @elseif($card->icon)
                            {{ $card->icon }}
                        @endif
                    </span>
                    <h3>{{ $card->title }}</h3>
                    <p>{{ $card->content }}</p>
                </div>
            @endforeach
        </div>
        @endif
    </div>
</section>
@endif

{{-- Process --}}
@if($processHeader || $processSteps->isNotEmpty() || $processImages->isNotEmpty())
<section class="section" id="process">
    <div class="container">
        @if($processHeader?->title || $processHeader?->content)
        <div class="section-header" data-reveal>
            @if($processHeader?->title)
            <h2>{{ $processHeader->title }}</h2>
            <div class="w-24 h-1 bg-gradient-to-r from-brand to-cyan-400 rounded-full mx-auto mb-6"></div>
            @endif
            @if($processHeader?->content)
            <p>{{ $processHeader->content }}</p>
            @endif
        </div>
        @endif

        <div class="hero-section section" style="background: var(--bg);">
            <div class="hero-bg-pattern"></div>
            <div class="container">
                <div class="hero-grid">
                    @if($processImages->isNotEmpty())
                    <div class="hero-visual" data-reveal="right">
                        <div class="hero-image-wrap" style="margin-top: 20px;">
                            <div id="featuresCarousel" class="custom-carousel">
                                <div class="carousel-inner">
                                    @foreach($processImages as $img)
                                        <div class="features-item {{ $loop->first ? 'active' : '' }}">
                                            <img src="{{ $img->imageUrl() }}" alt="{{ $img->title ?: 'Process' }}">
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    @if($processSteps->isNotEmpty())
                    <div class="hero-content margingtopmanage" data-reveal>
                        <div class="features-list-wrapper">
                            @foreach($processSteps->take(4) as $step)
                                <div class="box-forming2 borde-coloe {{ $loop->first ? 'active' : '' }}">
                                    <div class="card-number">{{ $loop->iteration }}</div>
                                    <div class="card-body-content">
                                        <h5 class="managetextproper">{{ $step->title }}</h5>
                                        <p class="managetext">{{ $step->content }}</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</section>
@endif

{{-- Impact --}}
@if($impactHeader || $impactStats->isNotEmpty())
<section class="section section-dark" id="impact">
    <div class="container">
        @if($impactHeader?->title || $impactHeader?->content)
        <div class="section-header" data-reveal>
            @if($impactHeader?->title)
            <h2>{{ $impactHeader->title }}</h2>
            <div class="w-24 h-1 bg-gradient-to-r from-brand to-cyan-400 rounded-full mx-auto mb-6"></div>
            @endif
            @if($impactHeader?->content)
            <p>{{ $impactHeader->content }}</p>
            @endif
        </div>
        @endif
        @if($impactStats->isNotEmpty())
        <div class="impact-grid">
            @foreach($impactStats as $impact)
                <div class="impact-card" data-reveal data-reveal-delay="{{ $loop->index * 0.08 }}">
                    <h3 @if(preg_match('/\d/', (string) $impact->title)) data-count="{{ $impact->title }}" @endif>{{ $impact->title }}</h3>
                    <p>{{ $impact->subtitle ?? $impact->content }}</p>
                </div>
            @endforeach
        </div>
        @endif
    </div>
</section>
@endif

{{-- Testimonials --}}
@if($testimonialsHeader || $testimonials->isNotEmpty())
<section class="section section-alt" id="testimonials">
    <div class="container">
        @if($testimonialsHeader?->title || $testimonialsHeader?->content)
        <div class="section-header" data-reveal>
            @if($testimonialsHeader?->title)
            <h2>{{ $testimonialsHeader->title }}</h2>
            <div class="w-24 h-1 bg-gradient-to-r from-brand to-cyan-400 rounded-full mx-auto mb-6"></div>
            @endif
            @if($testimonialsHeader?->content)
            <p>{{ $testimonialsHeader->content }}</p>
            @endif
        </div>
        @endif
        @if($testimonials->isNotEmpty())
        <div class="testimonial-carousel" data-reveal>
            @foreach($testimonials as $testimonial)
                <div class="testimonial-slide">
                    <p class="testimonial-quote">"{{ $testimonial->content }}"</p>
                    <div class="textalign">
                        <div class="w-24 h-1 bg-gradient-to-r from-brand to-cyan-400 rounded-full mb-6"></div>
                    </div>
                    <div class="testimonial-author">
                        @if($testimonial->imageUrl())
                            <img src="{{ $testimonial->imageUrl() }}" alt="{{ $testimonial->title }}" class="avatar">
                        @else
                            <div class="avatar">{{ strtoupper(substr($testimonial->title ?: 'V', 0, 1)) }}</div>
                        @endif
                        <div class="author-info">
                            <h4>{{ $testimonial->title }}</h4>
                            <span>{{ $testimonial->subtitle }}</span>
                        </div>
                    </div>
                </div>
            @endforeach
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
        @endif
    </div>
</section>
@endif

{{-- Gallery --}}
@if($galleryHeader || $galleryImages->isNotEmpty())
<section class="section gallery-section" id="gallery">
    <div class="container">
        @if($galleryHeader?->title || $galleryHeader?->content)
        <div class="section-header" data-reveal>
            @if($galleryHeader?->title)
            <h2>{{ $galleryHeader->title }}</h2>
            @endif
            @if($galleryHeader?->content)
            <p>{{ $galleryHeader->content }}</p>
            @endif
        </div>
        @endif
    </div>
    @if($galleryImages->isNotEmpty())
        <div class="gallery-marquee" data-reveal>
            <div class="gallery-track">
                @foreach($galleryImages as $img)
                    <div class="gallery-item cursor-hover">
                        <img src="{{ $img->imageUrl() }}" alt="{{ $img->title ?: 'Gallery' }}" loading="lazy">
                    </div>
                @endforeach
                @foreach($galleryImages as $img)
                    <div class="gallery-item cursor-hover" aria-hidden="true">
                        <img src="{{ $img->imageUrl() }}" alt="" loading="lazy">
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</section>
@endif

{{-- FAQ --}}
@if($faqHeader || $faqs->isNotEmpty())
<section class="section section-alt" id="faq">
    <div class="container">
        @if($faqHeader?->title || $faqHeader?->content)
        <div class="section-header" data-reveal>
            @if($faqHeader?->title)
            <h2>{{ $faqHeader->title }}</h2>
            <div class="w-24 h-1 bg-gradient-to-r from-brand to-cyan-400 rounded-full mx-auto mb-6"></div>
            @endif
            @if($faqHeader?->content)
            <p>{{ $faqHeader->content }}</p>
            @endif
        </div>
        @endif
        @if($faqs->isNotEmpty())
        <div class="faq-list" data-reveal>
            @foreach($faqs as $faq)
                <div class="faq-item">
                    <button type="button" class="faq-question">
                        {{ $faq->title }}
                        <i class="fa-solid fa-chevron-down"></i>
                    </button>
                    <div class="faq-answer">
                        <div class="faq-answer-inner">{{ $faq->description }}</div>
                    </div>
                </div>
            @endforeach
        </div>
        <div style="text-align:center;margin-top:32px;" data-reveal>
            <a href="{{ $faqViewAll?->link ?: route('website.faq') }}" class="btn-outline">{{ $faqViewAll?->title ?: 'View All FAQs' }}</a>
        </div>
        @endif
    </div>
</section>
@endif

{{-- CTA --}}
@if($cta?->title || $cta?->content)
<div class="container">
    <div class="cta-banner" data-reveal>
        @if($cta?->title)
        <h2>{{ $cta->title }}</h2>
        @endif
        @if($cta?->content)
        <p>{{ $cta->content }}</p>
        @endif
        <a href="{{ $cta?->link ?: route('website.contact') }}" class="btn-primary cursor-hover">{{ $L['nav_cta'] ?? 'Get in Touch' }} <i class="fa-solid fa-arrow-right"></i></a>
    </div>
</div>
@endif

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
