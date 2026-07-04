@extends('layouts.admin')

@section('title', 'VYAPTO')

@section('content')
<div class="main-section">
    <div class="sticky-top bg-white py-2 mb-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
        <h5 class="mb-0 fw-bold">Dashboard Overview</h5>
        <span class="badge text-bg-light border">Updated: {{ now()->format('d M Y, h:i A') }}</span>
    </div>

    <div class="row g-3 mb-3">
        <!-- <div class="col-md-6 col-xl-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="small text-muted">Total Users</div>
                            <div class="h4 mb-0 fw-bold">{{ $userCount }}</div>
                        </div>
                        <i class="bi bi-people-fill fs-3 text-primary"></i>
                    </div>
                </div>
            </div>
        </div> -->
        <div class="col-md-6 col-xl-6">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="small text-muted">Employees</div>
                            <div class="h4 mb-0 fw-bold">{{ $employeeCount }}</div>
                        </div>
                        <i class="bi bi-person-badge-fill fs-3 text-info"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl-6">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="small text-muted">Attendance Logs</div>
                            <div class="h4 mb-0 fw-bold">{{ $attendanceCount }}</div>
                        </div>
                        <i class="bi bi-clipboard2-check-fill fs-3 text-warning"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-3">
        <div class="col-md-6 col-xl-2">
            <div class="card shadow-sm border h-100">
                <div class="card-body py-3">
                    <div class="small text-muted">Vehicles</div>
                    <div class="h5 mb-0 fw-bold">{{ $vehicleCount }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl-2">
            <div class="card shadow-sm border h-100">
                <div class="card-body py-3">
                    <div class="small text-muted">Vendors</div>
                    <div class="h5 mb-0 fw-bold">{{ $vendorCount }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl-2">
            <div class="card shadow-sm border h-100">
                <div class="card-body py-3">
                    <div class="small text-muted">Hubs</div>
                    <div class="h5 mb-0 fw-bold">{{ $hubCount }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl-2">
            <div class="card shadow-sm border h-100">
                <div class="card-body py-3">
                    <div class="small text-muted">Assignments</div>
                    <div class="h5 mb-0 fw-bold">{{ $assignmentCount }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl-2">
            <div class="card shadow-sm border h-100">
                <div class="card-body py-3">
                    <div class="small text-muted">Vehicle Usage Logs</div>
                    <div class="h5 mb-0 fw-bold">{{ $vehicleUsageCount }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl-2">
            <div class="card shadow-sm border h-100">
                <div class="card-body py-3">
                    <div class="small text-muted">Today (Usage / Assign)</div>
                    <div class="h5 mb-0 fw-bold">{{ $todayVehicleUsageCount }} / {{ $todayAssignmentCount }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-3">
        <div class="col-xl-8">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white fw-semibold">Last 7 Days Activity Trend</div>
                <div class="card-body">
                    <canvas id="activityTrendChart" height="120"></canvas>
                </div>
            </div>
        </div>
        <div class="col-xl-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white fw-semibold">Assignment Status</div>
                <div class="card-body">
                    <canvas id="assignmentStatusChart" height="240"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3 d-none">
        <div class="col-lg-6">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white fw-semibold">Recent Vehicle Usage</div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-sm table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Vehicle</th>
                                    <th>User</th>
                                    <th>KM</th>
                                    <th>Time</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentVehicleUsage as $row)
                                    <tr>
                                        <td>{{ $row->vehicle_number }}</td>
                                        <td>{{ $row->user_name }}</td>
                                        <td>{{ $row->kms !== null ? number_format((float) $row->kms, 2) : '—' }}</td>
                                        <td>{{ \Carbon\Carbon::parse($row->created_at)->format('d M, h:i A') }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="4" class="text-center text-muted py-3">No data</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white fw-semibold">Recent Assignments</div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-sm table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>ID</th>
                                    <th>Vehicle</th>
                                    <th>Parcels</th>
                                    <th>Status</th>
                                    <th>Created</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentAssignments as $row)
                                    <tr>
                                        <td>#{{ $row->id }}</td>
                                        <td>{{ $row->vehicle_number ?? '—' }}</td>
                                        <td>{{ $row->parcel_quantity ?? '—' }}</td>
                                        <td><span class="badge text-bg-light border">{{ $row->status ?: '—' }}</span></td>
                                        <td>{{ \Carbon\Carbon::parse($row->created_at)->format('d M, h:i A') }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="5" class="text-center text-muted py-3">No data</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
@if(session('emailSuccess'))
<script>
    Swal.fire({
        icon: 'success',
        title: 'Email Sender',
        text: '{{ session('emailSuccess') }}',
        confirmButtonColor: '#3085d6',
        timer: 4000,
        showConfirmButton: false
    });
</script>
@endif

<script>
document.addEventListener('DOMContentLoaded', function () {
    var labels = @json($labels);
    var usageData = @json($vehicleUsageTrend);
    var assignmentData = @json($assignmentTrend);
    var statusRows = @json($assignmentStatusData);

    var trendCtx = document.getElementById('activityTrendChart');
    if (trendCtx) {
        new Chart(trendCtx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [
                    {
                        label: 'Vehicle Usage',
                        data: usageData,
                        borderColor: '#0d6efd',
                        backgroundColor: 'rgba(13, 110, 253, 0.15)',
                        tension: 0.35,
                        fill: true
                    },
                    {
                        label: 'Assignments',
                        data: assignmentData,
                        borderColor: '#198754',
                        backgroundColor: 'rgba(25, 135, 84, 0.1)',
                        tension: 0.35,
                        fill: true
                    }
                ]
            },
            options: {
                responsive: true,
                plugins: { legend: { position: 'top' } },
                scales: { y: { beginAtZero: true, ticks: { precision: 0 } } }
            }
        });
    }

    var statusCtx = document.getElementById('assignmentStatusChart');
    if (statusCtx) {
        var statusLabels = statusRows.map(function (item) { return item.status; });
        var statusData = statusRows.map(function (item) { return item.total; });
        var statusColors = ['#0d6efd', '#198754', '#ffc107', '#dc3545', '#6f42c1', '#20c997', '#fd7e14'];

        new Chart(statusCtx, {
            type: 'doughnut',
            data: {
                labels: statusLabels.length ? statusLabels : ['No Data'],
                datasets: [{
                    data: statusData.length ? statusData : [1],
                    backgroundColor: statusColors,
                    borderWidth: 1
                }]
            },
            options: {
                plugins: {
                    legend: { position: 'bottom' }
                }
            }
        });
    }
});
</script>

@endpush