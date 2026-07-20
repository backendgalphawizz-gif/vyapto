@extends('layouts.website')

@section('title', $blog->title)

@section('content')
@php
    $img = $blog->imageUrl() ?: asset('images/6slider.avif');
    $recentDefaults = [
        asset('images/7slider.avif'),
        asset('images/8slider.avif'),
        asset('images/9slider.avif'),
    ];
@endphp

<section class="blog-detail-hero">
    <div class="container">
        <a href="{{ route('website.blogs') }}" class="blog-back" data-reveal>&larr; Back to Blogs</a>
        <div class="blog-meta-row" data-reveal data-reveal-delay="0.05">
            @if($blog->published_at)
                <span>{{ $blog->published_at->format('F d, Y') }}</span>
            @endif
            @if($blog->author)
                <span>By {{ $blog->author }}</span>
            @endif
        </div>
        <h1 class="blog-detail-title" data-reveal data-reveal-delay="0.1">{{ $blog->title }}</h1>
        @if($blog->excerpt)
            <p class="blog-detail-lead" data-reveal data-reveal-delay="0.15">{{ $blog->excerpt }}</p>
        @endif
    </div>
</section>

<section class="blog-detail-body">
    <div class="container blog-detail-wrap">
        <div class="blog-media blog-media--detail" data-blog-tilt data-reveal="scale">
            <div class="blog-media-ghost" style="background-image:url('{{ $img }}')"></div>
            <div class="blog-media-frame">
                <img src="{{ $img }}" alt="{{ $blog->title }}">
                <span class="blog-wipe" aria-hidden="true"></span>
            </div>
        </div>

        @if($blog->content)
            <article class="blog-article" data-reveal data-reveal-delay="0.12">
                {!! $blog->content !!}
            </article>
        @endif

        <div class="blog-detail-actions" data-reveal>
            <a href="{{ route('website.blogs') }}" class="svc-btn-outline">
                <i class="fa-solid fa-arrow-left"></i> Back to Blogs
            </a>
            <a href="{{ route('website.contact') }}" class="svc-btn-primary">Get in Touch</a>
        </div>
    </div>
</section>

@if($recent->isNotEmpty())
<section class="blog-recent">
    <div class="container">
        <div class="prod-section-head" data-reveal>
            <div class="prod-meta">
                <span class="prod-meta-num">More</span>
                <span class="prod-meta-line"></span>
                <span class="prod-meta-cat">Recent Posts</span>
            </div>
            <h2 class="prod-section-title">Keep reading</h2>
        </div>
        <div class="blog-grid blog-grid--3">
            @foreach($recent as $i => $post)
                @php
                    $pImg = $post->imageUrl() ?: $recentDefaults[$i % count($recentDefaults)];
                @endphp
                <article class="blog-card" data-reveal data-reveal-delay="{{ $i * 0.08 }}">
                    <a href="{{ route('website.blogs.show', $post->slug) }}" class="blog-card-link">
                        <div class="blog-media" data-blog-tilt>
                            <div class="blog-media-ghost" style="background-image:url('{{ $pImg }}')"></div>
                            <div class="blog-media-frame">
                                <img src="{{ $pImg }}" alt="{{ $post->title }}">
                                <span class="blog-wipe" aria-hidden="true"></span>
                            </div>
                        </div>
                        <div class="blog-card-body">
                            <h3>{{ $post->title }}</h3>
                            <p>{{ $post->excerpt }}</p>
                            <span class="blog-read">Read more <i class="fa-solid fa-arrow-right"></i></span>
                        </div>
                    </a>
                </article>
            @endforeach
        </div>
    </div>
</section>
@endif
@endsection

@push('scripts')
<script>
(function () {
    const prefersReduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    if (prefersReduced) return;

    document.querySelectorAll('[data-blog-tilt]').forEach(el => {
        el.addEventListener('mousemove', (e) => {
            const rect = el.getBoundingClientRect();
            const x = (e.clientX - rect.left) / rect.width - 0.5;
            const y = (e.clientY - rect.top) / rect.height - 0.5;
            el.style.setProperty('--tilt-x', (y * -8).toFixed(2) + 'deg');
            el.style.setProperty('--tilt-y', (x * 10).toFixed(2) + 'deg');
            el.style.setProperty('--ghost-x', (x * -18).toFixed(1) + 'px');
            el.style.setProperty('--ghost-y', (y * -14).toFixed(1) + 'px');
        });
        el.addEventListener('mouseleave', () => {
            el.style.setProperty('--tilt-x', '0deg');
            el.style.setProperty('--tilt-y', '0deg');
            el.style.setProperty('--ghost-x', '0px');
            el.style.setProperty('--ghost-y', '0px');
        });
    });
})();
</script>
@endpush
