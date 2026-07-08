@extends('layouts.website')

@section('title', $blog->title)

@section('content')
<section class="detail-page">
    <div class="container">
        <div class="detail-card">
            @if($blog->imageUrl())
                <img src="{{ $blog->imageUrl() }}" alt="{{ $blog->title }}" class="detail-hero-image">
            @endif
            @if($blog->published_at)
                <p class="blog-meta">{{ $blog->published_at->format('F d, Y') }}@if($blog->author) &middot; By {{ $blog->author }}@endif</p>
            @endif
            <h1>{{ $blog->title }}</h1>
            @if($blog->excerpt)
                <p class="detail-lead">{{ $blog->excerpt }}</p>
            @endif
            @if($blog->content)
                <div class="blog-content">{!! $blog->content !!}</div>
            @endif
            <div style="margin-top:32px;">
                <a href="{{ route('website.blogs') }}" class="btn-outline">&larr; Back to Blogs</a>
            </div>
        </div>

        @if($recent->isNotEmpty())
            <div style="margin-top:48px;">
                <h2 class="section-heading">Recent Posts</h2>
                <div class="cards-grid">
                    @foreach($recent as $post)
                        <div class="card-item">
                            @if($post->imageUrl())
                                <img src="{{ $post->imageUrl() }}" alt="{{ $post->title }}" class="cover">
                            @endif
                            <div class="card-item-body">
                                <h3>{{ $post->title }}</h3>
                                <p>{{ $post->excerpt }}</p>
                                <a href="{{ route('website.blogs.show', $post->slug) }}" class="read-more">Read more &rarr;</a>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</section>
@endsection
