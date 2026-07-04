<div class="main-section">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="fw-bold mb-0">{{ $product ? 'Edit Product' : 'Add Product' }}</h4>
        <a href="{{ route('admin.website.products.index') }}" class="btn btn-outline-secondary btn-sm">Back</a>
    </div>
    <div class="card shadow-sm border-0 rounded-3">
        <div class="card-body">
            <form action="{{ $product ? route('admin.website.products.update', $product) : route('admin.website.products.store') }}" method="POST" enctype="multipart/form-data">
                @csrf @if($product) @method('PUT') @endif
                <div class="row g-3">
                    <div class="col-md-8">
                        <label class="form-label">Title *</label>
                        <input type="text" name="title" class="form-control" value="{{ old('title', $product->title ?? '') }}" required>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Sort Order</label>
                        <input type="number" name="sort_order" class="form-control" value="{{ old('sort_order', $product->sort_order ?? 0) }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Status</label>
                        <div class="form-check form-switch mt-2">
                            <input type="checkbox" name="status" value="1" class="form-check-input" {{ old('status', $product->status ?? true) ? 'checked' : '' }}>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">External Link</label>
                        <input type="url" name="link" class="form-control" value="{{ old('link', $product->link ?? '') }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Image</label>
                        <input type="file" name="image" class="form-control" accept="image/jpeg,image/jpg,image/png,image/webp">
                        @include('admin.website.partials.image-preview', ['record' => $product, 'fieldId' => 'product'])
                    </div>
                    <div class="col-12">
                        <label class="form-label">Short Description</label>
                        <textarea name="description" rows="2" class="form-control">{{ old('description', $product->description ?? '') }}</textarea>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Full Content (HTML)</label>
                        <textarea name="content" rows="6" class="form-control">{{ old('content', $product->content ?? '') }}</textarea>
                    </div>
                </div>
                <div class="text-end mt-3"><button type="submit" class="btn btn-primary">Save</button></div>
            </form>
        </div>
    </div>
</div>
