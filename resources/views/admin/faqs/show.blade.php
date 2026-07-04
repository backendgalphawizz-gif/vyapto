@extends('layouts.admin')
@section('title', 'View FAQ')

@section('content')
<div class="main-section">
	<div class="d-flex justify-content-between align-items-center mb-3">
		<h4 class="fw-bold mb-0">FAQ Details</h4>
		<a href="{{ route('admin.faqs.index') }}" class="btn btn-outline-secondary btn-sm rounded-3">
			<i class="bi bi-arrow-left me-1"></i> Back
		</a>
	</div>

	<div class="card shadow-sm border-0 rounded-3">
		<div class="card-body">
			<div class="row g-3">
				<div class="col-md-6">
					<label class="small text-muted mb-1">Title</label>
					<div class="fw-semibold">{{ $faq->title }}</div>
				</div>

				<div class="col-md-3">
					<label class="small text-muted mb-1">Category</label>
					<div>{{ $faq->category->name ?? 'N/A' }}</div>
				</div>

				<div class="col-md-3">
					<label class="small text-muted mb-1">Status</label>
					<div>
						@if((int) $faq->status === 1)
							<span class="badge bg-success">Active</span>
						@else
							<span class="badge bg-secondary">Inactive</span>
						@endif
					</div>
				</div>

				<div class="col-12">
					<label class="small text-muted mb-1">Description</label>
					<div class="border rounded p-3 bg-light">{{ $faq->description }}</div>
				</div>

				<div class="col-md-3">
					<label class="small text-muted mb-1">Sort Order</label>
					<div>{{ $faq->sort_order ?? 0 }}</div>
				</div>

				<div class="col-md-9">
					<label class="small text-muted mb-1 d-block">Image</label>
					@if($faq->image)
						<img src="{{ asset('storage/' . $faq->image) }}" alt="FAQ" width="120" height="120" class="rounded border" style="object-fit: cover;">
					@elseif($faq->image_url)
						<a href="{{ $faq->image_url }}" target="_blank" class="btn btn-sm btn-outline-primary">Open Image URL</a>
					@else
						<span class="text-muted">No image available</span>
					@endif
				</div>
			</div>

			<div class="mt-3 text-end">
				<a href="{{ route('admin.faqs.edit', $faq->id) }}" class="btn btn-secondary btn-sm">
					<i class="bi bi-pencil-square me-1"></i> Edit
				</a>
			</div>
		</div>
	</div>
</div>
@endsection
