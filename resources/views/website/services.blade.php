@extends('layouts.website')

@section('title', 'Our Services')

@section('content')
@php
    $hero = $sections->get('hero');
    $heroImage = $hero?->imageUrl() ?: asset('images/vyapto-warehouse-bg.png');
    $defaultImages = [
        asset('images/6slider.avif'),
        asset('images/7slider.avif'),
        asset('images/8slider.avif'),
        asset('images/9slider.avif'),
    ];
@endphp

<section class="svc-hero">
    <img src="{{ $heroImage }}" alt="" class="svc-hero-bg" aria-hidden="true">
    <div class="svc-hero-overlay"></div>
    <div class="container svc-hero-inner" data-reveal>
        <h1 class="svc-hero-title">
            <span class="svc-hero-accent">{{ $hero?->title ?? 'Solving real challenges.' }}</span>
            @if($hero?->subtitle)
                <span class="svc-hero-rest">{{ $hero->subtitle }}</span>
            @else
                <span class="svc-hero-rest">Delivering real results.</span>
            @endif
        </h1>
        <p class="svc-hero-copy">
            {{ $hero?->content ?? 'We understand the challenges businesses face every day. Vyapto is built to solve them with efficient solutions, transparency, and an unwavering commitment to quality service.' }}
        </p>
    </div>
</section>

<section class="svc-showcase">
    <div class="container">
        @forelse($services as $service)
            @php
                $index = $loop->iteration;
                $label = sprintf('Service %02d — %s', $index, $service->category ?: 'Service');
                $image = $service->imageUrl() ?: $defaultImages[($index - 1) % count($defaultImages)];
                $features = $service->featureList();
                $reversed = $loop->even;
            @endphp
            <article class="svc-row {{ $reversed ? 'svc-row--reverse' : '' }}">
                <div class="svc-row-copy" data-reveal="{{ $reversed ? 'right' : 'left' }}">
                    <p class="svc-row-label">{{ $label }}</p>
                    <h2 class="svc-row-title">{{ $service->title }}</h2>
                    @if($service->subtitle)
                        <p class="svc-row-tagline">{{ $service->subtitle }}</p>
                    @endif
                    @if($service->description)
                        <p class="svc-row-desc">{{ $service->description }}</p>
                    @endif

                    @if($features)
                        <div class="svc-includes" data-reveal data-reveal-delay="0.15">
                            <h3 class="svc-includes-title">What's Included:</h3>
                            <ul class="svc-includes-grid">
                                @foreach($features as $feature)
                                    <li data-reveal data-reveal-delay="{{ 0.18 + ($loop->index * 0.06) }}">
                                        <span class="svc-includes-arrow" aria-hidden="true">→</span>
                                        <span>{{ $feature }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <a href="{{ route('website.services.show', $service->slug) }}" class="svc-row-link">
                        Learn more <i class="fa-solid fa-arrow-right"></i>
                    </a>
                </div>

                <div class="svc-row-media" data-reveal="{{ $reversed ? 'left' : 'right' }}" data-reveal-delay="0.12">
                    <div class="svc-row-frame">
                        <img src="{{ $image }}" alt="{{ $service->title }}">
                    </div>
                </div>
            </article>
        @empty
            <p class="empty-state">Services coming soon.</p>
        @endforelse
    </div>
</section>
@endsection
