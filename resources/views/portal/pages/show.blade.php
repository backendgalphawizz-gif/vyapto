@extends('layouts.portal')

@section('title', $page->title)

@section('content')
<div class="app-card" style="max-width: 900px;">
    <div class="portal-page-content">
        {!! $page->content !!}
    </div>
</div>
@endsection
