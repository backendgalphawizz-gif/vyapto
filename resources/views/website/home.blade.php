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
    $impactHeader = $sections->get('impact_header');
    $impactStats = $sections->filter(fn ($s) => str_starts_with($s->section_key, 'impact_') && $s->section_key !== 'impact_header')->sortBy('sort_order');
    $testimonialsHeader = $sections->get('testimonials_header');
    $testimonials = $sections->filter(fn ($s) => str_starts_with($s->section_key, 'testimonial_'))->sortBy('sort_order');
    $slideCount = $testimonials->isNotEmpty() ? $testimonials->count() : 3;
    $galleryHeader = $sections->get('gallery_header');
    $galleryImages = $sections->filter(fn ($s) => str_starts_with($s->section_key, 'gallery_') && $s->section_key !== 'gallery_header')->sortBy('sort_order');
    $faqHeader = $sections->get('faq_header');
    $cta = $sections->get('cta');
    $stats = $sections->filter(fn ($s) => str_starts_with($s->section_key, 'stat_'))->sortBy('sort_order');
    $heroImage = $sections->get('hero_image')?->imageUrl() ?? 'https://images.unsplash.com/photo-1601584115917-0f970f2f0e6b?w=800&h=900&fit=crop';
    $heroImageLabel = $sections->get('hero_image')?->title;
@endphp

{{-- Hero --}}
<section class="hero-section" id="home">
  <div class="hero-bg-pattern"></div>
  <div class="container">
    <div class="hero-grid">
      <div class="hero-content" data-reveal>
        <div class="trust-badge">
          <span class="star">&#9733;</span>
          {{ $heroBadge?->title ?? 'Trusted by 500+ Businesses' }}
        </div>
        <h1>
          {{ $hero?->title ?? 'Complete Logistics Support for' }}
          <span class="highlight">{{ $hero?->subtitle ?? 'Your Business' }}</span>
        </h1>
        <p class="hero-subtitle">{{ $hero?->extra['tagline'] ?? ($hero?->content ? '' : 'Freight, Accounting, HR & IT Support — All Under One Roof') }}</p>
        @if($hero?->content)
          <p class="hero-desc">{{ $hero->content }}</p>
        @else
          <p class="hero-desc">We empower businesses with expert logistics, accounting, IT support, and HR solutions — all from a single trusted partner.</p>
        @endif
        <div class="hero-actions">
          <a href="{{ route('website.services') }}" class="btn-secondary">Explore Our Services <i class="fa-solid fa-arrow-right"></i></a>
          <a href="{{ route('website.contact') }}" class="btn-primary">Get in Touch</a>
        </div>
      </div>
      <div class="hero-visual" data-reveal="right">
        <div class="hero-image-wrap">
          <img src="{{ $heroImage }}" alt="{{ $companyName }}">
          <div class="hero-image-badge">
            <i class="fa-solid fa-truck-fast"></i>
            <span>{{ $heroImageLabel ?: 'Freight & Logistics Solutions' }}</span>
          </div>
        </div>
      </div>
    </div>
  </div>

  @if($stats->isNotEmpty())
  <div class="stats-bar">
    <div class="container">
      @foreach($stats->take(3) as $stat)
        <div class="stat-item" data-reveal>
          <h3 @if(preg_match('/\d/', $stat->title)) data-count="{{ $stat->title }}" @endif>{{ $stat->title }}</h3>
          <p>{{ $stat->subtitle }}</p>
        </div>
      @endforeach
    </div>
  </div>
  @endif
</section>

