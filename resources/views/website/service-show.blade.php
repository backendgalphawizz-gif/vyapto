@extends('layouts.website')

@section('title', $service->title)

@section('content')
<section class="detail-page">
    <div class="container">
        <div class="detail-card">
            @if($service->imageUrl())
                <img src="{{ $service->imageUrl() }}" alt="{{ $service->title }}" class="detail-hero-image">
            @else
                <div class="icon" style="margin-bottom:20px;"><i class="fa-solid {{ $service->icon ?? 'fa-truck' }}"></i></div>
            @endif
            <h1>{{ $service->title }}</h1>
            @if($service->description)
                <p class="detail-lead">{{ $service->description }}</p>
            @endif
            @if($service->content)
                <div class="content">{!! $service->content !!}</div>
            @endif
            <div class="detail-actions">
                <a href="{{ route('website.services') }}" class="btn-outline">&larr; Back to Services</a>
                <a href="{{ route('website.contact') }}" class="btn-primary">Get in Touch</a>
            </div>
        </div>
    </div>
</section>
@endsection
