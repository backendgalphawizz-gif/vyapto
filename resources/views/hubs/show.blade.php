@extends('layouts.admin')
@section('title', 'Hub Details')

@section('content')
<div class="main-section">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="fw-bold mb-0">Hub Details</h4>
        <div>
            <a href="{{ route('admin.hubs.index') }}" class="btn btn-outline-secondary btn-sm rounded-3 me-2">
                <i class="bi bi-arrow-left me-1"></i> Back
            </a>
            <a href="{{ route('admin.hubs.edit', $hub) }}" class="btn btn-secondary btn-sm rounded-3">
                <i class="bi bi-pencil-square me-1"></i> Edit
            </a>
        </div>
    </div>

    <div class="card shadow-sm rounded border mb-4">
        <div class="card-body p-4">
            <div class="row g-4">
                <div class="col-md-6">
                    <div class="mb-3"><label class="small text-muted mb-0">Hub Name</label><div class="fw-bold fs-5">{{ $hub->name }}</div></div>
                    <div class="mb-3"><label class="small text-muted mb-0">Hub ID</label><div class="fw-bold">{{ $hub->id }}</div></div>
                    <div class="mb-3"><label class="small text-muted mb-0">Address/Location</label><div class="fw-bold">{{ $hub->location ?? 'N/A' }}</div></div>
                    <div class="mb-3">
                        <label class="small text-muted mb-0">Current Status</label>
                        <div>
                            @if($hub->is_open)
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
                            <!-- @if($hub->latitude && $hub->longitude)
                            {{ $hub->latitude }}, {{ $hub->longitude }}
                            <a href="https://www.google.com/maps?q={{ $hub->latitude }},{{ $hub->longitude }}" target="_blank" class="btn btn-sm btn-outline-primary ms-2">Open Map</a>
                            @else
                            N/A
                            @endif -->
                        </div>
                    </div>
                    <div class="mb-3"><label class="small text-muted mb-0">Opening Time</label><div class="fw-bold">{{ $hub->opening_time ? date('h:i A', strtotime($hub->opening_time)) : 'N/A' }}</div></div>
                    <div class="mb-3"><label class="small text-muted mb-0">Closing Time</label><div class="fw-bold">{{ $hub->closing_time ? date('h:i A', strtotime($hub->closing_time)) : 'N/A' }}</div></div>
                    <div class="mb-3"><label class="small text-muted mb-0">Created At</label><div class="fw-bold">{{ $hub->created_at ? $hub->created_at->format('d M, Y h:i A') : 'N/A' }}</div></div>
                    <div class="mb-3"><label class="small text-muted mb-0">Last Updated</label><div class="fw-bold">{{ $hub->updated_at ? $hub->updated_at->format('d M, Y h:i A') : 'N/A' }}</div></div>
                </div>
            </div>

            <div class="border-top pt-3 mt-2 d-flex gap-2">
                <a href="{{ route('admin.hubs.edit', $hub) }}" class="btn btn-warning">
                    <i class="bi bi-pencil-square me-1"></i> Edit Hub
                </a>
                <form action="{{ route('admin.hubs.destroy', $hub) }}" method="POST" class="d-inline delete-form">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="bi bi-trash3-fill me-1"></i> Delete Hub
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
                    title: 'Delete hub?',
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