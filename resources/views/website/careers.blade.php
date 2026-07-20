@extends('layouts.website')

@section('title', 'Careers')

@section('content')
@php
    $hero = $sections->get('hero');
    $culture = $sections->get('culture');
    $rolesHeader = $sections->get('roles_header');
    $apply = $sections->get('apply');
    $heroImage = $hero?->imageUrl() ?: asset('images/vyapto-warehouse-bg.png');
    $cultureImage = $culture?->imageUrl() ?: asset('images/3slider.avif');
    $meta = $hero?->extra['meta'] ?? [
        ['label' => 'Open Categories', 'value' => (string) max($openings->count(), 6)],
        ['label' => 'Locations', 'value' => 'Bihar + Pan-India'],
        ['label' => 'Team Size', 'value' => '150+'],
    ];
    $values = collect($sections->keys())
        ->filter(fn ($k) => str_starts_with((string) $k, 'value_'))
        ->map(fn ($k) => $sections->get($k))
        ->filter()
        ->values();
    if ($values->isEmpty()) {
        $values = collect([
            (object) ['icon' => 'fa-shield-halved', 'title' => 'Integrity', 'content' => 'Honest, transparent, ethical, always.'],
            (object) ['icon' => 'fa-gears', 'title' => 'Efficiency', 'content' => 'We optimize, not just work harder.'],
            (object) ['icon' => 'fa-handshake', 'title' => 'Empowerment', 'content' => 'Opportunity for people and partners.'],
            (object) ['icon' => 'fa-lightbulb', 'title' => 'Innovation', 'content' => 'Smarter tools, smarter routes.'],
            (object) ['icon' => 'fa-bullseye', 'title' => 'Commitment', 'content' => 'Excellence in every service.'],
        ]);
    }
@endphp

{{-- HERO --}}
<section class="career-hero">
    <img src="{{ $heroImage }}" alt="" class="career-hero-bg" aria-hidden="true">
    <div class="career-hero-overlay"></div>
    <div class="container career-hero-inner" data-reveal>
        <div class="career-eyebrow">{{ $hero?->subtitle ?? 'Crew Manifest · Now Boarding' }}</div>
        <h1 class="career-hero-title">{!! nl2br(e($hero?->title ?? "Ride with a\ncompany that moves.")) !!}</h1>
        <p class="career-hero-lead">
            {{ $hero?->content ?? "150+ people strong and growing — on the road, in the hubs, behind the CRM, and on the franchise floor. If you like solving real problems for real businesses, there's a seat for you." }}
        </p>
        <div class="career-hero-meta">
            @foreach($meta as $item)
                <span>
                    {{ strtoupper($item['label'] ?? '') }}
                    <b>{{ $item['value'] ?? '' }}</b>
                </span>
            @endforeach
        </div>
    </div>
</section>

{{-- WHY WORK HERE --}}
<section class="career-culture">
    <div class="container">
        <div class="career-culture-grid">
            <div class="career-culture-copy" data-reveal="left">
                <div class="prod-meta">
                    <span class="prod-meta-num">{{ $culture?->extra['num'] ?? 'Culture' }}</span>
                    <span class="prod-meta-line"></span>
                    <span class="prod-meta-cat">{{ $culture?->extra['category'] ?? 'Why Vyapto' }}</span>
                </div>
                <h2 class="career-section-title">{!! nl2br(e($culture?->title ?? "Work that shows up\non the road, not just a slide.")) !!}</h2>
                <p class="career-lead">
                    {{ $culture?->content ?? "You'll see the outcome of your work the same day — a route run on time, a client onboarded, a hub cleared before the evening rush." }}
                </p>
            </div>
            <div class="career-photo" data-reveal="scale">
                <img src="{{ $cultureImage }}" alt="Life at Vyapto">
                <span class="career-photo-tag">Life at Vyapto</span>
            </div>
        </div>

        <div class="highlights-grid highlights-grid--5 values-grid" style="margin-top:64px;">
            @foreach($values as $value)
                <div class="highlight-card" data-reveal data-reveal-delay="{{ $loop->index * 0.06 }}">
                    <div class="icon-tile {{ $loop->even ? 'blue' : '' }}">
                        <i class="fa-solid {{ $value->icon ?? 'fa-star' }}"></i>
                    </div>
                    <h4>{{ $value->title }}</h4>
                    <p>{{ $value->content }}</p>
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- OPEN ROLES --}}
<section class="career-roles" id="roles">
    <div class="container">
        <div class="prod-section-head" data-reveal>
            <div class="prod-meta">
                <span class="prod-meta-num">{{ $rolesHeader?->extra['num'] ?? 'Positions' }}</span>
                <span class="prod-meta-line"></span>
                <span class="prod-meta-cat">{{ $rolesHeader?->extra['category'] ?? 'Open Categories' }}</span>
            </div>
            <h2 class="career-section-title">{!! nl2br(e($rolesHeader?->title ?? "Where you could\njoin the route.")) !!}</h2>
            <p class="prod-section-sub left">
                {{ $rolesHeader?->content ?? 'Hiring across every vertical we operate — reach out even if your exact role isn\'t listed.' }}
            </p>
        </div>

        <div class="role-list">
            @forelse($openings as $job)
                <div class="role-row" data-reveal data-reveal-delay="{{ $loop->index * 0.05 }}">
                    <div>
                        <h3>{{ $job->title }}</h3>
                        @if($job->excerpt)
                            <p>{{ $job->excerpt }}</p>
                        @endif
                    </div>
                    <div class="rt">{{ $job->location ?? 'Pan-India' }}</div>
                    <div class="rt">{{ $job->department ?? 'Full-time' }}</div>
                    <a href="#apply" class="svc-btn-outline role-apply-btn" data-role="{{ $job->title }}">Apply →</a>
                </div>
            @empty
                <p class="empty-state">No open roles right now — send us your details below and we’ll keep you in mind.</p>
            @endforelse
        </div>
    </div>
