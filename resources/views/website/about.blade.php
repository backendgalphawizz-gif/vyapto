@extends('layouts.website')

@section('title', 'About Us')
@section('meta_description', $sections->get('hero')?->content ?? 'About Vyapto Commerce Pvt. Ltd. — logistics, workforce, franchise and consumer products across India.')

@push('styles')
<link rel="stylesheet" href="{{ asset('assets/website/css/about-page.css') }}?v=1">
@endpush

@section('content')
@php
    $hero = $sections->get('hero');
    $heroBg = $sections->get('hero_bg')?->imageUrl()
        ?? $hero?->imageUrl()
        ?? 'https://images.unsplash.com/photo-1601584115917-0f970f2f0e6b?auto=format&fit=crop&w=1600&q=80';
    $heroMetas = $sections->filter(fn ($s) => str_starts_with($s->section_key, 'hero_meta_'))->sortBy('sort_order');
    $storyHeader = $sections->get('story_header');
    $story = $sections->get('story');
    $storyImage = $sections->get('story_image');
    $stats = $sections->filter(fn ($s) => str_starts_with($s->section_key, 'about_stat_'))->sortBy('sort_order');
    $visionHeader = $sections->get('vision_header');
    $visionQuote = $sections->get('vision_quote');
    $visionPoints = $sections->filter(fn ($s) => str_starts_with($s->section_key, 'vision_point_'))->sortBy('sort_order');
    $visionImage = $sections->get('vision_image');
    $whyHeader = $sections->get('why_header');
    $issues = $sections->filter(fn ($s) => str_starts_with($s->section_key, 'issue_'))->sortBy('sort_order');
    $advantageHeader = $sections->get('advantage_header');
    $advantages = $sections->filter(fn ($s) => str_starts_with($s->section_key, 'advantage_') && $s->section_key !== 'advantage_header')->sortBy('sort_order');
    $teamHeader = $sections->get('team_header');
    $team = $sections->filter(fn ($s) => preg_match('/^team_\d+$/', $s->section_key))->sortBy('sort_order');
    $teamCta = $sections->get('team_cta');
    $storyParagraphs = array_values(array_filter(array_map('trim', preg_split("/\r\n|\n|\r/", (string) ($story?->content ?? '')))));
@endphp

<div class="about-page">

{{-- HERO --}}
<section class="about-hero" style="background-image:url('{{ $heroBg }}');">
  <div class="wrap">
    <div class="eyebrow" data-reveal>{{ $hero?->subtitle ?? 'ABOUT VYAPTO' }}</div>
    <h1 data-reveal>{!! nl2br(e($hero?->title ?? "Every load has\nan origin.")) !!}</h1>
    <p class="lead" data-reveal>{{ $hero?->content ?? 'Vyapto Commerce Pvt. Ltd. is a fast-growing company based in Bihar, India, moving goods, people and opportunity across the country — one consignment at a time.' }}</p>
    @if($heroMetas->isNotEmpty())
    <div class="hero-meta" data-reveal>
      @foreach($heroMetas as $meta)
        <span>{{ $meta->title }} <b>{{ $meta->subtitle }}</b></span>
      @endforeach
    </div>
    @else
    <div class="hero-meta" data-reveal>
      <span>ORIGIN <b>Bihar, India</b></span>
      <span>ROUTE <b>Pan-India</b></span>
      <span>VERTICALS <b>04</b></span>
      <span>STATUS <b>In Transit — Growing</b></span>
    </div>
    @endif
  </div>
</section>

