@extends('layouts.admin')

@section('title', 'Vendor Management')

@section('content')



<div class="main-section">

    @if(session('error'))

        @push('scripts')

        <script>

            document.addEventListener('DOMContentLoaded', function () {

                Swal.fire({

                    icon: 'error',

                    title: 'Error',

                    text: '{{ session('error') }}',

                    confirmButtonColor: '#d33'

                });

            });

        </script>

        @endpush

    @endif



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



    <!-- Header with Add Button -->

    <div class="d-flex justify-content-between align-items-center mb-3">

        <h4 class="fw-bold">Vendor List</h4>

        <div>

            @include('partials.export-dropdown', [
                'exportRoute' => 'vendors.export',
                'exportQuery' => request()->only(['search', 'status', 'sort_by', 'sort_order']),
            ])

            <button class="btn btn-primary rounded-3" data-bs-toggle="modal" data-bs-target="#addVendorModal">

                <i class="bi bi-person-plus-fill me-1"></i> Add Vendor

            </button>

        </div>

    </div>



    <form method="GET" action="{{ route('vendors.index') }}" class="row g-2 mb-3">

        <div class="col-md-4">

            <input type="text" name="search" value="{{ request('search') }}"

                class="form-control"

                placeholder="Search by name, email, phone, city, state...">

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

            <a href="{{ route('vendors.index') }}" class="btn btn-outline-secondary">Reset</a>

        </div>

    </form>



    <!-- Vendors Table Card -->

    <div class="card shadow-sm rounded border mb-4">

        <div class="card-body">

            <div class="table-responsive">

                <table id="vendorsTable" class="table table-hover table-bordered align-middle mb-0">

                    <thead class="table-light text-center">

                        <tr>

                            <th style="width: 5%;">#</th>

                            <x-sortable-th name="name" label="Vendor Name" />

                            <th class="text-center" style="width: 80px;">Image</th>

                            <x-sortable-th name="email" label="Email" />

                            <x-sortable-th name="phone" label="Mobile" />

                            <x-sortable-th name="business_name" label="Business Name" />

                            <x-sortable-th name="pan_number" label="PAN" />

                            <x-sortable-th name="aadhar_number" label="Aadhar" />

                            <th>Aadhar Image</th>

                            <x-sortable-th name="gst_number" label="GST Number" />

                            <x-sortable-th name="state" label="State" />

                            <x-sortable-th name="city" label="City" />

                            <x-sortable-th name="address" label="Address" />
                            

                            <x-sortable-th name="status" label="Status" class="text-center" />

                            <th style="width: 10%;">Actions</th>

                        </tr>

                    </thead>

                    <tbody>

                        @forelse($vendors as $vendor)

                            <tr>

                                <td class="text-center">{{ $vendors->firstItem() + $loop->index }}</td>

                                <td>{{ $vendor->name ?? '-' }}</td>

                                <td class="text-center">                                    

                                    <img src="{{ $vendor->profile_image ? asset($vendor->profile_image) : asset('assets/admin/images/no-image.png') }}" 

                                         alt="Profile" 

                                         class="rounded-circle" 

                                         width="40" height="40" 

                                         style="object-fit: cover;" 

                                         onerror="this.src='{{ asset('assets/admin/images/no-image.png') }}'">

                                </td>

                                <td>{{ $vendor->email ?? '-' }}</td>

                                <td>{{ $vendor->phone ?? '-' }}</td>

                                <td>{{ $vendor->business_name ?? '-' }}</td>

                                <td>{{ $vendor->pan_number ?? '-' }}</td>

                                <td>{{ $vendor->aadhar_number ?? '-' }}</td>

                                <td class="text-center">
                                    @if($vendor->gst_document)
                                        <a href="{{ asset($vendor->gst_document) }}" target="_blank" class="btn btn-sm btn-outline-primary">View</a>
                                    @else
                                        -
                                    @endif
                                </td>

                                <td>{{ $vendor->gst_number ?? '-' }}</td>

                                <td>{{ $vendor->state ?? '-' }}</td>

                                <td>{{ $vendor->city ?? '-' }}</td>

                                <td>{{ $vendor->address ?? '-' }}</td>

                                <td class="text-center">

                                    <div class="form-check form-switch d-flex justify-content-center">

                                        <input class="form-check-input status-toggle" type="checkbox" role="switch" id="statusSwitch{{ $vendor->id }}" data-id="{{ $vendor->id }}" {{ $vendor->status ? 'checked' : '' }}>

                                    </div>

                                </td>

                                <!-- <td class="text-center">

                                 

                                     <button class="btn btn-sm btn-info text-white me-1" data-bs-toggle="modal" data-bs-target="#viewVendorModal{{ $vendor->id }}">

                                        <i class="bi bi-eye"></i>

                                    </button>


                                    <button class="btn btn-sm btn-secondary me-1" data-bs-toggle="modal" data-bs-target="#editVendorModal{{ $vendor->id }}">

                                        <i class="bi bi-pencil-square"></i>

                                    </button>

                               

                                    <form action="{{ route('vendors.destroy', $vendor->id) }}" method="POST" class="d-inline delete-form">

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
                data-bs-target="#viewVendorModal{{ $vendor->id }}">
            <i class="bi bi-eye"></i>
        </button>

        <!-- Edit -->
        <button class="btn btn-sm btn-secondary" 
                data-bs-toggle="modal" 
                data-bs-target="#editVendorModal{{ $vendor->id }}">
            <i class="bi bi-pencil-square"></i>
        </button>

        <!-- Delete -->
        <form action="{{ route('vendors.destroy', $vendor->id) }}" 
              method="POST" 
              class="d-inline delete-form"
              >
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

                                <td colspan="15" class="text-center py-4 text-muted">

                                    <i class="bi bi-inbox fs-1 d-block mb-2"></i>

                                    No Data Found

                                </td>

                            </tr>

                        @endforelse

                    </tbody>

                </table>

                <div class="mt-3 d-flex justify-content-between align-items-center">

                    <div class="text-muted small">

                        Showing {{ $vendors->firstItem() ?? 0 }}–{{ $vendors->lastItem() ?? 0 }} of {{ $vendors->total() }} entries

                    </div>

                    <div>

                        {{ $vendors->appends(request()->query())->links() }}

                    </div>

                </div>



            </div>

        </div>

    </div>



    <!-- View Vendor Modals -->

    @foreach($vendors as $vendor)

        <div class="modal fade" id="viewVendorModal{{ $vendor->id }}" tabindex="-1" aria-labelledby="viewVendorLabel{{ $vendor->id }}" aria-hidden="true">

            <div class="modal-dialog modal-lg modal-dialog-centered">

                <div class="modal-content border-info rounded-4 shadow-sm">

                    <div class="modal-header bg-info text-white py-2 px-3 border-bottom-0">

                        <h5 class="modal-title fw-bold" id="viewVendorLabel{{ $vendor->id }}">

                            <i class="bi bi-eye me-2"></i> Vendor Details

                        </h5>

                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>

                    </div>

                    <div class="modal-body">

                        <div class="text-center mb-4">

                            <img src="{{ $vendor->profile_image ? asset($vendor->profile_image) : asset('assets/admin/images/no-image.png') }}" 

                                 alt="Profile" 

                                 class="rounded-circle border shadow-sm" width="120" height="120" 

                                 style="object-fit: cover;" 

                                 onerror="this.src='{{ asset('assets/admin/images/no-image.png') }}'">

                        </div>

                        <div class="row g-4">

                            <div class="col-md-6">

                                <div class="mb-3">

                                    <label class="small text-muted mb-0">Full Name</label>

                                    <div class="fw-bold fs-5">{{ $vendor->name }}</div>

                                </div>

                                <div class="mb-3">

                                    <label class="small text-muted mb-0">Email</label>

                                    <div class="fw-bold">{{ $vendor->email }}</div>

                                </div>

                                <div class="mb-3">

                                    <label class="small text-muted mb-0">Mobile</label>

                                    <div class="fw-bold">{{ $vendor->phone }}</div>

                                </div>

                                <div class="mb-3">

                                    <label class="small text-muted mb-0">PAN</label>

                                    <div class="fw-bold">{{ $vendor->pan_number ?? 'N/A' }}</div>

                                </div>

                                <div class="mb-3">

                                    <label class="small text-muted mb-0">Aadhar</label>

                                    <div class="fw-bold">{{ $vendor->aadhar_number ?? 'N/A' }}</div>

                                </div>

                                <div class="mb-3">

                                    <label class="small text-muted mb-0">GST Number</label>

                                    <div class="fw-bold">{{ $vendor->gst_number ?? 'N/A' }}</div>

                                </div>

                            </div>

                            <div class="col-md-6">

                                <div class="mb-3">

                                    <label class="small text-muted mb-0">Status</label>

                                    <div>

                                        @if($vendor->status)

                                            <span class="badge bg-success">Active</span>

                                        @else

                                            <span class="badge bg-danger">Inactive</span>

                                        @endif

                                    </div>

                                </div>

                                <div class="mb-3">

                                    <label class="small text-muted mb-0">City/State</label>

                                    <div class="fw-bold">{{ $vendor->city ?? '-' }} / {{ $vendor->state ?? '-' }}</div>

                                </div>

                                <div class="mb-3">

                                    <label class="small text-muted mb-0">Address</label>

                                    <div class="fw-bold">{{ $vendor->address ?? '-' }}</div>

                                </div>

                                <div class="mb-3">

                                    <label class="small text-muted mb-0">Business Name</label>

                                    <div class="fw-bold">{{ $vendor->business_name ?? 'N/A' }}</div>

                                </div>

                                <div class="mb-3">

                                    <label class="small text-muted mb-0">Business Mobile</label>

                                    <div class="fw-bold">{{ $vendor->business_mobile ?? 'N/A' }}</div>

                                </div>

                                <div class="mb-3">

                                    <label class="small text-muted mb-0">Business PAN</label>

                                    <div class="fw-bold">{{ $vendor->buisness_pan ?? $vendor->business_pan ?? 'N/A' }}</div>

                                </div>

                                @if($vendor->gst_document)

                                <div class="mb-3">

                                    <label class="small text-muted mb-0">Aadhar Image</label>

                                    <div>

                                        <a href="{{ asset($vendor->gst_document) }}" target="_blank" class="btn btn-sm btn-outline-primary">View Image</a>

                                    </div>

                                </div>

                                @endif

                                <div class="mb-2 mt-3"><span class="small text-uppercase text-muted fw-bold">Bank &amp; cheque</span></div>

                                <div class="mb-3">

                                    <label class="small text-muted mb-0">Cancelled cheque details</label>

                                    <div class="fw-bold small" style="white-space:pre-wrap;">{{ $vendor->cancelled_cheque_details ?: '—' }}</div>

                                </div>

                                <div class="mb-3">

                                    <label class="small text-muted mb-0">Bank account number</label>

                                    <div class="fw-bold">{{ $vendor->bank_account_number ?? '—' }}</div>

                                </div>

                                <div class="mb-3">

                                    <label class="small text-muted mb-0">IFSC code</label>

                                    <div class="fw-bold">{{ $vendor->bank_ifsc_code ?? '—' }}</div>

                                </div>

                                @if($vendor->cancelled_cheque_image)

                                <div class="mb-3">

                                    <label class="small text-muted mb-0">Cancelled cheque image</label>

                                    <div><a href="{{ asset($vendor->cancelled_cheque_image) }}" target="_blank" class="btn btn-sm btn-outline-primary">View</a></div>

                                </div>

                                @endif

                                @if($vendor->bank_account_image)

                                <div class="mb-3">

                                    <label class="small text-muted mb-0">Bank account image</label>

                                    <div><a href="{{ asset($vendor->bank_account_image) }}" target="_blank" class="btn btn-sm btn-outline-primary">View</a></div>

                                </div>

                                @endif

                                @if($vendor->bank_statement_image)

                                <div class="mb-3">

                                    <label class="small text-muted mb-0">Bank statement image</label>

                                    <div><a href="{{ asset($vendor->bank_statement_image) }}" target="_blank" class="btn btn-sm btn-outline-primary">View</a></div>

                                </div>

                                @endif

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



    <!-- Edit Vendor Modals -->

    @foreach($vendors as $vendor)

        <div class="modal fade" id="editVendorModal{{ $vendor->id }}" tabindex="-1" aria-labelledby="editVendorLabel{{ $vendor->id }}" aria-hidden="true">

            <div class="modal-dialog modal-lg modal-dialog-centered">

                <div class="modal-content border-primary rounded-4 shadow-sm">

                    <form action="{{ route('vendors.update', $vendor->id) }}" method="POST" enctype="multipart/form-data">

                        @csrf

                        @method('PUT')

                        <div class="modal-header py-2 px-3 border-bottom-0">

                            <h5 class="modal-title fw-bold" id="editVendorLabel{{ $vendor->id }}">

                                <i class="bi bi-pencil-square me-2"></i> Edit Vendor

                            </h5>

                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>

                        </div>



                        <div class="modal-body">

                           <div class="row g-3">

                            <!-- Image Preview -->

                            <div class="col-12 text-center mb-3">

                                <img id="editPreview{{ $vendor->id }}" 

                                     src="{{ $vendor->profile_image ? asset($vendor->profile_image) : asset('assets/admin/images/no-image.png') }}" 

                                     class="rounded-circle border shadow-sm" width="100" height="100" style="object-fit: cover;" 

                                     onerror="this.src='{{ asset('assets/admin/images/no-image.png') }}'">

                            </div>

                            

                            <div class="col-12">

                                <label class="form-label">Profile Image</label>

                                <input type="file" name="profile_image" class="form-control @error('profile_image', 'vendorUpdate'.$vendor->id) is-invalid @enderror" accept="image/*"

                                    onchange="document.getElementById('editPreview{{ $vendor->id }}').src = window.URL.createObjectURL(this.files[0])">

                                    @error('profile_image', 'vendorUpdate'.$vendor->id)

                                        <div class="invalid-feedback">{{ $message }}</div>

                                    @enderror

                            </div>



                            <div class="col-md-6">

                                <label class="form-label">Full Name</label>

                                <input type="text" name="name" class="form-control @error('name', 'vendorUpdate'.$vendor->id) is-invalid @enderror" placeholder="Full Name" 

                                    value="{{ old('name', $vendor->name) }}" required>

                                    @error('name', 'vendorUpdate'.$vendor->id)

                                        <div class="invalid-feedback">{{ $message }}</div>

                                    @enderror

                            </div>

                            <div class="col-md-6">

                                <label class="form-label">Email</label>

                                <input type="email" name="email" class="form-control @error('email', 'vendorUpdate'.$vendor->id) is-invalid @enderror" placeholder="Email" 

                                    value="{{ old('email', $vendor->email) }}" required>

                                    @error('email', 'vendorUpdate'.$vendor->id)

                                        <div class="invalid-feedback">{{ $message }}</div>

                                    @enderror

                            </div>

                            <div class="col-md-6">

                                <label class="form-label">Mobile</label>

                                <input type="text" name="phone" class="form-control phone-only @error('phone', 'vendorUpdate'.$vendor->id) is-invalid @enderror" placeholder="10-digit mobile (9876543210)" 

                                    value="{{ old('phone', $vendor->phone) }}" maxlength="10" minlength="10" pattern="^[0-9]{10}$" inputmode="numeric" required>

                                    @error('phone', 'vendorUpdate'.$vendor->id)

                                        <div class="invalid-feedback">{{ $message }}</div>

                                    @enderror

                            </div>

                            <div class="col-md-6">

                                <label class="form-label">PAN</label>

                                <input type="text" name="pan_number" class="form-control pan-input @error('pan_number', 'vendorUpdate'.$vendor->id) is-invalid @enderror" placeholder="PAN (ABCDE1234F)" 

                                    value="{{ old('pan_number', $vendor->pan_number) }}" maxlength="10" minlength="10" pattern="^[A-Z]{5}[0-9]{4}[A-Z]{1}$" required>

                                    @error('pan_number', 'vendorUpdate'.$vendor->id)

                                        <div class="invalid-feedback">{{ $message }}</div>

                                    @enderror

                            </div>

                            <div class="col-md-6">

                                <label class="form-label">Aadhar</label>

                                <input type="text" name="aadhar_number" class="form-control aadhar-input @error('aadhar_number', 'vendorUpdate'.$vendor->id) is-invalid @enderror" placeholder="12-digit Aadhaar number" 

                                    value="{{ old('aadhar_number', $vendor->aadhar_number) }}" maxlength="12" minlength="12" pattern="^[0-9]{12}$" inputmode="numeric" required>

                                    @error('aadhar_number', 'vendorUpdate'.$vendor->id)

                                        <div class="invalid-feedback">{{ $message }}</div>

                                    @enderror

                            </div>

                            <div class="col-md-6">

                                <label class="form-label">Business Name <span class="text-muted">(Optional)</span></label>

                                <input type="text" name="business_name" class="form-control @error('business_name', 'vendorUpdate'.$vendor->id) is-invalid @enderror" placeholder="Business Name" maxlength="255" pattern="^[A-Za-z0-9][A-Za-z0-9\s\.\'&\-\/\(\)]*$" 

                                    value="{{ old('business_name', $vendor->business_name) }}">

                                    @error('business_name', 'vendorUpdate'.$vendor->id)

                                        <div class="invalid-feedback">{{ $message }}</div>

                                    @enderror

                            </div>

                            <div class="col-md-6">

                                <label class="form-label">Business Mobile <span class="text-muted">(Optional)</span></label>

                                <input type="text" name="business_mobile" class="form-control phone-only @error('business_mobile', 'vendorUpdate'.$vendor->id) is-invalid @enderror" placeholder="Business mobile (10 digits)" 

                                    value="{{ old('business_mobile', $vendor->business_mobile) }}" maxlength="10" minlength="10" pattern="^[0-9]{10}$" inputmode="numeric">

                                    @error('business_mobile', 'vendorUpdate'.$vendor->id)

                                        <div class="invalid-feedback">{{ $message }}</div>

                                    @enderror

                            </div>

                            <div class="col-md-6">

                                <label class="form-label">Business PAN <span class="text-danger">*</span></label>

                                <input type="text" name="buisness_pan" class="form-control pan-input @error('buisness_pan', 'vendorUpdate'.$vendor->id) is-invalid @enderror" placeholder="Business PAN (ABCDE1234F)" 

                                    value="{{ old('buisness_pan', $vendor->buisness_pan ?? $vendor->business_pan) }}" maxlength="10" minlength="10" pattern="^[A-Z]{5}[0-9]{4}[A-Z]{1}$" required>

                                    @error('buisness_pan', 'vendorUpdate'.$vendor->id)

                                        <div class="invalid-feedback">{{ $message }}</div>

                                    @enderror

                            </div>

                            <div class="col-md-6">

                                <label class="form-label">GST Number</label>

                                <input type="text" name="gst_number" class="form-control gst-input @error('gst_number', 'vendorUpdate'.$vendor->id) is-invalid @enderror" placeholder="GSTIN (22ABCDE1234F1Z5)" 

                                    value="{{ old('gst_number', $vendor->gst_number) }}" maxlength="15" minlength="15" pattern="^[0-9]{2}[A-Z]{5}[0-9]{4}[A-Z]{1}[1-9A-Z]{1}Z[0-9A-Z]{1}$">

                                    @error('gst_number', 'vendorUpdate'.$vendor->id)

                                        <div class="invalid-feedback">{{ $message }}</div>

                                    @enderror

                            </div>



                             <div class="col-md-6">

                                <label class="form-label">City</label>

                                <input type="text" name="city" class="form-control @error('city', 'vendorUpdate'.$vendor->id) is-invalid @enderror" placeholder="City" maxlength="100" pattern="^[A-Za-z][A-Za-z\s\.\'-]*$" 

                                    value="{{ old('city', $vendor->city) }}">

                                    @error('city', 'vendorUpdate'.$vendor->id)

                                        <div class="invalid-feedback">{{ $message }}</div>

                                    @enderror

                            </div>

                            <div class="col-md-6">

                                <label class="form-label">State</label>

                                <input type="text" name="state" class="form-control @error('state', 'vendorUpdate'.$vendor->id) is-invalid @enderror" placeholder="State" maxlength="100" pattern="^[A-Za-z][A-Za-z\s\.\'-]*$" 

                                    value="{{ old('state', $vendor->state) }}">

                                    @error('state', 'vendorUpdate'.$vendor->id)

                                        <div class="invalid-feedback">{{ $message }}</div>

                                    @enderror

                            </div>



                            <div class="col-12">

                                <label class="form-label">Address</label>

                                <textarea name="address" class="form-control @error('address', 'vendorUpdate'.$vendor->id) is-invalid @enderror" rows="2" placeholder="Address" maxlength="500">{{ old('address', $vendor->address) }}</textarea>

                                @error('address', 'vendorUpdate'.$vendor->id)

                                    <div class="invalid-feedback">{{ $message }}</div>

                                @enderror

                            </div>



                             <div class="col-md-6 d-none">

                                <label class="form-label">Latitude</label>

                                <input type="text" name="latitude" class="form-control @error('latitude', 'vendorUpdate'.$vendor->id) is-invalid @enderror" placeholder="Latitude" 

                                    value="{{ old('latitude', $vendor->latitude) }}">

                                    @error('latitude', 'vendorUpdate'.$vendor->id)

                                        <div class="invalid-feedback">{{ $message }}</div>

                                    @enderror

                            </div>

                            <div class="col-md-6 d-none">

                                <label class="form-label">Longitude</label>

                                <input type="text" name="longitude" class="form-control @error('longitude', 'vendorUpdate'.$vendor->id) is-invalid @enderror" placeholder="Longitude" 

                                    value="{{ old('longitude', $vendor->longitude) }}">

                                    @error('longitude', 'vendorUpdate'.$vendor->id)

                                        <div class="invalid-feedback">{{ $message }}</div>

                                    @enderror

                            </div>



                            <div class="col-12">

                                <label class="form-label">Aadhar Image (PDF/Image) <span class="text-muted">(Optional)</span> - <small>Leave empty to keep current</small></label>

                                <input type="file" name="aadhar_image" class="form-control @error('aadhar_image', 'vendorUpdate'.$vendor->id) is-invalid @enderror" accept="image/*,.pdf">

                                    @error('aadhar_image', 'vendorUpdate'.$vendor->id)

                                        <div class="invalid-feedback">{{ $message }}</div>

                                    @enderror

                                @if($vendor->gst_document)

                                    <div class="mt-1">

                                        <small><a href="{{ asset($vendor->gst_document) }}" target="_blank">View Current Aadhar Image</a></small>

                                    </div>

                                @endif

                            </div>

                            <div class="col-12"><hr class="my-2"><h6 class="text-secondary mb-0">Bank &amp; cheque details <span class="text-muted fw-normal small">(Optional)</span></h6></div>

                            <div class="col-12">

                                <label class="form-label">Cancelled cheque details</label>

                                <textarea name="cancelled_cheque_details" class="form-control @error('cancelled_cheque_details', 'vendorUpdate'.$vendor->id) is-invalid @enderror" rows="2" maxlength="2000" placeholder="e.g. account holder name as on cheque">{{ old('cancelled_cheque_details', $vendor->cancelled_cheque_details) }}</textarea>

                                @error('cancelled_cheque_details', 'vendorUpdate'.$vendor->id)

                                    <div class="invalid-feedback">{{ $message }}</div>

                                @enderror

                            </div>

                            <div class="col-12">

                                <label class="form-label">Cancelled cheque image (PDF / image)</label>

                                <input type="file" name="cancelled_cheque_image" class="form-control @error('cancelled_cheque_image', 'vendorUpdate'.$vendor->id) is-invalid @enderror" accept=".pdf,image/*">

                                @error('cancelled_cheque_image', 'vendorUpdate'.$vendor->id)

                                    <div class="invalid-feedback">{{ $message }}</div>

                                @enderror

                                @if($vendor->cancelled_cheque_image)

                                    <div class="mt-1"><small><a href="{{ asset($vendor->cancelled_cheque_image) }}" target="_blank">View current file</a> — upload a new file to replace</small></div>

                                @endif

                            </div>

                            <div class="col-md-6">

                                <label class="form-label">Bank account number</label>

                                <input type="text" name="bank_account_number" class="form-control bank-account-input @error('bank_account_number', 'vendorUpdate'.$vendor->id) is-invalid @enderror" placeholder="Digits only" value="{{ old('bank_account_number', $vendor->bank_account_number) }}" maxlength="20" inputmode="numeric" pattern="^[0-9]{6,20}$">

                                @error('bank_account_number', 'vendorUpdate'.$vendor->id)

                                    <div class="invalid-feedback">{{ $message }}</div>

                                @enderror

                            </div>

                            <div class="col-md-6">

                                <label class="form-label">IFSC code</label>

                                <input type="text" name="bank_ifsc_code" class="form-control ifsc-input @error('bank_ifsc_code', 'vendorUpdate'.$vendor->id) is-invalid @enderror" placeholder="e.g. HDFC0001234" value="{{ old('bank_ifsc_code', $vendor->bank_ifsc_code) }}" maxlength="11" minlength="11" pattern="^[A-Z]{4}0[A-Z0-9]{6}$">

                                @error('bank_ifsc_code', 'vendorUpdate'.$vendor->id)

                                    <div class="invalid-feedback">{{ $message }}</div>

                                @enderror

                            </div>

                            <div class="col-12">

                                <label class="form-label">Bank account image (PDF / image)</label>

                                <input type="file" name="bank_account_image" class="form-control @error('bank_account_image', 'vendorUpdate'.$vendor->id) is-invalid @enderror" accept=".pdf,image/*">

                                @error('bank_account_image', 'vendorUpdate'.$vendor->id)

                                    <div class="invalid-feedback">{{ $message }}</div>

                                @enderror

                                @if($vendor->bank_account_image)

                                    <div class="mt-1"><small><a href="{{ asset($vendor->bank_account_image) }}" target="_blank">View current file</a></small></div>

                                @endif

                            </div>

                            <div class="col-12">

                                <label class="form-label">Bank statement image (PDF / image)</label>

                                <input type="file" name="bank_statement_image" class="form-control @error('bank_statement_image', 'vendorUpdate'.$vendor->id) is-invalid @enderror" accept=".pdf,image/*">

                                @error('bank_statement_image', 'vendorUpdate'.$vendor->id)

                                    <div class="invalid-feedback">{{ $message }}</div>

                                @enderror

                                @if($vendor->bank_statement_image)

                                    <div class="mt-1"><small><a href="{{ asset($vendor->bank_statement_image) }}" target="_blank">View current file</a></small></div>

                                @endif

                            </div>



                           </div>

                        </div>



                        <div class="modal-footer border-top-0">

                            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>

                            <button type="submit" class="btn btn-primary">Update Vendor</button>

                        </div>

                    </form>

                </div>

            </div>

        </div>

    @endforeach



