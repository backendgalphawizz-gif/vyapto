@extends('layouts.admin')
@section('title', 'Assignment Details')

@section('content')
<div class="main-section">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="fw-bold mb-0">Assignment Details</h4>
        <div>
            <a href="{{ route('admin.assignment-parcel.index') }}" class="btn btn-outline-secondary btn-sm rounded-3 me-2">
                <i class="bi bi-arrow-left me-1"></i> Back
            </a>
            <a href="{{ route('admin.assignment-parcel.edit', $assignmentParcel) }}" class="btn btn-secondary btn-sm rounded-3">
                <i class="bi bi-pencil-square me-1"></i> Edit
            </a>
        </div>
    </div>

    <div class="card shadow-sm rounded border mb-4">
        <div class="card-body p-4">
            <div class="row g-4">
                <div class="col-md-6">
                    <div class="mb-3"><label class="small text-muted mb-0">Assignment ID</label><div class="fw-bold fs-5">#{{ $assignmentParcel->id }}</div></div>
                    <div class="mb-3"><label class="small text-muted mb-0">Assignment Date</label><div class="fw-bold">{{ $assignmentParcel->assignment_date ? date('d M, Y', strtotime($assignmentParcel->assignment_date)) : 'N/A' }}</div></div>
                    <div class="mb-3"><label class="small text-muted mb-0">Vendor</label><div class="fw-bold">{{ $assignmentParcel->vendor->name ?? 'N/A' }}<div class="small text-muted">{{ $assignmentParcel->vendor->email ?? '' }}</div></div></div>
                    <div class="mb-3"><label class="small text-muted mb-0">Vehicle</label><div class="fw-bold">{{ $assignmentParcel->vehicle->vehicle_number ?? 'N/A' }}<div class="small text-muted">Type: {{ $assignmentParcel->vehicle->type ?? 'N/A' }}</div></div></div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3"><label class="small text-muted mb-0">Hub</label><div class="fw-bold">{{ $assignmentParcel->hub->name ?? 'N/A' }}<div class="small text-muted">{{ $assignmentParcel->hub->location ?? '' }}</div></div></div>
                    <div class="mb-3"><label class="small text-muted mb-0">Parcel Quantity</label><div><span class="badge bg-info">{{ number_format($assignmentParcel->parcel_quantity) }} parcels</span></div></div>
                    <div class="mb-3"><label class="small text-muted mb-0">Status</label><div>{!! $assignmentParcel->status_badge !!}</div></div>
                    <div class="mb-3"><label class="small text-muted mb-0">Driver</label><div class="fw-bold">{{ $assignmentParcel->user->name ?? 'N/A' }}<div class="small text-muted">{{ $assignmentParcel->user->email ?? '' }}</div></div></div>
                </div>
            </div>

            @if($assignmentParcel->notes)
            <div class="border-top pt-3 mt-3 mb-3">
                <h6 class="fw-bold mb-2">Notes</h6>
                <p class="text-muted">{{ $assignmentParcel->notes }}</p>
            </div>
            @endif

            <div class="border-top pt-3 mt-3">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h6 class="fw-bold mb-0">Parcel Details</h6>
                    <span class="badge bg-light text-dark border">{{ $assignmentParcel->parcelDetails->count() }} items</span>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover table-bordered align-middle mb-0">
                        <thead class="table-light text-center">
                            <tr>
                                <th style="width: 6%;">#</th>
                                <th>Parcel ID</th>
                                <th>User</th>
                                <th>Status</th>
                                <th>Created</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($assignmentParcel->parcelDetails as $parcel)
                            <tr>
                                <td class="text-center">{{ $loop->iteration }}</td>
                                <td class="fw-semibold">{{ $parcel->parcel_id ?? ('SID-' . str_pad($parcel->id, 7, '0', STR_PAD_LEFT)) }}</td>
                                <td>
                                    <div class="fw-bold small">{{ $parcel->user->name ?? 'N/A' }}</div>
                                    <div class="small text-muted">{{ $parcel->user->email ?? '' }}</div>
                                </td>
                                <td class="text-center">{!! $parcel->status_badge !!}</td>
                                <td class="small text-muted">{{ $parcel->created_at ? $parcel->created_at->format('d M Y h:i A') : 'N/A' }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted">
                                    <i class="bi bi-inbox fs-5 me-1"></i>No parcel details found for this assignment.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="border-top pt-3 mt-2 d-flex gap-2">
                <a href="{{ route('admin.assignment-parcel.edit', $assignmentParcel) }}" class="btn btn-warning">
                    <i class="bi bi-pencil-square me-1"></i> Edit Assignment
                </a>
                <form action="{{ route('admin.assignment-parcel.destroy', $assignmentParcel) }}" method="POST" class="d-inline delete-form">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="bi bi-trash3-fill me-1"></i> Delete Assignment
                    </button>
                </form>
                <!-- <button type="button" class="btn btn-outline-secondary" onclick="window.print()">
                    <i class="bi bi-printer me-1"></i> Print
                </button> -->
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
                    title: 'Delete assignment?',
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