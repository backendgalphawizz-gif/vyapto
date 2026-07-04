@extends('layouts.admin')

@section('title', 'Vehicle Usage')

@section('content')
<div class="main-section">

    @if(session('success'))
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                Swal.fire({
                    icon: 'success',
                    title: 'Success',
                    text: @json(session('success')),
                    confirmButtonColor: '#3085d6',
                    timer: 3000,
                    timerProgressBar: true,
                    confirmButtonText: 'OK'
                });
            });
        </script>
    @endif

    @if(session('error'))
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: @json(session('error')),
                    confirmButtonColor: '#d33',
                    confirmButtonText: 'OK'
                });
            });
        </script>
    @endif

    @push('styles')
    @endpush

    {{-- Sticky header (match Role Management index) --}}
    <div class="sticky-top bg-white py-2 mb-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
        <h5 class="mb-0 fw-bold">Vehicle Usage</h5>
        <div class="d-flex gap-2 flex-wrap">
            <a href="{{ route('admin.vehicle-usage.today-km-summary') }}#today-km-summary"
               class="btn btn-success btn-rounded d-flex align-items-center gap-1">
                <i class="bi bi-speedometer2"></i> Today’s KM
            </a>
            <a href="{{ route('admin.vehicle-usage.today-km-summary', ['format' => 'json']) }}"
               class="btn btn-secondary btn-rounded d-flex align-items-center gap-1" target="_blank" rel="noopener noreferrer">
                <i class="bi bi-braces"></i> JSON
            </a>
            <a href="{{ route('admin.vehicle-usage.export', request()->query()) }}"
               class="btn btn-info btn-rounded d-flex align-items-center gap-1 text-white">
                <i class="bi bi-file-earmark-pdf"></i> Export PDF
            </a>
            <a href="{{ route('admin.vehicle-usage.create') }}"
               class="btn btn-primary btn-rounded d-flex align-items-center gap-1">
                <i class="bi bi-plus-circle"></i> Add record
            </a>
        </div>
    </div>

    {{-- Filters --}}
    <div class="bg-white border rounded p-3 mb-3">
        <form method="get" action="{{ route('admin.vehicle-usage.index') }}" class="row g-2 align-items-end">
            <div class="col-md-3">
                <label class="form-label small mb-0">Vehicle number</label>
                <input type="text" name="vehicle_number" class="form-control form-control-sm" value="{{ request('vehicle_number') }}" placeholder="Search…">
            </div>
            <div class="col-md-3">
                <label class="form-label small mb-0">Driver</label>
                <select name="user_id" class="form-select form-select-sm">
                    <option value="">All</option>
                    @foreach($users as $u)
                        <option value="{{ $u->id }}" @selected(request('user_id') == $u->id)>{{ $u->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label small mb-0">From</label>
                <input type="date" name="from_date" class="form-control form-control-sm" value="{{ request('from_date') }}">
            </div>
            <div class="col-md-2">
                <label class="form-label small mb-0">To</label>
                <input type="date" name="to_date" class="form-control form-control-sm" value="{{ request('to_date') }}">
            </div>
            <div class="col-md-2 d-flex gap-2">
                <button type="submit" class="btn btn-primary btn-sm btn-rounded flex-grow-1">Filter</button>
                <a href="{{ route('admin.vehicle-usage.index') }}" class="btn btn-outline-secondary btn-sm btn-rounded">Reset</a>
            </div>
        </form>
    </div>

    {{-- Today’s KM summary — same table style as Role Management --}}
    <h6 class="fw-bold mb-2">Today’s KM by driver <span class="text-muted fw-normal small">(1st &amp; 2nd entry)</span></h6>
    <div class="table-container mb-4" id="today-km-summary">
        <table class="table table-hover table-bordered mb-0 text-center">
            <thead>
                <tr>
                    <th class="text-start" style="width: 20%;">Vehicle &amp; driver</th>
                    <th style="width: 26%;">1st entry (start KM)</th>
                    <th style="width: 26%;">2nd entry (KM)</th>
                    <th style="width: 18%;">Difference</th>
                </tr>
            </thead>
            <tbody>
                @forelse($todayKmSummary ?? [] as $row)
                    <tr>
                        <td class="text-start">
                            <strong>{{ $row->vehicle_number }}</strong><br>
                            <small class="text-muted">{{ $row->user_name ?? 'User #' . ($row->user_id ?? '—') }}</small>
                        </td>
                        <td class="text-start">
                            @if($row->start_km !== null)
                                <strong>{{ number_format($row->start_km, 2) }}</strong> km<br>
                                <small class="text-muted"><i class="bi bi-clock"></i> {{ $row->first_entry_at?->format('H:i:s') ?? '—' }}</small>
                            @else
                                —
                            @endif
                        </td>
                        <td class="text-start">
                            @if($row->end_km !== null)
                                <strong>{{ number_format($row->end_km, 2) }}</strong> km<br>
                                <small class="text-muted"><i class="bi bi-clock"></i> {{ $row->second_entry_at?->format('H:i:s') ?? '—' }}</small>
                            @else
                                @if($row->start_km !== null)
                                    <span class="text-warning small">Need 2nd entry today</span>
                                @else
                                    —
                                @endif
                            @endif
                        </td>
                        <td>
                            @if($row->difference_km !== null)
                                <strong class="text-primary">{{ number_format($row->difference_km, 2) }}</strong> km
                            @else
                                —
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-muted py-4">No vehicle usage logged today yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- All records --}}
    <h6 class="fw-bold mb-2">All records</h6>
    <div class="table-container">
        <table class="table table-hover table-bordered mb-0 text-center">
            <thead>
                <tr>
                    <th style="width: 5%;">ID</th>
                    <th class="text-start">Vehicle</th>
                    <th class="text-start">Driver</th>
                    <th>KM</th>
                    <th>Image</th>
                    <th>Logged at</th>
                    <th style="width: 28%;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($vehicleUsages as $usage)
                    <tr>
                        <td>{{ $usage->id }}</td>
                        <td class="text-start">{{ $usage->vehicle_number }}</td>
                        <td class="text-start">{{ $usage->user->name ?? '—' }}</td>
                        <td>{{ $usage->kms !== null ? number_format((float) $usage->kms, 2) . ' km' : '—' }}</td>
                        <td>
                            @if($usage->image)
                                <a href="{{ $usage->image_url }}" target="_blank" rel="noopener" class="btn btn-info action-btn btn-sm text-white">
                                    <i class="fa-solid fa-image"></i> View
                                </a>
                            @else
                                —
                            @endif
                        </td>
                        <td><small class="text-muted">{{ $usage->created_at?->format('d M Y, H:i') }}</small></td>
                        <td>
                            <a href="{{ route('admin.vehicle-usage.show', $usage) }}" class="btn btn-secondary action-btn btn-sm">
                                <i class="fa-solid fa-eye"></i> View
                            </a>
                            <a href="{{ route('admin.vehicle-usage.edit', $usage) }}" class="btn btn-primary action-btn btn-sm">
                                <i class="fa-solid fa-pen-to-square"></i> Edit
                            </a>
                            <form action="{{ route('admin.vehicle-usage.destroy', $usage) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this record?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger action-btn btn-sm">
                                    <i class="fa-solid fa-trash"></i> Delete
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-muted py-4">No records match your filters.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-3 d-flex justify-content-end">
        {!! $vehicleUsages->withQueryString()->links('pagination::bootstrap-5') !!}
    </div>

</div>
@endsection
