{{-- Controller: use named error bags so modals show field errors — e.g. withErrors($validator, 'userCreation') and withErrors($validator, 'userUpdate'.$id) with ->withInput() --}}
@extends('layouts.admin')
@section('title', 'Employee Management')
@section('content')

@php
$staffRole = $roles->firstWhere('name', 'Staff Employee');
$staffRoleID = $staffRole ? $staffRole->id : null;
@endphp

<!-- We must pass $departments to this view from the controller as well -->

@push('styles')
<style>
    .validation-summary {
        display: flex;
        align-items: flex-start;
        gap: 10px;
        border: 1px solid #ffe69c;
        background: #fff8e1;
        border-radius: 10px;
        padding: 10px 12px;
    }
    .validation-summary .icon {
        color: #b58105;
        font-size: 0.95rem;
        margin-top: 1px;
    }
    .validation-summary .title {
        color: #5c4400;
        font-weight: 600;
        font-size: 0.88rem;
        margin-bottom: 1px;
    }
    .validation-summary .meta {
        color: #7a6000;
        font-size: 0.82rem;
        line-height: 1.25rem;
    }
    .form-control.is-invalid,
    .form-select.is-invalid {
        border-color: #dc3545;
        box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.12);
    }
</style>
@endpush

<div class="main-section">

    <!-- @if(session('error'))
    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: @json(session('error')),
                confirmButtonColor: '#d33'
            });
        });
    </script>
    @endpush
    @endif -->

    <!-- Success Message -->

    <!-- @if(session('success'))
    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
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
    @endpush
    @endif -->

    <!-- Header with Add Button -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="fw-bold">Employee List</h4>
        <div>
            @include('partials.export-dropdown', [
                'exportRoute' => 'employees.report',
                'exportQuery' => request()->only(['search', 'status', 'role_id', 'sort_by', 'sort_order']),
            ])
            <button class="btn btn-primary rounded-3" data-bs-toggle="modal" data-bs-target="#addUsersModal">
                <i class="bi bi-person-plus-fill me-1"></i> Add Employee
            </button>
        </div>
    </div>

    <form method="GET" action="{{ route('employees.index') }}" class="row g-2 mb-3">
        <div class="col-md-4">
            <input type="text" name="search" value="{{ request('search') }}"
                class="form-control"
                placeholder="Search by name, email, phone...">
        </div>

        <div class="col-md-3">
            <select name="role_id" class="form-select">
                <option value="">All Roles</option>
                @foreach($roles as $roleOption)
                <option value="{{ $roleOption->id }}" {{ request('role_id') == $roleOption->id ? 'selected' : '' }}>
                    {{ $roleOption->name }}
                </option>
                @endforeach
            </select>
        </div>

        <div class="col-md-2">
            <select name="status" class="form-select">
                <option value="">All Status</option>
                <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>Active</option>
                <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Inactive</option>
            </select>
        </div>
        <div class="col-auto">
            <input type="hidden" name="sort_by" value="{{ request('sort_by') }}">
            <input type="hidden" name="sort_order" value="{{ request('sort_order') }}">
            <button class="btn btn-primary">Search</button>
            <a href="{{ route('employees.index') }}" class="btn btn-outline-secondary">Reset</a>
        </div>
    </form>

    <!-- Users Table Card -->
    <div class="card shadow-sm rounded border mb-4">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover table-bordered align-middle mb-0">
                    <thead class="table-light text-center">
                        <tr>
                            <th style="width: 5%;">ID</th>
                            <x-sortable-th name="name" label="Full Name" />
                            <x-sortable-th name="role" label="Employee Role" />
                            <x-sortable-th name="department" label="Department" />
                            <th class="text-center" style="width: 80px;">Image</th>
                            <x-sortable-th name="status" label="Status" class="text-center" />
                            <th style="width: 10%;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>

                        @forelse($users as $user)
                        <tr>
                            <td class="text-center">{{ $users->firstItem() + $loop->index }}</td>
                            <td>
                                <div class="fw-bold">{{ $user->name ?? '-' }}</div>
                                <div class="small text-muted">{{ $user->phone ?? '' }}</div>
                            </td>
                            <td>
                                <span class="badge bg-secondary">{{ $user->role->name ?? 'No Role' }}</span>
                            </td>
                            <td>
                                <span class="badge bg-info">{{ $user->department->name ?? 'No Department' }}</span>
                            </td>
                            <td class="text-center">
                                <img src="{{ $user->profile_image ? asset($user->profile_image) : asset('assets/admin/images/no-image.png') }}"
                                    alt="Profile"
                                    class="rounded shadow-sm border"
                                    width="50" height="50"
                                    style="object-fit: cover;"
                                    onerror="this.src='{{ asset('assets/admin/images/no-image.png') }}'">
                            </td>
                            <td class="text-center">
                                <div class="form-check form-switch d-flex justify-content-center">
                                    <input class="form-check-input status-toggle" type="checkbox" role="switch"
                                        id="statusSwitch{{ $user->id }}"
                                        data-id="{{ $user->id }}"
                                        {{ $user->status ? 'checked' : '' }}>
                                </div>
                            </td>
                            <!-- <td class="text-center d-flex gap-2 middle">
                               
                                <button class="btn btn-sm btn-info text-white" data-bs-toggle="modal" data-bs-target="#viewUserModal{{ $user->id }}">
                                    <i class="bi bi-eye"></i>
                                </button>

                              
                                <button class="btn btn-sm btn-secondary" data-bs-toggle="modal" data-bs-target="#editUserModal{{ $user->id }}">
                                    <i class="bi bi-pencil-square"></i>
                                </button>

                               
                                <form action="{{ route('employees.destroy', $user->id) }}" method="POST" class="d-inline delete-form">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger">
                                        <i class="bi bi-trash3-fill"></i>
                                    </button>
                                </form>
                            </td> -->
                       <td class="text-center align-middle">
    <div class="d-flex justify-content-center gap-2">
        
        <!-- View -->
        <button class="btn btn-sm btn-info text-white" 
                data-bs-toggle="modal" 
                data-bs-target="#viewUserModal{{ $user->id }}">
            <i class="bi bi-eye"></i>
        </button>

        <!-- Edit -->
        <button class="btn btn-sm btn-secondary" 
                data-bs-toggle="modal" 
                data-bs-target="#editUserModal{{ $user->id }}">
            <i class="bi bi-pencil-square"></i>
        </button>

        <!-- Delete -->
        <form action="{{ route('employees.destroy', $user->id) }}" 
              method="POST" 
              class="d-inline delete-form">
            @csrf
            @method('DELETE')

            <button type="submit" class="btn btn-sm btn-danger">
                <i class="bi bi-trash3-fill"></i>
            </button>
        </form>

    </div>