{{-- Services --}}
@if($services->isNotEmpty())
<section class="section" id="services">
  <div class="container">
    <div class="section-header" data-reveal>
      <span class="section-badge"><i class="fa-solid fa-truck"></i> Our Services</span>
      <h2>{{ $servicesHeader?->title ?? 'Comprehensive Solutions Built for Growth' }}</h2>
      <p>{{ $servicesHeader?->content ?? 'Our integrated platform streamlines every aspect of your operations, from logistics to workforce management.' }}</p>
    </div>
    <div class="services-grid">
      @foreach($services as $service)
        <div class="service-card cursor-hover" data-reveal data-reveal-delay="{{ $loop->index * 0.1 }}">
          <div class="service-card-image">
            @if($service->imageUrl())
              <img src="{{ $service->imageUrl() }}" alt="{{ $service->title }}">
            @else
              <div class="icon-fallback"><i class="fa-solid {{ $service->icon ?? 'fa-truck' }}"></i></div>
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
      <span class="section-badge"><i class="fa-solid fa-handshake"></i> Why Us</span>
      <h2>{{ $whyHeader?->title ?? 'Why Partner With Us?' }}</h2>
      <p>{{ $whyHeader?->content ?? 'We deliver strategic advantages that directly impact your bottom line.' }}</p>
    </div>
    <div class="why-grid">
      @forelse($whyCards as $card)
        <div class="why-card" data-reveal>
          <span class="why-icon">{{ $card->icon ? '' : '⚡' }}@if($card->icon && str_starts_with($card->icon, 'fa-'))<i class="fa-solid {{ $card->icon }}" style="font-size:40px;color:var(--primary);"></i>@else{{ $card->icon }}@endif</span>
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
      <span class="section-badge"><i class="fa-solid fa-list-check"></i> Process</span>
      <h2>{{ $processHeader?->title ?? 'Our Streamlined Process' }}</h2>
      <p>{{ $processHeader?->content ?? 'From initial consultation to ongoing support, we\'ve optimized every step for maximum efficiency.' }}</p>
    </div>
    <div class="process-grid">
      @forelse($processSteps as $step)
        <div class="process-step" data-reveal data-reveal-delay="{{ $loop->index * 0.1 }}">
          <div class="step-number">{{ $loop->iteration }}</div>
          <h3>{{ $step->title }}</h3>
          <p>{{ $step->content }}</p>
        </div>
      @empty
        @foreach(['Consultation', 'Planning', 'Implementation', 'Ongoing Support'] as $title)
          <div class="process-step" data-reveal data-reveal-delay="{{ $loop->index * 0.1 }}">
            <div class="step-number">{{ $loop->iteration }}</div>
            <h3>{{ $title }}</h3>
            <p>We work closely with you at every stage to ensure seamless delivery.</p>
          </div>
        @endforeach
      @endforelse
    </div>
  </div>
</section>

{{-- Impact --}}
<section class="section section-dark" id="impact">
  <div class="container">
    <div class="section-header" data-reveal>
      <span class="section-badge" style="background:rgba(255,255,255,0.1);color:var(--accent);"><i class="fa-solid fa-chart-line"></i> Results</span>
      <h2>{{ $impactHeader?->title ?? 'Proven Impact, Measurable Results' }}</h2>
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
      <span class="section-badge"><i class="fa-solid fa-quote-left"></i> Testimonials</span>
      <h2>{{ $testimonialsHeader?->title ?? 'Voices from Our Team' }}</h2>
      <p>{{ $testimonialsHeader?->content ?? 'Hear what our team members say about working with us.' }}</p>
    </div>
    <div class="testimonial-carousel" data-reveal>
      @forelse($testimonials as $testimonial)
        <div class="testimonial-slide">
          <p class="testimonial-quote">"{{ $testimonial->content }}"</p>
          <div class="testimonial-author">
            @if($testimonial->imageUrl())
              <img src="{{ $testimonial->imageUrl() }}" alt="{{ $testimonial->title }}" class="avatar">
            @else
              <div class="avatar">{{ strtoupper(substr($testimonial->title, 0, 1)) }}</div>
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
      <span class="section-badge"><i class="fa-solid fa-images"></i> Gallery</span>
      <h2>{{ $galleryHeader?->title ?? 'Operations in Motion' }}</h2>
      <p>{{ $galleryHeader?->content ?? 'Real logistics operations and networks powering supply chains.' }}</p>
    </div>
  </div>
  @if($galleryImages->isNotEmpty())
    <div class="gallery-marquee" data-reveal>
      <div class="gallery-track">
        @foreach($galleryImages as $img)
          @if($img->imageUrl())
            <div class="gallery-item cursor-hover">
              <img src="{{ $img->imageUrl() }}" alt="{{ $img->title ?? 'Operations' }}" loading="lazy">
            </div>
          @endif
        @endforeach
      </div>
    </div>
  @else
    <div class="container">
      <div class="gallery-grid" data-reveal>
        @for($i = 1; $i <= 8; $i++)
          <div class="gallery-item">
            <img src="https://images.unsplash.com/photo-{{ ['1566576912321-d58ddd7a6088','1586528116311-ad8dd3c8310d','1494414623144-080708c2043b','1519003722464-d8e2f013f3cb','1601584115917-0f970f2f0e6b','1580674685258-234b35eb6d6d','1513828583688-c52646db42ef','1544626977-9e4c4d0d0c0c'][$i-1] ?? '1566576912321-d58ddd7a6088' }}?w=400&h=300&fit=crop" alt="Logistics operations {{ $i }}">
          </div>
        @endfor
      </div>
    </div>
  @endif
</section>

{{-- FAQ --}}
<section class="section section-alt" id="faq">
  <div class="container">
    <div class="section-header" data-reveal>
      <span class="section-badge"><i class="fa-solid fa-circle-question"></i> FAQ</span>
      <h2>{{ $faqHeader?->title ?? 'Frequently Asked Questions' }}</h2>
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
            <div class="faq-answer"><div class="faq-answer-inner">{{ $f[1] }}</div></div>
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
@endsection