</div>



<!-- Add Vendor Modal -->

<div class="modal fade" id="addVendorModal" tabindex="-1" aria-labelledby="addVendorModalLabel" aria-hidden="true">

    <div class="modal-dialog modal-lg modal-dialog-centered">

        <div class="modal-content border-primary rounded-4 shadow-sm">

            <form id="addVendorForm" action="{{ route('vendors.store') }}" method="POST" enctype="multipart/form-data" novalidate>

                @csrf

                <div class="modal-header py-2 px-3 border-bottom-0">

                    <h5 class="modal-title fw-bold" id="addVendorModalLabel">

                        <i class="bi bi-person-plus-fill me-2"></i> Add Vendor

                    </h5>

                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>

                </div>



                <div class="modal-body">

                    <div class="row g-3">

                         <!-- Image Preview -->

                        <div class="col-12 text-center">

                            <img id="addPreview" src="{{ asset('assets/admin/images/no-image.png') }}" class="rounded-circle border shadow-sm" width="100" height="100" style="object-fit: cover;">

                        </div>



                        <div class="col-12">

                            <label class="form-label">Profile Image <span class="text-muted">(Optional)</span></label>

                            <input type="file" name="profile_image" class="form-control @error('profile_image', 'vendorCreation') is-invalid @enderror" accept="image/*"

                                onchange="document.getElementById('addPreview').src = window.URL.createObjectURL(this.files[0])">

                            @error('profile_image', 'vendorCreation')

                                <div class="invalid-feedback">{{ $message }}</div>

                            @enderror

                        </div>



                        <div class="col-md-6">

                            <label class="form-label">Full Name <span class="text-danger">*</span></label>

                            <input type="text" name="name" class="form-control @error('name', 'vendorCreation') is-invalid @enderror" placeholder="Full Name" value="{{ old('name') }}" maxlength="255" pattern="^[A-Za-z][A-Za-z\s\.\'-]*$" required>

                            @error('name', 'vendorCreation')

                                <div class="invalid-feedback">{{ $message }}</div>

                            @enderror

                        </div>

                        <div class="col-md-6">

                            <label class="form-label">Email <span class="text-danger">*</span></label>

                            <input type="email" name="email" class="form-control @error('email', 'vendorCreation') is-invalid @enderror" placeholder="Email" value="{{ old('email') }}" required>

                            @error('email', 'vendorCreation')

                                <div class="invalid-feedback">{{ $message }}</div>

                            @enderror

                        </div>

                        <div class="col-md-6">

                            <label class="form-label">Mobile <span class="text-danger">*</span></label>

                            <input type="text" name="phone" class="form-control phone-only @error('phone', 'vendorCreation') is-invalid @enderror" placeholder="10-digit mobile (9876543210)" value="{{ old('phone') }}" maxlength="10" minlength="10" pattern="^[0-9]{10}$" inputmode="numeric" required>

                            @error('phone', 'vendorCreation')

                                <div class="invalid-feedback">{{ $message }}</div>

                            @enderror

                        </div>

                         <div class="col-md-6">

                            <label class="form-label">PAN <span class="text-danger">*</span></label>

                            <input type="text" name="pan_number" class="form-control pan-input @error('pan_number', 'vendorCreation') is-invalid @enderror" placeholder="PAN (ABCDE1234F)" value="{{ old('pan_number') }}" maxlength="10" minlength="10" pattern="^[A-Z]{5}[0-9]{4}[A-Z]{1}$" required>

                            @error('pan_number', 'vendorCreation')

                                <div class="invalid-feedback">{{ $message }}</div>

                            @enderror

                        </div>

                        <div class="col-md-6">

                            <label class="form-label">Aadhar <span class="text-danger">*</span></label>

                            <input type="text" name="aadhar_number" class="form-control aadhar-input @error('aadhar_number', 'vendorCreation') is-invalid @enderror" placeholder="12-digit Aadhaar number" value="{{ old('aadhar_number') }}" maxlength="12" minlength="12" pattern="^[0-9]{12}$" inputmode="numeric" required>

                            @error('aadhar_number', 'vendorCreation')

                                <div class="invalid-feedback">{{ $message }}</div>

                            @enderror

                        </div>

                        <div class="col-md-6">

                            <label class="form-label">Business Name <span class="text-muted">(Optional)</span></label>

                            <input type="text" name="business_name" class="form-control @error('business_name', 'vendorCreation') is-invalid @enderror" placeholder="Business Name" value="{{ old('business_name') }}" maxlength="255" pattern="^[A-Za-z0-9][A-Za-z0-9\s\.\'&\-\/\(\)]*$">

                            @error('business_name', 'vendorCreation')

                                <div class="invalid-feedback">{{ $message }}</div>

                            @enderror

                        </div>

                        <div class="col-md-6">

                            <label class="form-label">Business Mobile <span class="text-muted">(Optional)</span></label>

                            <input type="text" name="business_mobile" class="form-control phone-only @error('business_mobile', 'vendorCreation') is-invalid @enderror" placeholder="Business mobile (10 digits)" value="{{ old('business_mobile') }}" maxlength="10" minlength="10" pattern="^[0-9]{10}$" inputmode="numeric">

                            @error('business_mobile', 'vendorCreation')

                                <div class="invalid-feedback">{{ $message }}</div>

                            @enderror

                        </div>

                        <div class="col-md-6">

                            <label class="form-label">Business PAN <span class="text-danger">*</span></label>

                            <input type="text" name="buisness_pan" class="form-control pan-input @error('buisness_pan', 'vendorCreation') is-invalid @enderror" placeholder="Business PAN (ABCDE1234F)" value="{{ old('buisness_pan') }}" maxlength="10" minlength="10" pattern="^[A-Z]{5}[0-9]{4}[A-Z]{1}$" required>

                            @error('buisness_pan', 'vendorCreation')

                                <div class="invalid-feedback">{{ $message }}</div>

                            @enderror

                        </div>

                        <div class="col-md-6">

                            <label class="form-label">GST Number</label>

                            <input type="text" name="gst_number" class="form-control gst-input @error('gst_number', 'vendorCreation') is-invalid @enderror" placeholder="GSTIN (22ABCDE1234F1Z5)" value="{{ old('gst_number') }}" maxlength="15" minlength="15" pattern="^[0-9]{2}[A-Z]{5}[0-9]{4}[A-Z]{1}[1-9A-Z]{1}Z[0-9A-Z]{1}$">

                            @error('gst_number', 'vendorCreation')

                                <div class="invalid-feedback">{{ $message }}</div>

                            @enderror

                        </div>



                        <div class="col-md-6">

                            <label class="form-label">City</label>

                            <input type="text" name="city" class="form-control @error('city', 'vendorCreation') is-invalid @enderror" placeholder="City" value="{{ old('city') }}" maxlength="100" pattern="^[A-Za-z][A-Za-z\s\.\'-]*$">

                            @error('city', 'vendorCreation')

                                <div class="invalid-feedback">{{ $message }}</div>

                            @enderror

                        </div>

                        <div class="col-md-6">

                            <label class="form-label">State</label>

                            <input type="text" name="state" class="form-control @error('state', 'vendorCreation') is-invalid @enderror" placeholder="State" value="{{ old('state') }}" maxlength="100" pattern="^[A-Za-z][A-Za-z\s\.\'-]*$">

                            @error('state', 'vendorCreation')

                                <div class="invalid-feedback">{{ $message }}</div>

                            @enderror

                        </div>



                        <div class="col-12">

                            <label class="form-label">Address</label>

                             <textarea name="address" class="form-control @error('address', 'vendorCreation') is-invalid @enderror" rows="2" placeholder="Address" maxlength="500">{{ old('address') }}</textarea>

                            @error('address', 'vendorCreation')

                                <div class="invalid-feedback">{{ $message }}</div>

                            @enderror

                        </div>



                        <div class="col-md-6 d-none">

                                <label class="form-label">Latitude</label>

                                <input type="text" name="latitude" class="form-control @error('latitude', 'vendorCreation') is-invalid @enderror" placeholder="Latitude" 

                                    value="{{ old('latitude') }}">

                                    @error('latitude', 'vendorCreation')

                                        <div class="invalid-feedback">{{ $message }}</div>

                                    @enderror

                        </div>

                        <div class="col-md-6 d-none">

                            <label class="form-label">Longitude</label>

                            <input type="text" name="longitude" class="form-control @error('longitude', 'vendorCreation') is-invalid @enderror" placeholder="Longitude" 

                                value="{{ old('longitude') }}">

                                @error('longitude', 'vendorCreation')

                                    <div class="invalid-feedback">{{ $message }}</div>

                                @enderror

                        </div>



                        <div class="col-12">

                            <label class="form-label">Aadhar Image (PDF/Image) <span class="text-muted">(Optional)</span></label>

                            <input type="file" name="aadhar_image" class="form-control @error('aadhar_image', 'vendorCreation') is-invalid @enderror" accept=".pdf,image/*">

                            @error('aadhar_image', 'vendorCreation')

                                <div class="invalid-feedback">{{ $message }}</div>

                            @enderror

                        </div>

                        <div class="col-12"><hr class="my-2"><h6 class="text-secondary mb-0">Bank &amp; cheque details <span class="text-muted fw-normal small">(Optional)</span></h6></div>

                        <div class="col-12">

                            <label class="form-label">Cancelled cheque details</label>

                            <textarea name="cancelled_cheque_details" class="form-control @error('cancelled_cheque_details', 'vendorCreation') is-invalid @enderror" rows="2" placeholder="e.g. account holder name as on cheque, bank branch" maxlength="2000">{{ old('cancelled_cheque_details') }}</textarea>

                            @error('cancelled_cheque_details', 'vendorCreation')

                                <div class="invalid-feedback">{{ $message }}</div>

                            @enderror

                        </div>

                        <div class="col-12">

                            <label class="form-label">Cancelled cheque image (PDF / image)</label>

                            <input type="file" name="cancelled_cheque_image" class="form-control @error('cancelled_cheque_image', 'vendorCreation') is-invalid @enderror" accept=".pdf,image/*">

                            @error('cancelled_cheque_image', 'vendorCreation')

                                <div class="invalid-feedback">{{ $message }}</div>

                            @enderror

                        </div>

                        <div class="col-md-6">

                            <label class="form-label">Bank account number</label>

                            <input type="text" name="bank_account_number" class="form-control bank-account-input @error('bank_account_number', 'vendorCreation') is-invalid @enderror" placeholder="Account number (digits only)" value="{{ old('bank_account_number') }}" maxlength="20" inputmode="numeric" pattern="^[0-9]{6,20}$">

                            @error('bank_account_number', 'vendorCreation')

                                <div class="invalid-feedback">{{ $message }}</div>

                            @enderror

                        </div>

                        <div class="col-md-6">

                            <label class="form-label">IFSC code</label>

                            <input type="text" name="bank_ifsc_code" class="form-control ifsc-input @error('bank_ifsc_code', 'vendorCreation') is-invalid @enderror" placeholder="e.g. HDFC0001234" value="{{ old('bank_ifsc_code') }}" maxlength="11" minlength="11" pattern="^[A-Z]{4}0[A-Z0-9]{6}$">

                            @error('bank_ifsc_code', 'vendorCreation')

                                <div class="invalid-feedback">{{ $message }}</div>

                            @enderror

                        </div>

                        <div class="col-12">

                            <label class="form-label">Bank account image (PDF / image)</label>

                            <input type="file" name="bank_account_image" class="form-control @error('bank_account_image', 'vendorCreation') is-invalid @enderror" accept=".pdf,image/*">

                            @error('bank_account_image', 'vendorCreation')

                                <div class="invalid-feedback">{{ $message }}</div>

                            @enderror

                        </div>

                        <div class="col-12">

                            <label class="form-label">Bank statement image (PDF / image)</label>

                            <input type="file" name="bank_statement_image" class="form-control @error('bank_statement_image', 'vendorCreation') is-invalid @enderror" accept=".pdf,image/*">

                            @error('bank_statement_image', 'vendorCreation')

                                <div class="invalid-feedback">{{ $message }}</div>

                            @enderror

                        </div>



                    </div>

                </div>



                <div class="modal-footer border-top-0">

                    <button type="button" class="btn btn-outline-primary" data-bs-dismiss="modal">Cancel</button>

                    <button type="submit" class="btn btn-primary">Save Vendor</button>

                </div>

            </form>

        </div>

    </div>

