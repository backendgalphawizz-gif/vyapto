@extends('layouts.website')

@section('title', $sections->get('hero')?->title ?? 'About Us')
@section('meta_description', $sections->get('hero')?->content ?? '')

@push('styles')
<link rel="stylesheet" href="{{ asset('assets/website/css/about-page.css') }}?v=2">
@endpush

@section('content')
@php
    $hero = $sections->get('hero');
    $heroImage = $sections->get('hero_bg')?->imageUrl()
        ?? $hero?->imageUrl();
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
    $L = $siteLabels ?? [];
@endphp

<div class="about-page">

{{-- HERO --}}
<section class="prod-hero about-hero">
  @if($heroImage)
  <img src="{{ $heroImage }}" alt="" class="prod-hero-bg" aria-hidden="true">
  @endif
  <div class="prod-hero-overlay"></div>
  <div class="wrap">
    @if($hero?->subtitle)
    <div class="eyebrow" data-reveal>{{ $hero->subtitle }}</div>
    @endif
    @if($hero?->title)
    <h1 data-reveal>{!! nl2br(e($hero->title)) !!}</h1>
    @endif
    @if($hero?->content)
    <p class="lead" data-reveal>{{ $hero->content }}</p>
    @endif
    @if($heroMetas->isNotEmpty())
    <div class="hero-meta" data-reveal>
      @foreach($heroMetas as $meta)
        <span>{{ $meta->title }} <b>{{ $meta->subtitle }}</b></span>
      @endforeach
    </div>
    @endif
  </div>
</section>

{{-- 01 STORY --}}
@if($storyHeader || $story || $storyImage || $stats->isNotEmpty())
<section id="story">
  <div class="wrap">
    <div class="grid grid-2" style="align-items:center;gap:56px;">
      <div data-reveal="left">
        @if($storyHeader)
        <div class="ui-heading-group">
          <div class="ui-heading-meta">
            <span class="ui-heading-num">01</span>
            <span class="ui-heading-divider"></span>
            @if($storyHeader->subtitle)
            <span class="ui-heading-category">{{ $storyHeader->subtitle }}</span>
            @endif
          </div>
          @if($storyHeader->title)
          <h2 class="ui-heading-title">{!! nl2br(e($storyHeader->title)) !!}</h2>
          @endif
        </div>
        @endif
        @foreach($storyParagraphs as $i => $para)
          @if($i === 0)
            <p class="lead">{{ $para }}</p>
          @else
            <p>{{ $para }}</p>
          @endif
        @endforeach
      </div>
      @if($storyImage?->imageUrl())
      <div class="photo story-photo-fix" data-reveal="scale" style="height:420px;">
        <img src="{{ $storyImage->imageUrl() }}" alt="{{ $storyImage->title ?: 'Story' }}">
        @if($storyImage->title)
        <span class="photo-tag">{{ $storyImage->title }}</span>
        @endif
      </div>
      @endif
    </div>
  </div>

  @if($stats->isNotEmpty())
  <div class="wrap" data-reveal style="margin-top:64px;">
    <div class="stats" style="border-radius:var(--about-radius);overflow:hidden;">
      @foreach($stats as $stat)
        <div class="stat">
          <b @if(preg_match('/\d/', (string) $stat->title)) data-count="{{ $stat->title }}" @endif>{{ $stat->title }}</b>
          <span>{{ $stat->subtitle }}</span>
        </div>
      @endforeach
    </div>
  </div>
  @endif
</section>
@endif

{{-- 02 VISION --}}
@if($visionHeader || $visionQuote || $visionPoints->isNotEmpty() || $visionImage)
<section id="vision" class="about-vision" style="background:var(--paper);border-top:1px solid var(--line);">
  <div class="wrap">
    <div class="grid grid-2" style="align-items:center;gap:64px;">
      <div data-reveal="left">
        @if($visionHeader)
        <div class="ui-heading-group">
          <div class="ui-heading-meta">
            <span class="ui-heading-num">02</span>
            <span class="ui-heading-divider"></span>
            @if($visionHeader->subtitle)
            <span class="ui-heading-category">{{ $visionHeader->subtitle }}</span>
            @endif
          </div>
        </div>
        @endif
        @if($visionQuote?->content)
        <blockquote>"{{ $visionQuote->content }}"</blockquote>
        @endif
        @if($visionPoints->isNotEmpty())
        <div class="grid grid-2 vision-inner-grid" style="gap:24px 32px;margin-top:32px;">
          @foreach($visionPoints as $point)
            <div>
              <h4 style="font-size:1.05rem;font-weight:700;color:var(--navy);margin:0 0 6px;">{{ $point->title }}</h4>
              <p style="font-size:0.88rem;color:var(--muted);margin:0;line-height:1.5;">{{ $point->content }}</p>
            </div>
          @endforeach
        </div>
        @endif
      </div>
      @if($visionImage?->imageUrl())
      <div class="photo vision-photo-frame" data-reveal="scale" style="height:420px;box-shadow:0 20px 40px rgba(12,31,61,0.08);border:1px solid var(--line);">
        <img src="{{ $visionImage->imageUrl() }}" alt="{{ $visionImage->title ?: 'Vision' }}">
      </div>
      @endif
    </div>
  </div>
