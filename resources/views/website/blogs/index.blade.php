@extends('layouts.website')

@section('title', 'Blogs')

@section('content')
@php
    $hero = $sections->get('hero');
    $heroImage = $hero?->imageUrl() ?: asset('images/4slider.avif');
    $defaultImages = [
        asset('images/6slider.avif'),
        asset('images/7slider.avif'),
        asset('images/8slider.avif'),
        asset('images/9slider.avif'),
        asset('images/3slider.avif'),
        asset('images/1slider.avif'),
    ];
    $posts = $blogs->getCollection();
    $featured = $posts->first();
    $rest = $posts->slice(1);
@endphp

<section class="blog-hero">
    <img src="{{ $heroImage }}" alt="" class="blog-hero-bg" aria-hidden="true">
    <div class="blog-hero-overlay"></div>
    <div class="container blog-hero-inner" data-reveal>
        <div class="prod-meta">
            <span class="prod-meta-num" style="color:#ffb07a;">Insights</span>
            <span class="prod-meta-line" style="background:#ffb07a;"></span>
            <span class="prod-meta-cat" style="color:rgba(255,255,255,0.7);">From the Route</span>
        </div>
        <h1 class="blog-hero-title">{{ $hero?->title ?? 'Stories that move with us' }}</h1>
        <p class="blog-hero-lead">
            {{ $hero?->content ?? 'Insights, updates, and stories from the Vyapto team — logistics, workforce, and the road ahead.' }}
        </p>
    </div>
</section>

<section class="blog-listing">
    <div class="container">
        @if($featured)
            @php
                $fImg = $featured->imageUrl() ?: $defaultImages[0];
            @endphp
            <a href="{{ route('website.blogs.show', $featured->slug) }}" class="blog-featured" data-reveal>
                <div class="blog-media blog-media--featured" data-blog-tilt>
                    <div class="blog-media-ghost" style="background-image:url('{{ $fImg }}')"></div>
                    <div class="blog-media-frame">
                        <img src="{{ $fImg }}" alt="{{ $featured->title }}">
                        <span class="blog-wipe" aria-hidden="true"></span>
                    </div>
                    <span class="blog-featured-badge">Featured</span>
                </div>
                <div class="blog-featured-copy">
                    <div class="blog-meta-row">
                        @if($featured->published_at)
                            <span>{{ $featured->published_at->format('M d, Y') }}</span>
                        @endif
                        @if($featured->author)
                            <span>{{ $featured->author }}</span>
                        @endif
                    </div>
                    <h2>{{ $featured->title }}</h2>
                    @if($featured->excerpt)
                        <p>{{ $featured->excerpt }}</p>
                    @endif
                    <span class="blog-read">Read story <i class="fa-solid fa-arrow-right"></i></span>
                </div>
            </a>
        @endif

        <div class="blog-grid">
            @forelse($rest as $i => $blog)
                @php
                    $img = $blog->imageUrl() ?: $defaultImages[($i + 1) % count($defaultImages)];
                @endphp
                <article class="blog-card" data-reveal data-reveal-delay="{{ ($i % 3) * 0.08 }}">
                    <a href="{{ route('website.blogs.show', $blog->slug) }}" class="blog-card-link">
                        <div class="blog-media" data-blog-tilt>
                            <div class="blog-media-ghost" style="background-image:url('{{ $img }}')"></div>
                            <div class="blog-media-frame">
                                <img src="{{ $img }}" alt="{{ $blog->title }}">
                                <span class="blog-wipe" aria-hidden="true"></span>
                            </div>
                        </div>
                        <div class="blog-card-body">
                            <div class="blog-meta-row">
                                @if($blog->published_at)
                                    <span>{{ $blog->published_at->format('M d, Y') }}</span>
                                @endif
                                @if($blog->author)
                                    <span>{{ $blog->author }}</span>
                                @endif
                            </div>
                            <h3>{{ $blog->title }}</h3>
                            @if($blog->excerpt)
                                <p>{{ $blog->excerpt }}</p>
                            @endif
                            <span class="blog-read">Read more <i class="fa-solid fa-arrow-right"></i></span>
                        </div>
                    </a>
                </article>
            @empty
                @unless($featured)
                    <p class="empty-state">No blog posts yet.</p>
                @endunless
            @endforelse
        </div>

        @if($blogs->hasPages())
            <div class="blog-pagination">{{ $blogs->links() }}</div>
        @endif
    </div>
</section>
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
