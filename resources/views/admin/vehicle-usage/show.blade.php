@extends('layouts.admin')
@section('title', 'Vehicle Usage Details')

@section('content')
<div class="main-section">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="fw-bold mb-0">Vehicle Usage Details #{{ $vehicleUsage->id }}</h4>
        <a href="{{ route('admin.vehicle-usage.index') }}" class="btn btn-outline-secondary btn-sm rounded-3">
            <i class="bi bi-arrow-left me-1"></i> Back to List
        </a>
    </div>

    <div class="card shadow-sm rounded border mb-4">
        <div class="card-body">
            <div class="row g-4">
                <div class="col-md-7">
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered align-middle mb-0">
                            <tbody>
                                <tr>
                                    <th style="width: 35%;">Record ID</th>
                                    <td>#{{ $vehicleUsage->id }}</td>
                                </tr>
                                <tr>
                                    <th>Vehicle Number</th>
                                    <td class="fw-bold">{{ $vehicleUsage->vehicle_number }}</td>
                                </tr>
                                <tr>
                                    <th>Driver</th>
                                    <td>{{ $vehicleUsage->user->name ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Driver Email</th>
                                    <td>{{ $vehicleUsage->user->email ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th>KMs Driven</th>
                                    <td>
                                        @if($vehicleUsage->kms)
                                        <span class="badge bg-info">{{ number_format($vehicleUsage->kms, 2) }} km</span>
                                        @else
                                        <span class="text-muted">N/A</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Usage Date</th>
                                    <td>{{ $vehicleUsage->created_at ? $vehicleUsage->created_at->format('d M Y, h:i A') : 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Last Updated</th>
                                    <td>{{ $vehicleUsage->updated_at ? $vehicleUsage->updated_at->format('d M Y, h:i A') : 'N/A' }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="col-md-5">
                    <div class="card border h-100">
                        <div class="card-header bg-light">
                            <h6 class="mb-0 fw-semibold">Vehicle Image</h6>
                        </div>
                        <div class="card-body text-center">
                            @if($vehicleUsage->image)
                            <img src="{{ asset('storage/' . $vehicleUsage->image) }}" alt="Vehicle Image" class="img-fluid rounded border" style="max-height: 320px; object-fit: cover;">
                            <div class="mt-3 d-flex justify-content-center gap-2">
                                <a href="{{ asset('storage/' . $vehicleUsage->image) }}" target="_blank" rel="noopener" class="btn btn-sm btn-info text-white">
                                    <i class="bi bi-eye me-1"></i> View Full
                                </a>
                                <a href="{{ asset('storage/' . $vehicleUsage->image) }}" download class="btn btn-sm btn-success">
                                    <i class="bi bi-download me-1"></i> Download
                                </a>
                            </div>
                            @else
                            <div class="text-muted py-4">
                                <i class="bi bi-image fs-1 d-block mb-2"></i>
                                No image available
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card-footer bg-white d-flex gap-2 justify-content-end">
            <a href="{{ route('admin.vehicle-usage.edit', $vehicleUsage) }}" class="btn btn-secondary">
                <i class="bi bi-pencil-square me-1"></i> Edit
            </a>

            <form action="{{ route('admin.vehicle-usage.destroy', $vehicleUsage) }}" method="POST" class="d-inline delete-form">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger">
                    <i class="bi bi-trash3-fill me-1"></i> Delete
                </button>
            </form>

            <!-- <button type="button" class="btn btn-outline-secondary" onclick="window.print()">
                <i class="bi bi-printer me-1"></i> Print
            </button> -->
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.delete-form').forEach(function (form) {
            form.addEventListener('submit', function (e) {
                e.preventDefault();
                Swal.fire({
                    title: 'Delete record?',
                    text: 'This action cannot be undone.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Yes, delete',
                    cancelButtonText: 'Cancel'
                }).then(function (result) {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });
    });
</script>
@endpush