{{-- 01 STORY --}}
<section id="story">
  <div class="wrap">
    <div class="grid grid-2" style="align-items:center;gap:56px;">
      <div data-reveal="left">
        <div class="ui-heading-group">
          <div class="ui-heading-meta">
            <span class="ui-heading-num">01</span>
            <span class="ui-heading-divider"></span>
            <span class="ui-heading-category">{{ $storyHeader?->subtitle ?? 'OUR COMPANY STORY' }}</span>
          </div>
          <h2 class="ui-heading-title">{!! nl2br(e($storyHeader?->title ?? "Built on the road,\nnot in a boardroom.")) !!}</h2>
        </div>
        @forelse($storyParagraphs as $i => $para)
          @if($i === 0)
            <p class="lead">{{ $para }}</p>
          @else
            <p>{{ $para }}</p>
          @endif
        @empty
          <p class="lead">Vyapto started with a simple observation: businesses across India were losing time and money to unreliable transport, unverified manpower, and disconnected operations.</p>
          <p>What began as a transportation and logistics operation in Bihar has grown into an integrated services company — spanning Transportation &amp; Logistics, Manpower Solutions, Franchise Operations and Consumer Products.</p>
          <p>Today, that same approach — solve the problem in front of us, do it with integrity, and stay close to the ground — still runs every route we plan and every hire we place.</p>
        @endforelse
      </div>
      <div class="photo story-photo-fix" data-reveal="scale" style="height:420px;">
        <img src="{{ $storyImage?->imageUrl() ?? 'https://images.unsplash.com/photo-1586528116311-ad8dd3c8310d?auto=format&fit=crop&w=800&q=85' }}" alt="{{ $storyImage?->title ?? 'Vyapto distribution hub' }}">
        <span class="photo-tag">{{ $storyImage?->title ?? 'Distribution Hub' }}</span>
      </div>
    </div>
  </div>

  <div class="wrap" data-reveal style="margin-top:64px;">
    <div class="stats" style="border-radius:var(--about-radius);overflow:hidden;">
      @forelse($stats as $stat)
        <div class="stat">
          <b @if(preg_match('/\d/', $stat->title)) data-count="{{ $stat->title }}" @endif>{{ $stat->title }}</b>
          <span>{{ $stat->subtitle }}</span>
        </div>
      @empty
        <div class="stat"><b data-count="100+">100+</b><span>Business Clients Served</span></div>
        <div class="stat"><b data-count="1500+">1500+</b><span>Deliveries Daily</span></div>
        <div class="stat"><b data-count="150+">150+</b><span>Workforce Managed</span></div>
        <div class="stat"><b data-count="200+">200+</b><span>Pincodes Covered</span></div>
      @endforelse
    </div>
  </div>
</section>

{{-- 02 VISION --}}
<section id="vision" class="about-vision" style="background:var(--paper);border-top:1px solid var(--line);">
  <div class="wrap">
    <div class="grid grid-2" style="align-items:center;gap:64px;">
      <div data-reveal="left">
        <div class="ui-heading-group">
          <div class="ui-heading-meta">
            <span class="ui-heading-num">02</span>
            <span class="ui-heading-divider"></span>
            <span class="ui-heading-category">{{ $visionHeader?->subtitle ?? 'OUR VISION' }}</span>
          </div>
        </div>
        <blockquote>
          "{{ $visionQuote?->content ?? 'To become a trusted growth partner for businesses by delivering dependable logistics, workforce, franchise, and distribution solutions that create sustainable value.' }}"
        </blockquote>
        <div class="grid grid-2 vision-inner-grid" style="gap:24px 32px;margin-top:32px;">
          @forelse($visionPoints as $point)
            <div>
              <h4 style="font-size:1.05rem;font-weight:700;color:var(--navy);margin:0 0 6px;">{{ $point->title }}</h4>
              <p style="font-size:0.88rem;color:var(--muted);margin:0;line-height:1.5;">{{ $point->content }}</p>
            </div>
          @empty
            <div>
              <h4 style="font-size:1.05rem;font-weight:700;color:var(--navy);margin:0 0 6px;">Innovate &amp; Excel</h4>
              <p style="font-size:0.88rem;color:var(--muted);margin:0;line-height:1.5;">Constantly improving our services through technology, optimization, and on-time execution.</p>
            </div>
            <div>
              <h4 style="font-size:1.05rem;font-weight:700;color:var(--navy);margin:0 0 6px;">Empower &amp; Impact</h4>
              <p style="font-size:0.88rem;color:var(--muted);margin:0;line-height:1.5;">Enabling businesses to scale and creating long-term positive economic impact.</p>
            </div>
          @endforelse
        </div>
      </div>
      <div class="photo vision-photo-fix" data-reveal="scale" style="height:420px;box-shadow:0 20px 40px rgba(12,31,61,0.08);border:1px solid var(--line);">
        <img src="{{ $visionImage?->imageUrl() ?? 'https://images.unsplash.com/photo-1586528116311-ad8dd3c8310d?auto=format&fit=crop&w=800&q=85' }}" alt="{{ $visionImage?->title ?? 'Vyapto logistics vision' }}">
      </div>
    </div>
  </div>
