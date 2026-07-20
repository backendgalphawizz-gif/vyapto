@extends('layouts.website')

@section('title', $service->title)

@section('content')
@php
    $features = $service->featureList();
    $icon = $service->icon ?: 'fa-truck';
@endphp

<section class="svc-detail">
    <div class="container svc-detail-wrap">
        <article class="svc-detail-card" data-reveal="scale">
            <div class="svc-detail-icon" data-reveal data-reveal-delay="0.05">
                <i class="fa-solid {{ $icon }}"></i>
            </div>

            @if($service->category)
                <p class="svc-detail-category" data-reveal data-reveal-delay="0.08">{{ $service->category }}</p>
            @endif

            <h1 class="svc-detail-title" data-reveal data-reveal-delay="0.1">{{ $service->title }}</h1>

            @if($service->subtitle)
                <p class="svc-detail-tagline" data-reveal data-reveal-delay="0.12">{{ $service->subtitle }}</p>
            @endif

            @if($service->description)
                <p class="svc-detail-desc" data-reveal data-reveal-delay="0.14">{{ $service->description }}</p>
            @endif

            @if($service->imageUrl())
                <div class="svc-detail-media" data-reveal data-reveal-delay="0.16">
                    <img src="{{ $service->imageUrl() }}" alt="{{ $service->title }}">
                </div>
            @endif

            @if($features)
                <div class="svc-detail-includes" data-reveal data-reveal-delay="0.18">
                    <h2 class="svc-includes-title">What's Included:</h2>
                    <ul class="svc-includes-grid">
                        @foreach($features as $feature)
                            <li data-reveal data-reveal-delay="{{ 0.2 + ($loop->index * 0.05) }}">
                                <span class="svc-includes-arrow" aria-hidden="true">→</span>
                                <span>{{ $feature }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if($service->content)
                <div class="svc-detail-body" data-reveal data-reveal-delay="0.22">
                    {!! $service->content !!}
                </div>
            @endif

            <div class="svc-detail-actions" data-reveal data-reveal-delay="0.24">
                <a href="{{ route('website.services') }}" class="svc-btn-outline">
                    <i class="fa-solid fa-arrow-left"></i> Back to Services
                </a>
                <a href="{{ route('website.contact') }}" class="svc-btn-primary">Get in Touch</a>
            </div>
        </article>
    </div>
</section>
@endsection
