@extends('layouts.admin')

@section('title', 'Salary Slips Management')

@section('content')



<div class="main-section" style="margin: 12px 40px;">
  


    <!-- Success Message -->

    @if(session('success'))

        @push('scripts')

        <script>

            document.addEventListener('DOMContentLoaded', function () {

                Swal.fire({

                    icon: 'success',

                    title: 'Success',

                    text: '{{ session('success') }}',

                    confirmButtonColor: '#3085d6',

                    timer: 3000,

                    timerProgressBar: true,

                    confirmButtonText: 'OK'

                });

            });

        </script>

        @endpush

    @endif

    

    @if(session('error'))

        @push('scripts')

        <script>

            document.addEventListener('DOMContentLoaded', function () {

                Swal.fire({

                    icon: 'error',

                    title: 'Error',

                    text: '{{ session('error') }}',

                    confirmButtonColor: '#d33',

                    confirmButtonText: 'OK'

                });

            });

        </script>

        @endpush

    @endif



    <!-- Header -->

    <div class="d-flex justify-content-between align-items-center mb-3">

        <h4 class="fw-bold m-0"><i class="bi bi-file-earmark-text me-2"></i>Salary Slips</h4>

        <div>

            @include('partials.export-dropdown', [
                'exportRoute' => 'salary-slips.index',
                'exportQuery' => request()->except('page'),
            ])

            <!-- <button type="button" class="btn btn-warning btn-sm rounded-3 me-2" title="Generate Salary">
                <i class="bi bi-gear-fill me-1"></i> Generate Salary
            </button> -->

            <!-- <button class="btn btn-primary btn-sm rounded-3" data-bs-toggle="modal" data-bs-target="#addSlipModal" title="Add New Salary Slip">

                <i class="bi bi-plus-lg me-1"></i> Add Slip

            </button> -->

        </div>

    </div>



    <!-- Filter Form -->

    <form method="GET" action="{{ route('salary-slips.index') }}" class="row g-2 mb-3">



       <div class="col-md-6">

            <input type="text" name="search" value="{{ request('search') }}"

                class="form-control"

                placeholder="Search..." title="Search by Name/Email">

        </div>



        <div class="col-md-2">

            <select name="employee_id" class="form-select" title="Filter by Employee">

                <option value="">All Employees</option>

                @foreach($employees as $emp)

                    <option value="{{ $emp->id }}" {{ request('employee_id') == $emp->id ? 'selected' : '' }}>

                        {{ $emp->name }}

                    </option>

                @endforeach

            </select>

        </div>



        <div class="col-md-2">

            <select name="month" class="form-select" title="Filter by Month">

                <option value="">All Months</option>

                @for($m=1; $m<=12; $m++)

                    <option value="{{ $m }}" {{ request('month') == $m ? 'selected' : '' }}>

                        {{ date('F', mktime(0, 0, 0, $m, 10)) }}

                    </option>

                @endfor

            </select>

        </div>



        <div class="col-md-2">

            <select name="year" class="form-select" title="Filter by Year">

                <option value="">All Years</option>

                @for($y=date('Y'); $y>=2020; $y--)

                    <option value="{{ $y }}" {{ request('year') == $y ? 'selected' : '' }}>

                        {{ $y }}

                    </option>

                @endfor

            </select>

        </div>



        <div class="col-md-3">

            <input type="date" name="from_date" value="{{ request('from_date') }}" class="form-control" placeholder="From Date" title="From Date">

        </div>

        <div class="col-md-3">

            <input type="date" name="to_date" value="{{ request('to_date') }}" class="form-control" placeholder="To Date" title="To Date">

        </div>



        <div class="col-auto">

             <button class="btn btn-primary" title="Apply Filters">Search</button>

             <a href="{{ route('salary-slips.index') }}" class="btn btn-outline-secondary" title="Reset Filters">Reset</a>

        </div>

    </form>

    

    <!-- Table Card -->

    <div class="card shadow-sm rounded border mb-4">

        <div class="card-body">

            <div class="table-responsive">

                <table class="table table-hover table-bordered align-middle mb-0">

                    <thead class="table-light text-center">

                        <tr>

                            <th class="py-3 border-bottom-0" style="width: 5%;">ID</th>

                            <th class="py-3 border-bottom-0">Employee</th>

                            <th class="py-3 border-bottom-0">Month/Year</th>

                            <th class="py-3 border-bottom-0">File</th>

                            <th class="py-3 border-bottom-0">Uploaded At</th>

                            <th class="py-3 border-bottom-0" style="width: 10%;">Actions</th>

                        </tr>

                    </thead>

                    <tbody class="border-top-0">

                        @forelse($salarySlips as $slip)

                        <tr>

                            <td class="text-center text-muted small">{{ $loop->iteration + ($salarySlips->currentPage() - 1) * $salarySlips->perPage() }}</td>

                            <td>

                                @if($slip->employee)

                                <div class="d-flex align-items-center">

                                    <div class="ms-2">

                                        <div class="fw-bold">{{ $slip->employee->name }}</div>

                                        <div class="text-muted small">{{ $slip->employee->email }}</div>

                                    </div>

                                </div>

                                @else

                                <span class="text-danger">Employee Deleted</span>

                                @endif

                            </td>

                            <td class="text-center">

                                <span class="badge bg-info bg-opacity-10 text-info rounded-pill px-3">

                                    {{ date('F', mktime(0, 0, 0, $slip->month, 10)) }} {{ $slip->year }}

                                </span>

                            </td>

                            <td class="text-center">

                                @if($slip->file_path)
                                    {{-- Uploaded PDF --}}
                                    <a href="{{ asset($slip->file_path) }}" target="_blank"
                                       class="btn btn-sm btn-outline-danger" title="View PDF">
                                        <i class="bi bi-file-earmark-pdf-fill"></i> PDF
                                    </a>
                                @else
                                    {{-- Form-generated slip --}}
                                    <a href="{{ route('salary-slips.show', $slip->slip_id) }}"
                                       target="_blank"
                                       class="btn btn-sm btn-outline-success" title="View Salary Slip">
                                        <i class="bi bi-file-earmark-text-fill"></i> View Slip
                                    </a>
                                @endif

                            </td>

                            <td class="text-center text-muted small font-monospace">

                                {{ $slip->created_at->format('M d, Y H:i') }}

                            </td>

                            <!-- <td class="text-center">

                                <div class="btn-group" role="group">

                                    <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editSlipModal{{ $slip->slip_id }}" title="Edit">

                                        <i class="bi bi-pencil-square"></i>

                                    </button>

                                    <form action="{{ route('salary-slips.destroy', $slip->slip_id) }}" method="POST" class="d-inline delete-form">

                                        @csrf

                                        @method('DELETE')

                                        <button type="submit" class="btn btn-sm btn-outline-danger border-start-0" title="Delete">

                                            <i class="bi bi-trash"></i>

                                        </button>

                                    </form>

                                </div>

                            </td> -->
