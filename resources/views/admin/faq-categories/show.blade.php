@extends('layouts.admin')
@section('title', 'View FAQ Category')

@section('content')
<div class="main-section">
	<div class="d-flex justify-content-between align-items-center mb-3">
		<h4 class="fw-bold mb-0">FAQ Category Details</h4>
		<a href="{{ route('admin.faq-categories.index') }}" class="btn btn-outline-secondary btn-sm rounded-3">
			<i class="bi bi-arrow-left me-1"></i> Back
		</a>
	</div>

	<div class="card shadow-sm border-0 rounded-3 mb-3">
		<div class="card-body">
			<div class="row g-3">
				<div class="col-md-6">
					<label class="small text-muted mb-1">Category Name</label>
					<div class="fw-bold">{{ $faqCategory->name }}</div>
				</div>
				<div class="col-md-6">
					<label class="small text-muted mb-1">Total FAQs</label>
					<div><span class="badge bg-info">{{ $faqCategory->faqs->count() }}</span></div>
				</div>
			</div>
		</div>
	</div>

	<div class="card shadow-sm border-0 rounded-3">
		<div class="card-header bg-light fw-semibold">FAQs Under This Category</div>
		<div class="card-body">
			<div class="table-responsive">
				<table class="table table-bordered table-hover align-middle mb-0">
					<thead class="table-light text-center">
						<tr>
							<th style="width: 8%;">ID</th>
							<th>Title</th>
							<th>Description</th>
							<th style="width: 12%;">Status</th>
							<!-- <th style="width: 12%;">Sort Order</th> -->
						</tr>
					</thead>
					<tbody>
						@forelse($faqCategory->faqs as $faq)
							<tr>
								<td class="text-center">{{ $loop->iteration }}</td>
								<td>{{ $faq->title }}</td>
								<td>{{ \Illuminate\Support\Str::limit($faq->description, 70) }}</td>
								<td class="text-center">
									@if((int) $faq->status === 1)
										<span class="badge bg-success">Active</span>
									@else
										<span class="badge bg-secondary">Inactive</span>
									@endif
								</td>
								<!-- <td class="text-center">{{ $faq->sort_order ?? 0 }}</td> -->
							</tr>
						@empty
							<tr>
								<td colspan="4" class="text-center py-4 text-muted">No FAQs available in this category.</td>
							</tr>
						@endforelse
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>
@endsection
