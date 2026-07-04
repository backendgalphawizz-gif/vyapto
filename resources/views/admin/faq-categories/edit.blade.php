@extends('layouts.admin')
@section('title', 'Edit FAQ Category')

@section('content')
<div class="main-section">
	<div class="d-flex justify-content-between align-items-center mb-3">
		<h4 class="fw-bold mb-0">Edit FAQ Category</h4>
		<a href="{{ route('admin.faq-categories.index') }}" class="btn btn-outline-secondary btn-sm rounded-3">
			<i class="bi bi-arrow-left me-1"></i> Back
		</a>
	</div>

	<div class="card shadow-sm border-0 rounded-3">
		<div class="card-body">
			<form action="{{ route('admin.faq-categories.update', $faqCategory->id) }}" method="POST">
				@csrf
				@method('PUT')

				<div class="mb-3">
					<label class="form-label">Category Name <span class="text-danger">*</span></label>
					<input
						type="text"
						name="name"
						class="form-control @error('name') is-invalid @enderror"
						value="{{ old('name', $faqCategory->name) }}"
						placeholder="Enter category name"
						maxlength="255"
						required>
					@error('name')
						<div class="invalid-feedback">{{ $message }}</div>
					@enderror
				</div>

				<div class="text-end">
					<button type="submit" class="btn btn-primary">
						<i class="bi bi-check-circle me-1"></i> Update Category
					</button>
				</div>
			</form>
		</div>
	</div>
</div>
@endsection
