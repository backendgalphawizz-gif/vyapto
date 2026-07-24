@extends('layouts.admin')
@section('title', 'Office Details')

@section('content')
<div class="main-section">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="fw-bold mb-0">Office Details</h4>
        <div>
            <a href="{{ route('admin.offices.index') }}" class="btn btn-outline-secondary btn-sm rounded-3 me-2">
                <i class="bi bi-arrow-left me-1"></i> Back
            </a>
            <a href="{{ route('admin.offices.edit', $office) }}" class="btn btn-secondary btn-sm rounded-3">
                <i class="bi bi-pencil-square me-1"></i> Edit
            </a>
        </div>
    </div>

    <div class="card shadow-sm rounded border mb-4">
        <div class="card-body p-4">
            <div class="row g-4">
                <div class="col-md-6">
                    <div class="mb-3"><label class="small text-muted mb-0">Office Name</label><div class="fw-bold fs-5">{{ $office->name }}</div></div>
                    <div class="mb-3"><label class="small text-muted mb-0">Office ID</label><div class="fw-bold">{{ $office->id }}</div></div>
                    <div class="mb-3"><label class="small text-muted mb-0">Address/Location</label><div class="fw-bold">{{ $office->location ?? 'N/A' }}</div></div>
                    <div class="mb-3">
                        <label class="small text-muted mb-0">Current Status</label>
                        <div>
                            @if($office->is_open)
                            <span class="badge bg-success">Open</span>
                            @else
                            <span class="badge bg-danger">Closed</span>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="small text-muted mb-0">Coordinates</label>
                        <div class="fw-bold">
                            <!-- @if($office->latitude && $office->longitude)
                            {{ $office->latitude }}, {{ $office->longitude }}
                            <a href="https://www.google.com/maps?q={{ $office->latitude }},{{ $office->longitude }}" target="_blank" class="btn btn-sm btn-outline-primary ms-2">Open Map</a>
                            @else
                            N/A
                            @endif -->
                        </div>
                    </div>
                    <div class="mb-3"><label class="small text-muted mb-0">Opening Time</label><div class="fw-bold">{{ $office->opening_time ? date('h:i A', strtotime($office->opening_time)) : 'N/A' }}</div></div>
                    <div class="mb-3"><label class="small text-muted mb-0">Closing Time</label><div class="fw-bold">{{ $office->closing_time ? date('h:i A', strtotime($office->closing_time)) : 'N/A' }}</div></div>
                    <div class="mb-3"><label class="small text-muted mb-0">Created At</label><div class="fw-bold">{{ $office->created_at ? $office->created_at->format('d M, Y h:i A') : 'N/A' }}</div></div>
                    <div class="mb-3"><label class="small text-muted mb-0">Last Updated</label><div class="fw-bold">{{ $office->updated_at ? $office->updated_at->format('d M, Y h:i A') : 'N/A' }}</div></div>
                </div>
            </div>

            <div class="border-top pt-3 mt-2 d-flex gap-2">
                <a href="{{ route('admin.offices.edit', $office) }}" class="btn btn-warning">
                    <i class="bi bi-pencil-square me-1"></i> Edit Office
                </a>
                <form action="{{ route('admin.offices.destroy', $office) }}" method="POST" class="d-inline delete-form">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="bi bi-trash3-fill me-1"></i> Delete Office
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.delete-form').forEach(function(form) {
            form.addEventListener('submit', function(e) {
                e.preventDefault();

                Swal.fire({
                    title: 'Delete Office?',
                    text: 'This action cannot be undone.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Yes, delete',
                    cancelButtonText: 'Cancel'
                }).then(function(result) {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });
    });
</script>
@endpush