<td class="text-center align-middle">
    <div class="d-flex justify-content-center gap-2">

        <!-- Edit -->
        <button class="btn btn-sm btn-secondary"
                data-bs-toggle="modal"
                data-bs-target="#editSlipModal{{ $slip->slip_id }}"
                title="Edit">
            <i class="bi bi-pencil-square"></i>
        </button>

        <!-- Delete -->
        <form action="{{ route('salary-slips.destroy', $slip->slip_id) }}"
              method="POST"
              class="d-inline delete-form"
              >
            @csrf
            @method('DELETE')

            <button type="submit" class="btn btn-sm btn-danger" title="Delete">
                <i class="bi bi-trash"></i>
            </button>
        </form>

    </div>
</td>
                        </tr>

                        @empty

                        <tr>

                            <td colspan="6" class="text-center py-5 text-muted">

                                <i class="bi bi-inbox fs-1 d-block mb-2"></i>

                                No salary slips found.

                            </td>

                        </tr>

                        @endforelse

                    </tbody>

                </table>

                <div class="mt-3 d-flex justify-content-between align-items-center">

                    <div class="text-muted small">

                        Showing {{ $salarySlips->firstItem() ?? 0 }}–{{ $salarySlips->lastItem() ?? 0 }} of {{ $salarySlips->total() }} entries

                    </div>

                    <div>

                        {{ $salarySlips->appends(request()->query())->links() }}

                    </div>

                </div>



            </div>

        </div>

    </div>

    @foreach($salarySlips as $slip)

        <div class="modal fade" id="editSlipModal{{ $slip->slip_id }}" tabindex="-1" aria-hidden="true">

            <div class="modal-dialog modal-dialog-centered modal-xl">

                <div class="modal-content border-0 shadow">

                    <form action="{{ route('salary-slips.update', $slip->slip_id) }}" method="POST" enctype="multipart/form-data">

                        @csrf
                        @method('PUT')

                        <div class="modal-header bg-primary text-white">
                            <h5 class="modal-title fw-bold">
                                <i class="bi bi-pencil-square me-2"></i>Edit Salary Slip
                                — {{ $slip->employee->name ?? 'N/A' }}
                                ({{ date('F', mktime(0,0,0,$slip->month,1)) }} {{ $slip->year }})
                            </h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                        </div>

                        <div class="modal-body">

                            @if($errors->{'update'.$slip->slip_id}->any())
                                <div class="alert alert-danger small py-2">
                                    <ul class="mb-0 ps-3">
                                        @foreach($errors->{'update'.$slip->slip_id}->all() as $err)
                                            <li>{{ $err }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            {{-- Employee + Period --}}
                            <div class="row g-3 mb-3 pb-3 border-bottom">
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold small text-muted text-uppercase">Employee</label>
                                    <select name="employee_id" class="form-select" required>
                                        @foreach($employees as $emp)
                                            <option value="{{ $emp->id }}" {{ $slip->employee_id == $emp->id ? 'selected' : '' }}>
                                                {{ $emp->name }} ({{ $emp->email }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label fw-semibold small text-muted text-uppercase">Basic Salary (₹)</label>
                                    <div class="input-group">
                                        <span class="input-group-text">₹</span>
                                        <input type="number" name="basic_salary" step="0.01" min="0"
                                               class="form-control"
                                               value="{{ old('basic_salary', $slip->basic_salary) }}"
                                               readonly
                                               placeholder="0.00">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label fw-semibold small text-muted text-uppercase">Month <span class="text-danger">*</span></label>
                                    <select name="month" class="form-select" required>
                                        @for($m=1; $m<=12; $m++)
                                            <option value="{{ $m }}" {{ $slip->month == $m ? 'selected' : '' }}>
                                                {{ date('F', mktime(0,0,0,$m,1)) }}
                                            </option>
                                        @endfor
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label fw-semibold small text-muted text-uppercase">Year <span class="text-danger">*</span></label>
                                    <select name="year" class="form-select" required>
                                        @for($y=date('Y'); $y>=2020; $y--)
                                            <option value="{{ $y }}" {{ $slip->year == $y ? 'selected' : '' }}>{{ $y }}</option>
                                        @endfor
                                    </select>
                                </div>
                            </div>

                            {{-- Salary Information Fields --}}
                            <h6 class="fw-bold text-muted text-uppercase mb-3" style="font-size:0.75rem;letter-spacing:0.05em;">
                                <i class="bi bi-bar-chart-line me-1"></i> Salary Information
                            </h6>

                            @php
                            $editFields = [
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
                            $editChunks = array_chunk($editFields, 5);
                            @endphp

                            <div class="row g-2 mb-1">
                                @foreach($editChunks as $colFields)
                                <div class="col-md-6">
                                    <div class="row g-2 mb-1">
                                        <div class="col-8"><span class="small fw-semibold text-muted">Component</span></div>
                                        <div class="col-2 text-center"><span class="small fw-semibold text-muted">Value</span></div>
                                        <div class="col-2 text-center"><span class="small fw-semibold text-muted">Type</span></div>
                                    </div>
                                    @foreach($colFields as $field)
                                    <div class="row g-2 mb-2 align-items-center">
                                        <div class="col-8">
                                            <label class="form-label mb-0 small">{{ $field['label'] }}</label>
                                        </div>
                                        <div class="col-2">
                                            <input type="number" step="0.01" min="0"
                                                   name="{{ $field['key'] }}_value"
                                                   class="form-control form-control-sm edit-slip-value"
                                                   data-field="{{ $field['key'] }}"
                                                   data-basic="{{ $slip->basic_salary }}"
                                                   value="{{ old($field['key'].'_value', $slip->{$field['key'].'_value'}) }}"
                                                   placeholder="0">
                                        </div>
                                        <div class="col-2">
                                            <select name="{{ $field['key'] }}_type"
                                                    class="form-select form-select-sm edit-slip-type"
                                                    data-field="{{ $field['key'] }}">
                                                <option value="%"     {{ old($field['key'].'_type', $slip->{$field['key'].'_type'}) == '%'     ? 'selected' : '' }}>%</option>
                                                <option value="fixed" {{ old($field['key'].'_type', $slip->{$field['key'].'_type'}) == 'fixed' ? 'selected' : '' }}>Fixed</option>
                                            </select>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                                @endforeach
                            </div>

                            {{-- Computed Totals --}}
                            <div class="border-top pt-3 mt-2">
                                <h6 class="fw-bold text-muted text-uppercase mb-3" style="font-size:0.75rem;letter-spacing:0.05em;">
                                    <i class="bi bi-calculator me-1"></i> Computed Totals
                                </h6>
                                <div class="row g-3">
                                    <div class="col-md-3">
                                        <label class="form-label small fw-semibold">Net Taxable Income (₹)</label>
                                        <input type="number" step="0.01" name="net_taxable_income"
                                               class="form-control form-control-sm"
                                               value="{{ old('net_taxable_income', $slip->net_taxable_income) }}">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label small fw-semibold">Total Tax Payable (₹)</label>
                                        <input type="number" step="0.01" name="total_tax_payable"
                                               class="form-control form-control-sm"
                                               value="{{ old('total_tax_payable', $slip->total_tax_payable) }}">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label small fw-semibold">Total Tax Recovered (₹)</label>
                                        <input type="number" step="0.01" name="total_tax_recovered"
                                               class="form-control form-control-sm"
                                               value="{{ old('total_tax_recovered', $slip->total_tax_recovered) }}">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label small fw-semibold">Balance Tax Recoverable (₹)</label>
                                        <input type="number" step="0.01" name="balance_tax_recoverable"
                                               class="form-control form-control-sm"
                                               value="{{ old('balance_tax_recoverable', $slip->balance_tax_recoverable) }}">
                                    </div>
                                </div>
                            </div>

                            {{-- PDF Upload (only for PDF slips) --}}
                            @if($slip->file_path)
                            <div class="border-top pt-3 mt-3">
                                <label class="form-label small fw-semibold">Replace PDF File (Optional)</label>
                                <input type="file" name="file" class="form-control form-control-sm" accept="application/pdf">
                                <div class="form-text">
                                    Current: <a href="{{ asset($slip->file_path) }}" target="_blank">View current PDF</a>
                                </div>
                            </div>
                            @endif

                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-floppy me-1"></i> Update Slip
                            </button>
                        </div>

                    </form>

                </div>

            </div>

        </div>

    @endforeach



</div>



<!-- Add Modal -->

<div class="modal fade" id="addSlipModal" tabindex="-1" aria-hidden="true">

    <div class="modal-dialog modal-dialog-centered">

        <div class="modal-content border-primary rounded-4 shadow-sm">

            <form id="addSlipForm" action="{{ route('salary-slips.store') }}" method="POST" enctype="multipart/form-data">

                @csrf

                <div class="modal-header py-2 px-3 border-bottom-0">

                    <h5 class="modal-title fw-bold"><i class="bi bi-file-earmark-plus me-2"></i>Add Salary Slip</h5>

                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>

                </div>

                <div class="modal-body">

                    <div class="mb-3">

                        <label class="form-label">Employee <span class="text-danger">*</span></label>

                        <select name="employee_id" class="form-select" required>

                            <option value="">Select Employee</option>

                            @foreach($employees as $emp)

                                <option value="{{ $emp->id }}" {{ old('employee_id') == $emp->id ? 'selected' : '' }}>

                                    {{ $emp->name }} ({{ $emp->email }})

                                </option>

                            @endforeach

                        </select>

                    </div>

                    <div class="row g-2 mb-3">

                        <div class="col-md-6">

                            <label class="form-label">Month <span class="text-danger">*</span></label>

                            <select name="month" class="form-select" required>

                                <option value="">Month</option>

                                @for($m=1; $m<=12; $m++)

                                    <option value="{{ $m }}" {{ old('month') == $m ? 'selected' : '' }}>

                                        {{ date('F', mktime(0, 0, 0, $m, 10)) }}

                                    </option>

                                @endfor

                            </select>

                        </div>

                        <div class="col-md-6">

                            <label class="form-label">Year <span class="text-danger">*</span></label>

                            <select name="year" class="form-select" required>

                                @for($y=date('Y'); $y>=2020; $y--)

                                    <option value="{{ $y }}" {{ old('year') == $y ? 'selected' : '' }}>

                                        {{ $y }}

                                    </option>

                                @endfor

                            </select>

                        </div>

                    </div>

                    <div class="mb-3">

                        <label class="form-label">PDF File <span class="text-danger">*</span></label>

                        <input type="file" name="file" class="form-control" accept="application/pdf" required>

                    </div>

                </div>

                <div class="modal-footer border-top-0">

                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>

                    <button type="submit" class="btn btn-primary">Upload Slip</button>

                </div>

            </form>

        </div>

    </div>

</div>



@endsection



@push('scripts')

<script>

    document.addEventListener('DOMContentLoaded', function () {

        @if ($errors->creation->any())

            var addModal = new bootstrap.Modal(document.getElementById('addSlipModal'));

            addModal.show();

        @endif



        @foreach($salarySlips as $slip)

            @if ($errors->{'update'.$slip->slip_id}->any())

                var editModal = new bootstrap.Modal(document.getElementById('editSlipModal{{ $slip->slip_id }}'));

                editModal.show();

            @endif

        @endforeach

        

        // Live Net Taxable Income for edit modals
        document.querySelectorAll('.edit-slip-value, .edit-slip-type').forEach(function(el) {
            el.addEventListener('change', computeEditTotals);
            el.addEventListener('input',  computeEditTotals);
        });

        function computeEditTotals(e) {
            const form  = e.target.closest('form');
            if (!form) return;
            const basicInput = form.querySelector('input[name="basic_salary"]');
            const basic = basicInput ? (parseFloat(basicInput.value) || 0) : 0;

            const earningKeys = ['hra','special_allow','stat_bonus','perquisite'];
            const deductKeys  = ['pt','exempt_reimburse','deduction_10','deduction_16','deduction_24','deduction_via'];

            let gross = basic;
            earningKeys.forEach(function(k) {
                const inp = form.querySelector(`input[name="${k}_value"]`);
                const sel = form.querySelector(`select[name="${k}_type"]`);
                if (inp) { const v = parseFloat(inp.value)||0; gross += sel && sel.value==='%' ? (v/100)*basic : v; }
            });

            let totalDed = 0;
            deductKeys.forEach(function(k) {
                const inp = form.querySelector(`input[name="${k}_value"]`);
                const sel = form.querySelector(`select[name="${k}_type"]`);
                if (inp) { const v = parseFloat(inp.value)||0; totalDed += sel && sel.value==='%' ? (v/100)*basic : v; }
            });

            const netField = form.querySelector('input[name="net_taxable_income"]');
            if (netField) netField.value = Math.max(0, gross - totalDed).toFixed(2);
        }

        document.querySelectorAll('.delete-form').forEach(function(form) {

            form.addEventListener('submit', function(e) {

                e.preventDefault();

                Swal.fire({

                    title: 'Are you sure?',

                    text: "This salary slip will be deleted permanently!",

                    icon: 'warning',

                    showCancelButton: true,

                    confirmButtonText: 'Yes, delete it!',

                    cancelButtonText: 'Cancel',

                    confirmButtonColor: '#7066e0',

                    cancelButtonColor: '#3085d6',

                }).then((result) => {

                    if (result.isConfirmed) {

                        form.submit();

                    }

                });

            });

        });

    });

</script>

@endpush

