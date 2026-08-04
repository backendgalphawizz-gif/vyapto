@extends('layouts.website')

@section('title', $sections->get('hero')?->title ?? 'Contact Us')

@section('content')
@php
    $sectionHeading = $sections->get('section_heading');
    $formHeading = $sections->get('form_heading');
    $labelEmail = $sections->get('label_email')?->title ?: 'Email';
    $labelPhone = $sections->get('label_phone')?->title ?: 'Phone';
    $labelAddress = $sections->get('label_address')?->title ?: 'Address';
    $labelName = $sections->get('label_name')?->title ?: 'Name *';
    $labelMessage = $sections->get('label_message')?->title ?: 'Message *';
    $labelSubject = $sections->get('label_subject')?->title ?: 'Subject';
    $submitButton = $sections->get('submit_button')?->title ?: 'Send Message';
    $contactSide = $sections->get('contact_info');
@endphp

@include('website.partials.page-hero', ['hero' => $sections->get('hero'), 'fallbackTitle' => 'Contact Us'])

<section class="content-section">
    <div class="container">
    <h2 class="section-heading-left">{{ $sectionHeading?->title ?: 'Get in Touch' }}</h2>
        <div class="contact-grid">
        <div>
    @if($contactSide && $contactSide->imageUrl())
        <img src="{{ $contactSide->imageUrl() }}" alt="{{ $contactSide->title ?: 'Contact' }}" class="contact-side-image">
    @endif

    @if(!empty($companyEmail))
    <div class="contact-info-item contact-form">
        <i class="fa-solid fa-envelope"></i>
        <div>
            <strong>{{ $labelEmail }}</strong><br>
            <a href="mailto:{{ $companyEmail }}">{{ $companyEmail }}</a>
        </div>
    </div>
    @endif

    @if(!empty($companyPhone))
    <div class="contact-info-item contact-form">
        <i class="fa-solid fa-phone"></i>
        <div>
            <strong>{{ $labelPhone }}</strong><br>
            <a href="tel:{{ $companyPhone }}">{{ $companyPhone }}</a>
        </div>
    </div>
    @endif

    @if(!empty($companyAddress))
    <div class="contact-info-item contact-form">
        <i class="fa-solid fa-location-dot"></i>
        <div>
            <strong>{{ $labelAddress }}</strong><br>
            {{ $companyAddress }}
        </div>
    </div>
    @endif
</div>

            <div class="contact-form">
                <h3 class="form-heading">{{ $formHeading?->title ?: 'Send a Message' }}</h3>
                <form action="{{ route('website.contact.store') }}" method="POST">
                    @csrf
                    <label for="name">{{ $labelName }}</label>
                    <input type="text" id="name" name="name" value="{{ old('name') }}" required>
                    @error('name')<p class="field-error">{{ $message }}</p>@enderror

                    <label for="email">{{ $labelEmail }} *</label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" required>
                    @error('email')<p class="field-error">{{ $message }}</p>@enderror

                    <label for="phone">{{ $labelPhone }}</label>
                    <input type="text" id="phone" name="phone" value="{{ old('phone') }}">

                    <label for="subject">{{ $labelSubject }}</label>
                    <input type="text" id="subject" name="subject" value="{{ old('subject', request('subject')) }}">

                    <label for="message">{{ $labelMessage }}</label>
                    <textarea id="message" name="message" required>{{ old('message') }}</textarea>
                    @error('message')<p class="field-error">{{ $message }}</p>@enderror

                    <button type="submit" class="btn-primary">{{ $submitButton }}</button>
                </form>
            </div>
        </div>
    </div>
</section>
@endsection
