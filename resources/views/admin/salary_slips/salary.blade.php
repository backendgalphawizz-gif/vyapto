@extends('layouts.admin')

@section('title', 'User Salary Management')

@section('content')
<div class="container-fluid px-4">

    {{-- Page Header --}}
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h4 class="mb-0 fw-bold text-dark">
                </i>User Salary Management
            </h4>
           
        </div>
        @if($availableEmployees->isNotEmpty())
            <button class="btn btn-primary btn-rounded d-flex align-items-center gap-1" data-bs-toggle="modal" data-bs-target="#createSalaryModal">
                <i class="bi bi-plus-lg me-1"></i> Add Salary
            </button>
        @else
            <span class="btn btn-secondary disabled" title="All employees already have a salary record.">
                <i class="bi bi-plus-lg me-1"></i> Add Salary
            </span>
        @endif
    </div>

    {{-- Flash Messages --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-circle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Filter Card --}}
    <div class="card shadow-sm mb-4 border-0">
        <div class="card-body">
            <form method="GET" action="{{ route('user-salaries.index') }}" class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label class="form-label small fw-semibold text-muted">Search Employee</label>
                    <input type="text" name="search" class="form-control form-control-sm"
                           placeholder="Name or email…" value="{{ request('search') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-semibold text-muted">Employee</label>
                    <select name="user_id" class="form-select form-select-sm">
                        <option value="">All Employees</option>
                        @foreach($employees as $emp)
                            <option value="{{ $emp->id }}" {{ request('user_id') == $emp->id ? 'selected' : '' }}>
                                {{ $emp->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-semibold text-muted">Salary Type</label>
                    <select name="salary_type" class="form-select form-select-sm">
                        <option value="">All Types</option>
                        <option value="monthly"  {{ request('salary_type') == 'monthly'  ? 'selected' : '' }}>Monthly</option>
                        <option value="weekly"   {{ request('salary_type') == 'weekly'   ? 'selected' : '' }}>Weekly</option>
                        <option value="daily"    {{ request('salary_type') == 'daily'    ? 'selected' : '' }}>Daily</option>
                    </select>
                </div>
                <div class="col-md-2 d-flex gap-2">
                    <button type="submit" class="btn btn-primary btn-sm w-100">
                    Filter
                    </button>
                    <a href="{{ route('user-salaries.index') }}" class="btn btn-outline-secondary btn-sm w-100">
                     Reset
                    </a>
                </div>
            </form>
        </div>
    </div>

    {{-- Table Card --}}
    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-3">ID</th>
                            <th>Employee</th>
                            <th>Salary Amount</th>
                            <th>Salary Type</th>
                            <th>Effective From</th>
                            <th>Created At</th>
                            <th class="text-end pe-3">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($salaries as $index => $salary)
                        <tr>
                            <td class="ps-3 text-muted small">
                                {{ $salaries->firstItem() + $index }}
                            </td>
                            <td>
                                <div class="fw-semibold">{{ $salary->user->name ?? 'N/A' }}</div>
                                <div class="text-muted small">{{ $salary->user->email ?? '' }}</div>
                            </td>
                            <td class="fw-semibold text-success">
                                ₹{{ number_format($salary->salary_amount, 2) }}
                            </td>
                            <td>
                                @php
                                    $badgeClass = match($salary->salary_type) {
                                        'monthly' => 'bg-primary',
                                        'weekly'  => 'bg-info text-dark',
                                        'daily'   => 'bg-warning text-dark',
                                        default   => 'bg-secondary',
                                    };
                                @endphp
                                <span class="badge {{ $badgeClass }} text-capitalize">
                                    {{ $salary->salary_type }}
                                </span>
                            </td>
                            <td>
                                {{ $salary->effective_from ? $salary->effective_from->format('d M Y') : '—' }}
                            </td>
                            <td class="text-muted small">
                                {{ $salary->created_at->format('d M Y, h:i A') }}
                            </td>
                            <td class="text-end pe-3">
                                {{-- Generate Slip Button --}}
                                <button class="btn btn-sm btn-success me-1"
                                    data-bs-toggle="modal"
                                    data-bs-target="#generateSlipModal{{ $salary->id }}"
                                    title="Generate Salary Slip">
                                    <i class="bi bi-file-earmark-plus"></i> Slip
                                </button>
                                {{-- Edit Button --}}
                                <button class="btn btn-sm btn-secondary me-1"
                                    data-bs-toggle="modal"
                                    data-bs-target="#editSalaryModal{{ $salary->id }}"
                                    title="Edit">
                                    <i class="bi bi-pencil-square"></i>
                                </button>
                                {{-- Delete Button --}}
                                <button class="btn btn-sm btn-danger"
                                    data-bs-toggle="modal"
                                    data-bs-target="#deleteSalaryModal{{ $salary->id }}"
                                    title="Delete">
                                    <i class="bi bi-trash3"></i>
                                </button>
                            </td>
                        </tr>

                        {{-- ── Edit Modal ──────────────────────────────────────── --}}
                        <div class="modal fade" id="editSalaryModal{{ $salary->id }}" tabindex="-1"
                             aria-labelledby="editSalaryLabel{{ $salary->id }}" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content border-0 shadow">
                                    <form method="POST"
                                          action="{{ route('user-salaries.update', $salary->id) }}">
                                        @csrf
                                        @method('PUT')
                                        <div class="modal-header bg-primary text-white">
                                            <h5 class="modal-title" id="editSalaryLabel{{ $salary->id }}">
                                                <i class="bi bi-pencil-square me-2"></i>Edit Salary
                                            </h5>
                                            <button type="button" class="btn-close btn-close-white"
                                                    data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            @if($errors->{'salary_update' . $salary->id}->any())
                                                <div class="alert alert-danger small py-2">
                                                    <ul class="mb-0 ps-3">
                                                        @foreach($errors->{'salary_update' . $salary->id}->all() as $err)
                                                            <li>{{ $err }}</li>
                                                        @endforeach
                                                    </ul>
                                                </div>
                                            @endif

                                            <div class="mb-3">
                                                <label class="form-label fw-semibold">Employee <span class="text-danger">*</span></label>
                                                <select name="user_id" class="form-select" required>
                                                    <option value="">Select Employee</option>
                                                    @foreach($employees as $emp)
                                                        {{-- Show: current record's user OR employees with no salary record --}}
                                                        @if($emp->id === $salary->user_id || !in_array($emp->id, $takenUserIds))
                                                            <option value="{{ $emp->id }}"
                                                                {{ old('user_id', $salary->user_id) == $emp->id ? 'selected' : '' }}>
                                                                {{ $emp->name }} ({{ $emp->email }})
                                                                @if($emp->id === $salary->user_id) (current) @endif
                                                            </option>
                                                        @endif
                                                    @endforeach
                                                </select>
                                                <div class="form-text text-muted">Only this employee and unassigned employees are shown.</div>
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label fw-semibold">Salary Amount (₹) <span class="text-danger">*</span></label>
                                                <div class="input-group">
                                                    <span class="input-group-text">₹</span>
                                                    <input type="number" name="salary_amount" step="0.01" min="0" inputmode="decimal"
                                                           class="form-control non-negative-input"
                                                           value="{{ old('salary_amount', $salary->salary_amount) }}"
                                                           placeholder="e.g. 25000.00" required>
                                                </div>
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label fw-semibold">Salary Type <span class="text-danger">*</span></label>
                                                <select name="salary_type" class="form-select" required>
                                                    <option value="">Select Type</option>
                                                    <option value="monthly" {{ old('salary_type', $salary->salary_type) == 'monthly' ? 'selected' : '' }}>Monthly</option>
                                                    <option value="weekly"  {{ old('salary_type', $salary->salary_type) == 'weekly'  ? 'selected' : '' }}>Weekly</option>
                                                    <option value="daily"   {{ old('salary_type', $salary->salary_type) == 'daily'   ? 'selected' : '' }}>Daily</option>
                                                </select>
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label fw-semibold">Effective From</label>
                                                <input type="date" name="effective_from" class="form-control"
                                                       value="{{ old('effective_from', $salary->effective_from ? $salary->effective_from->format('Y-m-d') : '') }}">
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary"
                                                    data-bs-dismiss="modal">Cancel</button>
                                            <button type="submit" class="btn btn-primary">
                                                <i class="bi bi-floppy me-1"></i> Update Salary
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                        {{-- ── Delete Confirm Modal ────────────────────────────── --}}
                        <div class="modal fade" id="deleteSalaryModal{{ $salary->id }}" tabindex="-1"
                             aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered modal-sm">
                                <div class="modal-content border-0 shadow">
                                    <div class="modal-header bg-danger text-white">
                                        <h5 class="modal-title">
                                            <i class="bi bi-exclamation-triangle me-2"></i>Confirm Delete
                                        </h5>
                                        <button type="button" class="btn-close btn-close-white"
                                                data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body text-center py-4">
                                        <p class="mb-1">Delete salary record for</p>
                                        <p class="fw-bold mb-0">{{ $salary->user->name ?? 'this employee' }}?</p>
                                        <p class="text-muted small mt-1">This action cannot be undone.</p>
                                    </div>
                                    <div class="modal-footer justify-content-center">
                                        <button type="button" class="btn btn-secondary btn-sm"
                                                data-bs-dismiss="modal">Cancel</button>
                                        <form method="POST"
                                              action="{{ route('user-salaries.destroy', $salary->id) }}">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-primary btn-sm">
                                                <i class="bi bi-trash3 me-1"></i> Delete
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- ── Generate Salary Slip Modal ─────────────────────── --}}
                        <div class="modal fade" id="generateSlipModal{{ $salary->id }}" tabindex="-1"
                             aria-labelledby="generateSlipLabel{{ $salary->id }}" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered modal-xl">
                                <div class="modal-content border-0 shadow">
                                    <form method="POST"
                                          action="{{ route('user-salaries.generate-slip', $salary->id) }}">
                                        @csrf
                                        <div class="modal-header bg-success text-white">
                                            <h5 class="modal-title" id="generateSlipLabel{{ $salary->id }}">
                                                <i class="bi bi-file-earmark-plus me-2"></i>
                                                Generate Salary Slip — {{ $salary->user->name ?? 'N/A' }}
                                            </h5>
                                            <button type="button" class="btn-close btn-close-white"
                                                    data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">

                                            @if($errors->{'generate_slip_' . $salary->id}->any())
                                                <div class="alert alert-danger small py-2">
                                                    <ul class="mb-0 ps-3">
                                                        @foreach($errors->{'generate_slip_' . $salary->id}->all() as $err)
                                                            <li>{{ $err }}</li>
                                                        @endforeach
                                                    </ul>
                                                </div>
                                            @endif

                                            {{-- Employee + Period --}}
                                            <div class="row g-3 mb-3 pb-3 border-bottom">
                                                <div class="col-md-4">
                                                    <label class="form-label fw-semibold small text-muted text-uppercase">Employee</label>
                                                    <input type="text" class="form-control" value="{{ $salary->user->name ?? 'N/A' }}" readonly>
                                                </div>
                                                <div class="col-md-2">
                                                    <label class="form-label fw-semibold small text-muted text-uppercase">Basic Salary (₹)</label>
                                                    <input type="text" class="form-control fw-bold text-success"
                                                           value="₹{{ number_format($salary->salary_amount, 2) }}" readonly>
                                                </div>
                                                <div class="col-md-3">
                                                    <label class="form-label fw-semibold small text-muted text-uppercase">Month <span class="text-danger">*</span></label>
                                                    <select name="month" class="form-select" required>
                                                        <option value="">Select Month</option>
                                                        @foreach(range(1,12) as $m)
                                                            <option value="{{ $m }}" {{ old('month', date('n')) == $m ? 'selected' : '' }}>
                                                                {{ date('F', mktime(0,0,0,$m,1)) }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="col-md-3">
                                                    <label class="form-label fw-semibold small text-muted text-uppercase">Year <span class="text-danger">*</span></label>
                                                    <select name="year" class="form-select" required>
                                                        @foreach(range(date('Y'), 2020) as $y)
                                                            <option value="{{ $y }}" {{ old('year', date('Y')) == $y ? 'selected' : '' }}>{{ $y }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>

                                            {{-- Salary Information Fields --}}
                                            <h6 class="fw-bold text-muted text-uppercase mb-3" style="font-size:0.75rem; letter-spacing:0.05em;">
                                                <i class="bi bi-bar-chart-line me-1"></i> Salary Information
                                            </h6>

                                            {{-- Column Headers --}}
                                            <div class="row g-2 mb-1">
                                                <div class="col-md-6">
                                                    <div class="row g-2">
                                                        <div class="col-8"><span class="small fw-semibold text-muted">Component</span></div>
                                                        <div class="col-2 text-center"><span class="small fw-semibold text-muted">Value</span></div>
                                                        <div class="col-2 text-center"><span class="small fw-semibold text-muted">Type</span></div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="row g-2">
                                                        <div class="col-8"><span class="small fw-semibold text-muted">Component</span></div>
                                                        <div class="col-2 text-center"><span class="small fw-semibold text-muted">Value</span></div>
                                                        <div class="col-2 text-center"><span class="small fw-semibold text-muted">Type</span></div>
                                                    </div>
                                                </div>
                                            </div>

                                            @php
                                            $slipFields = [
                                                ['key'=>'pt',               'label'=>'PT (Professional Tax)'],
                                                ['key'=>'hra',              'label'=>'HRA'],
                                                ['key'=>'special_allow',    'label'=>'Special Allowance'],
                                                ['key'=>'stat_bonus',       'label'=>'Stat Bonus'],
                                                ['key'=>'perquisite',       'label'=>'Add: Perquisite & Other Income'],
                                                ['key'=>'exempt_reimburse', 'label'=>'Less: Exempt Reimbursement'],
                                                ['key'=>'deduction_10',     'label'=>'Less: Deduction U/s 10'],
                                                ['key'=>'deduction_16',     'label'=>'Less: Deduction U/s 16 (Std. Deduction)'],
                                                ['key'=>'deduction_24',     'label'=>'Less: Deduction U/s 24 (Housing Loss)'],
                                                ['key'=>'deduction_via',    'label'=>'Less: Deduction U/s Chapter VIA'],
                                            ];
                                            $chunks = array_chunk($slipFields, 5);
                                            @endphp

                                            <div class="row g-2">
                                                @foreach($chunks as $colFields)
                                                <div class="col-md-6">
                                                    @foreach($colFields as $field)
                                                    <div class="row g-2 mb-2 align-items-center">
                                                        <div class="col-8">
                                                            <label class="form-label mb-0 small">{{ $field['label'] }}</label>
                                                        </div>
                                                        <div class="col-2">
                                                            <input type="number" step="0.01" min="0" inputmode="decimal"
                                                                   name="{{ $field['key'] }}_value"
                                                                   class="form-control form-control-sm slip-value non-negative-input"
                                                                   data-field="{{ $field['key'] }}"
                                                                   data-basic="{{ $salary->salary_amount }}"
                                                                   value="{{ old($field['key'].'_value', 0) }}"
                                                                   placeholder="0">
                                                        </div>
                                                        <div class="col-2">
                                                            <select name="{{ $field['key'] }}_type"
                                                                    class="form-select form-select-sm slip-type"
                                                                    data-field="{{ $field['key'] }}">
                                                                <option value="%"     {{ old($field['key'].'_type', '%') == '%'     ? 'selected' : '' }}>%</option>
                                                                <option value="fixed" {{ old($field['key'].'_type', '%') == 'fixed' ? 'selected' : '' }}>Fixed</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    @endforeach
                                                </div>
                                                @endforeach
                                            </div>

                                            {{-- Computed Totals --}}
                                            <div class="border-top pt-3 mt-2">
                                                <h6 class="fw-bold text-muted text-uppercase mb-3" style="font-size:0.75rem; letter-spacing:0.05em;">
                                                    <i class="bi bi-calculator me-1"></i> Computed Totals
                                                </h6>
                                                <div class="row g-3">
                                                    <div class="col-md-3">
                                                        <label class="form-label small fw-semibold">Net Taxable Income (₹)</label>
                                                        <input type="number" step="0.01" min="0" inputmode="decimal" name="net_taxable_income"
                                                               id="net_taxable_{{ $salary->id }}"
                                                               class="form-control form-control-sm non-negative-input"
                                                               value="{{ old('net_taxable_income', 0) }}"
                                                               placeholder="0.00">
                                                    </div>
                                                    <div class="col-md-3">
                                                        <label class="form-label small fw-semibold">Total Tax Payable (₹)</label>
                                                        <input type="number" step="0.01" min="0" inputmode="decimal" name="total_tax_payable"
                                                               class="form-control form-control-sm non-negative-input"
                                                               value="{{ old('total_tax_payable', 0) }}"
                                                               placeholder="0.00">
                                                    </div>
                                                    <div class="col-md-3">
                                                        <label class="form-label small fw-semibold">Total Tax Recovered (₹)</label>
                                                        <input type="number" step="0.01" min="0" inputmode="decimal" name="total_tax_recovered"
                                                               class="form-control form-control-sm non-negative-input"
                                                               value="{{ old('total_tax_recovered', 0) }}"
                                                               placeholder="0.00">
                                                    </div>
                                                    <div class="col-md-3">
                                                        <label class="form-label small fw-semibold">Balance Tax Recoverable (₹)</label>
                                                        <input type="number" step="0.01" min="0" inputmode="decimal" name="balance_tax_recoverable"
                                                               class="form-control form-control-sm non-negative-input"
                                                               value="{{ old('balance_tax_recoverable', 0) }}"
                                                               placeholder="0.00">
                                                    </div>
                                                </div>
                                            </div>

                                        </div>{{-- /modal-body --}}
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary"
                                                    data-bs-dismiss="modal">Cancel</button>
                                            <button type="submit" class="btn btn-success">
                                                <i class="bi bi-file-earmark-check me-1"></i> Generate Slip
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                <i class="bi bi-inbox fs-2 d-block mb-2"></i>
                                No salary records found.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if($salaries->hasPages())
            <div class="d-flex justify-content-between align-items-center px-3 py-3 border-top">
                <div class="text-muted small">
                    Showing {{ $salaries->firstItem() }}–{{ $salaries->lastItem() }}
                    of {{ $salaries->total() }} records
                </div>
                {{ $salaries->links() }}
            </div>
            @endif
        </div>
    </div>
</div>

{{-- ════ Create Salary Modal ═══════════════════════════════════════════════ --}}
<div class="modal fade" id="createSalaryModal" tabindex="-1"
     aria-labelledby="createSalaryLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <form method="POST" action="{{ route('user-salaries.store') }}">
                @csrf
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="createSalaryLabel">
                        <i class="bi bi-plus-circle me-2"></i>Add New Salary
                    </h5>
                    <button type="button" class="btn-close btn-close-white"
                            data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    @if($errors->salary_create->any())
                        <div class="alert alert-danger small py-2">
                            <ul class="mb-0 ps-3">
                                @foreach($errors->salary_create->all() as $err)
                                    <li>{{ $err }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Employee <span class="text-danger">*</span></label>
                        <select name="user_id" class="form-select" required>
                            <option value="">Select Employee</option>
                            @foreach($availableEmployees as $emp)
                                <option value="{{ $emp->id }}"
                                    {{ old('user_id') == $emp->id ? 'selected' : '' }}>
                                    {{ $emp->name }} ({{ $emp->email }})
                                </option>
                            @endforeach
                        </select>
                        <div class="form-text text-muted">Only employees without an existing salary record are shown.</div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Salary Amount (₹) <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text">₹</span>
                            <input type="number" name="salary_amount" step="0.01" min="0" inputmode="decimal"
                                   class="form-control non-negative-input"
                                   value="{{ old('salary_amount') }}"
                                   placeholder="e.g. 25000.00" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Salary Type <span class="text-danger">*</span></label>
                        <select name="salary_type" class="form-select" required>
                            <option value="">Select Type</option>
                            <option value="monthly" {{ old('salary_type') == 'monthly' ? 'selected' : '' }}>Monthly</option>
                            <option value="weekly"  {{ old('salary_type') == 'weekly'  ? 'selected' : '' }}>Weekly</option>
                            <option value="daily"   {{ old('salary_type') == 'daily'   ? 'selected' : '' }}>Daily</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Effective From</label>
                        <input type="date" name="effective_from" class="form-control"
                               value="{{ old('effective_from') }}">
                        <div class="form-text">Leave blank if effective immediately.</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary"
                            data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-floppy me-1"></i> Save Salary
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('styles')
<style>
    .table th {
        font-size: 0.78rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        color: #6c757d;
    }
    .card { border-radius: 12px; }
</style>
@endpush

@push('scripts')
<script>
    // Auto-reopen create modal on validation errors
    @if($errors->salary_create->any())
        (new bootstrap.Modal(document.getElementById('createSalaryModal'))).show();
    @endif

    // Auto-reopen edit / generate-slip modals on validation errors
    @foreach($salaries as $salary)
        @if($errors->{'salary_update' . $salary->id}->any())
            (new bootstrap.Modal(document.getElementById('editSalaryModal{{ $salary->id }}'))).show();
        @endif
        @if($errors->{'generate_slip_' . $salary->id}->any())
            (new bootstrap.Modal(document.getElementById('generateSlipModal{{ $salary->id }}'))).show();
        @endif
    @endforeach

    // Resolve a field value: if type=% → (value/100)*basicSalary, else → value as-is
    function resolveAmount(input) {
        const modal   = input.closest('form');
        const key     = input.dataset.field;
        const typeEl  = modal.querySelector(`select[name="${key}_type"]`);
        const basic   = parseFloat(input.dataset.basic) || 0;
        const val     = parseFloat(input.value) || 0;
        return typeEl && typeEl.value === '%' ? (val / 100) * basic : val;
    }

    // Attach live-compute to every generate slip modal
    document.querySelectorAll('.slip-value, .slip-type').forEach(function(el) {
        el.addEventListener('change', computeTotals);
        el.addEventListener('input',  computeTotals);
    });

    function computeTotals(e) {
        const form = e.target.closest('form');
        if (!form) return;

        const basic = parseFloat(form.querySelector('.slip-value')?.dataset.basic) || 0;

        // Earnings added to basic
        const earningKeys  = ['hra', 'special_allow', 'stat_bonus', 'perquisite'];
        // Deductions from gross
        const deductKeys   = ['pt', 'exempt_reimburse', 'deduction_10', 'deduction_16', 'deduction_24', 'deduction_via'];

        let grossIncome = basic;
        earningKeys.forEach(function(k) {
            const inp = form.querySelector(`input[name="${k}_value"]`);
            if (inp) {
                const typeEl = form.querySelector(`select[name="${k}_type"]`);
                const v = parseFloat(inp.value) || 0;
                grossIncome += typeEl && typeEl.value === '%' ? (v / 100) * basic : v;
            }
        });

        let totalDeductions = 0;
        deductKeys.forEach(function(k) {
            const inp = form.querySelector(`input[name="${k}_value"]`);
            if (inp) {
                const typeEl = form.querySelector(`select[name="${k}_type"]`);
                const v = parseFloat(inp.value) || 0;
                totalDeductions += typeEl && typeEl.value === '%' ? (v / 100) * basic : v;
            }
        });

        const netTaxable = Math.max(0, grossIncome - totalDeductions);
        const netField = form.querySelector('input[name="net_taxable_income"]');
        if (netField) netField.value = netTaxable.toFixed(2);
    }

    // Block minus sign, scientific notation (e/E), and plus on non-negative number fields
    (function () {
        function blockBadKeys(e) {
            if (['-', 'e', 'E', '+'].indexOf(e.key) !== -1) {
                e.preventDefault();
            }
        }
        function stripNegativeOnInput(e) {
            const el = e.target;
            let v = el.value;
            if (v.indexOf('-') !== -1) {
                v = v.replace(/-/g, '');
                el.value = v === '' || v === '.' ? '' : v;
            }
        }
        function onPaste(e) {
            const t = (e.clipboardData || window.clipboardData).getData('text');
            if (/[-eE+]/.test(t)) {
                e.preventDefault();
            }
        }
        document.querySelectorAll('.non-negative-input').forEach(function (el) {
            el.addEventListener('keydown', blockBadKeys);
            el.addEventListener('input', stripNegativeOnInput);
            el.addEventListener('paste', onPaste);
        });
    })();
</script>
@endpush

@endsection
