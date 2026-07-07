@extends('layouts.website')

@section('title', 'Our Services')

@section('content')
@include('website.partials.page-hero', ['hero' => $sections->get('hero'), 'fallbackTitle' => 'Our Services'])

<section class="content-section">
    <div class="container">
        <div class="services-grid">
            @forelse($services as $service)
                <div class="service-card" data-reveal>
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
            @empty
                <p class="empty-state">Services coming soon.</p>
            @endforelse
        </div>
    </div>
</section>
@endsection
