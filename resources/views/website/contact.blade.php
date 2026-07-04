@extends('layouts.website')

@section('title', 'Contact Us')

@section('content')
@include('website.partials.page-hero', ['hero' => $sections->get('hero'), 'fallbackTitle' => 'Contact Us'])

<section class="content-section">
    <div class="container">
        <div class="contact-grid">
            <div>
                @php $contactSide = $sections->get('contact_info'); @endphp
                @if($contactSide && $contactSide->imageUrl())
                    <img src="{{ $contactSide->imageUrl() }}" alt="Contact" class="contact-side-image">
                @endif
                <h2 class="section-heading-left">Get in Touch</h2>

                @if($companyEmail)
                <div class="contact-info-item">
                    <i class="fa-solid fa-envelope"></i>
                    <div>
                        <strong>Email</strong><br>
                        <a href="mailto:{{ $companyEmail }}">{{ $companyEmail }}</a>
                    </div>
                </div>
                @endif

                @if($companyPhone)
                <div class="contact-info-item">
                    <i class="fa-solid fa-phone"></i>
                    <div>
                        <strong>Phone</strong><br>
                        <a href="tel:{{ $companyPhone }}">{{ $companyPhone }}</a>
                    </div>
                </div>
                @endif

                @if($companyAddress)
                <div class="contact-info-item">
                    <i class="fa-solid fa-location-dot"></i>
                    <div>
                        <strong>Address</strong><br>
                        {{ $companyAddress }}
                    </div>
                </div>
                @endif
            </div>

            <div class="contact-form">
                <h3 class="form-heading">Send a Message</h3>
                <form action="{{ route('website.contact.store') }}" method="POST">
                    @csrf
                    <label for="name">Name *</label>
                    <input type="text" id="name" name="name" value="{{ old('name') }}" required>
                    @error('name')<p class="field-error">{{ $message }}</p>@enderror

                    <label for="email">Email *</label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" required>
                    @error('email')<p class="field-error">{{ $message }}</p>@enderror

                    <label for="phone">Phone</label>
                    <input type="text" id="phone" name="phone" value="{{ old('phone') }}">

                    <label for="subject">Subject</label>
                    <input type="text" id="subject" name="subject" value="{{ old('subject', request('subject')) }}">

                    <label for="message">Message *</label>
                    <textarea id="message" name="message" required>{{ old('message') }}</textarea>
                    @error('message')<p class="field-error">{{ $message }}</p>@enderror

                    <button type="submit" class="btn-primary">Send Message</button>
                </form>
            </div>
        </div>
    </div>
</section>
@endsection