</div>



@endsection



@push('scripts')

<script>

    document.addEventListener('DOMContentLoaded', function () {

        @if ($errors->vendorCreation->any())

            var addVendorModal = new bootstrap.Modal(document.getElementById('addVendorModal'));

            addVendorModal.show();

        @endif



        @foreach($vendors as $vendor)

            @if ($errors->{'vendorUpdate'.$vendor->id}->any())

                var editVendorModal = new bootstrap.Modal(document.getElementById('editVendorModal{{ $vendor->id }}'));

                editVendorModal.show();

            @endif

        @endforeach

    });

    

    document.addEventListener('DOMContentLoaded', function () {

        document.querySelectorAll('.delete-form').forEach(function(form) {

            form.addEventListener('submit', function(e) {

                e.preventDefault();



                Swal.fire({

                    title: 'Are you sure?',

                    text: "This vendor will be deleted permanently!",

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

                var vendorId = this.getAttribute('data-id');

                var status = this.checked ? 1 : 0;

                var url = '{{ route("vendors.updateStatus") }}'; 



                fetch(url, {

                    method: 'POST',

                    headers: {

                        'Content-Type': 'application/json',

                        'X-CSRF-TOKEN': '{{ csrf_token() }}'

                    },

                    body: JSON.stringify({

                        id: vendorId,

                        status: status

                    })

                })

                .then(response => response.json())

                .then(data => {

                    if(data.success) {

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



        // Add Vendor Form Validation

        const addVendorForm = document.getElementById('addVendorForm');

        if (addVendorForm) {

            addVendorForm.addEventListener('submit', function (event) {

                if (!addVendorForm.checkValidity()) {

                    event.preventDefault();

                    event.stopPropagation();

                }

                addVendorForm.classList.add('was-validated');

            }, false);

            

             // Allow only numbers for phone fields (10 digits)
             document.querySelectorAll('.phone-only').forEach(function(phoneInput) {
                 phoneInput.addEventListener('input', function() {
                     this.value = this.value.replace(/\D/g, '').slice(0, 10);
                 });
             });

             // Aadhaar must be 12 digits
             document.querySelectorAll('.aadhar-input').forEach(function(aadharInput) {
                 aadharInput.addEventListener('input', function() {
                     this.value = this.value.replace(/\D/g, '').slice(0, 12);
                 });
             });

             // PAN format helper
             document.querySelectorAll('.pan-input').forEach(function(panInput) {
                 panInput.addEventListener('input', function() {
                     this.value = this.value.toUpperCase().replace(/[^A-Z0-9]/g, '').slice(0, 10);
                 });
             });

             // GST format helper
             document.querySelectorAll('.gst-input').forEach(function(gstInput) {
                 gstInput.addEventListener('input', function() {
                     this.value = this.value.toUpperCase().replace(/[^A-Z0-9]/g, '').slice(0, 15);
                 });
             });

             document.querySelectorAll('.ifsc-input').forEach(function(inp) {
                 inp.addEventListener('input', function() {
                     this.value = this.value.toUpperCase().replace(/[^A-Z0-9]/g, '').slice(0, 11);
                 });
             });

             document.querySelectorAll('.bank-account-input').forEach(function(inp) {
                 inp.addEventListener('input', function() {
                     this.value = this.value.replace(/\D/g, '').slice(0, 20);
                 });
             });

        }

    });



</script>

@endpush

