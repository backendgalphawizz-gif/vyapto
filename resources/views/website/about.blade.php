@extends('layouts.website')

@section('title', 'About Us')

@section('content')
@php
    $hero = $sections->get('hero');
    $overview = $sections->get('overview');
    $whoWeAre = $sections->get('who_we_are');
    $timeline = $sections->filter(fn ($s) => str_starts_with($s->section_key, 'milestone_'))->sortBy('sort_order');
    $locations = $sections->filter(fn ($s) => str_starts_with($s->section_key, 'location_'))->sortBy('sort_order');
    $whyHeader = $sections->get('why_choose');
    $whyCards = $sections->filter(fn ($s) => str_starts_with($s->section_key, 'why_choose_'))->sortBy('sort_order');
    $cta = $sections->get('cta');
@endphp

@include('website.partials.page-hero', ['hero' => $hero, 'fallbackTitle' => 'About ' . ($companyName ?? 'Us')])

<section class="content-section">
    <div class="container">
        <div class="about-intro" data-reveal>
            <div>
                <h2>{{ $overview?->title ?? 'Powering Smarter Logistics & Driving Real Growth' }}</h2>
                <p>{{ $overview?->content ?? 'We go beyond basics by helping our clients manage operations, optimizing processes, and staying ahead in a highly competitive industry. We support businesses by simplifying the operational side of logistics.' }}</p>
                @if($whoWeAre)
                    <p>{{ $whoWeAre->content }}</p>
                @endif
            </div>
            <div>
                @if($overview?->imageUrl())
                    <img src="{{ $overview->imageUrl() }}" alt="About us" style="border-radius:var(--radius-lg);box-shadow:var(--shadow-lg);">
                @else
                    <img src="https://images.unsplash.com/photo-1586528116311-ad8dd3c8310d?w=600&h=400&fit=crop" alt="Logistics operations" style="border-radius:var(--radius-lg);box-shadow:var(--shadow-lg);">
                @endif
            </div>
        </div>

        @if($timeline->isNotEmpty())
        <div class="career-block" data-reveal>
            <h3>Our Journey — Growth & Milestones</h3>
            <div class="timeline">
                @foreach($timeline as $item)
                    <div class="timeline-item">
                        <div class="year">{{ $item->subtitle ?? $item->title }}</div>
                        <h3>{{ $item->title }}</h3>
                        <p>{{ $item->content }}</p>
                    </div>
                @endforeach
            </div>
        </div>
        @endif

        @if($locations->isNotEmpty())
        <div class="career-block" data-reveal>
            <h3>Our Locations</h3>
            <div class="locations-grid">
                @foreach($locations as $loc)
                    <div class="location-card">
                        <h4>{{ $loc->title }}</h4>
                        <span>{{ $loc->subtitle ?? $loc->content }}</span>
                    </div>
                @endforeach
            </div>
        </div>
        @endif

        <div class="career-block" data-reveal>
            <div class="section-header" style="margin-bottom:40px;">
                <h2>{{ $whyHeader?->title ?? 'Why Choose Us?' }}</h2>
                <p>{{ $whyHeader?->content ?? 'There are several reasons why you should choose us.' }}</p>
            </div>
            <div class="why-grid">
                @forelse($whyCards as $card)
                    <div class="why-card">
                        <span class="why-icon">@if($card->icon)<i class="fa-solid {{ $card->icon }}" style="font-size:32px;color:var(--primary);"></i>@endif</span>
                        <h3>{{ $card->title }}</h3>
                        <p>{{ $card->content }}</p>
                    </div>
                @empty
                    @foreach([
                        ['fa-clock', 'Operational Experience', 'Years of hands-on experience serving the logistics and transportation sector.'],
                        ['fa-chart-line', 'Scalable Structure', 'A fully-trained support model designed to evolve as your requirements grow.'],
                        ['fa-users', 'Customer-Centric Approach', 'Dedicated success teams focused on providing customized service and support.'],
                    ] as $w)
                        <div class="why-card">
                            <span class="why-icon"><i class="fa-solid {{ $w[0] }}" style="font-size:32px;color:var(--primary);"></i></span>
                            <h3>{{ $w[1] }}</h3>
                            <p>{{ $w[2] }}</p>
                        </div>
                    @endforeach
                @endforelse
            </div>
        </div>
    </div>
</section>

<div class="container">
    <div class="cta-banner" data-reveal>
        <h2>{{ $cta?->title ?? "Let's Simplify Logistics Together!" }}</h2>
        <p>{{ $cta?->content ?? 'Partner with us today and develop smarter logistics operations.' }}</p>
        <a href="{{ route('website.contact') }}" class="btn-primary">Partner With Us <i class="fa-solid fa-arrow-right"></i></a>
    </div>
</div>
@endsection
