@extends('layouts.website')

@section('title', $product->title)

@section('content')
<section class="detail-page">
    <div class="container">
        <div class="detail-card">
            @if($product->imageUrl())
                <img src="{{ $product->imageUrl() }}" alt="{{ $product->title }}" class="detail-hero-image">
            @endif
            <h1>{{ $product->title }}</h1>
            @if($product->description)
                <p class="detail-lead">{{ $product->description }}</p>
            @endif
            @if($product->content)
                <div class="content">{!! $product->content !!}</div>
            @endif
            <div class="detail-actions">
                <a href="{{ route('website.products') }}" class="btn-outline">&larr; Back to Products</a>
                @if($product->link)
                    <a href="{{ $product->link }}" class="btn-primary" target="_blank" rel="noopener">Visit Product</a>
                @endif
            </div>
        </div>
    </div>
</section>
@endsection