</section>

{{-- 03 WHY US --}}
<section class="mist" id="why-us">
  <div class="wrap">
    <div class="ui-heading-group" data-reveal>
      <div class="ui-heading-meta">
        <span class="ui-heading-num">03</span>
        <span class="ui-heading-divider"></span>
        <span class="ui-heading-category">{{ $whyHeader?->subtitle ?? 'Operational Strength' }}</span>
      </div>
      <h2 class="ui-heading-title">{!! nl2br(e($whyHeader?->title ?? "We solve the problem\nbefore it costs you a client.")) !!}</h2>
      <p style="color:var(--muted);margin-top:10px;">{{ $whyHeader?->content ?? 'Every challenge businesses face has a Vyapto answer built directly against it.' }}</p>
    </div>

    <div class="grid grid-2" style="gap:16px;">
      @forelse($issues as $issue)
        <div class="card" data-reveal style="display:flex;gap:16px;align-items:flex-start;">
          <span class="tag">ISSUE</span>
          <div>
            <h3 style="font-size:1.05rem;">{{ $issue->title }}</h3>
            <p>{{ $issue->content }}</p>
            @if($issue->subtitle)
              <p class="solution">→ {{ $issue->subtitle }}</p>
            @endif
          </div>
        </div>
      @empty
        @foreach([
          ['Delayed deliveries', 'Unreliable transport partners and poor route planning.', 'Reliable logistics network with real-time tracking.'],
          ['Workforce shortages', 'Skilled, dependable manpower is hard to find fast.', 'Ready-to-deploy, verified workforce pool.'],
          ['High operational costs', 'Inefficient processes inflate business expenses.', 'Smart planning, transparent cost-efficient pricing.'],
          ['Inconsistent supply', 'Irregular supply chains hurt customer trust.', 'Quality-assured, hygiene-checked distribution.'],
        ] as $row)
          <div class="card" data-reveal style="display:flex;gap:16px;align-items:flex-start;">
            <span class="tag">ISSUE</span>
            <div>
              <h3 style="font-size:1.05rem;">{{ $row[0] }}</h3>
              <p>{{ $row[1] }}</p>
              <p class="solution">→ {{ $row[2] }}</p>
            </div>
          </div>
        @endforeach
      @endforelse
    </div>

    <div class="route-line--full" data-reveal>
      <span class="marker">{{ $advantageHeader?->title ?? 'THE VYAPTO ADVANTAGE' }}</span>
      <span class="dash"></span>
    </div>

    <div class="grid grid-3">
      @forelse($advantages as $i => $adv)
        <div class="card" data-reveal data-reveal-delay="{{ $loop->index * 0.05 }}">
          <div class="icon-tile {{ $loop->odd ? 'blue' : '' }}">
            <i class="fa-solid {{ $adv->icon ?: 'fa-layer-group' }}"></i>
          </div>
          <h3>{{ $adv->title }}</h3>
          <p>{{ $adv->content }}</p>
        </div>
      @empty
        @foreach([
          ['fa-layer-group', 'Integrated Solutions', 'One-stop solutions across multiple business needs.', false],
          ['fa-map', 'Pan-India Network', 'Strong presence and operations across India.', true],
          ['fa-users', 'Experienced Team', 'Skilled professionals with domain expertise.', false],
          ['fa-clipboard-check', 'Quality Assured', 'Consistent, high standards in everything we do.', true],
          ['fa-shield-halved', 'Ethical & Transparent', 'Complete transparency, always.', false],
          ['fa-chart-line', 'Growth-Oriented', 'Reducing cost, increasing profitability, together.', true],
        ] as $a)
          <div class="card" data-reveal>
            <div class="icon-tile {{ $a[3] ? 'blue' : '' }}"><i class="fa-solid {{ $a[0] }}"></i></div>
            <h3>{{ $a[1] }}</h3>
            <p>{{ $a[2] }}</p>
          </div>
        @endforeach
      @endforelse
    </div>
  </div>
