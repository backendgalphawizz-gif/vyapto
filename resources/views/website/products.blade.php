@extends('layouts.website')

@section('title', 'Our Products')

@section('content')
@include('website.partials.page-hero', ['hero' => $sections->get('hero'), 'fallbackTitle' => 'Our Products'])

<section class="content-section">
    <div class="container">
        <div class="cards-grid">
            @forelse($products as $product)
                <div class="card-item" data-reveal>
                    @if($product->imageUrl())
                        <img src="{{ $product->imageUrl() }}" alt="{{ $product->title }}" class="cover">
                    @else
                        <div class="icon"><i class="fa-solid fa-box"></i></div>
                    @endif
                    <div class="card-item-body">
                        <h3>{{ $product->title }}</h3>
                        <p>{{ $product->description }}</p>
                        <a href="{{ route('website.products.show', $product->slug) }}" class="read-more">Learn more &rarr;</a>
                    </div>
                </div>
            @empty
                <p class="empty-state">Products coming soon.</p>
            @endforelse
        </div>
    </div>
</section>
@endsection
