@extends('layouts.admin')
@section('title', 'Attendance Management')

@section('content')

<div class="main-section">

    @if(session('success'))
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            Swal.fire({
                icon: 'success',
                title: 'Success',
                text: '{{ session('success') }}',
                confirmButtonColor: '#3085d6',
                timer: 3000,
                timerProgressBar: true
            });
        });
    </script>
    @endif

    <!-- Header & Summary Counts -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="fw-bold m-0"><i class="bi bi-calendar-check me-2"></i>Attendance Report</h4>
        
        <div class="d-flex gap-2">
            <span class="badge bg-white text-success border border-success shadow-sm p-2 d-flex align-items-center gap-1">
                <i class="bi bi-check-circle-fill"></i> Present: <strong>{{ $totalPresent }}</strong>
            </span>
            <span class="badge bg-white text-danger border border-danger shadow-sm p-2 d-flex align-items-center gap-1">
                <i class="bi bi-x-circle-fill"></i> Absent: <strong>{{ $totalAbsent }}</strong>
            </span>
            <span class="badge bg-white text-warning border border-warning shadow-sm p-2 d-flex align-items-center gap-1">
                <i class="bi bi-exclamation-circle-fill"></i> Late: <strong>{{ $totalLate }}</strong>
            </span>
             <span class="badge bg-white text-info border border-info shadow-sm p-2 d-flex align-items-center gap-1">
                <i class="bi bi-clock-history"></i> Early: <strong>{{ $totalEarly }}</strong>
            </span>
        </div>
    </div>

    <!-- Filter Form -->
    <form method="POST" action="{{ route('attendance.filter') }}" class="row g-2 mb-3">
        @csrf
        <div class="col-md-2">
            <input type="date" name="from_date" value="{{ $fromDate }}" class="form-control" placeholder="From Date" title="From Date">
        </div>
        <div class="col-md-2">
            <input type="date" name="to_date" value="{{ $toDate }}" class="form-control" placeholder="To Date" title="To Date">
        </div>

        <div class="col-md-2">
            <select name="employee_id" class="form-select">
                <option value="">All Employees</option>
                @foreach($employeeList as $emp)
                    <option value="{{ $emp->id }}" {{ (isset($filterEmployeeId) && $filterEmployeeId == $emp->id) ? 'selected' : '' }}>
                        {{ $emp->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="col-md-2">
            <select name="status" class="form-select">
                <option value="">All Status</option>
                <option value="Present" {{ (isset($filterStatus) && $filterStatus == 'Present') ? 'selected' : '' }}>Present</option>
                <option value="Absent" {{ (isset($filterStatus) && $filterStatus == 'Absent') ? 'selected' : '' }}>Absent</option>
            </select>
        </div>

        <div class="col-md-2">
            <select name="exception" class="form-select">
                <option value="">All Exceptions</option>
                <option value="Late arrival" {{ (isset($filterException) && $filterException == 'Late arrival') ? 'selected' : '' }}>Late arrival</option>
                <option value="Early leave" {{ (isset($filterException) && $filterException == 'Early leave') ? 'selected' : '' }}>Early leave</option>
            </select>
        </div>

            <div class="col-auto">
            <button type="submit" class="btn btn-primary" title="Apply Filters">
                Search
            </button>
            <a href="{{ route('attendance.index') }}" class="btn btn-outline-secondary" title="Reset Filters">
                Reset
            </a>
        </div>
    </form>

    <!-- Attendance Table Card -->
    <div class="card shadow-sm rounded border mb-4">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover table-bordered align-middle mb-0">
                    <thead class="table-light text-center">
                        <tr>
                            <th class="py-3 border-bottom-0" style="width: 5%;">#</th>
                            
                            <!-- Date -->
                            <th class="py-3 border-bottom-0">
                                 <a href="{{ request()->fullUrlWithQuery([
                                     'sort_by' => 'date', 
                                     'sort_order' => request('sort_by') == 'date' && request('sort_order') == 'asc' ? 'desc' : 'asc',
                                     'filter' => request('filter')
                                 ]) }}" class="text-decoration-none text-secondary d-flex align-items-center justify-content-center gap-1">
                                    Date 
                                    @if(request('sort_by') == 'date')
                                        <i class="bi bi-sort-{{ request('sort_order') == 'asc' ? 'down' : 'up' }}"></i>
                                    @else
                                        <i class="bi bi-arrow-down-up text-muted opacity-25"></i>
                                    @endif
                                </a>
                            </th>
                            
                            <!-- Employee -->
                            <th class="py-3 border-bottom-0">
                                <a href="{{ request()->fullUrlWithQuery([
                                    'sort_by' => 'employee', 
                                    'sort_order' => request('sort_by') == 'employee' && request('sort_order') == 'asc' ? 'desc' : 'asc',
                                    'filter' => request('filter')
                                ]) }}" class="text-decoration-none text-secondary d-flex align-items-center justify-content-center gap-1">
                                    Employee
                                    @if(request('sort_by') == 'employee')
                                        <i class="bi bi-sort-{{ request('sort_order') == 'asc' ? 'down' : 'up' }}"></i>
                                    @else
                                        <i class="bi bi-arrow-down-up text-muted opacity-25"></i>
                                    @endif
                                </a>
                            </th>

                            <!-- Status -->
                            <th class="py-3 border-bottom-0">
                                <a href="{{ request()->fullUrlWithQuery([
                                    'sort_by' => 'status', 
                                    'sort_order' => request('sort_by') == 'status' && request('sort_order') == 'asc' ? 'desc' : 'asc',
                                    'filter' => request('filter')
                                ]) }}" class="text-decoration-none text-secondary d-flex align-items-center justify-content-center gap-1">
                                    Status
                                    @if(request('sort_by') == 'status')
                                        <i class="bi bi-sort-{{ request('sort_order') == 'asc' ? 'down' : 'up' }}"></i>
                                    @else
                                        <i class="bi bi-arrow-down-up text-muted opacity-25"></i>
                                    @endif
                                </a>
                            </th>

                            <!-- Punch In -->
                            <th class="py-3 border-bottom-0">
                                 <a href="{{ request()->fullUrlWithQuery([
                                     'sort_by' => 'punch_in', 
                                     'sort_order' => request('sort_by') == 'punch_in' && request('sort_order') == 'asc' ? 'desc' : 'asc',
                                     'filter' => request('filter')
                                 ]) }}" class="text-decoration-none text-secondary d-flex align-items-center justify-content-center gap-1">
                                    Punch In
                                    @if(request('sort_by') == 'punch_in')
                                        <i class="bi bi-sort-{{ request('sort_order') == 'asc' ? 'down' : 'up' }}"></i>
                                    @else
                                        <i class="bi bi-arrow-down-up text-muted opacity-25"></i>
                                    @endif
                                </a>
                            </th>

                            <!-- Punch Out -->
                            <th class="py-3 border-bottom-0">
                                 <a href="{{ request()->fullUrlWithQuery([
                                     'sort_by' => 'punch_out', 
                                     'sort_order' => request('sort_by') == 'punch_out' && request('sort_order') == 'asc' ? 'desc' : 'asc',
                                     'filter' => request('filter')
                                 ]) }}" class="text-decoration-none text-secondary d-flex align-items-center justify-content-center gap-1">
                                    Punch Out
                                    @if(request('sort_by') == 'punch_out')
                                        <i class="bi bi-sort-{{ request('sort_order') == 'asc' ? 'down' : 'up' }}"></i>
                                    @else
                                        <i class="bi bi-arrow-down-up text-muted opacity-25"></i>
                                    @endif
                                </a>
                            </th>

                            <!-- Punch In Location -->
                            <th class="py-3 border-bottom-0">
                                <span class="text-secondary d-flex align-items-center justify-content-center gap-1">In Location</span>    
                            </th>

                            <!-- Punch Out Location -->
                            <th class="py-3 border-bottom-0">
                                <span class="text-secondary d-flex align-items-center justify-content-center gap-1">Out Location</span>    
                            </th>


                            <!-- Exception -->
                            <th class="py-3 border-bottom-0">
                                 <a href="{{ request()->fullUrlWithQuery([
                                     'sort_by' => 'exception', 
                                     'sort_order' => request('sort_by') == 'exception' && request('sort_order') == 'asc' ? 'desc' : 'asc',
                                     'filter' => request('filter')
                                 ]) }}" class="text-decoration-none text-secondary d-flex align-items-center justify-content-center gap-1">
                                    Exception
                                    @if(request('sort_by') == 'exception')
                                        <i class="bi bi-sort-{{ request('sort_order') == 'asc' ? 'down' : 'up' }}"></i>
                                    @else
                                        <i class="bi bi-arrow-down-up text-muted opacity-25"></i>
                                    @endif
                                </a>
                            </th>
                            <th class="py-3 border-bottom-0" style="width: 10%;">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="border-top-0">
                        @forelse($attendances as $row)
                            @php
                                // Handle array index access since we are paginating a collection of arrays
                                $index = $loop->index;
                                $firstItem = $attendances->firstItem();
                                $serial = $firstItem ? $firstItem + $index : $index + 1;
                                $modalId = $row['id'] ? 'editAttendanceModal'.$row['id'] : 'createAttendanceModal'.$index;
                            @endphp
                        <tr>
                            <td class="text-center text-muted small">{{ $serial }}</td>
                            <td class="text-center">
                                <div class="d-flex flex-column align-items-center">
                                    <span class="fw-bold fs-6">{{ \Carbon\Carbon::parse($row['date'])->format('d M, Y') }}</span>
                                    <span class="small text-muted">{{ \Carbon\Carbon::parse($row['date'])->format('l') }}</span>
                                </div>
                            </td>
                            <td>
                                <h6 class="mb-0 text-dark small fw-bold">{{ $row['employee']['name'] }}</h6>
                                <small class="text-muted" style="font-size: 0.75rem;">{{ $row['employee']['email'] }}</small>
                            </td>
                            <td class="text-center">
                                @if($row['status'] == 'Present')
                                    <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3">Present</span>
                                @elseif($row['status'] == 'Half Day')
                                    <span class="badge bg-warning bg-opacity-10 text-warning rounded-pill px-3">Half Day</span>
                                @else
                                    <span class="badge bg-danger bg-opacity-10 text-danger rounded-pill px-3">Absent</span>
                                @endif
                            </td>
                            <td class="text-center small font-monospace text-muted">
                                {{ $row['punch_in'] ? \Carbon\Carbon::parse($row['punch_in'])->format('h:i A') : '-' }}
                            </td>
                            <td class="text-center small font-monospace text-muted">
                                {{ $row['punch_out'] ? \Carbon\Carbon::parse($row['punch_out'])->format('h:i A') : '-' }}
                            </td>
                            <td class="text-center small text-muted">{{ $row['punch_in_location'] ?? '-' }}</td>
                            <td class="text-center small text-muted">{{ $row['punch_out_location'] ?? '-' }}</td>
                            <td class="text-center">
                                @if(str_contains($row['exception'] ?? '', 'Late arrival') || ($row['punch_in_exception'] ?? '') == 'Late arrival')
                                    <span class="text-warning small fw-bold d-inline-flex align-items-center gap-1">
                                        <i class="bi bi-exclamation-triangle-fill"></i> Late
                                    </span>
                                @elseif(str_contains($row['exception'] ?? '', 'Early leave') || ($row['punch_out_exception'] ?? '') == 'Early leave')
                                    <span class="text-info small fw-bold d-inline-flex align-items-center gap-1">
                                        <i class="bi bi-box-arrow-right"></i> Early
                                    </span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>

                            <!-- <td class="text-center">
                                    <button type="button" class="btn btn-sm btn-info text-white" data-bs-toggle="modal" data-bs-target="#viewAttendanceModal{{ $row['id'] ? $row['id'] : $index }}">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                    <button type="button" class="btn btn-sm btn-secondary" data-bs-toggle="modal" data-bs-target="#{{ $modalId }}">
                                        <i class="bi bi-pencil-square"></i>
                                    </button>
                            </td> -->
                            <td class="text-center align-middle">
    <div class="d-flex justify-content-center gap-2">

        <!-- View -->
        <button type="button" 
                class="btn btn-sm btn-info text-white" 
                data-bs-toggle="modal" 
                data-bs-target="#viewAttendanceModal{{ $row['id'] ? $row['id'] : $index }}">
            <i class="bi bi-eye"></i>
        </button>

        <!-- Edit -->
        <button type="button" 
                class="btn btn-sm btn-secondary" 
                data-bs-toggle="modal" 
                data-bs-target="#{{ $modalId }}">
            <i class="bi bi-pencil-square"></i>
        </button>

    </div>
