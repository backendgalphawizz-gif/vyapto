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
                        <label class="form-label">Category label</label>
                        <input type="text" name="category" class="form-control" value="{{ old('category', $product->category ?? '') }}" placeholder="Fleet Platform">
                        <small class="text-muted">Shown as “PRODUCT 01 — Fleet Platform”</small>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Subtitle / tagline</label>
                        <input type="text" name="subtitle" class="form-control" value="{{ old('subtitle', $product->subtitle ?? '') }}" placeholder="Smart Vehicle Management">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Icon (FA class)</label>
                        <input type="text" name="icon" class="form-control" value="{{ old('icon', $product->icon ?? '') }}" placeholder="fa-truck">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">External Link</label>
                        <input type="url" name="link" class="form-control" value="{{ old('link', $product->link ?? '') }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Image</label>
                        <input type="file" name="image" class="form-control" accept="image/jpeg,image/jpg,image/png,image/webp,image/avif">
                        @include('admin.website.partials.image-preview', ['record' => $product, 'fieldId' => 'product'])
                    </div>
                    <div class="col-12">
                        <label class="form-label">Short Description</label>
                        <textarea name="description" rows="3" class="form-control">{{ old('description', $product->description ?? '') }}</textarea>
                    </div>
                    <div class="col-12">
                        <label class="form-label">What's Included / Highlights</label>
                        <textarea name="features" rows="4" class="form-control" placeholder="One item per line">{{ old('features', isset($product) && is_array($product->features) ? implode("\n", $product->features) : ($product->features ?? '')) }}</textarea>
                        <small class="text-muted">One feature per line — shown as orange arrows under the description.</small>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Built for (chips)</label>
                        <textarea name="chips" rows="3" class="form-control" placeholder="One chip per line">{{ old('chips', isset($product) ? implode("\n", $product->chipList()) : '') }}</textarea>
                        <small class="text-muted">Used on VMS-style products (e.g. Logistics companies, Staffing agencies).</small>
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
