@extends('layouts.admin')
@section('title', 'Assignment Parcels')

@section('content')
<div class="main-section">
    @if(session('error'))
    <div id="errorMessage" data-message="{{ session('error') }}"></div>
    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var errorHolder = document.getElementById('errorMessage');
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: errorHolder ? errorHolder.dataset.message : '',
                confirmButtonColor: '#d33'
            });
        });
    </script>
    @endpush
    @endif

    @if(session('success'))
    <div id="successMessage" data-message="{{ session('success') }}"></div>
    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var successHolder = document.getElementById('successMessage');
            Swal.fire({
                icon: 'success',
                title: 'Success',
                text: successHolder ? successHolder.dataset.message : '',
                confirmButtonColor: '#3085d6',
                timer: 2500,
                timerProgressBar: true
            });
        });
    </script>
    @endpush
    @endif

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="fw-bold mb-0">Assignment Parcels</h4>
        <div>
            <!-- <a href="{{ route('admin.assignment-parcel.report') }}" class="btn btn-outline-secondary btn-sm rounded-3 me-2">
                <i class="bi bi-graph-up me-1"></i> Report
            </a> -->
            @include('partials.export-dropdown', [
                'exportRoute' => 'admin.assignment-parcel.export',
                'exportQuery' => request()->only(['status', 'hub_id', 'from_date', 'to_date']),
            ])
            <a href="{{ route('admin.assignment-parcel.create') }}" class="btn btn-primary rounded-3">
                <i class="bi bi-plus-circle me-1"></i> New Assignment
            </a>
        </div>
    </div>

    <div class="card shadow-sm rounded border mb-4">
        <div class="card-body">
            <!-- Filter Form -->
            <form method="GET" action="{{ route('admin.assignment-parcel.index') }}" class="row g-2 mb-3">
                <div class="col-md-3">
                    <select name="status" class="form-select form-select-sm">
                        <option value="">All Status</option>
                        @foreach($statuses as $key => $value)
                        <option value="{{ $key }}" {{ request('status') == $key ? 'selected' : '' }}>{{ $value }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <select name="hub_id" class="form-select form-select-sm">
                        <option value="">All Hubs</option>
                        @foreach($hubs as $hub)
                        <option value="{{ $hub->id }}" {{ request('hub_id') == $hub->id ? 'selected' : '' }}>{{ $hub->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <input type="date" name="from_date" class="form-control form-control-sm" value="{{ request('from_date') }}">
                </div>
                <div class="col-md-2">
                    <input type="date" name="to_date" class="form-control form-control-sm" value="{{ request('to_date') }}">
                </div>
                <div class="col-auto">
                    <button type="submit" class="btn btn-primary btn-sm">Filter</button>
                    <a href="{{ route('admin.assignment-parcel.index') }}" class="btn btn-outline-secondary btn-sm">Reset</a>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-hover table-bordered align-middle mb-0">
                    <thead class="table-light text-center">
                        <tr>
                            <th style="width: 5%;">ID</th>
                            <th>Assignment Date</th>
                            <th>Vendor</th>
                            <th>Vehicle</th>
                            <th>Driver</th>
                            <th>Hub</th>
                            <th>Quantity</th>
                            <th>Status</th>
                            <th style="width: 12%;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($assignments as $assignment)
                        <tr>
                            <td class="text-center">{{ $assignments->firstItem() + $loop->index }}</td>
                            <td>{{ $assignment->assignment_date ? date('d M Y', strtotime($assignment->assignment_date)) : 'N/A' }}</td>
                            <td><div class="fw-bold small">{{ $assignment->vendor->name ?? 'N/A' }}</div></td>
                            <td><div class="small">{{ $assignment->vehicle->vehicle_number ?? 'N/A' }}</div></td>
                            <td><div class="fw-bold small">{{ $assignment->user->name ?? 'N/A' }}</div></td>
                            <td><div class="small">{{ $assignment->hub->name ?? 'N/A' }}</div></td>
                            <td class="text-center"><span class="badge bg-info">{{ $assignment->parcel_quantity ?? 0 }}</span></td>
                            <td class="text-center">{!! $assignment->status_badge ?? '<span class="badge bg-secondary">N/A</span>' !!}</td>
                            <!-- <td class="text-center">
                                <a href="{{ route('admin.assignment-parcel.show', $assignment) }}" class="btn btn-sm btn-info text-white" title="View">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('admin.assignment-parcel.edit', $assignment) }}" class="btn btn-sm btn-secondary" title="Edit">
                                    <i class="bi bi-pencil-square"></i>
                                </a>
                                <form action="{{ route('admin.assignment-parcel.destroy', $assignment) }}" method="POST" class="d-inline delete-form">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" title="Delete">
                                        <i class="bi bi-trash3-fill"></i>
                                    </button>
                                </form>
                            </td> -->
                            <td class="text-center align-middle">
    <div class="d-flex justify-content-center gap-2">
                                <a href="{{ route('admin.assignment-parcel.show', $assignment) }}" class="btn btn-sm btn-info text-white" title="View">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('admin.assignment-parcel.edit', $assignment) }}" class="btn btn-sm btn-secondary" title="Edit">
                                    <i class="bi bi-pencil-square"></i>
                                </a>
                                <form action="{{ route('admin.assignment-parcel.destroy', $assignment) }}" method="POST" class="d-inline delete-form">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" title="Delete">
                                        <i class="bi bi-trash3-fill"></i>
                                    </button>
                                </form>
    </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center py-4 text-muted">
                                <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                No Assignment Data Found
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3 d-flex justify-content-between align-items-center">
                <div class="text-muted small">
                    Showing {{ $assignments->firstItem() ?? 0 }}-{{ $assignments->lastItem() ?? 0 }} of {{ $assignments->total() }} entries
                </div>
                <div>
                    {{ $assignments->links() }}
                </div>
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
                    confirmButtonColor: '#7066e0',
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