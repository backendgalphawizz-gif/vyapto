@extends('layouts.website')

@section('title', 'Our Services')

@section('content')
@include('website.partials.page-hero', ['hero' => $sections->get('hero'), 'fallbackTitle' => 'Our Services'])

<section class="content-section">
    <div class="container">
        <div class="cards-grid">
            @forelse($services as $service)
                <div class="card-item">
                    @if($service->imageUrl())
                        <img src="{{ $service->imageUrl() }}" alt="{{ $service->title }}" class="cover">
                    @else
                        <div class="icon"><i class="fa-solid {{ $service->icon ?? 'fa-truck' }}"></i></div>
                    @endif
                    <h3>{{ $service->title }}</h3>
                    <p>{{ $service->description }}</p>
                    <a href="{{ route('website.services.show', $service->slug) }}" class="read-more">Learn more &rarr;</a>
                </div>
            @empty
                <p class="empty-state">Services coming soon.</p>
            @endforelse
        </div>
    </div>
</section>
@endsection
