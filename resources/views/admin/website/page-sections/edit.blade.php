@extends('layouts.admin')
@section('title', 'Edit Page Section')

@section('content')
<div class="main-section">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="fw-bold mb-0">Edit: {{ $section->page }} / {{ $section->section_key }}</h4>
        <a href="{{ route('admin.website.page-sections.index') }}" class="btn btn-outline-secondary btn-sm">Back</a>
    </div>

    <div class="card shadow-sm border-0 rounded-3">
        <div class="card-body">
            <form action="{{ route('admin.website.page-sections.update', $section) }}" method="POST" enctype="multipart/form-data">
                @csrf @method('PUT')
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Title</label>
                        <input type="text" name="title" class="form-control" value="{{ old('title', $section->title) }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Subtitle</label>
                        <input type="text" name="subtitle" class="form-control" value="{{ old('subtitle', $section->subtitle) }}">
                    </div>
                    <div class="col-12">
                        <label class="form-label">Content</label>
                        <textarea name="content" rows="5" class="form-control">{{ old('content', $section->content) }}</textarea>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Section Image</label>
                        <input type="file" name="image" class="form-control" accept="image/jpeg,image/jpg,image/png,image/webp">
                        <small class="text-muted">Used as hero background, about photo, or feature image on the public site.</small>
                        @if($section->image)
                            <div class="mt-2">
                                <img src="{{ $section->imageUrl() }}" alt="Current" class="rounded border" style="max-height:120px;object-fit:cover;">
                                <div class="form-check mt-2">
                                    <input type="checkbox" name="remove_image" value="1" class="form-check-input" id="remove_image">
                                    <label class="form-check-label" for="remove_image">Remove current image</label>
                                </div>
                            </div>
                        @endif
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Icon (Font Awesome class)</label>
                        <input type="text" name="icon" class="form-control" value="{{ old('icon', $section->icon) }}" placeholder="fa-truck">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Link</label>
                        <input type="text" name="link" class="form-control" value="{{ old('link', $section->link) }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Sort Order</label>
                        <input type="number" name="sort_order" class="form-control" value="{{ old('sort_order', $section->sort_order) }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Status</label>
                        <div class="form-check form-switch mt-2">
                            <input type="checkbox" name="status" value="1" class="form-check-input" {{ old('status', $section->status) ? 'checked' : '' }}>
                            <label class="form-check-label">Active</label>
                        </div>
                    </div>
                </div>
                <div class="text-end mt-3">
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