</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">
                                <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                No Data Found
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="mt-3 d-flex justify-content-between align-items-center">
                    <div class="text-muted small">
                        Showing {{ $users->firstItem() ?? 0 }}–{{ $users->lastItem() ?? 0 }} of {{ $users->total() }} entries
                    </div>
                    <div>
                        {{ $users->appends(request()->query())->links() }}
                    </div>
                </div>

            </div>
        </div>
    </div>

    <!-- Loading Spinner Modal -->
    <div class="modal fade" id="loadingModal" tabindex="-1" aria-labelledby="loadingModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 rounded-4 shadow">
                <div class="modal-body text-center py-5">
                    <div class="spinner-border text-primary mb-3" role="status" style="width: 3rem; height: 3rem;">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <h5 class="fw-bold mt-3">Processing...</h5>
                    <p class="text-muted small mb-0">Please wait while we save your changes</p>
                </div>
            </div>
        </div>
    </div>

    <!-- View User Modals -->
    @foreach($users as $user)
    <div class="modal fade" id="viewUserModal{{ $user->id }}" tabindex="-1" aria-labelledby="viewUserLabel{{ $user->id }}" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-info rounded-4 shadow-sm">
                <div class="modal-header bg-info text-white py-2 px-3 border-bottom-0">
                    <h5 class="modal-title fw-bold" id="viewUserLabel{{ $user->id }}">
                        <i class="bi bi-eye me-2"></i> Employee Details
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="text-center mb-4">
                        <img src="{{ $user->profile_image ? asset($user->profile_image) : asset('assets/admin/images/no-image.png') }}"
                            alt="Profile"
                            class="rounded-circle border shadow-sm" width="120" height="120"
                            style="object-fit: cover;"
                            onerror="this.src='{{ asset('assets/admin/images/no-image.png') }}'">
                    </div>
                    @php $isStaffView = isset($user->role) && $user->role->name === 'Staff Employee'; @endphp
                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="mb-2"><label class="small text-muted mb-0">Full Name</label><div class="fw-bold fs-5">{{ $user->name ?? 'N/A' }}</div></div>
                            <div class="mb-2"><label class="small text-muted mb-0">Email</label><div class="fw-bold">{{ $user->email ?? 'N/A' }}</div></div>
                            <div class="mb-2"><label class="small text-muted mb-0">Mobile</label><div class="fw-bold">{{ $user->phone ?? 'N/A' }}</div></div>
                            <div class="mb-2"><label class="small text-muted mb-0">Date of Birth</label><div class="fw-bold">{{ $user->date_of_birth ? \Carbon\Carbon::parse($user->date_of_birth)->format('d M, Y') : 'N/A' }}</div></div>
                            <div class="mb-2"><label class="small text-muted mb-0">Gender</label><div class="fw-bold text-capitalize">{{ $user->gender ?? 'N/A' }}</div></div>
                            <div class="mb-2"><label class="small text-muted mb-0">Marital Status</label><div class="fw-bold text-capitalize">{{ $user->marital_status ?? 'N/A' }}</div></div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-2"><label class="small text-muted mb-0">Employee Role</label><div class="fw-bold">{{ $user->role->name ?? 'N/A' }}</div></div>
                            @if(!$isStaffView)
                            <div class="mb-2"><label class="small text-muted mb-0">Department</label><div class="fw-bold">{{ $user->department->name ?? 'N/A' }}</div></div>
                            @endif
                            @if($isStaffView)
                            <div class="mb-2"><label class="small text-muted mb-0">Job Type</label><div class="fw-bold">{{ $user->job_type ?? 'N/A' }}</div></div>
                            @endif
                            @if(!$isStaffView)
                            <div class="mb-2"><label class="small text-muted mb-0">Father's Name</label><div class="fw-bold">{{ $user->father_name ?? 'N/A' }}</div></div>
                            @endif
                            <div class="mb-2"><label class="small text-muted mb-0">Place of Birth</label><div class="fw-bold">{{ $user->place_of_birth ?? 'N/A' }}</div></div>
                            <div class="mb-2">
                                <label class="small text-muted mb-0">Status</label>
                                <div>
                                    @if($user->status)
                                    <span class="badge bg-success">Active</span>
                                    @else
                                    <span class="badge bg-danger">Inactive</span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="col-12">
                            <div class="mb-2"><label class="small text-muted mb-0">Address</label><div class="fw-bold">{{ $user->address ?? 'N/A' }}</div></div>
                        </div>

                        @if(!$isStaffView)
                        <div class="col-12">
                            <h6 class="fw-bold mb-1">KYC Details</h6>
                            <div class="row g-3">
                                <div class="col-md-6"><label class="small text-muted mb-0">Aadhar Number</label><div class="fw-bold">{{ $user->aadhar_card_no ?? 'N/A' }}</div></div>
                                <div class="col-md-6"><label class="small text-muted mb-0">PAN Number</label><div class="fw-bold">{{ $user->pan_card_no ?? 'N/A' }}</div></div>
                                <div class="col-md-6"><label class="small text-muted mb-0">Driving License Number</label><div class="fw-bold">{{ $user->driving_license_no ?? 'N/A' }}</div></div>
                                <div class="col-md-6"><label class="small text-muted mb-0">Aadhar Image</label><div>@if($user->aadhar_card_image)<a href="{{ asset($user->aadhar_card_image) }}" target="_blank" class="btn btn-sm btn-outline-primary">View</a>@else N/A @endif</div></div>
                                <div class="col-md-6"><label class="small text-muted mb-0">PAN Image</label><div>@if($user->pan_card_image)<a href="{{ asset($user->pan_card_image) }}" target="_blank" class="btn btn-sm btn-outline-primary">View</a>@else N/A @endif</div></div>
                                <div class="col-md-6"><label class="small text-muted mb-0">Driving License File</label><div>@if($user->driving_license_image)<a href="{{ asset($user->driving_license_image) }}" target="_blank" class="btn btn-sm btn-outline-primary">View</a>@else N/A @endif</div></div>
                            </div>
                        </div>

                        <div class="col-12">
                            <h6 class="fw-bold mb-1">Bank Details</h6>
                            <div class="row g-3">
                                <div class="col-md-6"><label class="small text-muted mb-0">Bank Account Number</label><div class="fw-bold">{{ $user->bank_account_no ?? 'N/A' }}</div></div>
                                <div class="col-md-6"><label class="small text-muted mb-0">IFSC Code</label><div class="fw-bold">{{ $user->ifsc_code ?? 'N/A' }}</div></div>
                                <div class="col-md-6"><label class="small text-muted mb-0">Bank Name</label><div class="fw-bold">{{ $user->bank_name ?? 'N/A' }}</div></div>
                                <div class="col-md-6"><label class="small text-muted mb-0">Branch</label><div class="fw-bold">{{ $user->bank_branch ?? 'N/A' }}</div></div>
                                <div class="col-md-6"><label class="small text-muted mb-0">Bank Proof</label><div>@if($user->bank_proof_image)<a href="{{ asset($user->bank_proof_image) }}" target="_blank" class="btn btn-sm btn-outline-primary">View</a>@else N/A @endif</div></div>
                            </div>
                        </div>
                        @endif

                        <div class="col-12">
                            <div class="border-top pt-2">
                                <label class="small text-muted mb-0">Joined On</label>
                                <div class="text-dark">{{ $user->created_at->format('d M, Y h:i A') }}</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top-0 bg-light">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    @endforeach

    <!-- Edit User Modals -->
    @foreach($users as $user)
    <div class="modal fade" id="editUserModal{{ $user->id }}" tabindex="-1" aria-labelledby="editUserLabel{{ $user->id }}" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-primary rounded-4 shadow-sm">
                <form action="{{ route('employees.update', $user->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="modal-header py-2 px-3 border-bottom-0">
                        <h5 class="modal-title fw-bold" id="editUserLabel{{ $user->id }}">
                            <i class="bi bi-pencil-square me-2"></i> Edit Employee
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">
                        @if($errors->{'userUpdate'.$user->id}->any())
                        <div class="validation-summary mb-3" role="alert">
                            <i class="bi bi-exclamation-circle-fill icon"></i>
                            <div>
                                <div class="title">{{ $errors->{'userUpdate'.$user->id}->count() }} field(s) need attention</div>
                                <div class="meta">{{ $errors->{'userUpdate'.$user->id}->first() }} Please check highlighted inputs below.</div>
                            </div>
                        </div>
                        @endif
                        <div class="row g-3">
                            <!-- Image Preview -->
                            <!-- apply onerror -->
                            <div class="col-12 text-center mb-3">
                                <img id="editPreview{{ $user->id }}"
                                    src="{{ $user->profile_image ? asset($user->profile_image) : asset('assets/admin/images/no-image.png') }}"
                                    class="rounded-circle border shadow-sm" width="100" height="100" style="object-fit: cover;"
                                    onerror="this.src='{{ asset('assets/admin/images/no-image.png') }}'">
                            </div>


                            <div class="col-12">
                                <label class="form-label">Profile Image</label>
                                <input type="file" name="profile_image" class="form-control @error('profile_image', 'userUpdate'.$user->id) is-invalid @enderror" accept="image/*"
                                    onchange="if(this.files&&this.files[0]){var el=document.getElementById('editPreview{{ $user->id }}');el.src=URL.createObjectURL(this.files[0]);}">
                                @error('profile_image', 'userUpdate'.$user->id)
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Full Name</label>
                                <input type="text" name="fullname" class="form-control @error('fullname', 'userUpdate'.$user->id) is-invalid @enderror" placeholder="Full Name"
                                    value="{{ old('fullname', $user->name) }}" pattern="[a-zA-Z\s]+" title="Only letters and spaces allowed" required>
                                @error('fullname', 'userUpdate'.$user->id)
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6" id="editDepartmentContainer{{ $user->id }}">
                                <label class="form-label">Department <span class="text-danger">*</span></label>
                                <select name="department_id" id="editDepartmentSelect{{ $user->id }}" class="form-select @error('department_id', 'userUpdate'.$user->id) is-invalid @enderror" required>
                                    <option value="" disabled>Select Department</option>
                                    @foreach($departments as $dept)
                                    <option value="{{ $dept->id }}" {{ old('department_id', $user->department_id) == $dept->id ? 'selected' : '' }}>
                                        {{ $dept->name }}
                                    </option>
                                    @endforeach
                                </select>
                                @error('department_id', 'userUpdate'.$user->id)
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Email</label>
                                <input type="email" name="email" class="form-control @error('email', 'userUpdate'.$user->id) is-invalid @enderror" placeholder="Email"
                                    value="{{ old('email', $user->email) }}" required>
                                @error('email', 'userUpdate'.$user->id)
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Mobile</label>
                                <input type="text" name="phone" class="form-control @error('phone', 'userUpdate'.$user->id) is-invalid @enderror" placeholder="Mobile"
                                    value="{{ old('phone', $user->phone) }}" pattern="[0-9]{10}" title="Enter valid mobile number (10 digits)" maxlength="10" oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 10);" required>
                                @error('phone', 'userUpdate'.$user->id)
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Employee Role</label>
                                <select name="role_id" class="form-select @error('role_id', 'userUpdate'.$user->id) is-invalid @enderror" required onchange="toggleEditFieldsByRole(this, '{{ $user->id }}', { fromServer: false })">
                                    <option disabled>Select Role</option>
                                    @foreach($roles as $roleOption)
                                    <option value="{{ $roleOption->id }}"
                                        {{ old('role_id', $user->role_id) == $roleOption->id ? 'selected' : '' }}>
                                        {{ $roleOption->name }}
                                    </option>
                                    @endforeach
                                </select>
                                @error('role_id', 'userUpdate'.$user->id)
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Job Type Dropdown (Edit) -->
                            <div class="col-md-6 {{ (old('job_type', $user->job_type) || ($user->role && $user->role->name == 'Staff Employee')) ? '' : 'd-none' }}" id="editJobTypeContainer{{ $user->id }}">
                                <label class="form-label">Job Type</label>
                                <select name="job_type" id="editJobTypeSelect{{ $user->id }}" class="form-select @error('job_type', 'userUpdate'.$user->id) is-invalid @enderror"
                                    {{ (old('job_type', $user->job_type) || ($user->role && $user->role->name == 'Staff Employee')) ? 'required' : '' }}>
                                    <option value="" selected disabled>Select Job Type</option>
                                    <option value="Full Time" {{ old('job_type', $user->job_type) == 'Full Time' ? 'selected' : '' }}>Full Time</option>
                                    <option value="Half Time" {{ old('job_type', $user->job_type) == 'Half Time' ? 'selected' : '' }}>Half Time</option>
                                </select>
                                @error('job_type', 'userUpdate'.$user->id)
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Date of Birth <span class="text-danger">*</span></label>
                                <input type="date" name="date_of_birth" max="{{ now()->format('Y-m-d') }}" class="form-control @error('date_of_birth', 'userUpdate'.$user->id) is-invalid @enderror"
                                    value="{{ old('date_of_birth', $user->date_of_birth ? \Carbon\Carbon::parse($user->date_of_birth)->format('Y-m-d') : '') }}" required>
                                @error('date_of_birth', 'userUpdate'.$user->id)
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Gender <span class="text-danger">*</span></label>
                                <select name="gender" class="form-select @error('gender', 'userUpdate'.$user->id) is-invalid @enderror" required>
                                    <option value="" disabled>Select Gender</option>
                                    @php $editGender = strtolower((string) old('gender', $user->gender ?? '')); @endphp
                                    <option value="male" {{ $editGender === 'male' ? 'selected' : '' }}>Male</option>
                                    <option value="female" {{ $editGender === 'female' ? 'selected' : '' }}>Female</option>
                                    <option value="other" {{ $editGender === 'other' ? 'selected' : '' }}>Other</option>
                                </select>
                                @error('gender', 'userUpdate'.$user->id)
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Marital Status <span class="text-danger">*</span></label>
                                <select name="marital_status" class="form-select @error('marital_status', 'userUpdate'.$user->id) is-invalid @enderror" required>
                                    <option value="" disabled>Select Marital Status</option>
                                    @php $editMarital = strtolower((string) old('marital_status', $user->marital_status ?? '')); @endphp
                                    <option value="single" {{ $editMarital === 'single' ? 'selected' : '' }}>Single</option>
                                    <option value="married" {{ $editMarital === 'married' ? 'selected' : '' }}>Married</option>
                                    <option value="divorced" {{ $editMarital === 'divorced' ? 'selected' : '' }}>Divorced</option>
                                    <option value="widowed" {{ $editMarital === 'widowed' ? 'selected' : '' }}>Widowed</option>
                                </select>
                                @error('marital_status', 'userUpdate'.$user->id)
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Place of Birth <span class="text-danger">*</span></label>
                                <input type="text" name="place_of_birth" class="form-control @error('place_of_birth', 'userUpdate'.$user->id) is-invalid @enderror"
                                    value="{{ old('place_of_birth', $user->place_of_birth) }}" pattern="[A-Za-z\s]+" title="Only letters and spaces allowed"
                                    oninput="this.value = this.value.replace(/[^A-Za-z\s]/g, '');" maxlength="255" required>
                                @error('place_of_birth', 'userUpdate'.$user->id)
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 edit-additional-fields edit-additional-fields-{{ $user->id }}">
                                <label class="form-label">Father's Name</label>
                                <input type="text" name="father_name" class="form-control @error('father_name', 'userUpdate'.$user->id) is-invalid @enderror"
                                    value="{{ old('father_name', $user->father_name) }}" pattern="[A-Za-z\s]+" title="Only letters and spaces allowed"
                                    oninput="this.value = this.value.replace(/[^A-Za-z\s]/g, '');" maxlength="255">
                                @error('father_name', 'userUpdate'.$user->id)
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Password <small>(Leave blank if no change)</small></label>
                                <input type="password" name="password" class="form-control @error('password', 'userUpdate'.$user->id) is-invalid @enderror" placeholder="Password">
                                @error('password', 'userUpdate'.$user->id)
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Confirm Password</label>
                                <input type="password" name="password_confirmation" class="form-control" placeholder="Confirm Password">
                            </div>
                            <div class="col-12">
                                <label class="form-label">Address</label>
                                <textarea name="address" class="form-control @error('address', 'userUpdate'.$user->id) is-invalid @enderror" placeholder="Address" rows="2" required>{{ old('address', $user->address) }}</textarea>
                                @error('address', 'userUpdate'.$user->id)
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12 mt-2 edit-additional-fields edit-additional-fields-{{ $user->id }}">
                                <h6 class="fw-bold mb-1">KYC Details</h6>
                                <hr class="mt-1 mb-2">
                            </div>

                            <div class="col-md-6 edit-additional-fields edit-additional-fields-{{ $user->id }}">
                                <label class="form-label">Aadhar Card Number</label>
                                <input type="text" name="aadhar_card_no" class="form-control @error('aadhar_card_no', 'userUpdate'.$user->id) is-invalid @enderror"
                                    value="{{ old('aadhar_card_no', $user->aadhar_card_no) }}" pattern="[0-9]{12}" maxlength="12"
                                    oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 12);">
                                @error('aadhar_card_no', 'userUpdate'.$user->id)
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 edit-additional-fields edit-additional-fields-{{ $user->id }}">
                                <label class="form-label">Aadhar Card Image</label>
                                <input type="file" name="aadhar_card_image" class="form-control @error('aadhar_card_image', 'userUpdate'.$user->id) is-invalid @enderror" accept="image/jpeg,image/png,image/jpg,image/gif"
                                    data-preview-wrap="editAadharPreviewWrap{{ $user->id }}"
                                    data-preview-img="editAadharPreview{{ $user->id }}"
                                    data-preview-link="editAadharPreviewLink{{ $user->id }}"
                                    data-existing-url="{{ $user->aadhar_card_image ? asset($user->aadhar_card_image) : '' }}"
                                    data-existing-type="image"
                                    onchange="renderFilePreview(this)">
                                <div id="editAadharPreviewWrap{{ $user->id }}" class="mt-2 {{ $user->aadhar_card_image ? '' : 'd-none' }}">
                                    <img id="editAadharPreview{{ $user->id }}" src="{{ $user->aadhar_card_image ? asset($user->aadhar_card_image) : '' }}" class="img-thumbnail" style="max-height: 120px;">
                                    <a id="editAadharPreviewLink{{ $user->id }}" href="#" target="_blank" class="btn btn-sm btn-outline-secondary d-none">View file</a>
                                </div>
                                @error('aadhar_card_image', 'userUpdate'.$user->id)
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 edit-additional-fields edit-additional-fields-{{ $user->id }}">
                                <label class="form-label">PAN Card Number</label>
                                <input type="text" name="pan_card_no" class="form-control @error('pan_card_no', 'userUpdate'.$user->id) is-invalid @enderror"
                                    value="{{ old('pan_card_no', $user->pan_card_no) }}" pattern="[A-Z]{5}[0-9]{4}[A-Z]{1}" maxlength="10"
                                    oninput="this.value = this.value.toUpperCase();">
                                @error('pan_card_no', 'userUpdate'.$user->id)
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 edit-additional-fields edit-additional-fields-{{ $user->id }}">
                                <label class="form-label">PAN Card Image</label>
                                <input type="file" name="pan_card_image" class="form-control @error('pan_card_image', 'userUpdate'.$user->id) is-invalid @enderror" accept="image/jpeg,image/png,image/jpg,image/gif"
                                    data-preview-wrap="editPanPreviewWrap{{ $user->id }}"
                                    data-preview-img="editPanPreview{{ $user->id }}"
                                    data-preview-link="editPanPreviewLink{{ $user->id }}"
                                    data-existing-url="{{ $user->pan_card_image ? asset($user->pan_card_image) : '' }}"
                                    data-existing-type="image"
                                    onchange="renderFilePreview(this)">
                                <div id="editPanPreviewWrap{{ $user->id }}" class="mt-2 {{ $user->pan_card_image ? '' : 'd-none' }}">
                                    <img id="editPanPreview{{ $user->id }}" src="{{ $user->pan_card_image ? asset($user->pan_card_image) : '' }}" class="img-thumbnail" style="max-height: 120px;">
                                    <a id="editPanPreviewLink{{ $user->id }}" href="#" target="_blank" class="btn btn-sm btn-outline-secondary d-none">View file</a>
                                </div>
                                @error('pan_card_image', 'userUpdate'.$user->id)
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 edit-additional-fields edit-additional-fields-{{ $user->id }}">
                                <label class="form-label">Driving License Number</label>
                                <input type="text" name="driving_license_no" class="form-control @error('driving_license_no', 'userUpdate'.$user->id) is-invalid @enderror"
                                    value="{{ old('driving_license_no', $user->driving_license_no) }}" maxlength="50">
                                @error('driving_license_no', 'userUpdate'.$user->id)
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 edit-additional-fields edit-additional-fields-{{ $user->id }}">
                                <label class="form-label">Driving License Image/PDF</label>
                                <input type="file" name="driving_license_image" class="form-control @error('driving_license_image', 'userUpdate'.$user->id) is-invalid @enderror" accept="image/jpeg,image/png,image/jpg,pdf"
                                    data-preview-wrap="editDrivingPreviewWrap{{ $user->id }}"
                                    data-preview-img="editDrivingPreview{{ $user->id }}"
                                    data-preview-link="editDrivingPreviewLink{{ $user->id }}"
                                    data-existing-url="{{ $user->driving_license_image ? asset($user->driving_license_image) : '' }}"
                                    data-existing-type="{{ $user->driving_license_image && strtolower(pathinfo($user->driving_license_image, PATHINFO_EXTENSION)) === 'pdf' ? 'pdf' : 'image' }}"
                                    onchange="renderFilePreview(this)">
                                <div id="editDrivingPreviewWrap{{ $user->id }}" class="mt-2 {{ $user->driving_license_image ? '' : 'd-none' }}">
                                    <img id="editDrivingPreview{{ $user->id }}" src="{{ $user->driving_license_image && strtolower(pathinfo($user->driving_license_image, PATHINFO_EXTENSION)) !== 'pdf' ? asset($user->driving_license_image) : '' }}" class="img-thumbnail {{ $user->driving_license_image && strtolower(pathinfo($user->driving_license_image, PATHINFO_EXTENSION)) !== 'pdf' ? '' : 'd-none' }}" style="max-height: 120px;">
                                    <a id="editDrivingPreviewLink{{ $user->id }}" href="{{ $user->driving_license_image ? asset($user->driving_license_image) : '#' }}" target="_blank" class="btn btn-sm btn-outline-secondary {{ $user->driving_license_image && strtolower(pathinfo($user->driving_license_image, PATHINFO_EXTENSION)) === 'pdf' ? '' : 'd-none' }}">View current file</a>
                                </div>
                                @error('driving_license_image', 'userUpdate'.$user->id)
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12 mt-2 edit-additional-fields edit-additional-fields-{{ $user->id }}">
                                <h6 class="fw-bold mb-1">Bank Details</h6>
                                <hr class="mt-1 mb-2">
                            </div>

                            <div class="col-md-6 edit-additional-fields edit-additional-fields-{{ $user->id }}">
                                <label class="form-label">Bank Account Number</label>
                                <input type="text" name="bank_account_no" class="form-control @error('bank_account_no', 'userUpdate'.$user->id) is-invalid @enderror"
                                    value="{{ old('bank_account_no', $user->bank_account_no) }}" pattern="[0-9]{10,16}" title="Bank account number must be 10 to 16 digits"
                                    minlength="10" maxlength="16" oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 16);">
                                @error('bank_account_no', 'userUpdate'.$user->id)
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 edit-additional-fields edit-additional-fields-{{ $user->id }}">
                                <label class="form-label">IFSC Code</label>
                                <input type="text" name="ifsc_code" class="form-control @error('ifsc_code', 'userUpdate'.$user->id) is-invalid @enderror"
                                    value="{{ old('ifsc_code', $user->ifsc_code) }}" pattern="[A-Z]{4}0[A-Z0-9]{6}" maxlength="11"
                                    oninput="this.value = this.value.toUpperCase();">
                                @error('ifsc_code', 'userUpdate'.$user->id)
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 edit-additional-fields edit-additional-fields-{{ $user->id }}">
                                <label class="form-label">Bank Name</label>
                                <input type="text" name="bank_name" class="form-control @error('bank_name', 'userUpdate'.$user->id) is-invalid @enderror"
                                    value="{{ old('bank_name', $user->bank_name) }}" maxlength="255">
                                @error('bank_name', 'userUpdate'.$user->id)
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 edit-additional-fields edit-additional-fields-{{ $user->id }}">
                                <label class="form-label">Branch Name</label>
                                <input type="text" name="bank_branch" class="form-control @error('bank_branch', 'userUpdate'.$user->id) is-invalid @enderror"
                                    value="{{ old('bank_branch', $user->bank_branch) }}" maxlength="255">
                                @error('bank_branch', 'userUpdate'.$user->id)
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 edit-additional-fields edit-additional-fields-{{ $user->id }}">
                                <label class="form-label">Bank Proof Image/PDF</label>
                                <input type="file" name="bank_proof_image" class="form-control @error('bank_proof_image', 'userUpdate'.$user->id) is-invalid @enderror" accept="image/jpeg,image/png,image/jpg,pdf"
                                    data-preview-wrap="editBankProofPreviewWrap{{ $user->id }}"
                                    data-preview-img="editBankProofPreview{{ $user->id }}"
                                    data-preview-link="editBankProofPreviewLink{{ $user->id }}"
                                    data-existing-url="{{ $user->bank_proof_image ? asset($user->bank_proof_image) : '' }}"
                                    data-existing-type="{{ $user->bank_proof_image && strtolower(pathinfo($user->bank_proof_image, PATHINFO_EXTENSION)) === 'pdf' ? 'pdf' : 'image' }}"
                                    onchange="renderFilePreview(this)">
                                <div id="editBankProofPreviewWrap{{ $user->id }}" class="mt-2 {{ $user->bank_proof_image ? '' : 'd-none' }}">
                                    <img id="editBankProofPreview{{ $user->id }}" src="{{ $user->bank_proof_image && strtolower(pathinfo($user->bank_proof_image, PATHINFO_EXTENSION)) !== 'pdf' ? asset($user->bank_proof_image) : '' }}" class="img-thumbnail {{ $user->bank_proof_image && strtolower(pathinfo($user->bank_proof_image, PATHINFO_EXTENSION)) !== 'pdf' ? '' : 'd-none' }}" style="max-height: 120px;">
                                    <a id="editBankProofPreviewLink{{ $user->id }}" href="{{ $user->bank_proof_image ? asset($user->bank_proof_image) : '#' }}" target="_blank" class="btn btn-sm btn-outline-secondary {{ $user->bank_proof_image && strtolower(pathinfo($user->bank_proof_image, PATHINFO_EXTENSION)) === 'pdf' ? '' : 'd-none' }}">View current file</a>
                                </div>
                                @error('bank_proof_image', 'userUpdate'.$user->id)
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer border-top-0">
                        <button type="button" class="btn btn-outline-primary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary" onclick="showFormLoader()">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endforeach