</td>
                        </tr>

                        <!-- View Modal -->
                        <div class="modal fade" id="viewAttendanceModal{{ $row['id'] ? $row['id'] : $index }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered modal-lg">
                                <div class="modal-content rounded-4 shadow-sm border-0">
                                    <div class="modal-header bg-light py-3 px-4 border-bottom">
                                        <div class="d-flex align-items-center gap-3">
                                            <div>
                                                <h5 class="modal-title fw-bold mb-0">{{ $row['employee']['name'] }}</h5>
                                                <div class="text-muted small">
                                                    <i class="bi bi-calendar3 me-1"></i> {{ \Carbon\Carbon::parse($row['date'])->format('l, d M Y') }}
                                                    <span class="mx-2">•</span>
                                                    @if($row['status'] == 'Present')
                                                        <span class="badge bg-success bg-opacity-10 text-success">Present</span>
                                                    @elseif($row['status'] == 'Half Day')
                                                        <span class="badge bg-warning bg-opacity-10 text-warning">Half Day</span>
                                                    @else
                                                        <span class="badge bg-danger bg-opacity-10 text-danger">Absent</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body p-4 bg-light">
                                        @if($row['status'] == 'Absent')
                                            <div class="text-center py-5">
                                                <div class="display-1 text-muted opacity-25 mb-3">
                                                    <i class="bi bi-person-x"></i>
                                                </div>
                                                <h5 class="text-muted fw-normal">Marked as Absent</h5>
                                                <p class="text-muted small">No punch data available for this day.</p>
                                            </div>
                                        @else
                                            <div class="row g-4">
                                                <!-- Punch In Card -->
                                                <div class="col-md-6">
                                                    <div class="card h-100 border-0 shadow-sm">
                                                        <div class="card-header bg-white border-bottom-0 pt-4 pb-0 px-4">
                                                            <div class="d-flex align-items-center gap-2 text-primary">
                                                                <i class="bi bi-box-arrow-in-right fs-4"></i>
                                                                <h6 class="fw-bold mb-0 text-uppercase letter-spacing-1">Punch In</h6>
                                                            </div>
                                                        </div>
                                                        <div class="card-body px-4 pb-4 pt-2">
                                                            <div class="display-6 fw-bold text-dark mb-1">
                                                                {{ $row['punch_in'] ? \Carbon\Carbon::parse($row['punch_in'])->format('h:i A') : '--:--' }}
                                                            </div>
                                                            @if(str_contains($row['exception'] ?? '', 'Late arrival') || ($row['punch_in_exception'] ?? '') == 'Late arrival')
                                                                <span class="badge bg-warning text-dark mb-3"><i class="bi bi-exclamation-triangle me-1"></i> Late Arrival</span>
                                                            @else
                                                                <div class="mb-3" style="min-height: 24px;"></div>
                                                            @endif
                                                            
                                                            <div class="d-flex align-items-start gap-2 text-secondary small bg-light p-2 rounded">
                                                                <i class="bi bi-geo-alt-fill mt-1 flex-shrink-0"></i>
                                                                <div>
                                                                    <div class="fw-semibold text-dark mb-1">Location</div>
                                                                    <div style="line-height: 1.4;">{{ $row['punch_in_location'] ?: 'Location not available' }}</div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Punch Out Card -->
                                                <div class="col-md-6">
                                                    <div class="card h-100 border-0 shadow-sm">
                                                        <div class="card-header bg-white border-bottom-0 pt-4 pb-0 px-4">
                                                            <div class="d-flex align-items-center gap-2 text-info">
                                                                <i class="bi bi-box-arrow-right fs-4"></i>
                                                                <h6 class="fw-bold mb-0 text-uppercase letter-spacing-1">Punch Out</h6>
                                                            </div>
                                                        </div>
                                                        <div class="card-body px-4 pb-4 pt-2">
                                                            <div class="display-6 fw-bold text-dark mb-1">
                                                                {{ $row['punch_out'] ? \Carbon\Carbon::parse($row['punch_out'])->format('h:i A') : '--:--' }}
                                                            </div>
                                                            @if(str_contains($row['exception'] ?? '', 'Early leave') || ($row['punch_out_exception'] ?? '') == 'Early leave')
                                                                <span class="badge bg-info text-dark mb-3"><i class="bi bi-info-circle me-1"></i> Early Leave</span>
                                                            @else
                                                                <div class="mb-3" style="min-height: 24px;"></div>
                                                            @endif

                                                            <div class="d-flex align-items-start gap-2 text-secondary small bg-light p-2 rounded">
                                                                <i class="bi bi-geo-alt-fill mt-1 flex-shrink-0"></i>
                                                                <div>
                                                                    <div class="fw-semibold text-dark mb-1">Location</div>
                                                                    <div style="line-height: 1.4;">{{ $row['punch_out_location'] ?: 'Location not available' }}</div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                @if(!empty($row['punch_in_exception']) || !empty($row['punch_out_exception']) || !empty($row['exception']))
                                                <div class="col-12">
                                                    <div class="alert alert-light border d-flex align-items-start gap-3 mb-0">
                                                        <i class="bi bi-info-circle-fill text-muted fs-5 mt-1"></i>
                                                        <div>
                                                            <h6 class="fw-bold mb-1">System Notes & Exceptions</h6>
                                                            <p class="mb-0 text-muted small">
                                                                {{ $row['exception'] ?: ($row['punch_in_exception'] . ' ' . $row['punch_out_exception']) }}
                                                            </p>
                                                        </div>
                                                    </div>
                                                </div>
                                                @endif
                                            </div>
                                        @endif
                                    </div>
                                    <div class="modal-footer border-top-0 bg-light">
                                        <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Close</button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Edit/Create Modal for this row -->
                        <div class="modal fade" id="{{ $modalId }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content border-primary rounded-4 shadow-sm">
                                    <div class="modal-header py-2 px-3 border-bottom-0">
                                        <h5 class="modal-title fw-bold">
                                            <i class="bi bi-pencil-square me-2"></i> 
                                            {{ $row['id'] ? 'Edit Attendance' : 'Mark Attendance' }}
                                        </h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    
                                    @if($row['id'])
                                    <form action="{{ route('attendance.update', $row['id']) }}" method="POST" class="attendance-form">
                                        @method('PUT')
                                    @else
                                    <form action="{{ route('attendance.store') }}" method="POST" class="attendance-form">
                                    @endif
                                        @csrf
                                        
                                        <input type="hidden" name="employee_id" value="{{ $row['employee']['id'] }}">
                                        <input type="hidden" name="date" value="{{ $row['date'] }}">

                                        <div class="modal-body">
                                            <div class="mb-3 text-center bg-light p-2 rounded">
                                                <small class="text-muted d-block">Employee</small>
                                                <span class="fw-bold">{{ $row['employee']['name'] }}</span>
                                                <span class="mx-2 text-muted">|</span>
                                                <small class="text-muted">Date: </small>
                                                <span class="fw-bold">{{ \Carbon\Carbon::parse($row['date'])->format('d M, Y') }}</span>
                                            </div>

                                            <div class="row g-3">
                                                <div class="col-12">
                                                    <label class="form-label small fw-bold text-muted">Status</label>
                                                    <select name="status" class="form-select" onchange="toggleAttendanceFields(this, '{{ $modalId }}')">
                                                        <option value="Present" {{ $row['status'] == 'Present' ? 'selected' : '' }}>Present</option>
                                                        <option value="Absent" {{ $row['status'] == 'Absent' ? 'selected' : '' }}>Absent</option>
                                                    </select>
                                                </div>

                                                <div id="{{ $modalId }}-fields" class="{{ $row['status'] == 'Absent' ? 'd-none' : '' }}">
                                                    <div class="row g-3">
                                                        <div class="col-6">
                                                            <label class="form-label small fw-bold text-muted">Punch In <span class="text-danger">*</span></label>
                                                            <input type="time" name="punch_in_time" class="form-control" 
                                                                value="{{ $row['punch_in'] ? \Carbon\Carbon::parse($row['punch_in'])->format('H:i') : '' }}"
                                                                {{ $row['status'] == 'Present' ? 'required' : '' }}>
                                                            <div class="invalid-feedback"></div>
                                                        </div>
                                                        <div class="col-6">
                                                            <label class="form-label small fw-bold text-muted">Punch Out</label>
                                                            <input type="time" name="punch_out_time" class="form-control" 
                                                                value="{{ $row['punch_out'] ? \Carbon\Carbon::parse($row['punch_out'])->format('H:i') : '' }}">
                                                            <div class="invalid-feedback"></div>
                                                        </div>
                                                        
                                                        <div class="col-6">
                                                            <label class="form-label small fw-bold text-muted">Punch In Location</label>
                                                            <input type="text" name="punch_in_location" class="form-control" value="{{ $row['punch_in_location'] }}" placeholder="Punch In Location">
                                                            <div class="invalid-feedback"></div>
                                                        </div>

                                                        <div class="col-6">
                                                            <label class="form-label small fw-bold text-muted">Punch Out Location</label>
                                                            <input type="text" name="punch_out_location" class="form-control" value="{{ $row['punch_out_location'] }}" placeholder="Punch Out Location">
                                                            <div class="invalid-feedback"></div>
                                                        </div>

                                                        <div class="col-12">

                                                                <label class="form-label small fw-bold text-muted">Exception</label>
                                                                <select name="exception" class="form-select">
                                                                    <option value="">None</option>
                                                                    <option value="Late arrival" {{ (str_contains($row['exception'] ?? '', 'Late arrival') || ($row['punch_in_exception'] ?? '') == 'Late arrival') ? 'selected' : '' }}>Late arrival</option>
                                                                    <option value="Early leave" {{ (str_contains($row['exception'] ?? '', 'Early leave') || ($row['punch_out_exception'] ?? '') == 'Early leave') ? 'selected' : '' }}>Early leave</option>
                                                                </select>
                                                                <div class="invalid-feedback"></div>
                                                            </div>

                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="modal-footer border-top-0">
                                            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                                            <button type="submit" class="btn btn-primary">Save Changes</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                        @empty
                        <tr>
                            <td colspan="9" class="text-center py-5">
                                <div class="text-muted opacity-50 mb-3">
                                    <i class="bi bi-calendar-x" style="font-size: 3rem;"></i>
                                </div>
                                <h6 class="text-muted">No attendance records found tailored to your filters.</h6>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="mt-3 d-flex justify-content-between align-items-center">
                    <div class="text-muted small">
                        Showing {{ $attendances->firstItem() ?? 0 }}–{{ $attendances->lastItem() ?? 0 }} of {{ $attendances->total() }} entries
                    </div>
                    @if($attendances->hasPages())
                    <div>
                        {{-- Append 'filter' parameter to pagination if it exists in current request, to maintain filters across pages --}}
                        {{ $attendances->appends(array_merge(request()->query(), request()->has('filter') ? ['filter' => 1] : []))->links() }}
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
    function toggleAttendanceFields(select, modalId) {
        const fields = document.getElementById(modalId + '-fields');
        const punchInInput = fields.querySelector('input[name="punch_in_time"]');
        
        if (select.value === 'Present') {
            fields.classList.remove('d-none');
            if(punchInInput) punchInInput.setAttribute('required', 'required');
        } else {
            fields.classList.add('d-none');
            if(punchInInput) punchInInput.removeAttribute('required');
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        const forms = document.querySelectorAll('.attendance-form');
        
        forms.forEach(form => {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                const formData = new FormData(this);
                
               
                let submitBtn = e.submitter;
                if (!submitBtn || !submitBtn.tagName) { 
                     submitBtn = this.querySelector('button[type="submit"], input[type="submit"]');
                }

             
                const hasBtn = submitBtn && (submitBtn.tagName === 'BUTTON' || submitBtn.tagName === 'INPUT');
                const originalText = hasBtn ? submitBtn.innerHTML : '';
                
                // Clear previous errors
                this.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
                this.querySelectorAll('.invalid-feedback').forEach(el => el.textContent = '');

                if (hasBtn) {
                    submitBtn.disabled = true;
                    submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Saving...';
                }

                fetch(this.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                })
                .then(async response => {
                    const isJson = response.headers.get('content-type')?.includes('application/json');
                    const data = isJson ? await response.json() : null;
                    
                    if (response.ok) {
                        return { status: response.status, body: data };
                    } else {
                        return Promise.reject({ status: response.status, body: data });
                    }
                })
                .then(({ status, body }) => {
                    if (hasBtn) {
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = originalText;
                    }

                    if (status === 200) {
                        const modalEl = this.closest('.modal');
                        const modal = bootstrap.Modal.getInstance(modalEl);
                        if(modal) modal.hide();

                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: body.success,
                            timer: 1500,
                            showConfirmButton: false
                        }).then(() => {
                            window.location.reload();
                        });
                    }
                })
                .catch(error => {
                    if (hasBtn) {
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = originalText;
                    }

                    if (error.status === 422) {
                        const errors = error.body.errors;
                        this.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
                        this.querySelectorAll('.invalid-feedback').forEach(el => {
                            el.textContent = '';
                            el.style.display = 'none';
                        });

                        for (const field in errors) {
                            const input = this.querySelector(`[name="${field}"]`);
                            if (input) {
                                input.classList.add('is-invalid');
                                
                                // Find the invalid-feedback div that is a sibling or inside the same parent container
                                let feedbackUrl = input.parentElement.querySelector('.invalid-feedback');
                                if (feedbackUrl) {
                                    feedbackUrl.textContent = errors[field][0];
                                    feedbackUrl.style.display = 'block';
                                }
                            }
                        }
                    } else {
                        console.error('Error:', error);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Something went wrong. Please try again.'
                        });
                    }
                });
            });
        });
    });
</script>
@endpush