</section>

{{-- 04 TEAM --}}
<section id="team" style="background:var(--paper);border-top:1px solid var(--line);">
  <div class="wrap">
    <div class="ui-heading-group center" data-reveal style="margin-bottom:64px;">
      <div class="ui-heading-meta">
        <span class="ui-heading-num">04</span>
        <span class="ui-heading-divider"></span>
        <span class="ui-heading-category">{{ $teamHeader?->subtitle ?? 'Our Force' }}</span>
      </div>
      <h2 class="ui-heading-title">{!! nl2br(e($teamHeader?->title ?? "The people behind\nevery delivery.")) !!}</h2>
      <p style="color:var(--muted);max-width:560px;margin:12px auto 0;font-size:1rem;">{{ $teamHeader?->content ?? '150+ trained professionals — drivers, dispatchers, franchise managers and support staff — working as one crew across Bihar and beyond.' }}</p>
    </div>

    <div class="grid grid-4 team-grid-override">
      @forelse($team as $member)
        <div class="about-team-card" data-reveal>
          <div class="photo-wrap">
            @if($member->imageUrl())
              <img src="{{ $member->imageUrl() }}" alt="{{ $member->title }}">
            @else
              <img src="https://ui-avatars.com/api/?name={{ urlencode($member->title) }}&background=0c1f3d&color=fff&size=400" alt="{{ $member->title }}">
            @endif
          </div>
          <div class="body">
            <div class="dept {{ strtolower((string) $member->icon) === 'blue' ? 'blue' : '' }}">{{ $member->subtitle ?? 'Team' }}</div>
            <h3>{{ $member->title }}</h3>
            <p>{{ $member->content }}</p>
          </div>
        </div>
      @empty
        @foreach([
          ['Rahul Sharma', 'Leadership', 'Head of Operations — oversees logistics, route planning and hub management across all Vyapto locations.', 'https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?auto=format&fit=crop&w=400&q=80', 'orange'],
          ['Priya Verma', 'Workforce', 'Workforce Manager — sources, verifies and deploys skilled and semi-skilled personnel for clients across the country.', 'https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?auto=format&fit=crop&w=400&q=80', 'orange'],
          ['Amit Singh', 'Franchise', 'Franchise Director — builds and grows Vyapto\'s franchise partner network from onboarding to operational excellence.', 'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?auto=format&fit=crop&w=400&q=80', 'blue'],
          ['Neha Jha', 'Finance', 'Finance & Compliance — manages bookkeeping, tax compliance and financial controls for all Vyapto verticals.', 'https://images.unsplash.com/photo-1607746882042-944635dfe10e?auto=format&fit=crop&w=400&q=80', 'blue'],
        ] as $m)
          <div class="about-team-card" data-reveal>
            <div class="photo-wrap"><img src="{{ $m[3] }}" alt="{{ $m[0] }}"></div>
            <div class="body">
              <div class="dept {{ $m[4] === 'blue' ? 'blue' : '' }}">{{ $m[1] }}</div>
              <h3>{{ $m[0] }}</h3>
              <p>{{ $m[2] }}</p>
            </div>
          </div>
        @endforeach
      @endforelse
    </div>

    <div data-reveal style="text-align:center;margin-top:48px;">
      <a href="{{ $teamCta?->link ?: route('website.careers') }}" class="btn-join">
        {{ $teamCta?->title ?? 'Join the crew' }} →
      </a>
    </div>
  </div>
</section>

</div>
@endsection
