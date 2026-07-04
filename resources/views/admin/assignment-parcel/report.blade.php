@extends('layouts.admin')
@section('title', 'Assignment Reports')

@section('content')
<div class="main-section">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="fw-bold mb-0">Assignment Reports</h4>
        <div>
            <a href="{{ route('admin.assignment-parcel.export', request()->all()) }}" class="btn btn-success btn-sm rounded-3 me-2">
                <i class="bi bi-download me-1"></i> Export
            </a>
            <a href="{{ route('admin.assignment-parcel.index') }}" class="btn btn-outline-secondary btn-sm rounded-3">
                <i class="bi bi-arrow-left me-1"></i> Back
            </a>
        </div>
    </div>

    <div class="card shadow-sm rounded border mb-4">
        <div class="card-body">
            <!-- Filter Form -->
            <form method="GET" action="{{ route('admin.assignment-parcel.report') }}" class="row g-2 mb-4">
                <div class="col-md-3">
                    <input type="date" name="from_date" class="form-control form-control-sm" value="{{ request('from_date') }}" placeholder="From Date">
                </div>
                <div class="col-md-3">
                    <input type="date" name="to_date" class="form-control form-control-sm" value="{{ request('to_date') }}" placeholder="To Date">
                </div>
                <div class="col-md-3">
                    <select name="hub_id" class="form-select form-select-sm">
                        <option value="">All Hubs</option>
                        @foreach($hubs as $hub)
                        <option value="{{ $hub->id }}" {{ request('hub_id') == $hub->id ? 'selected' : '' }}>{{ $hub->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-auto">
                    <button type="submit" class="btn btn-primary btn-sm">Generate</button>
                    <a href="{{ route('admin.assignment-parcel.report') }}" class="btn btn-outline-secondary btn-sm">Reset</a>
                </div>
            </form>

            <!-- Statistics Cards -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="info-box bg-info text-white rounded p-3">
                        <div class="text-center">
                            <div class="small text-uppercase mb-1">Total Assignments</div>
                            <div class="fs-4 fw-bold">{{ number_format($totalAssignments) }}</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="info-box bg-success text-white rounded p-3">
                        <div class="text-center">
                            <div class="small text-uppercase mb-1">Total Parcels</div>
                            <div class="fs-4 fw-bold">{{ number_format($totalParcels) }}</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="info-box bg-warning text-white rounded p-3">
                        <div class="text-center">
                            <div class="small text-uppercase mb-1">Avg Parcels/Assign</div>
                            <div class="fs-4 fw-bold">{{ $totalAssignments > 0 ? number_format($totalParcels / $totalAssignments, 2) : 0 }}</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="info-box bg-primary text-white rounded p-3">
                        <div class="text-center">
                            <div class="small text-uppercase mb-1">Delivered</div>
                            <div class="fs-4 fw-bold">{{ number_format($assignmentsByStatus['delivered'] ?? 0) }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Status Distribution Chart -->
            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="card shadow-sm rounded border">
                        <div class="card-body">
                            <h6 class="fw-bold mb-3">Status Distribution</h6>
                            <canvas id="statusChart" height="300"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card shadow-sm rounded border">
                        <div class="card-body">
                            <h6 class="fw-bold mb-3">Parcels by Hub</h6>
                            <canvas id="hubChart" height="300"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Detailed Report Table -->
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
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($assignments as $assignment)
                        <tr>
                            <td class="text-center fw-bold">{{ $assignment->id }}</td>
                            <td>{{ $assignment->assignment_date ? date('d M Y', strtotime($assignment->assignment_date)) : 'N/A' }}</td>
                            <td><div class="fw-bold small">{{ $assignment->vendor->name ?? 'N/A' }}</div></td>
                            <td><div class="small">{{ $assignment->vehicle->vehicle_number ?? 'N/A' }}</div></td>
                            <td><div class="fw-bold small">{{ $assignment->user->name ?? 'N/A' }}</div></td>
                            <td><div class="small">{{ $assignment->hub->name ?? 'N/A' }}</div></td>
                            <td class="text-center"><span class="badge bg-info">{{ $assignment->parcel_quantity ?? 0 }}</span></td>
                            <td class="text-center">{!! $assignment->status_badge ?? '<span class="badge bg-secondary">N/A</span>' !!}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-4 text-muted">
                                <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                No Assignment Data Found
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Status Distribution Chart
        const statusCtx = document.getElementById('statusChart').getContext('2d');
        const statusChart = new Chart(statusCtx, {
        type: 'pie',
        data: {
            labels: {!! json_encode(array_keys($assignmentsByStatus)) !!},
            datasets: [{
                data: {!! json_encode(array_values($assignmentsByStatus)) !!},
                backgroundColor: [
                    '#ffc107', // pending - yellow
                    '#17a2b8', // assigned - teal
                    '#007bff', // in_transit - blue
                    '#28a745', // delivered - green
                    '#dc3545'  // cancelled - red
                ]
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });

    // Parcels by Hub Chart
    const hubCtx = document.getElementById('hubChart').getContext('2d');
    const hubChart = new Chart(hubCtx, {
        type: 'bar',
        data: {
            labels: {!! json_encode(array_keys($parcelsByHub)) !!},
            datasets: [{
                label: 'Number of Parcels',
                data: {!! json_encode(array_values($parcelsByHub)) !!},
                backgroundColor: '#007bff',
                borderColor: '#0056b3',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Number of Parcels'
                    }
                },
                x: {
                    title: {
                        display: true,
                        text: 'Hubs'
                    }
                }
            }
        }
    });
    });
</script>
@endpush

<style>
.info-box {
    padding: 20px;
    border-radius: 8px;
    color: white;
    text-align: center;
}
.info-box-content {
    width: 100%;
}
.info-box-number {
    font-size: 24px;
    font-weight: bold;
}
.info-box-text {
    font-size: 14px;
    text-transform: uppercase;
}
</style>
@endsection