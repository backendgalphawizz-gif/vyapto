<div class="main-section">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="fw-bold mb-0">{{ $item ? 'Edit Career Item' : 'Add Career Item' }}</h4>
        <a href="{{ route('admin.website.careers.index') }}" class="btn btn-outline-secondary btn-sm">Back</a>
    </div>
    <div class="card shadow-sm border-0 rounded-3">
        <div class="card-body">
            <form action="{{ $item ? route('admin.website.careers.update', $item) : route('admin.website.careers.store') }}" method="POST" enctype="multipart/form-data">
                @csrf @if($item) @method('PUT') @endif
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Category *</label>
                        <select name="category" class="form-select" required>
                            @foreach($categories as $key => $label)
                                <option value="{{ $key }}" {{ old('category', $item->category ?? '') === $key ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-8">
                        <label class="form-label">Title *</label>
                        <input type="text" name="title" class="form-control" value="{{ old('title', $item->title ?? '') }}" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Department</label>
                        <input type="text" name="department" class="form-control" value="{{ old('department', $item->department ?? '') }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Location</label>
                        <input type="text" name="location" class="form-control" value="{{ old('location', $item->location ?? '') }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Published At</label>
                        <input type="datetime-local" name="published_at" class="form-control" value="{{ old('published_at', isset($item->published_at) ? $item->published_at->format('Y-m-d\TH:i') : '') }}">
                    </div>
                    <div class="col-12">
                        <label class="form-label">Excerpt</label>
                        <textarea name="excerpt" rows="2" class="form-control">{{ old('excerpt', $item->excerpt ?? '') }}</textarea>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Content (HTML)</label>
                        <textarea name="content" rows="6" class="form-control">{{ old('content', $item->content ?? '') }}</textarea>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Link</label>
                        <input type="text" name="link" class="form-control" value="{{ old('link', $item->link ?? '') }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Image</label>
                        <input type="file" name="image" class="form-control" accept="image/jpeg,image/jpg,image/png,image/webp">
                        @include('admin.website.partials.image-preview', ['record' => $item, 'fieldId' => 'career'])
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Sort</label>
                        <input type="number" name="sort_order" class="form-control" value="{{ old('sort_order', $item->sort_order ?? 0) }}">
                    </div>
                    <div class="col-md-1">
                        <label class="form-label">Active</label>
                        <div class="form-check form-switch mt-2">
                            <input type="checkbox" name="status" value="1" class="form-check-input" {{ old('status', $item->status ?? true) ? 'checked' : '' }}>
                        </div>
                    </div>
                </div>
                <div class="text-end mt-3"><button type="submit" class="btn btn-primary">Save</button></div>
            </form>
        </div>
    </div>
</div>
