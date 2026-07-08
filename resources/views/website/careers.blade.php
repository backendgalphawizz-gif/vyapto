@extends('layouts.website')

@section('title', 'Careers & Highlights')

@section('content')
@include('website.partials.page-hero', ['hero' => $sections->get('hero'), 'fallbackTitle' => 'Careers & Highlights'])

<section class="content-section">
    <div class="container">

        @if($lifeAtVyapto->isNotEmpty())
        <div class="career-block">
            <h3>Life at Vyapto</h3>
            <div class="cards-grid">
                @foreach($lifeAtVyapto as $item)
                    <div class="card-item">
                        @if($item->imageUrl())
                            <img src="{{ $item->imageUrl() }}" alt="{{ $item->title }}" class="cover">
                        @endif
                        <div class="card-item-body">
                            <h3>{{ $item->title }}</h3>
                            <p>{{ $item->excerpt }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        @endif

        @if($deliveryPartners->isNotEmpty())
        <div class="career-block">
            <h3>Join as Delivery Partner</h3>
            <div class="cards-grid">
                @foreach($deliveryPartners as $item)
                    <div class="card-item">
                        @if($item->imageUrl())
                            <img src="{{ $item->imageUrl() }}" alt="{{ $item->title }}" class="cover">
                        @endif
                        <div class="card-item-body">
                            <h3>{{ $item->title }}</h3>
                            <p>{{ $item->excerpt }}</p>
                            @if($item->link)
                                <a href="{{ $item->link }}" class="btn-primary">Apply Now</a>
                            @else
                                <a href="{{ route('portal.register') }}" class="btn-primary">Register Now</a>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        @endif

        @if($openings->isNotEmpty())
        <div class="career-block">
            <h3>Current Openings</h3>
            <div class="cards-grid">
                @foreach($openings as $job)
                    <div class="card-item">
                        @if($job->imageUrl())
                            <img src="{{ $job->imageUrl() }}" alt="{{ $job->title }}" class="cover">
                        @endif
                        <div class="card-item-body">
                            <h3>{{ $job->title }}</h3>
                            @if($job->department || $job->location)
                                <p class="blog-meta">
                                    @if($job->department)<i class="fa-solid fa-building"></i> {{ $job->department }}@endif
                                    @if($job->location) &nbsp;|&nbsp; <i class="fa-solid fa-location-dot"></i> {{ $job->location }}@endif
                                </p>
                            @endif
                            <p>{{ $job->excerpt }}</p>
                            <a href="{{ route('website.contact') }}?subject=Job Application: {{ urlencode($job->title) }}" class="read-more">Apply &rarr;</a>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        @endif

        @if($news->isNotEmpty())
        <div class="career-block">
            <h3>News & Updates</h3>
            <div class="cards-grid">
                @foreach($news as $item)
                    <div class="card-item">
                        @if($item->imageUrl())
                            <img src="{{ $item->imageUrl() }}" alt="{{ $item->title }}" class="cover">
                        @endif
                        <div class="card-item-body">
                            @if($item->published_at)
                                <p class="blog-meta">{{ $item->published_at->format('M d, Y') }}</p>
                            @endif
                            <h3>{{ $item->title }}</h3>
                            <p>{{ $item->excerpt }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        @endif

    </div>
</section>
@endsection
