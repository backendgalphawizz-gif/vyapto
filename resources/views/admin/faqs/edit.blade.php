@extends('layouts.admin')
@section('title', 'Edit FAQ')

@section('content')
<div class="main-section">
	<div class="d-flex justify-content-between align-items-center mb-3">
		<h4 class="fw-bold mb-0">Edit FAQ</h4>
		<a href="{{ route('admin.faqs.index') }}" class="btn btn-outline-secondary btn-sm rounded-3">
			<i class="bi bi-arrow-left me-1"></i> Back
		</a>
	</div>

	<div class="card shadow-sm border-0 rounded-3">
		<div class="card-body">
			<form action="{{ route('admin.faqs.update', $faq->id) }}" method="POST" enctype="multipart/form-data">
				@csrf
				@method('PUT')

				<div class="row g-3">
					<div class="col-md-6">
						<label class="form-label">Title <span class="text-danger">*</span></label>
						<input type="text" name="title" class="form-control @error('title') is-invalid @enderror" value="{{ old('title', $faq->title) }}" maxlength="255" required>
						@error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
					</div>

					<div class="col-md-3">
						<label class="form-label">Category <span class="text-danger">*</span></label>
						<select name="faq_category_id" class="form-select @error('faq_category_id') is-invalid @enderror" required>
							<option value="" disabled>Select category</option>
							@foreach($categories as $category)
								<option value="{{ $category->id }}" {{ old('faq_category_id', $faq->faq_category_id) == $category->id ? 'selected' : '' }}>
									{{ $category->name }}
								</option>
							@endforeach
						</select>
						@error('faq_category_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
					</div>

					<div class="col-md-3">
						<label class="form-label">Status</label>
						<select name="status" class="form-select @error('status') is-invalid @enderror">
							<option value="1" {{ old('status', (string) $faq->status) == '1' ? 'selected' : '' }}>Active</option>
							<option value="0" {{ old('status', (string) $faq->status) == '0' ? 'selected' : '' }}>Inactive</option>
						</select>
						@error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
					</div>

					<div class="col-12">
						<label class="form-label">Description <span class="text-danger">*</span></label>
						<textarea name="description" rows="4" class="form-control @error('description') is-invalid @enderror" required>{{ old('description', $faq->description) }}</textarea>
						@error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
					</div>

					<div class="col-md-4">
						<label class="form-label">Sort Order</label>
						<input type="number" min="0" name="sort_order" class="form-control @error('sort_order') is-invalid @enderror" value="{{ old('sort_order', $faq->sort_order ?? 0) }}">
						@error('sort_order')<div class="invalid-feedback">{{ $message }}</div>@enderror
					</div>

					<div class="col-md-4">
						<label class="form-label">Replace Image</label>
						<input type="file" name="image" accept="image/jpeg,image/jpg,image/png,image/webp" class="form-control @error('image') is-invalid @enderror">
						@error('image')<div class="invalid-feedback">{{ $message }}</div>@enderror
					</div>

					<div class="col-md-4">
						<label class="form-label">Image URL</label>
						<input type="url" name="image_url" class="form-control @error('image_url') is-invalid @enderror" value="{{ old('image_url', $faq->image_url) }}" placeholder="https://example.com/image.png">
						@error('image_url')<div class="invalid-feedback">{{ $message }}</div>@enderror
					</div>

					<div class="col-12">
						<label class="form-label d-block">Current Image</label>
						@if($faq->image)
							<img src="{{ asset('storage/' . $faq->image) }}" alt="FAQ" width="96" height="96" class="rounded border" style="object-fit: cover;">
						@elseif($faq->image_url)
							<a href="{{ $faq->image_url }}" target="_blank" class="btn btn-sm btn-outline-primary">Open Image URL</a>
						@else
							<span class="text-muted">No image available</span>
						@endif
					</div>
				</div>

				<div class="text-end mt-3">
					<button type="submit" class="btn btn-primary">
						<i class="bi bi-check-circle me-1"></i> Update FAQ
					</button>
				</div>
			</form>
		</div>
	</div>
</div>
@endsection
