@extends('layouts.admin')
@section('title', 'Add Page Section')

@section('content')
<div class="main-section">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="fw-bold mb-0">Add Page Section</h4>
        <a href="{{ route('admin.website.page-sections.index') }}" class="btn btn-outline-secondary btn-sm">Back</a>
    </div>

    <div class="card shadow-sm border-0 rounded-3">
        <div class="card-body">
            <form action="{{ route('admin.website.page-sections.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Page <span class="text-danger">*</span></label>
                        <select name="page" class="form-select @error('page') is-invalid @enderror" required>
                            @foreach($pages as $p)
                                <option value="{{ $p }}" {{ old('page') === $p ? 'selected' : '' }}>{{ ucfirst($p) }}</option>
                            @endforeach
                        </select>
                        @error('page')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Section Key <span class="text-danger">*</span></label>
                        <input type="text" name="section_key" class="form-control @error('section_key') is-invalid @enderror" value="{{ old('section_key') }}" placeholder="my_custom_section" required>
                        <small class="text-muted">Lowercase letters, numbers, underscores only.</small>
                        @error('section_key')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Sort Order</label>
                        <input type="number" name="sort_order" class="form-control" value="{{ old('sort_order', 0) }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Title</label>
                        <input type="text" name="title" class="form-control" value="{{ old('title') }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Subtitle</label>
                        <input type="text" name="subtitle" class="form-control" value="{{ old('subtitle') }}">
                    </div>
                    <div class="col-12">
                        <label class="form-label">Content</label>
                        <textarea name="content" rows="4" class="form-control">{{ old('content') }}</textarea>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Image</label>
                        <input type="file" name="image" class="form-control" accept="image/jpeg,image/jpg,image/png,image/webp,image/avif">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Icon</label>
                        <input type="text" name="icon" class="form-control" value="{{ old('icon') }}" placeholder="fa-truck">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Link URL</label>
                        <input type="text" name="link" class="form-control" value="{{ old('link') }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Status</label>
                        <div class="form-check form-switch mt-2">
                            <input type="checkbox" name="status" value="1" class="form-check-input" checked>
                            <label class="form-check-label">Active</label>
                        </div>
                    </div>
                </div>
                <div class="text-end mt-3">
                    <button type="submit" class="btn btn-primary">Create Section</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
