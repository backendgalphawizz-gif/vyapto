@extends('layouts.admin')
@section('title', 'FAQs')

@section('content')
<div class="main-section">
	<div class="d-flex justify-content-between align-items-center mb-3">
		<h4 class="fw-bold mb-0">FAQs</h4>
		<a href="{{ route('admin.faqs.create') }}" class="btn btn-primary rounded-3">
			<i class="bi bi-plus-circle me-1"></i> Add FAQ
		</a>
	</div>

	@if(session('success'))
		<div class="alert alert-success alert-dismissible fade show" role="alert">
			{{ session('success') }}
			<button type="button" class="btn-close" data-bs-dismiss="alert"></button>
		</div>
	@endif

	@if(session('error'))
		<div class="alert alert-danger alert-dismissible fade show" role="alert">
			{{ session('error') }}
			<button type="button" class="btn-close" data-bs-dismiss="alert"></button>
		</div>
	@endif

	<div class="card shadow-sm border-0 rounded-3">
		<div class="card-body">
			<div class="table-responsive">
				<table class="table table-hover table-bordered align-middle mb-0">
					<thead class="table-light text-center">
						<tr>
							<th style="width: 6%;">ID</th>
							<th style="width: 26%;">Title</th>
							<th style="width: 16%;">Category</th>
							<th style="width: 10%;">Status</th>
							<th style="width: 10%;">Sort</th>
							<th style="width: 12%;">Image</th>
							<th style="width: 20%;">Actions</th>
						</tr>
					</thead>
					<tbody>
						@forelse($faqs as $faq)
							<tr>
								<td class="text-center">{{ $faqs->firstItem() + $loop->index }}</td>
								<td>
									<div class="fw-semibold">{{ $faq->title }}</div>
									<div class="small text-muted">{{ \Illuminate\Support\Str::limit($faq->description, 70) }}</div>
								</td>
								<td>{{ $faq->category->name ?? 'N/A' }}</td>
								<td class="text-center">
									@if((int) $faq->status === 1)
										<span class="badge bg-success">Active</span>
									@else
										<span class="badge bg-secondary">Inactive</span>
									@endif
								</td>
								<td class="text-center">{{ $faq->sort_order ?? 0 }}</td>
								<td class="text-center">
									@if($faq->image)
										<img src="{{ asset('storage/' . $faq->image) }}" alt="FAQ" width="56" height="56" class="rounded border" style="object-fit: cover;">
									@elseif($faq->image_url)
										<a href="{{ $faq->image_url }}" target="_blank" class="btn btn-sm btn-outline-primary">View URL</a>
									@else
										<span class="text-muted">N/A</span>
									@endif
								</td>
								<td class="text-center">
									<a href="{{ route('admin.faqs.show', $faq->id) }}" class="btn btn-sm btn-info text-white p-2">
										<i class="bi bi-eye"></i>
									</a>
									<a href="{{ route('admin.faqs.edit', $faq->id) }}" class="btn btn-sm btn-secondary p-2">
										<i class="bi bi-pencil-square"></i>
									</a>
									<!-- <form action="{{ route('admin.faqs.destroy', $faq->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this FAQ?');">
										@csrf
										@method('DELETE')
										<button type="submit" class="btn btn-sm btn-danger">
											<i class="bi bi-trash3-fill"></i>
										</button>
									</form> -->
									<form action="{{ route('admin.faqs.destroy', $faq->id) }}" 
      method="POST" 
      class="d-inline delete-form">
    @csrf
    @method('DELETE')

    <button type="submit" class="btn btn-sm btn-danger p-2">
        <i class="bi bi-trash3-fill"></i>
    </button>
</form>
								</td>
							</tr>
						@empty
							<tr>
								<td colspan="7" class="text-center py-4 text-muted">No FAQs found.</td>
							</tr>
						@endforelse
					</tbody>
				</table>
			</div>

			@if(method_exists($faqs, 'links'))
				<div class="mt-3 d-flex justify-content-end">
					{{ $faqs->links() }}
				</div>
			@endif
		</div>
	</div>
</div>
@endsection


<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
document.addEventListener('submit', function(e) {
    if (e.target.classList.contains('delete-form')) {
        e.preventDefault();

        Swal.fire({
            title: 'Are you sure?',
            text: "Delete this FAQ?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#7066e0',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                e.target.submit();
            }
        });
    }
});
</script>