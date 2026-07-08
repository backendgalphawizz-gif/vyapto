@extends('layouts.website')

@section('title', 'FAQ')

@section('content')
@include('website.partials.page-hero', ['hero' => $sections->get('hero'), 'fallbackTitle' => 'Frequently Asked Questions'])

<section class="content-section">
    <div class="container">
        <div class="faq-list">
            @forelse($faqs as $faq)
                <div class="faq-item" data-reveal>
                    <button type="button" class="faq-question">
                        {{ $faq->title }}
                        <i class="fa-solid fa-chevron-down"></i>
                    </button>
                    <div class="faq-answer">
                        <div class="faq-answer-inner">{{ $faq->description }}</div>
                    </div>
                </div>
            @empty
                <p class="empty-state">No FAQs available yet. Please check back soon.</p>
            @endforelse
        </div>
    </div>
</section>

<div class="container">
    <div class="cta-banner" data-reveal>
        <h2>Still have questions?</h2>
        <p>Our team is here to help. Reach out and we'll get back to you promptly.</p>
        <a href="{{ route('website.contact') }}" class="btn-primary">Contact Us <i class="fa-solid fa-arrow-right"></i></a>
    </div>
</div>
@endsection