</div>

<!-- Add User Modal -->
<!-- Add User Modal -->
<div class="modal fade" id="addUsersModal" tabindex="-1" aria-labelledby="addUsersModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-primary rounded-4 shadow-sm">
            <form id="addUserForm" action="{{ route('employees.store') }}" method="POST" enctype="multipart/form-data" novalidate>
                @csrf
                <div class="modal-header py-2 px-3 border-bottom-0">
                    <h5 class="modal-title fw-bold" id="addUsersModalLabel">
                        <i class="bi bi-person-plus-fill me-2"></i> Add Employee
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <!-- @if($errors->userCreation->any())
                    <div class="validation-summary mb-2" role="alert">
                        <i class="bi bi-exclamation-circle-fill icon"></i>
                        <div>
                            <div class="title">{{ $errors->userCreation->count() }} field(s) need attention</div>
                            <div class="meta">{{ $errors->userCreation->first() }} Please check highlighted inputs below.</div>
                        </div>
                    </div>
                    <div class="small text-muted mb-2">
                        <i class="bi bi-info-circle me-1"></i> For new employee forms, file uploads must be chosen again after validation fail (browser security).
                    </div>
                    @endif -->
                    <div class="row g-3">
                        <!-- Basic Fields (Always Visible) -->
                        <div class="col-12 text-center">
                            <img id="addPreview"
                                src="{{ asset('assets/admin/images/no-image.png') }}"
                                class="rounded-circle border shadow-sm" width="100" height="100" style="object-fit: cover;"
                                onerror="this.src='{{ asset('assets/admin/images/no-image.png') }}'">
                        </div>
                        
                        <div class="col-12">
                            <label class="form-label">Profile Image</label>
                            <input type="file" name="profile_image" class="form-control @error('profile_image', 'userCreation') is-invalid @enderror" accept="image/*"
                                onchange="if(this.files&&this.files[0]){document.getElementById('addPreview').src=URL.createObjectURL(this.files[0]);}">
                            @error('profile_image', 'userCreation')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Full Name <span class="text-danger">*</span></label>
                            <input type="text" name="fullname" class="form-control @error('fullname', 'userCreation') is-invalid @enderror" placeholder="Full Name" value="{{ old('fullname') }}" pattern="[A-Za-z\s]+" title="Only letters and spaces allowed" oninput="this.value = this.value.replace(/[^A-Za-z\s]/g, '');" maxlength="255" required>
                            @error('fullname', 'userCreation')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Email <span class="text-danger">*</span></label>
                            <input type="email" name="email" class="form-control @error('email', 'userCreation') is-invalid @enderror" placeholder="Email" value="{{ old('email') }}" maxlength="255" required>
                            @error('email', 'userCreation')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Mobile <span class="text-danger">*</span></label>
                            <input type="text" name="phone" class="form-control @error('phone', 'userCreation') is-invalid @enderror" placeholder="Mobile" value="{{ old('phone') }}" pattern="[0-9]{10}" title="Enter valid mobile number (10 digits)" maxlength="10" oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 10);" required>
                            @error('phone', 'userCreation')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Employee Role <span class="text-danger">*</span></label>
                            <select name="role_id" id="roleSelect" class="form-select @error('role_id', 'userCreation') is-invalid @enderror" required onchange="toggleFieldsByRole(this, { fromServer: false })">
                                <option value="" selected disabled>Select Role</option>
                                @foreach($roles as $roleOption)
                                <option value="{{ $roleOption->id }}" data-role-name="{{ $roleOption->name }}" {{ old('role_id') == $roleOption->id ? 'selected' : '' }}>
                                    {{ $roleOption->name }}
                                </option>
                                @endforeach
                            </select>
                            @error('role_id', 'userCreation')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Date of Birth (Always Visible) -->
                        <div class="col-md-6">
                            <label class="form-label">Date of Birth <span class="text-danger">*</span></label>
                            <input type="date" name="date_of_birth" max="{{ now()->format('Y-m-d') }}" class="form-control @error('date_of_birth', 'userCreation') is-invalid @enderror" value="{{ old('date_of_birth') }}" required>
                            @error('date_of_birth', 'userCreation')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Gender (Always Visible) -->
                        <div class="col-md-6">
                            <label class="form-label">Gender <span class="text-danger">*</span></label>
                            <select name="gender" class="form-select @error('gender', 'userCreation') is-invalid @enderror" required>
                                <option value="" selected disabled>Select Gender</option>
                                <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>Male</option>
                                <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>Female</option>
                                <option value="other" {{ old('gender') == 'other' ? 'selected' : '' }}>Other</option>
                            </select>
                            @error('gender', 'userCreation')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Marital Status (Always Visible) -->
                        <div class="col-md-6">
                            <label class="form-label">Marital Status <span class="text-danger">*</span></label>
                            <select name="marital_status" class="form-select @error('marital_status', 'userCreation') is-invalid @enderror" required>
                                <option value="" selected disabled>Select Marital Status</option>
                                <option value="single" {{ old('marital_status') == 'single' ? 'selected' : '' }}>Single</option>
                                <option value="married" {{ old('marital_status') == 'married' ? 'selected' : '' }}>Married</option>
                                <option value="divorced" {{ old('marital_status') == 'divorced' ? 'selected' : '' }}>Divorced</option>
                                <option value="widowed" {{ old('marital_status') == 'widowed' ? 'selected' : '' }}>Widowed</option>
                            </select>
                            @error('marital_status', 'userCreation')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Address (Always Visible) -->
                        <div class="col-12">
                            <label class="form-label">Address <span class="text-danger">*</span></label>
                            <textarea name="address" class="form-control @error('address', 'userCreation') is-invalid @enderror" placeholder="Address" rows="2" maxlength="500" required>{{ old('address') }}</textarea>
                            @error('address', 'userCreation')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Place of Birth (Always Visible) -->
                        <div class="col-md-6">
                            <label class="form-label">Place of Birth <span class="text-danger">*</span></label>
                            <input type="text" name="place_of_birth" class="form-control @error('place_of_birth', 'userCreation') is-invalid @enderror" placeholder="Place of Birth" value="{{ old('place_of_birth') }}" pattern="[A-Za-z\s]+" title="Only letters and spaces allowed" oninput="this.value = this.value.replace(/[^A-Za-z\s]/g, '');" maxlength="255" required>
                            @error('place_of_birth', 'userCreation')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Department (Always Visible but required for non-staff) -->
                        <div class="col-md-6" id="departmentContainer">
                            <label class="form-label">Department <span class="text-danger">*</span></label>
                            <select name="department_id" id="departmentSelect" class="form-select @error('department_id', 'userCreation') is-invalid @enderror">
                                <option value="" disabled {{ old('department_id') ? '' : 'selected' }}>Select Department</option>
                                @foreach($departments as $dept)
                                <option value="{{ $dept->id }}" {{ old('department_id') == $dept->id ? 'selected' : '' }}>
                                    {{ $dept->name }}
                                </option>
                                @endforeach
                            </select>
                            @error('department_id', 'userCreation')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Job Type (Only for Staff Employee) -->
                        <div class="col-md-6 d-none" id="jobTypeContainer">
                            <label class="form-label">Job Type <span class="text-danger">*</span></label>
                            <select name="job_type" id="jobTypeSelect" class="form-select @error('job_type', 'userCreation') is-invalid @enderror">
                                <option value="" selected disabled>Select Job Type</option>
                                <option value="Full Time" {{ old('job_type') == 'Full Time' ? 'selected' : '' }}>Full Time</option>
                                <option value="Half Time" {{ old('job_type') == 'Half Time' ? 'selected' : '' }}>Half Time</option>
                            </select>
                            @error('job_type', 'userCreation')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Password (Always Visible) -->
                        <div class="col-md-6">
                            <label class="form-label">Password <span class="text-danger">*</span></label>
                            <input type="password" name="password" class="form-control @error('password', 'userCreation') is-invalid @enderror" placeholder="Password" minlength="6" required>
                            @error('password', 'userCreation')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Confirm Password <span class="text-danger">*</span></label>
                            <input type="password" name="password_confirmation" class="form-control @error('password_confirmation', 'userCreation') is-invalid @enderror" placeholder="Confirm Password" minlength="6" required>
                            @error('password_confirmation', 'userCreation')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Father's Name (Only for non-staff) -->
                        <div class="col-md-6 additional-fields" style="display: none;">
                            <label class="form-label">Father's Name</label>
                            <input type="text" name="father_name" class="form-control @error('father_name', 'userCreation') is-invalid @enderror" placeholder="Father's Name" value="{{ old('father_name') }}" pattern="[A-Za-z\s]+" title="Only letters and spaces allowed" oninput="this.value = this.value.replace(/[^A-Za-z\s]/g, '');" maxlength="255">
                            @error('father_name', 'userCreation')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Aadhar Details (Only for non-staff) -->
                        <div class="col-md-6 additional-fields" style="display: none;">
                            <label class="form-label">Aadhar Card Number</label>
                            <input type="text" name="aadhar_card_no" class="form-control @error('aadhar_card_no', 'userCreation') is-invalid @enderror" placeholder="Aadhar Card Number" value="{{ old('aadhar_card_no') }}" pattern="[0-9]{12}" title="Enter valid 12-digit Aadhar number" maxlength="12" oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 12);">
                            @error('aadhar_card_no', 'userCreation')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12 additional-fields" style="display: none;">
                            <label class="form-label">Aadhar Card Image</label>
                            <input type="file" name="aadhar_card_image" class="form-control @error('aadhar_card_image', 'userCreation') is-invalid @enderror" accept="image/jpeg,image/png,image/jpg,image/gif"
                                data-preview-wrap="addAadharPreviewWrap"
                                data-preview-img="addAadharPreview"
                                data-preview-link="addAadharPreviewLink"
                                onchange="renderFilePreview(this)">
                            <div id="addAadharPreviewWrap" class="mt-2 d-none">
                                <img id="addAadharPreview" src="" class="img-thumbnail" style="max-height: 120px;">
                                <a id="addAadharPreviewLink" href="#" target="_blank" class="btn btn-sm btn-outline-secondary d-none">View file</a>
                            </div>
                            <small class="text-muted">Allowed: JPEG, PNG, JPG, GIF (Max: 2MB)</small>
                            @error('aadhar_card_image', 'userCreation')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- PAN Details (Only for non-staff) -->
                        <div class="col-md-6 additional-fields" style="display: none;">
                            <label class="form-label">PAN Card Number</label>
                            <input type="text" name="pan_card_no" class="form-control @error('pan_card_no', 'userCreation') is-invalid @enderror" placeholder="PAN Card Number" value="{{ old('pan_card_no') }}" pattern="[A-Z]{5}[0-9]{4}[A-Z]{1}" title="Enter valid PAN card number (e.g., ABCDE1234F)" maxlength="10" oninput="this.value = this.value.toUpperCase();">
                            @error('pan_card_no', 'userCreation')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12 additional-fields" style="display: none;">
                            <label class="form-label">PAN Card Image</label>
                            <input type="file" name="pan_card_image" class="form-control @error('pan_card_image', 'userCreation') is-invalid @enderror" accept="image/jpeg,image/png,image/jpg,image/gif"
                                data-preview-wrap="addPanPreviewWrap"
                                data-preview-img="addPanPreview"
                                data-preview-link="addPanPreviewLink"
                                onchange="renderFilePreview(this)">
                            <div id="addPanPreviewWrap" class="mt-2 d-none">
                                <img id="addPanPreview" src="" class="img-thumbnail" style="max-height: 120px;">
                                <a id="addPanPreviewLink" href="#" target="_blank" class="btn btn-sm btn-outline-secondary d-none">View file</a>
                            </div>
                            <small class="text-muted">Allowed: JPEG, PNG, JPG, GIF (Max: 2MB)</small>
                            @error('pan_card_image', 'userCreation')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Driving License (Only for non-staff) -->
                        <div class="col-md-6 additional-fields" style="display: none;">
                            <label class="form-label">Driving License Number</label>
                            <input type="text" name="driving_license_no" class="form-control @error('driving_license_no', 'userCreation') is-invalid @enderror" placeholder="Driving License Number" value="{{ old('driving_license_no') }}" maxlength="50">
                            @error('driving_license_no', 'userCreation')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12 additional-fields" style="display: none;">
                            <label class="form-label">Driving License</label>
                            <input type="file" name="driving_license_image" class="form-control @error('driving_license_image', 'userCreation') is-invalid @enderror" accept="image/jpeg,image/png,image/jpg,image/gif,pdf"
                                data-preview-wrap="addDrivingPreviewWrap"
                                data-preview-img="addDrivingPreview"
                                data-preview-link="addDrivingPreviewLink"
                                onchange="renderFilePreview(this)">
                            <div id="addDrivingPreviewWrap" class="mt-2 d-none">
                                <img id="addDrivingPreview" src="" class="img-thumbnail d-none" style="max-height: 120px;">
                                <a id="addDrivingPreviewLink" href="#" target="_blank" class="btn btn-sm btn-outline-secondary d-none">View file</a>
                            </div>
                            <small class="text-muted">Allowed: JPEG, PNG, JPG, GIF, PDF (Max: 2MB)</small>
                            @error('driving_license_image', 'userCreation')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Bank Details Section (Only for non-staff) -->
                        <div class="col-12 mt-3 additional-fields" style="display: none;">
                            <h6 class="fw-bold">Bank Details</h6>
                            <hr class="mt-1 mb-3">
                        </div>

                        <div class="col-md-6 additional-fields" style="display: none;">
                            <label class="form-label">Bank Account Number</label>
                            <input type="text" name="bank_account_no" class="form-control @error('bank_account_no', 'userCreation') is-invalid @enderror" placeholder="Bank Account Number" value="{{ old('bank_account_no') }}" pattern="[0-9]{10,16}" title="Bank account number must be 10 to 16 digits" minlength="10" maxlength="16" oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 16);">
                            @error('bank_account_no', 'userCreation')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 additional-fields" style="display: none;">
                            <label class="form-label">IFSC Code</label>
                            <input type="text" name="ifsc_code" class="form-control @error('ifsc_code', 'userCreation') is-invalid @enderror" placeholder="IFSC Code" value="{{ old('ifsc_code') }}" pattern="[A-Z]{4}0[A-Z0-9]{6}" title="Enter valid IFSC code (e.g., SBIN0012345)" maxlength="11" oninput="this.value = this.value.toUpperCase();">
                            @error('ifsc_code', 'userCreation')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 additional-fields" style="display: none;">
                            <label class="form-label">Bank Name</label>
                            <input type="text" name="bank_name" class="form-control @error('bank_name', 'userCreation') is-invalid @enderror" placeholder="Bank Name" value="{{ old('bank_name') }}" maxlength="255">
                            @error('bank_name', 'userCreation')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 additional-fields" style="display: none;">
                            <label class="form-label">Branch Name</label>
                            <input type="text" name="bank_branch" class="form-control @error('bank_branch', 'userCreation') is-invalid @enderror" placeholder="Branch Name" value="{{ old('bank_branch') }}" maxlength="255">
                            @error('bank_branch', 'userCreation')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12 additional-fields" style="display: none;">
                            <label class="form-label">Bank Passbook/Cancelled Cheque Image</label>
                            <input type="file" name="bank_proof_image" class="form-control @error('bank_proof_image', 'userCreation') is-invalid @enderror" accept="image/jpeg,image/png,image/jpg,image/gif,pdf"
                                data-preview-wrap="addBankProofPreviewWrap"
                                data-preview-img="addBankProofPreview"
                                data-preview-link="addBankProofPreviewLink"
                                onchange="renderFilePreview(this)">
                            <div id="addBankProofPreviewWrap" class="mt-2 d-none">
                                <img id="addBankProofPreview" src="" class="img-thumbnail d-none" style="max-height: 120px;">
                                <a id="addBankProofPreviewLink" href="#" target="_blank" class="btn btn-sm btn-outline-secondary d-none">View file</a>
                            </div>
                            <small class="text-muted">Allowed: JPEG, PNG, JPG, GIF, PDF (Max: 2MB)</small>
                            @error('bank_proof_image', 'userCreation')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="modal-footer border-top-0">
                    <button type="button" class="btn btn-outline-primary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" onclick="showFormLoader()">Save Employee</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    // Global variables for form state preservation
    let formStateKey = 'employeeFormState';
    let validationToastShown = false;

    // Show loading modal during form submission
    function showFormLoader() {
        const el = document.getElementById('loadingModal');
        if (!el) return;
        let m = bootstrap.Modal.getInstance(el);
        if (!m) m = new bootstrap.Modal(el);
        m.show();
    }

    function hideFormLoader() {
        const el = document.getElementById('loadingModal');
        const m = el ? bootstrap.Modal.getInstance(el) : null;
        if (m) m.hide();
    }

    // Save form state before submission (password, file inputs)
    function saveFormState(formSelector, formId = null) {
        const form = document.querySelector(formSelector);
        if (!form) return;

        const state = {};

        const passwordInput = form.querySelector('input[name="password"]');
        if (passwordInput && passwordInput.value) state.password = passwordInput.value;
        const confirmInput = form.querySelector('input[name="password_confirmation"]');
        if (confirmInput && confirmInput.value) state.password_confirmation = confirmInput.value;

        // Store in sessionStorage
        sessionStorage.setItem(formStateKey + (formId || ''), JSON.stringify(state));
    }

    // Restore form state after validation error (password only; file inputs cannot be restored after redirect)
    function restoreFormState(formSelector, formId = null) {
        const form = document.querySelector(formSelector);
        if (!form) return;

        const savedState = sessionStorage.getItem(formStateKey + (formId || ''));
        if (!savedState) return;

        try {
            const state = JSON.parse(savedState);
            if (state.password) {
                const passwordInput = form.querySelector('input[name="password"]');
                if (passwordInput) passwordInput.value = state.password;
            }
            const confirmInput = form.querySelector('input[name="password_confirmation"]');
            if (confirmInput && state.password_confirmation) confirmInput.value = state.password_confirmation;
        } catch (e) {
            console.warn('Failed to restore form state', e);
        }
    }

    // Clear form state from storage
    function clearFormState(formId = null) {
        sessionStorage.removeItem(formStateKey + (formId || ''));
    }

    function focusFirstInvalidField(containerSelector) {
        const container = document.querySelector(containerSelector);
        if (!container) return;
        const firstInvalid = container.querySelector('.is-invalid, :invalid');
        if (!firstInvalid) return;
        if (typeof firstInvalid.focus === 'function') firstInvalid.focus();
        if (typeof firstInvalid.scrollIntoView === 'function') {
            firstInvalid.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
    }

    function showValidationToast(message) {
        if (validationToastShown) return;
        validationToastShown = true;
        Swal.fire({
            toast: true,
            position: 'top-end',
            icon: 'warning',
            title: message,
            showConfirmButton: false,
            timer: 2600,
            timerProgressBar: true
        });
    }

    document.addEventListener('DOMContentLoaded', function() {
        @if($errors->userCreation->any())
        var addUserModal = new bootstrap.Modal(document.getElementById('addUsersModal'));
        addUserModal.show();
        var roleSelect = document.getElementById('roleSelect');
        if (roleSelect) toggleFieldsByRole(roleSelect, { fromServer: true });
        restoreFormState('#addUserForm');
        setTimeout(function() {
            focusFirstInvalidField('#addUsersModal');
        }, 250);
        showValidationToast('Please check highlighted fields in Add Employee form.');
        @endif

        @foreach($users as $user)
        @if($errors->{'userUpdate'.$user->id}->any())
        var editUserModal = new bootstrap.Modal(document.getElementById('editUserModal{{ $user->id }}'));
        editUserModal.show();
        var editRoleSelect{{ $user->id }} = document.querySelector('#editUserModal{{ $user->id }} select[name="role_id"]');
        if (editRoleSelect{{ $user->id }}) toggleEditFieldsByRole(editRoleSelect{{ $user->id }}, '{{ $user->id }}', { fromServer: true });
        restoreFormState('form[action="{{ route('employees.update', $user->id) }}"]', '{{ $user->id }}');
        setTimeout(function() {
            focusFirstInvalidField('#editUserModal{{ $user->id }}');
        }, 250);
        showValidationToast('Please check highlighted fields in Edit Employee form.');
        @endif
        @endforeach

        @foreach($users as $user)
        var initEditRoleSelect{{ $user->id }} = document.querySelector('#editUserModal{{ $user->id }} select[name="role_id"]');
        if (initEditRoleSelect{{ $user->id }}) toggleEditFieldsByRole(initEditRoleSelect{{ $user->id }}, '{{ $user->id }}', { fromServer: true });
        @endforeach
    });

    document.addEventListener('DOMContentLoaded', function() {
        // Edit User Forms - Save state on submit
        document.querySelectorAll('form[action*="employees/"][method="POST"]').forEach(function(form) {
            const updateAction = form.getAttribute('action');
            if (updateAction && updateAction.includes('employees/') && form.querySelector('input[name="_method"][value="PUT"]')) {
                const userId = updateAction.match(/\d+/)[0];
                form.addEventListener('submit', function(event) {
                    saveFormState('form[action="' + updateAction + '"]', userId);
                    if (!form.checkValidity()) {
                        event.preventDefault();
                        event.stopPropagation();
                        hideFormLoader();
                    }
                    form.classList.add('was-validated');
                });
            }
        });

        document.querySelectorAll('.delete-form').forEach(function(form) {
            form.addEventListener('submit', function(e) {
                e.preventDefault();

                Swal.fire({
                    title: 'Are you sure?',
                    text: "This employee will be deleted permanently!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, delete it!',
                    cancelButtonText: 'Cancel',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });

        // Status Toggle Script
        document.querySelectorAll('.status-toggle').forEach(function(toggle) {
            toggle.addEventListener('change', function() {
                var userId = this.getAttribute('data-id');
                var status = this.checked ? 1 : 0;
                var url = '{{ route("employees.updateStatus") }}';

                fetch(url, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            id: userId,
                            status: status
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            const Toast = Swal.mixin({
                                toast: true,
                                position: 'top-end',
                                showConfirmButton: false,
                                timer: 3000,
                                timerProgressBar: true,
                                didOpen: (toast) => {
                                    toast.addEventListener('mouseenter', Swal.stopTimer)
                                    toast.addEventListener('mouseleave', Swal.resumeTimer)
                                }
                            })

                            Toast.fire({
                                icon: 'success',
                                title: data.success
                            })
                        } else {
                            Swal.fire('Error', 'Something went wrong!', 'error');
                            this.checked = !status;
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        Swal.fire('Error', 'An error occurred.', 'error');
                        this.checked = !status;
                    });
            });
        });

        // Add User Form Validation
        const addUserForm = document.getElementById('addUserForm');
        if (addUserForm) {
            addUserForm.addEventListener('submit', function(event) {
                // Save form state before submission
                saveFormState('#addUserForm');
                if (!addUserForm.checkValidity()) {
                    event.preventDefault();
                    event.stopPropagation();
                    hideFormLoader();
                }
                addUserForm.classList.add('was-validated');
            }, false);

            // Allow only numbers for phone
            const phoneInput = addUserForm.querySelector('input[name="phone"]');
            if (phoneInput) {
                phoneInput.addEventListener('input', function(e) {
                    this.value = this.value.replace(/[^0-9]/g, '').slice(0, 10);
                });
            }

            // Reset form fields when modal is hidden
            document.getElementById('addUsersModal').addEventListener('hidden.bs.modal', function () {
                addUserForm.reset();
                addUserForm.classList.remove('was-validated');
                var deptC = document.getElementById('departmentContainer');
                var deptS = document.getElementById('departmentSelect');
                var jobC = document.getElementById('jobTypeContainer');
                var jobS = document.getElementById('jobTypeSelect');
                if (deptC) deptC.style.display = 'block';
                if (jobC) jobC.classList.add('d-none');
                if (deptS) {
                    deptS.disabled = false;
                    deptS.setAttribute('required', 'required');
                }
                if (jobS) {
                    jobS.removeAttribute('required');
                    jobS.disabled = true;
                }
                document.querySelectorAll('.additional-fields').forEach(function(f) { f.style.display = 'none'; });
                addUserForm.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
            });

            // Initialize role-based field visibility when modal is shown
            document.getElementById('addUsersModal').addEventListener('shown.bs.modal', function () {
                const roleSelect = document.getElementById('roleSelect');
                if (!roleSelect.value) {
                    var deptC = document.getElementById('departmentContainer');
                    var deptS = document.getElementById('departmentSelect');
                    var jobC = document.getElementById('jobTypeContainer');
                    var jobS = document.getElementById('jobTypeSelect');
                    if (deptC) deptC.style.display = 'block';
                    if (jobC) jobC.classList.add('d-none');
                    if (deptS) {
                        deptS.disabled = false;
                        deptS.setAttribute('required', 'required');
                    }
                    if (jobS) {
                        jobS.removeAttribute('required');
                        jobS.disabled = true;
                    }
                }
            });
        }
    });

    /**
     * Show/Hide custom fields based on Role selection
     */
    function toggleJobType(selectElement, containerId = 'jobTypeContainer') {
        const container = document.getElementById(containerId);
        if (!container) return;

        const selectedText = selectElement.options[selectElement.selectedIndex].text.trim().toLowerCase();

        // Use a more generic check or specific role name
        // The user specifically asked for "Staff Employee"
        if (selectedText === 'staff employee') {
            container.classList.remove('d-none');
            // Make required if visible?
            const jobSelect = container.querySelector('select');
            if (jobSelect) jobSelect.setAttribute('required', 'required');
        } else {
            container.classList.add('d-none');
            const jobSelect = container.querySelector('select');
            if (jobSelect) {
                jobSelect.removeAttribute('required');
                jobSelect.value = ""; // Clear selection
            }
        }
    }
    /**
 * Show/Hide fields based on Role selection.
 * opts.fromServer: true after redirect with validation errors or on init — do not clear inputs (preserves old()).
 */
function toggleFieldsByRole(selectElement, opts) {
    opts = opts || {};
    const fromServer = !!opts.fromServer;

    const selectedOption = selectElement.options[selectElement.selectedIndex];
    const roleName = selectedOption ? selectedOption.getAttribute('data-role-name') : null;

    if (!roleName) return;

    const isStaff = roleName === 'Staff Employee';
    const isDriver = roleName.toLowerCase().includes('driver');

    const additionalFields = document.querySelectorAll('.additional-fields');
    const departmentContainer = document.getElementById('departmentContainer');
    const jobTypeContainer = document.getElementById('jobTypeContainer');
    const departmentSelect = document.getElementById('departmentSelect');
    const jobTypeSelect = document.getElementById('jobTypeSelect');

    function clearHiddenFieldInputs(container) {
        container.querySelectorAll('input, select, textarea').forEach(function(input) {
            if (input.type === 'file') {
                input.value = '';
                const w = input.getAttribute('data-preview-wrap');
                const iw = input.getAttribute('data-preview-img');
                const lw = input.getAttribute('data-preview-link');
                if (w) {
                    const wrap = document.getElementById(w);
                    if (wrap) wrap.classList.add('d-none');
                }
                if (iw) {
                    const img = document.getElementById(iw);
                    if (img) { img.src = ''; img.classList.add('d-none'); }
                }
                if (lw) {
                    const link = document.getElementById(lw);
                    if (link) { link.href = '#'; link.classList.add('d-none'); }
                }
            } else if (input.type === 'checkbox' || input.type === 'radio') {
                input.checked = false;
            } else {
                input.value = '';
            }
            input.removeAttribute('required');
        });
    }

    if (isStaff) {
        if (!fromServer) {
            additionalFields.forEach(function(field) { clearHiddenFieldInputs(field); });
        }
        additionalFields.forEach(function(field) {
            field.style.display = 'none';
            field.querySelectorAll('input, select, textarea').forEach(function(input) {
                input.removeAttribute('required');
            });
        });

        if (departmentContainer) departmentContainer.style.display = 'none';
        if (departmentSelect) {
            departmentSelect.removeAttribute('required');
            departmentSelect.disabled = true;
            if (!fromServer) departmentSelect.value = '';
        }

        if (jobTypeContainer) jobTypeContainer.classList.remove('d-none');
        if (jobTypeSelect) {
            jobTypeSelect.disabled = false;
            jobTypeSelect.setAttribute('required', 'required');
            if (!fromServer && !jobTypeSelect.value) jobTypeSelect.selectedIndex = 0;
        }

        setDriverKycRequiredFields(document, false, false);
    } else {
        if (!fromServer && jobTypeSelect) {
            jobTypeSelect.removeAttribute('required');
            jobTypeSelect.value = '';
        }

        additionalFields.forEach(function(field) {
            field.style.display = 'block';
        });

        if (jobTypeContainer) jobTypeContainer.classList.add('d-none');
        if (jobTypeSelect) {
            jobTypeSelect.removeAttribute('required');
            jobTypeSelect.disabled = true;
        }

        if (departmentContainer) departmentContainer.style.display = 'block';
        if (departmentSelect) {
            departmentSelect.disabled = false;
            departmentSelect.setAttribute('required', 'required');
        }

        setDriverKycRequiredFields(document, isDriver, false);
    }
}

function setDriverKycRequiredFields(scope, isRequired, allowExistingFile = false) {
    const requiredNames = [
        'father_name',
        'aadhar_card_no',
        'aadhar_card_image',
        'pan_card_no',
        'pan_card_image'
    ];

    requiredNames.forEach(function(name) {
        scope.querySelectorAll('[name="' + name + '"]').forEach(function(input) {
            const isFileField = input.type === 'file';
            const hasExistingFile = !!(input.getAttribute('data-existing-url') || '').trim();

            if (isRequired) {
                if (isFileField && allowExistingFile && hasExistingFile) {
                    input.removeAttribute('required');
                } else {
                    input.setAttribute('required', 'required');
                }
            } else {
                input.removeAttribute('required');
            }
        });
    });
}

function toggleEditFieldsByRole(selectElement, userId, opts) {
    opts = opts || {};
    const fromServer = !!opts.fromServer;

    const modal = document.getElementById('editUserModal' + userId);
    if (!modal) return;

    const selectedText = selectElement.options[selectElement.selectedIndex].text.trim().toLowerCase();
    const isStaff = selectedText === 'staff employee';
    const isDriver = selectedText.includes('driver');

    const additionalFields = modal.querySelectorAll('.edit-additional-fields-' + userId);
    const departmentContainer = document.getElementById('editDepartmentContainer' + userId);
    const departmentSelect = document.getElementById('editDepartmentSelect' + userId);
    const jobTypeContainer = document.getElementById('editJobTypeContainer' + userId);
    const jobTypeSelect = document.getElementById('editJobTypeSelect' + userId);

    function clearEditHiddenInputs(field) {
        field.querySelectorAll('input, select, textarea').forEach(function(input) {
            if (input.type === 'file') {
                input.value = '';
                const pw = input.getAttribute('data-preview-wrap');
                if (pw) {
                    const wrap = document.getElementById(pw);
                    const exUrl = (input.getAttribute('data-existing-url') || '').trim();
                    if (wrap) {
                        if (exUrl) {
                            wrap.classList.remove('d-none');
                            renderFilePreview(input);
                        } else {
                            wrap.classList.add('d-none');
                        }
                    }
                }
            } else if (input.type !== 'hidden') {
                input.value = '';
            }
            input.removeAttribute('required');
        });
    }

    if (isStaff) {
        if (!fromServer) {
            additionalFields.forEach(function(field) { clearEditHiddenInputs(field); });
        }
        additionalFields.forEach(function(field) {
            field.style.display = 'none';
            field.querySelectorAll('input, select, textarea').forEach(function(input) {
                input.removeAttribute('required');
            });
        });

        if (jobTypeContainer) jobTypeContainer.classList.remove('d-none');
        if (jobTypeSelect) {
            jobTypeSelect.setAttribute('required', 'required');
            if (!fromServer && !jobTypeSelect.value) jobTypeSelect.selectedIndex = 0;
        }

        if (departmentContainer) departmentContainer.style.display = 'none';
        if (departmentSelect) {
            departmentSelect.removeAttribute('required');
            departmentSelect.disabled = true;
            if (!fromServer) departmentSelect.value = '';
        }
        setDriverKycRequiredFields(modal, false, true);
    } else {
        if (!fromServer && jobTypeSelect) {
            jobTypeSelect.removeAttribute('required');
            jobTypeSelect.value = '';
        }

        additionalFields.forEach(function(field) {
            field.style.display = 'block';
        });

        if (jobTypeContainer) jobTypeContainer.classList.add('d-none');
        if (jobTypeSelect) {
            jobTypeSelect.removeAttribute('required');
            jobTypeSelect.disabled = true;
        }

        if (departmentContainer) departmentContainer.style.display = 'block';
        if (departmentSelect) {
            departmentSelect.disabled = false;
            departmentSelect.setAttribute('required', 'required');
        }

        modal.querySelectorAll('input[data-preview-wrap]').forEach(function(inp) {
            renderFilePreview(inp);
        });

        setDriverKycRequiredFields(modal, isDriver, true);
    }
}

document.addEventListener('DOMContentLoaded', function() {
    const roleSelect = document.getElementById('roleSelect');
    if (roleSelect && roleSelect.value) {
        toggleFieldsByRole(roleSelect, { fromServer: true });
    }

        const today = new Date().toISOString().split('T')[0];
        document.querySelectorAll('input[name="date_of_birth"]').forEach(function(input) {
            input.setAttribute('max', today);
            input.addEventListener('change', function() {
                if (this.value && this.value > today) {
                    this.value = today;
                }
            });
        });

    document.querySelectorAll('input[data-preview-wrap]').forEach(function(input) {
        renderFilePreview(input);
    });
});

function renderFilePreview(input) {
    const wrapId = input.getAttribute('data-preview-wrap');
    const imgId = input.getAttribute('data-preview-img');
    const linkId = input.getAttribute('data-preview-link');

    if (!wrapId || !imgId || !linkId) return;

    const wrap = document.getElementById(wrapId);
    const img = document.getElementById(imgId);
    const link = document.getElementById(linkId);
    if (!wrap || !img || !link) return;

    const file = input.files && input.files.length ? input.files[0] : null;
    const existingUrl = input.getAttribute('data-existing-url') || '';
    const existingType = input.getAttribute('data-existing-type') || '';
    const previewUrl = file ? URL.createObjectURL(file) : existingUrl;

    if (!previewUrl) {
        wrap.classList.add('d-none');
        img.classList.add('d-none');
        img.removeAttribute('src');
        link.classList.add('d-none');
        link.removeAttribute('href');
        return;
    }

    const fileName = file ? file.name.toLowerCase() : previewUrl.toLowerCase();
    const isPdf = file ? (file.type === 'application/pdf' || fileName.endsWith('.pdf')) : (existingType === 'pdf' || fileName.endsWith('.pdf'));

    wrap.classList.remove('d-none');

    if (isPdf) {
        img.classList.add('d-none');
        img.removeAttribute('src');
        link.classList.remove('d-none');
        link.href = previewUrl;
        link.textContent = file ? 'Preview selected PDF' : 'View current PDF';
    } else {
        link.classList.add('d-none');
        link.removeAttribute('href');
        img.classList.remove('d-none');
        img.src = previewUrl;
    }
}
</script>
@endpush