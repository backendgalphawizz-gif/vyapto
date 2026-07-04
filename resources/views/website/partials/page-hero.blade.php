@php
    $heroImage = $hero?->imageUrl();
@endphp
<section class="page-hero {{ $heroImage ? 'page-hero--image' : '' }}">
    @if($heroImage)
        <img src="{{ $heroImage }}" alt="" class="page-hero-bg" aria-hidden="true">
    @endif
    <div class="container page-hero-content">
        @if($hero)
            <h1>{{ $hero->title ?? $fallbackTitle }}</h1>
            @if($hero->subtitle)<span class="page-hero-subtitle">{{ $hero->subtitle }}</span>@endif
            @if($hero->content)<p>{{ $hero->content }}</p>@endif
        @else
            <h1>{{ $fallbackTitle }}</h1>
        @endif
    </div>
</section>