</section>

{{-- APPLICATION CTA --}}
<section class="career-apply" id="apply">
    <div class="container">
        <div class="career-apply-grid">
            <div class="career-apply-copy" data-reveal="left">
                <div class="prod-meta">
                    <span class="prod-meta-num" style="color:#ffb07a;">{{ $apply?->extra['num'] ?? 'Join' }}</span>
                    <span class="prod-meta-line" style="background:#ffb07a;"></span>
                    <span class="prod-meta-cat" style="color:rgba(255,255,255,0.7);">{{ $apply?->extra['category'] ?? 'Application Note' }}</span>
                </div>
                <h2 class="career-section-title" style="color:#fff;">{!! nl2br(e($apply?->title ?? "Send us your\ndetails. We'll route it.")) !!}</h2>
                <p class="career-apply-text">
                    {{ $apply?->content ?? 'Drop your details below and our workforce team will match you to the right category — no long forms, no waiting for a portal login.' }}
                </p>
            </div>

            <form id="careers-form" class="career-form" method="POST" action="{{ route('website.careers.apply') }}" data-reveal="scale">
                @csrf
                <label for="career_name">Full name</label>
                <input id="career_name" type="text" name="name" value="{{ old('name') }}" placeholder="e.g. Ramesh Kumar" required>

                <label for="career_category">Category of interest</label>
                <input id="career_category" type="text" name="category" value="{{ old('category') }}" placeholder="Logistics / Manpower / Franchise / IT / Accounting" required>

                <label for="career_contact">Phone / Email</label>
                <input id="career_contact" type="text" name="contact" value="{{ old('contact') }}" placeholder="Where should we reach you?" required>

                <button type="submit" class="svc-btn-primary career-submit">Submit application →</button>
            </form>
        </div>
    </div>
</section>

{{-- Success Modal --}}
<div id="apply-modal" class="career-modal {{ session('career_applied') ? 'is-open' : '' }}" aria-hidden="{{ session('career_applied') ? 'false' : 'true' }}">
    <div class="career-modal-card">
        <div class="career-modal-icon">✓</div>
        <h3>Application Received</h3>
        <p>Thank you for your interest in joining Vyapto! Our workforce operations team will review your details and reach out to you within 24 hours.</p>
        <button type="button" id="close-modal-btn" class="svc-btn-primary career-submit">Close Window</button>
    </div>
</div>
@endsection

@push('scripts')
<script>
(function () {
    const modal = document.getElementById('apply-modal');
    const closeBtn = document.getElementById('close-modal-btn');
    const form = document.getElementById('careers-form');
    const categoryInput = document.getElementById('career_category');

    document.querySelectorAll('.role-apply-btn').forEach(btn => {
        btn.addEventListener('click', function (e) {
            e.preventDefault();
            const role = this.getAttribute('data-role') || '';
            if (categoryInput && role) categoryInput.value = role;
            const applySection = document.getElementById('apply');
            if (applySection) {
                applySection.scrollIntoView({ behavior: 'smooth' });
                const firstInput = applySection.querySelector('input');
                if (firstInput) firstInput.focus();
            }
        });
    });

    function closeModal() {
        if (!modal) return;
        modal.classList.remove('is-open');
        modal.setAttribute('aria-hidden', 'true');
        if (form) form.reset();
    }

    if (closeBtn) closeBtn.addEventListener('click', closeModal);
    if (modal) {
        modal.addEventListener('click', function (e) {
            if (e.target === modal) closeModal();
        });
    }
})();
</script>
@endpush
