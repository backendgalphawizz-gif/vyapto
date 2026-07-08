@extends('layouts.website')

@section('title', 'Blogs')

@section('content')
@include('website.partials.page-hero', ['hero' => $sections->get('hero'), 'fallbackTitle' => 'Blogs'])

<section class="content-section">
    <div class="container">
        <div class="cards-grid">
            @forelse($blogs as $blog)
                <div class="card-item">
                    @if($blog->imageUrl())
                        <img src="{{ $blog->imageUrl() }}" alt="{{ $blog->title }}" class="cover">
                    @endif
                    <div class="card-item-body">
                        @if($blog->published_at)
                            <p class="blog-meta">{{ $blog->published_at->format('M d, Y') }}@if($blog->author) &middot; {{ $blog->author }}@endif</p>
                        @endif
                        <h3>{{ $blog->title }}</h3>
                        <p>{{ $blog->excerpt }}</p>
                        <a href="{{ route('website.blogs.show', $blog->slug) }}" class="read-more">Read more &rarr;</a>
                    </div>
                </div>
            @empty
                <p class="empty-state">No blog posts yet.</p>
            @endforelse
        </div>
        @if($blogs->hasPages())
            <div class="pagination">{{ $blogs->links() }}</div>
        @endif
    </div>
</section>
@endsection
