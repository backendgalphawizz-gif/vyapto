@extends('layouts.portal')

@section('title', 'FAQs')

@section('page_subtitle')
Find answers to common questions about attendance, shipments, salary, and more.
@endsection

@section('content')
@php
    $totalFaqs = $faqs->flatten()->count();
@endphp

@if($totalFaqs === 0)
    <div class="app-card faq-empty text-center">
        <div class="faq-empty-icon"><i class="bi bi-question-circle"></i></div>
        <h5>No FAQs yet</h5>
        <p class="text-muted mb-0">Help articles will appear here once they are published by your admin team.</p>
    </div>
@else
    @foreach($faqs as $category => $items)
        <div class="app-card faq-table-card mb-3">
            <div class="faq-table-head">
                <h2>{{ $category }}</h2>
                <span class="faq-category-badge">{{ $items->count() }}</span>
            </div>

            <div class="portal-table-wrap">
                <table class="portal-table faq-table">
                    <thead>
                        <tr>
                            <th style="width: 56px;">No</th>
                            <th style="width: 22%;">Question</th>
                            <th>Answer</th>
                            <th style="width: 72px;">Image</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($items as $faq)
                            @php
                                $imageUrl = $faq->image
                                    ? asset('storage/'.$faq->image)
                                    : ($faq->image_url ?: null);
                            @endphp
                            <tr>
                                <td class="text-center text-muted">{{ $loop->iteration }}</td>
                                <td class="faq-table-question">{{ $faq->title }}</td>
                                <td class="faq-table-answer">{!! nl2br(e($faq->description)) !!}</td>
                                <td class="text-center">
                                    @if($imageUrl)
                                        <a href="{{ $imageUrl }}" target="_blank" class="faq-thumb-link" title="View image">
                                            <img src="{{ $imageUrl }}" alt="{{ $faq->title }}" class="faq-thumb">
                                        </a>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endforeach
@endif
@endsection
