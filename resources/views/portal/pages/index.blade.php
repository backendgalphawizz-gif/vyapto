@extends('layouts.portal')

@section('title', 'Policies & Pages')

@section('page_subtitle')
Read company policies, terms, and other published information.
@endsection

@section('content')
@if($pages->isEmpty())
    <div class="app-card text-center text-muted">No pages published yet.</div>
@else
    <div class="content-grid-2">
        @foreach($pages as $page)
            <a href="{{ route('portal.pages.show', $page->key) }}" class="app-card d-block text-decoration-none text-dark page-link-card">
                <div class="d-flex align-items-start gap-3">
                    <div class="page-link-icon"><i class="bi bi-file-earmark-text"></i></div>
                    <div>
                        <strong>{{ $page->title }}</strong>
                        <small class="d-block text-muted mt-1">{{ Str::limit(strip_tags($page->content), 90) }}</small>
                    </div>
                </div>
            </a>
        @endforeach
    </div>
@endif
@endsection
