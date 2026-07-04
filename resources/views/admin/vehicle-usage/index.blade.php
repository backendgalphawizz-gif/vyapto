@extends('layouts.admin')
@section('title', 'Vehicle Usage Records')

@section('content')
<div class="main-section">
    @if(session('success'))
    <div id="usageSuccessMessage" data-message="{{ session('success') }}"></div>
    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var successHolder = document.getElementById('usageSuccessMessage');
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
        <h4 class="fw-bold mb-0">Vehicle Usage Records</h4>
        <div>
            @include('partials.export-dropdown', [
                'exportRoute' => 'admin.vehicle-usage.export',
                'exportQuery' => request()->only(['vehicle_number', 'user_id', 'from_date', 'to_date']),
            ])
            <a href="{{ route('admin.vehicle-usage.create') }}" class="btn btn-primary rounded-3">
                <i class="bi bi-plus-circle me-1"></i> Add New Record
            </a>
        </div>
    </div>

    <div class="card shadow-sm rounded border mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.vehicle-usage.index') }}" class="row g-2 mb-3">
                <div class="col-md-3">
                    <input type="text" name="vehicle_number" class="form-control form-control-sm" placeholder="Search vehicle number" value="{{ request('vehicle_number') }}">
                </div>
                <div class="col-md-3">
                    <select name="user_id" class="form-select form-select-sm">
                        <option value="">All Drivers</option>
                        @foreach($users as $user)
                        <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
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
                    <a href="{{ route('admin.vehicle-usage.index') }}" class="btn btn-outline-secondary btn-sm">Reset</a>
                </div>
            </form>

            @if(session('error'))
            <div class="alert alert-danger py-2">{{ session('error') }}</div>
            @endif

            <div class="table-responsive">
                <table class="table table-hover table-bordered align-middle mb-0">
                    <thead class="table-light text-center">
                        <tr>
                            <th style="width: 6%;">ID</th>
                            <th style="width: 10%;">Image</th>
                            <th>Vehicle Number</th>
                            <th>Driver</th>
                            <th>KMs Driven</th>
                            <th>Usage Date</th>
                            <th style="width: 14%;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($vehicleUsages as $usage)
                        <tr>
                            <td class="text-center">{{ $vehicleUsages->firstItem() + $loop->index }}</td>
                            <td class="text-center">
                                @if($usage->image)
                                <a href="{{ asset('storage/' . $usage->image) }}" target="_blank" rel="noopener">
                                    <img src="{{ asset('storage/' . $usage->image) }}" alt="Vehicle" class="rounded shadow-sm border" width="50" height="50" style="object-fit: cover;">
                                </a>
                                @else
                                <span class="text-muted small">No Image</span>
                                @endif
                            </td>
                            <td><div class="fw-bold">{{ $usage->vehicle_number }}</div></td>
                            <td>{{ $usage->user->name ?? 'N/A' }}</td>
                            <td>
                                @if($usage->kms)
                                <span class="badge bg-info">{{ number_format($usage->kms, 2) }} km</span>
                                @else
                                <span class="text-muted">N/A</span>
                                @endif
                            </td>
                            <td>{{ $usage->created_at ? $usage->created_at->format('d M Y, h:i A') : 'N/A' }}</td>
                            <td class="text-center">
                                <a href="{{ route('admin.vehicle-usage.show', $usage) }}" class="btn btn-sm btn-info text-white" title="View">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('admin.vehicle-usage.edit', $usage) }}" class="btn btn-sm btn-secondary" title="Edit">
                                    <i class="bi bi-pencil-square"></i>
                                </a>
                                <form action="{{ route('admin.vehicle-usage.destroy', $usage) }}" method="POST" class="d-inline delete-form">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" title="Delete">
                                        <i class="bi bi-trash3-fill"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">
                                <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                No vehicle usage records found
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3 d-flex justify-content-between align-items-center">
                <div class="text-muted small">
                    Showing {{ $vehicleUsages->firstItem() ?? 0 }}-{{ $vehicleUsages->lastItem() ?? 0 }} of {{ $vehicleUsages->total() ?? 0 }} entries
                </div>
                <div>
                    {{ $vehicleUsages->links() }}
                </div>
            </div>
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
                    confirmButtonColor: '#7066e0',
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