</section>
@endif

{{-- 03 WHY US --}}
@if($whyHeader || $issues->isNotEmpty() || $advantages->isNotEmpty())
<section class="mist" id="why-us">
  <div class="wrap">
    @if($whyHeader)
    <div class="ui-heading-group" data-reveal>
      <div class="ui-heading-meta">
        <span class="ui-heading-num">03</span>
        <span class="ui-heading-divider"></span>
        @if($whyHeader->subtitle)
        <span class="ui-heading-category">{{ $whyHeader->subtitle }}</span>
        @endif
      </div>
      @if($whyHeader->title)
      <h2 class="ui-heading-title">{!! nl2br(e($whyHeader->title)) !!}</h2>
      @endif
      @if($whyHeader->content)
      <p style="color:var(--muted);margin-top:10px;">{{ $whyHeader->content }}</p>
      @endif
    </div>
    @endif

    @if($issues->isNotEmpty())
    <div class="grid grid-2" style="gap:16px;">
      @foreach($issues as $issue)
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
      @endforeach
    </div>
    @endif

    @if($advantageHeader?->title)
    <div class="route-line--full" data-reveal>
      <span class="marker">{{ $advantageHeader->title }}</span>
      <span class="dash"></span>
    </div>
    @endif

    @if($advantages->isNotEmpty())
    <div class="grid grid-3">
      @foreach($advantages as $adv)
        <div class="card" data-reveal data-reveal-delay="{{ $loop->index * 0.05 }}">
          <div class="icon-tile {{ $loop->odd ? 'blue' : '' }}">
            <i class="fa-solid {{ $adv->icon ?: 'fa-layer-group' }}"></i>
          </div>
          <h3>{{ $adv->title }}</h3>
          <p>{{ $adv->content }}</p>
        </div>
      @endforeach
    </div>
    @endif
  </div>
</section>
@endif

{{-- 04 TEAM --}}
@if($teamHeader || $team->isNotEmpty() || $teamCta)
<section id="team" style="background:var(--paper);border-top:1px solid var(--line);">
  <div class="wrap">
    @if($teamHeader)
    <div class="ui-heading-group center" data-reveal style="margin-bottom:64px;">
      <div class="ui-heading-meta">
        <span class="ui-heading-num">04</span>
        <span class="ui-heading-divider"></span>
        @if($teamHeader->subtitle)
        <span class="ui-heading-category">{{ $teamHeader->subtitle }}</span>
        @endif
      </div>
      @if($teamHeader->title)
      <h2 class="ui-heading-title">{!! nl2br(e($teamHeader->title)) !!}</h2>
      @endif
      @if($teamHeader->content)
      <p style="color:var(--muted);max-width:560px;margin:12px auto 0;font-size:1rem;">{{ $teamHeader->content }}</p>
      @endif
    </div>
    @endif

    @if($team->isNotEmpty())
    <div class="grid grid-4 team-grid-override">
      @foreach($team as $member)
        <div class="about-team-card" data-reveal>
          <div class="photo-wrap">
            @if($member->imageUrl())
              <img src="{{ $member->imageUrl() }}" alt="{{ $member->title }}">
            @else
              <img src="https://ui-avatars.com/api/?name={{ urlencode($member->title) }}&background=0c1f3d&color=fff&size=400" alt="{{ $member->title }}">
            @endif
          </div>
          <div class="body">
            @if($member->subtitle)
            <div class="dept {{ strtolower((string) $member->icon) === 'blue' ? 'blue' : '' }}">{{ $member->subtitle }}</div>
            @endif
            <h3>{{ $member->title }}</h3>
            <p>{{ $member->content }}</p>
          </div>
        </div>
      @endforeach
    </div>
    @endif

    @if($teamCta?->title)
    <div data-reveal style="text-align:center;margin-top:48px;">
      <a href="{{ $teamCta->link ?: route('website.careers') }}" class="btn-join">
        {{ $teamCta->title }} →
      </a>
    </div>
    @endif
  </div>
</section>
@endif

</div>
@endsection
