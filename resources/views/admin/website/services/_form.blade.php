<div class="main-section">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="fw-bold mb-0">{{ $service ? 'Edit Service' : 'Add Service' }}</h4>
        <a href="{{ route('admin.website.services.index') }}" class="btn btn-outline-secondary btn-sm">Back</a>
    </div>
    <div class="card shadow-sm border-0 rounded-3">
        <div class="card-body">
            <form action="{{ $service ? route('admin.website.services.update', $service) : route('admin.website.services.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @if($service) @method('PUT') @endif
                <div class="row g-3">
                    <div class="col-md-8">
                        <label class="form-label">Title *</label>
                        <input type="text" name="title" class="form-control" value="{{ old('title', $service->title ?? '') }}" required>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Sort Order</label>
                        <input type="number" name="sort_order" class="form-control" value="{{ old('sort_order', $service->sort_order ?? 0) }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Status</label>
                        <div class="form-check form-switch mt-2">
                            <input type="checkbox" name="status" value="1" class="form-check-input" {{ old('status', $service->status ?? true) ? 'checked' : '' }}>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Icon (FA class)</label>
                        <input type="text" name="icon" class="form-control" value="{{ old('icon', $service->icon ?? '') }}" placeholder="fa-truck">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Image</label>
                        <input type="file" name="image" class="form-control" accept="image/jpeg,image/jpg,image/png,image/webp">
                        @include('admin.website.partials.image-preview', ['record' => $service, 'fieldId' => 'service'])
                    </div>
                    <div class="col-12">
                        <label class="form-label">Short Description</label>
                        <textarea name="description" rows="2" class="form-control">{{ old('description', $service->description ?? '') }}</textarea>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Full Content (HTML)</label>
                        <textarea name="content" rows="6" class="form-control">{{ old('content', $service->content ?? '') }}</textarea>
                    </div>
                </div>
                <div class="text-end mt-3"><button type="submit" class="btn btn-primary">Save</button></div>
            </form>
        </div>
    </div>
</div>
