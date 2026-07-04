@extends('layouts.admin')
@section('title', 'Attendance Report')
@section('content')

@php
    $overallPresent  = $reportRows->sum(fn($r) => $r['summary']['present']);
    $overallFullDay  = $reportRows->sum(fn($r) => $r['summary']['full_day']);
    $overallHalfDay  = $reportRows->sum(fn($r) => $r['summary']['half_day']);
    $overallAbsent   = $reportRows->sum(fn($r) => $r['summary']['absent']);
    $totalEmployees  = $reportRows->count();
@endphp

<style>
    /* ── wrapper ── */
    .ar-wrap {
        overflow: auto;
        max-height: calc(100vh - 285px);
        border-radius: 0 0 10px 10px;
    }

    /* ── table base ── */
    .ar-table {
        border-collapse: separate;
        border-spacing: 0;
        min-width: max-content;
        margin-bottom: 0;
    }
    .ar-table th,
    .ar-table td {
        font-size: 0.82rem;
        padding: 0.5rem 0.65rem;
        white-space: nowrap;
        vertical-align: middle;
        border-color: #e3e6ea !important;
    }
    .ar-table tbody tr:hover .col-id,
    .ar-table tbody tr:hover .col-name {
        background-color: #f0f4ff !important;
    }
    .ar-table tbody tr:hover td:not(.col-id):not(.col-name):not(.col-pres):not(.col-full):not(.col-half):not(.col-absent):not(.col-total) {
        background-color: #f8f9fa;
    }

    /* ── sticky thead ── */
    .ar-table thead th {
        position: sticky;
        top: 0;
        z-index: 10;
        background: #f1f4f8;
        font-weight: 700;
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: .04em;
        color: #4a5568;
        border-bottom: 2px solid #d1d9e0 !important;
    }

    /* ── fixed left ── */
    .col-id {
        position: sticky; left: 0; z-index: 11;
        background: #fff;
        box-shadow: 2px 0 6px -2px rgba(0,0,0,.08);
        min-width: 54px;
    }
    .col-name {
        position: sticky; left: 54px; z-index: 11;
        background: #fff;
        box-shadow: 2px 0 6px -2px rgba(0,0,0,.08);
        min-width: 230px;
    }
    .ar-table thead .col-id,
    .ar-table thead .col-name { background: #f1f4f8; }

    /* ── fixed right ── */
    .col-total  { position:sticky; right:0;     z-index:11; min-width:95px; box-shadow:-2px 0 6px -2px rgba(0,0,0,.08); }
    .col-absent { position:sticky; right:95px;  z-index:11; min-width:95px; box-shadow:-2px 0 6px -2px rgba(0,0,0,.08); }
    .col-half   { position:sticky; right:190px; z-index:11; min-width:95px; box-shadow:-2px 0 6px -2px rgba(0,0,0,.08); }
    .col-full   { position:sticky; right:285px; z-index:11; min-width:95px; box-shadow:-2px 0 6px -2px rgba(0,0,0,.08); }
    .col-pres   { position:sticky; right:380px; z-index:11; min-width:95px; box-shadow:-2px 0 6px -2px rgba(0,0,0,.08); }
    .ar-table thead .col-total  { background:#f1f4f8; }
    .ar-table thead .col-absent { background:#f1f4f8; }
    .ar-table thead .col-half   { background:#f1f4f8; }
    .ar-table thead .col-full   { background:#f1f4f8; }
    .ar-table thead .col-pres   { background:#f1f4f8; }

    /* ── day columns ── */
    .day-col { min-width: 112px; text-align: center; }
    .day-hdr-date { font-size: 0.95rem; font-weight: 700; line-height: 1; }
    .day-hdr-name { font-size: 0.65rem; font-weight: 500; opacity: .65; margin-top:2px; }

    /* ── status badges ── */
    .sl {
        display:inline-block; padding:3px 10px;
        border-radius:20px; font-size:0.72rem; font-weight:600;
    }
    .sl-present  { background:#d1fae5; color:#065f46; }
    .sl-fullday  { background:#dbeafe; color:#1e40af; }
    .sl-halfday  { background:#fef9c3; color:#92400e; }
    .sl-absent   { background:#fee2e2; color:#991b1b; }
    .sl-late     { background:#ede9fe; color:#5b21b6; }
    .sl-neutral  { background:#f1f5f9; color:#64748b; }

    /* ── stat cards ── */
    .sc {
        border-radius:12px; padding:10px 18px;
        display:flex; align-items:center; gap:12px;
        border:1px solid transparent; flex:1; min-width:120px;
    }
    .sc-icon {
        width:38px; height:38px; border-radius:10px;
        display:flex; align-items:center; justify-content:center;
        font-size:1rem; flex-shrink:0;
    }
    .sc-val { font-size:1.35rem; font-weight:800; line-height:1; }
    .sc-lbl { font-size:0.7rem; font-weight:500; opacity:.72; margin-top:2px; }
    .sc-emp    { background:#f5f3ff; border-color:#ddd6fe; }
    .sc-emp    .sc-icon { background:#ede9fe; color:#5b21b6; } .sc-emp    .sc-val { color:#5b21b6; }
    .sc-pres   { background:#f0fdf4; border-color:#bbf7d0; }
    .sc-pres   .sc-icon { background:#d1fae5; color:#065f46; } .sc-pres   .sc-val { color:#065f46; }
    .sc-full   { background:#eff6ff; border-color:#bfdbfe; }
    .sc-full   .sc-icon { background:#dbeafe; color:#1e40af; } .sc-full   .sc-val { color:#1e40af; }
    .sc-half   { background:#fefce8; border-color:#fde68a; }
    .sc-half   .sc-icon { background:#fef9c3; color:#92400e; } .sc-half   .sc-val { color:#92400e; }
    .sc-abs    { background:#fef2f2; border-color:#fecaca; }
    .sc-abs    .sc-icon { background:#fee2e2; color:#991b1b; } .sc-abs    .sc-val { color:#991b1b; }

</style>

<div class="main-section">

    {{-- PAGE HEADER --}}
    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
        <div>
            <h4 class="fw-bold mb-0">
                <i class="bi bi-calendar-check-fill me-2 text-primary"></i>Attendance Report
            </h4>
            <small class="text-muted">Monthly day-wise attendance; use Day + Status to list who matched on one date.</small>
        </div>
        <div class="d-flex align-items-center gap-2 flex-wrap">
            @include('partials.export-dropdown', [
                'exportRoute' => 'attendance.report',
                'exportQuery' => request()->only(['month', 'employee_id', 'filter_date', 'day_status']),
            ])
            <a href="{{ route('attendance.index') }}" class="btn btn-outline-secondary btn-sm rounded-3">
                <i class="bi bi-arrow-left me-1"></i> Back to Attendance
            </a>
        </div>
    </div>

    {{-- STAT CARDS --}}
    <div class="d-flex flex-wrap gap-2 mb-3">
        <div class="sc sc-emp">
            <div class="sc-icon"><i class="bi bi-people-fill"></i></div>
            <div><div class="sc-val">{{ $totalEmployees }}</div><div class="sc-lbl">Employees</div></div>
        </div>
        <div class="sc sc-pres">
            <div class="sc-icon"><i class="bi bi-check-circle-fill"></i></div>
            <div><div class="sc-val">{{ $overallPresent }}</div><div class="sc-lbl">Present</div></div>
        </div>
        <div class="sc sc-full">
            <div class="sc-icon"><i class="bi bi-sun-fill"></i></div>
            <div><div class="sc-val">{{ $overallFullDay }}</div><div class="sc-lbl">Full Day</div></div>
        </div>
        <div class="sc sc-half">
            <div class="sc-icon"><i class="bi bi-hourglass-split"></i></div>
            <div><div class="sc-val">{{ $overallHalfDay }}</div><div class="sc-lbl">Half Day</div></div>
        </div>
        <div class="sc sc-abs">
            <div class="sc-icon"><i class="bi bi-x-circle-fill"></i></div>
            <div><div class="sc-val">{{ $overallAbsent }}</div><div class="sc-lbl">Absent</div></div>
        </div>
    </div>

    {{-- FILTER --}}
    <form method="GET" action="{{ route('attendance.report') }}" class="row g-2 mb-3 align-items-end">
        <div class="col-md-2">
            <label class="form-label small text-muted mb-1">Month</label>
            <input type="month" name="month" value="{{ $selectedMonth }}" class="form-control" required>
        </div>
        <div class="col-md-3">
            <label class="form-label small text-muted mb-1">Employee</label>
            <select name="employee_id" class="form-select">
                <option value="">All Employees</option>
                @foreach($employeeList as $emp)
                    <option value="{{ $emp->id }}" {{ (string)$selectedEmployee === (string)$emp->id ? 'selected' : '' }}>
                        {{ $emp->name }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <label class="form-label small text-muted mb-1">Day (filter)</label>
            <input type="date" name="filter_date" value="{{ $selectedFilterDate ?? '' }}"
                min="{{ $monthDateMin }}" max="{{ $monthDateMax }}" class="form-control"
                title="Pick a day in the selected month, then a status below">
        </div>
        <div class="col-md-2">
            <label class="form-label small text-muted mb-1">Status on that day</label>
            <select name="day_status" class="form-select">
                <option value="">— Any —</option>
                <option value="present" {{ ($selectedDayStatus ?? '') === 'present' ? 'selected' : '' }}>Present</option>
                <option value="absent" {{ ($selectedDayStatus ?? '') === 'absent' ? 'selected' : '' }}>Absent</option>
                <option value="half_day" {{ ($selectedDayStatus ?? '') === 'half_day' ? 'selected' : '' }}>Half Day</option>
                <option value="late" {{ ($selectedDayStatus ?? '') === 'late' ? 'selected' : '' }}>Late</option>
            </select>
        </div>
        <div class="col-auto">
            <button type="submit" class="btn btn-primary">Search</button>
            <a href="{{ route('attendance.report') }}" class="btn btn-outline-secondary">Reset</a>
        </div>
    </form>
    @if(!empty($dayFilterSummary))
        <div class="alert alert-info py-2 px-3 mb-3 small">
            <i class="bi bi-funnel-fill me-1"></i>
            Rows match <strong>{{ $dayFilterSummary }}</strong> (full month columns still shown per employee).
        </div>
    @endif

    {{-- TABLE CARD --}}
    <div class="card shadow-sm rounded-3 border mb-4">
        <div class="card-header bg-white border-bottom d-flex align-items-center justify-content-between py-2 px-3">
            <span class="fw-semibold text-secondary small">
                <i class="bi bi-table me-1"></i>
                {{ \Carbon\Carbon::createFromFormat('Y-m', $selectedMonth)->format('F Y') }} — Monthly Attendance Matrix
            </span>
            <div class="d-flex gap-2 small">
                <span class="sl sl-present">Present</span>
                <span class="sl sl-fullday">Full Day</span>
                <span class="sl sl-halfday">Half Day</span>
                <span class="sl sl-late">Late</span>
                <span class="sl sl-absent">Absent</span>
            </div>
        </div>
        <div class="ar-wrap">
            <table class="table table-bordered table-hover align-middle mb-0 ar-table">
                <thead>
                    <tr>
                        <th class="col-id text-center">ID</th>
                        <th class="col-name text-start">Full Name</th>
                        @foreach($dateColumns as $day)
                            <th class="day-col text-center">
                                <div class="day-hdr-date">{{ $day->format('d') }}</div>
                                <div class="day-hdr-name">{{ $day->format('D') }}</div>
                            </th>
                        @endforeach
                        <th class="col-pres   text-center" style="color:#065f46;">Present</th>
                        <th class="col-full   text-center" style="color:#1e40af;">Full Day</th>
                        <th class="col-half   text-center" style="color:#92400e;">Half Day</th>
                        <th class="col-absent text-center" style="color:#991b1b;">Absent</th>
                        <th class="col-total  text-center">Total Days</th>
                    </tr>
                </thead>
                <tbody class="border-top-0">
                    @forelse($reportRows as $i => $row)
                    <tr>
                        <td class="col-id text-center text-muted">{{ $i + 1 }}</td>
                        <td class="col-name">
                            <div class="fw-semibold" style="font-size:.82rem;">{{ $row['employee']->name }}</div>
                            <div class="text-muted" style="font-size:.7rem;">{{ $row['employee']->role->name ?? '' }}</div>
                        </td>
                        @foreach($row['day_stats'] as $ds)
                            @php
                                $slCls = match($ds['label']) {
                                    'Present'  => 'sl-present',
                                    'Full Day' => 'sl-fullday',
                                    'Half Day' => 'sl-halfday',
                                    'Late'     => 'sl-late',
                                    '—'        => 'sl-neutral',
                                    default    => 'sl-absent',
                                };
                            @endphp
                            <td class="text-center" style="padding:.35rem .3rem;">
                                <span class="sl {{ $slCls }}">{{ $ds['label'] }}</span>
                            </td>
                        @endforeach
                        <td class="col-pres text-center fw-bold" style="background:#f0fdf4;color:#065f46;">{{ $row['summary']['present'] }}</td>
                        <td class="col-full text-center fw-bold" style="background:#eff6ff;color:#1e40af;">{{ $row['summary']['full_day'] }}</td>
                        <td class="col-half text-center fw-bold" style="background:#fefce8;color:#92400e;">{{ $row['summary']['half_day'] }}</td>
                        <td class="col-absent text-center fw-bold" style="background:#fef2f2;color:#991b1b;">{{ $row['summary']['absent'] }}</td>
                        <td class="col-total text-center fw-bold" style="background:#f8fafc;">{{ $row['summary']['working_days'] }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="{{ $dateColumns->count() + 7 }}" class="text-center py-5">
                            <div class="text-muted opacity-50 mb-3">
                                <i class="bi bi-calendar-x" style="font-size:3rem;"></i>
                            </div>
                            <h6 class="text-muted fw-normal">No attendance records found for selected filters.</h6>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
