@extends('layouts.website')

@section('title', 'Our Products')

@section('content')
@php
    $hero = $sections->get('hero');
    $heroImage = $hero?->imageUrl()
        ?: 'https://images.unsplash.com/photo-1553877522-43269d4ea984?auto=format&fit=crop&w=1600&q=85';
    $foods = $products->firstWhere('slug', 'vyapto-foods') ?? $products->first();
    $vms = $products->firstWhere('slug', 'vyapto-vms') ?? $products->skip(1)->first();
@endphp

{{-- HERO --}}
<section class="prod-hero">
    <img src="{{ $heroImage }}" alt="" class="prod-hero-bg" aria-hidden="true">
    <div class="prod-hero-overlay"></div>
    <div class="container prod-hero-inner" data-reveal>
        <h1 class="prod-hero-title">{{ $hero?->title ?? 'Our Products' }}</h1>
        <p class="prod-hero-lead">
            {{ $hero?->content ?? "Built on the trust we've earned through our services — now bringing quality directly to you." }}
        </p>
    </div>
</section>

@if($foods)
@php
    $foodImage = $foods->imageUrl()
        ?: 'https://images.unsplash.com/photo-1599490659213-e2b9527bd087?auto=format&fit=crop&w=900&q=80';
    $foodFeatures = $foods->featureList();
    $gallery = $foods->extraValue('gallery', []);
    $badges = $foods->extraValue('badges', []);
    $visionPillars = $foods->extraValue('vision_pillars', []);
    $galleryHeader = $foods->extraValue('gallery_header', []);
    $visionHeader = $foods->extraValue('vision_header', []);
    $expectation = $foods->extraValue('expectation');
@endphp
<section class="prod-block" id="foods">
    <div class="container">
        <div class="prod-showcase">
            <div class="prod-showcase-media" data-reveal="left">
                <div class="prod-frame">
                    <img src="{{ $foodImage }}" alt="{{ $foods->title }}">
                </div>
            </div>
            <div class="prod-showcase-copy" data-reveal="right">
                <div class="prod-meta">
                    <span class="prod-meta-num">Product 01</span>
                    <span class="prod-meta-line"></span>
                    <span class="prod-meta-cat">{{ $foods->category ?? 'Vyapto Foods' }}</span>
                </div>
                <h2 class="prod-title">{{ $foods->title }}</h2>
                @if($foods->subtitle)
                    <p class="prod-tagline">{{ $foods->subtitle }}</p>
                @endif
                @if($foods->description)
                    <p class="prod-desc">{{ $foods->description }}</p>
                @endif

                @if($foodFeatures)
                    <h3 class="svc-includes-title">Product highlights:</h3>
                    <ul class="prod-highlights">
                        @foreach($foodFeatures as $feature)
                            <li><span class="svc-includes-arrow">→</span> {{ $feature }}</li>
                        @endforeach
                    </ul>
                @endif

                @if($foods->link)
                    <a href="{{ $foods->link }}" target="_blank" rel="noopener" class="svc-btn-primary prod-cta">
                        Visit Vyapto Foods Website <i class="fa-solid fa-arrow-right"></i>
                    </a>
                @endif
            </div>
        </div>

        @if($gallery)
        <div class="prod-subblock">
            <div class="prod-section-head center" data-reveal>
                <div class="prod-meta">
                    <span class="prod-meta-num">{{ $galleryHeader['num'] ?? 'Makhana' }}</span>
                    <span class="prod-meta-line"></span>
                    <span class="prod-meta-cat">{{ $galleryHeader['category'] ?? 'Our First Range' }}</span>
                </div>
                <h2 class="prod-section-title">{{ $galleryHeader['title'] ?? 'Six flavors, one honest snack.' }}</h2>
                <p class="prod-section-sub">{{ $galleryHeader['subtitle'] ?? 'Roasted · Light · Nutritious — Made with love in Bihar.' }}</p>
            </div>

            <div class="flavors-grid">
                @foreach($gallery as $item)
                    @php
                        $gImg = \App\Support\WebsiteMedia::url($item['image'] ?? null)
                            ?: ($loop->first
                                ? 'https://images.unsplash.com/photo-1621939514649-16e1d15c1fbb?auto=format&fit=crop&w=900&q=80'
                                : 'https://images.unsplash.com/photo-1606313564200-e75d5e30476c?auto=format&fit=crop&w=900&q=80');
                    @endphp
                    <article class="flavor-card" data-reveal="{{ $loop->odd ? 'left' : 'right' }}">
                        <div class="flavor-img-wrap">
                            <img src="{{ $gImg }}" alt="{{ $item['title'] ?? 'Flavor range' }}">
                        </div>
                        <div class="flavor-info">
                            @if(!empty($item['meta']))
                                <div class="flavor-meta">{{ $item['meta'] }}</div>
                            @endif
                            <h3 class="flavor-title">{{ $item['title'] ?? '' }}</h3>
                            @if(!empty($item['desc']))
                                <p class="flavor-desc">{{ $item['desc'] }}</p>
                            @endif
                        </div>
                    </article>
                @endforeach
            </div>

            @if($badges)
            <div class="badge-bar" data-reveal>
                @foreach($badges as $badge)
                    <div class="badge-item">
                        <i class="fa-solid {{ $badge['icon'] ?? 'fa-check' }}"></i>
                        {{ $badge['label'] ?? '' }}
                    </div>
                @endforeach
            </div>
            @endif
        </div>
        @endif

        @if($visionPillars)
        <div class="prod-subblock">
            <div class="prod-section-head" data-reveal>
                <div class="prod-meta">
                    <span class="prod-meta-num">{{ $visionHeader['num'] ?? 'Vision' }}</span>
                    <span class="prod-meta-line"></span>
                    <span class="prod-meta-cat">{{ $visionHeader['category'] ?? 'Purity & Impact' }}</span>
                </div>
                <h2 class="prod-section-title">{{ $visionHeader['title'] ?? 'Our Wider Vision' }}</h2>
                <p class="prod-section-sub left">{{ $visionHeader['subtitle'] ?? 'To create a brand that stands for purity, trust, and wellness — while empowering local communities and contributing to a healthier tomorrow.' }}</p>
            </div>

            <div class="highlights-grid highlights-grid--5">
                @foreach($visionPillars as $pillar)
                    <div class="highlight-card" data-reveal data-reveal-delay="{{ $loop->index * 0.06 }}">
                        <div class="icon-tile {{ $loop->even ? 'blue' : '' }}">
                            <i class="fa-solid {{ $pillar['icon'] ?? 'fa-star' }}"></i>
                        </div>
                        <h4>{{ $pillar['title'] ?? '' }}</h4>
                        <p>{{ $pillar['desc'] ?? '' }}</p>
                    </div>
                @endforeach
            </div>
        </div>
        @endif

        @if($expectation)
            <div class="expect-card" data-reveal="scale">
                <h3>What you can expect:</h3>
                <p>{{ $expectation }}</p>
            </div>
        @endif
    </div>
