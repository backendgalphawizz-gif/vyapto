@extends('layouts.website')

@section('title', $product->title)

@section('content')
@php
    $features = $product->featureList();
    $icon = $product->icon ?: 'fa-box';
@endphp

<section class="svc-detail">
    <div class="container svc-detail-wrap">
        <article class="svc-detail-card" data-reveal="scale">
            <div class="svc-detail-icon" data-reveal data-reveal-delay="0.05">
                <i class="fa-solid {{ $icon }}"></i>
            </div>

            @if($product->category)
                <p class="svc-detail-category" data-reveal data-reveal-delay="0.08">{{ $product->category }}</p>
            @endif

            <h1 class="svc-detail-title" data-reveal data-reveal-delay="0.1">{{ $product->title }}</h1>

            @if($product->subtitle)
                <p class="svc-detail-tagline" data-reveal data-reveal-delay="0.12">{{ $product->subtitle }}</p>
            @endif

            @if($product->description)
                <p class="svc-detail-desc" data-reveal data-reveal-delay="0.14">{{ $product->description }}</p>
            @endif

            @if($product->imageUrl())
                <div class="svc-detail-media" data-reveal data-reveal-delay="0.16">
                    <img src="{{ $product->imageUrl() }}" alt="{{ $product->title }}">
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

            @if($product->content)
                <div class="svc-detail-body" data-reveal data-reveal-delay="0.22">
                    {!! $product->content !!}
                </div>
            @endif

            <div class="svc-detail-actions" data-reveal data-reveal-delay="0.24">
                <a href="{{ route('website.products') }}" class="svc-btn-outline">
                    <i class="fa-solid fa-arrow-left"></i> Back to Products
                </a>
                @if($product->link)
                    <a href="{{ $product->link }}" class="svc-btn-primary" target="_blank" rel="noopener">Visit Product</a>
                @else
                    <a href="{{ route('website.contact') }}" class="svc-btn-primary">Get in Touch</a>
                @endif
            </div>
        </article>
    </div>
</section>
@endsection
