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
            @if($product->link)
                <a href="{{ $product->link }}" class="cta-btn" style="margin-top:24px;display:inline-flex;" target="_blank" rel="noopener">Visit Product</a>
            @endif
            <div style="margin-top:32px;">
                <a href="{{ route('website.products') }}" class="read-more">&larr; Back to Products</a>
            </div>
        </div>
    </div>
</section>
@endsection