</section>
@endif

@if($vms)
@php
    $vmsImage = $vms->imageUrl() ?: asset('images/app-screen-1.png');
    $chips = $vms->chipList();
    $capabilities = $vms->extraValue('capabilities', []);
    $capHeader = $vms->extraValue('capabilities_header', []);
@endphp
<section class="prod-block prod-block--alt" id="vms">
    <div class="container">
        <div class="prod-showcase prod-showcase--reverse">
            <div class="prod-showcase-copy" data-reveal="left">
                <div class="prod-meta">
                    <span class="prod-meta-num">Product 02</span>
                    <span class="prod-meta-line"></span>
                    <span class="prod-meta-cat">{{ $vms->category ?? 'Vyapto VMS' }}</span>
                </div>
                <h2 class="prod-title">{{ $vms->title }}</h2>
                @if($vms->subtitle)
                    <p class="prod-tagline prod-tagline--blue">{{ $vms->subtitle }}</p>
                @endif
                @if($vms->description)
                    <p class="prod-desc">{{ $vms->description }}</p>
                @endif

                @if($chips)
                    <h3 class="svc-includes-title">Built for:</h3>
                    <div class="vms-chips">
                        @foreach($chips as $chip)
                            <span class="vms-chip">{{ $chip }}</span>
                        @endforeach
                    </div>
                @endif

                <div class="vms-vision">
                    <h4>{{ $vms->extraValue('vision_label', 'Our Vision:') }}</h4>
                    <p class="vms-vision-title">{{ $vms->extraValue('vision_title', 'One Platform. Complete Control.') }}</p>
                    <p class="vms-vision-text">{{ $vms->extraValue('vision_text', 'Building a smarter ecosystem where businesses, vendors, and operations work together seamlessly through technology.') }}</p>
                </div>

                @if($vms->link)
                    <a href="{{ $vms->link }}" target="_blank" rel="noopener" class="svc-btn-primary prod-cta">
                        Explore VMS <i class="fa-solid fa-arrow-right"></i>
                    </a>
                @else
                    <a href="{{ route('website.products.show', $vms->slug) }}" class="svc-btn-primary prod-cta">
                        Learn more <i class="fa-solid fa-arrow-right"></i>
                    </a>
                @endif
            </div>

            <div class="prod-showcase-media" data-reveal="right">
                <div class="prod-frame">
                    <img src="{{ $vmsImage }}" alt="{{ $vms->title }}">
                </div>
            </div>
        </div>

        @if($capabilities)
        <div class="prod-subblock">
            <div class="prod-section-head center" data-reveal>
                <div class="prod-meta">
                    <span class="prod-meta-num">{{ $capHeader['num'] ?? 'Capabilities' }}</span>
                    <span class="prod-meta-line"></span>
                    <span class="prod-meta-cat">{{ $capHeader['category'] ?? 'System Architecture' }}</span>
                </div>
                <h2 class="prod-section-title">{{ $capHeader['title'] ?? 'Core Benefits & Key Capabilities' }}</h2>
            </div>

            <div class="highlights-grid highlights-grid--3">
                @foreach($capabilities as $cap)
                    <div class="highlight-card" data-reveal data-reveal-delay="{{ $loop->index * 0.06 }}">
                        <div class="icon-tile {{ $loop->even ? 'blue' : '' }}">
                            <i class="fa-solid {{ $cap['icon'] ?? 'fa-cube' }}"></i>
                        </div>
                        <h4>{{ $cap['title'] ?? '' }}</h4>
                        <p>{{ $cap['desc'] ?? '' }}</p>
                    </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>
</section>
@endif
@endsection
