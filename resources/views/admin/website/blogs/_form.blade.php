<div class="main-section">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="fw-bold mb-0">{{ $blog ? 'Edit Blog' : 'Add Blog' }}</h4>
        <a href="{{ route('admin.website.blogs.index') }}" class="btn btn-outline-secondary btn-sm">Back</a>
    </div>
    <div class="card shadow-sm border-0 rounded-3">
        <div class="card-body">
            <form action="{{ $blog ? route('admin.website.blogs.update', $blog) : route('admin.website.blogs.store') }}" method="POST" enctype="multipart/form-data">
                @csrf @if($blog) @method('PUT') @endif
                <div class="row g-3">
                    <div class="col-md-8">
                        <label class="form-label">Title *</label>
                        <input type="text" name="title" class="form-control" value="{{ old('title', $blog->title ?? '') }}" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Author</label>
                        <input type="text" name="author" class="form-control" value="{{ old('author', $blog->author ?? '') }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Published At</label>
                        <input type="datetime-local" name="published_at" class="form-control" value="{{ old('published_at', isset($blog->published_at) ? $blog->published_at->format('Y-m-d\TH:i') : '') }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Featured Image</label>
                        <input type="file" name="image" class="form-control" accept="image/jpeg,image/jpg,image/png,image/webp">
                        @include('admin.website.partials.image-preview', ['record' => $blog, 'fieldId' => 'blog'])
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Sort</label>
                        <input type="number" name="sort_order" class="form-control" value="{{ old('sort_order', $blog->sort_order ?? 0) }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Active</label>
                        <div class="form-check form-switch mt-2">
                            <input type="checkbox" name="status" value="1" class="form-check-input" {{ old('status', $blog->status ?? true) ? 'checked' : '' }}>
                        </div>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Excerpt</label>
                        <textarea name="excerpt" rows="2" class="form-control">{{ old('excerpt', $blog->excerpt ?? '') }}</textarea>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Content (HTML)</label>
                        <textarea name="content" rows="8" class="form-control">{{ old('content', $blog->content ?? '') }}</textarea>
                    </div>
                </div>
                <div class="text-end mt-3"><button type="submit" class="btn btn-primary">Save</button></div>
            </form>
        </div>
    </div>
</div>
