@extends('layouts.admin')

@section('title', 'Vehicle Management')

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



    {{-- Success Message --}}

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

                    timerProgressBar: true

                });

            });

        </script>

        @endpush

    @endif



    <div class="d-flex justify-content-between align-items-center mb-3">

        <h4 class="fw-bold">Vehicle List</h4>

        <div>

            @include('partials.export-dropdown', [
                'exportRoute' => 'vehicles.export',
                'exportQuery' => request()->only(['search', 'vehicle_type', 'status']),
            ])

            <button class="btn btn-primary rounded-3" data-bs-toggle="modal" data-bs-target="#addVehicleModal">

                Add Vehicle

            </button>

        </div>

    </div>



    <form method="GET" action="{{ route('vehicles.index') }}" class="row g-2 mb-3">

        <div class="col-md-4">

            <input type="text" name="search" value="{{ request('search') }}"

                class="form-control"

                placeholder="Search vehicle or vendor">

        </div>



        <div class="col-md-3">

            <select name="vehicle_type" class="form-select">

                <option value="">All Types</option>

                @foreach($vehicleTypes as $type)

                    <option value="{{ $type }}" {{ request('vehicle_type')==$type?'selected':'' }}>

                        {{ $type }}

                    </option>

                @endforeach

            </select>

        </div>



        <div class="col-md-2">

            <select name="status" class="form-select">

                <option value="">All Status</option>

                <option value="1" {{ request('status')=='1'?'selected':'' }}>Active</option>

                <option value="0" {{ request('status')==='0'?'selected':'' }}>Inactive</option>

            </select>

        </div>



<div class="col-auto">

            <input type="hidden" name="sort_by" value="{{ request('sort_by') }}">

            <input type="hidden" name="sort_order" value="{{ request('sort_order') }}">

            <button class="btn btn-primary">Search</button>

            <a href="{{ route('vehicles.index') }}" class="btn btn-outline-secondary">Reset</a>

        </div>

    </form>



    <div class="card shadow-sm rounded border mb-4">

        <div class="card-body">

            <div class="table-responsive">



                <table class="table table-hover table-bordered align-middle mb-0">

                    <thead class="table-light text-center">

                        <tr>

                            <th style="width:5%">#</th>

                            <x-sortable-th name="vehicle_number" label="Vehicle Number" />

                            <x-sortable-th name="vendor" label="Vendor" />

                            <x-sortable-th name="vehicle_type" label="Type" />

                            <th>RC</th>

                            <th>Insurance</th>

                            <x-sortable-th name="status" label="Status" />

                            <th style="width:10%">Actions</th>

                        </tr>

                    </thead>



                    <tbody>

                        @forelse($vehicles as $vehicle)

                        <tr>

                            <td class="text-center">{{ $vehicles->firstItem() + $loop->index }}</td>

                            <td>{{ $vehicle->vehicle_number }}</td>

                            <td>{{ $vehicle->vendor->name ?? '-' }}</td>

                            <td>{{ $vehicle->vehicle_type }}</td>

                            <td class="text-center">

                                <img src="{{ asset('storage/'.$vehicle->rc_image) }}"

                                    width="60"

                                    onerror="this.src='/assets/admin/images/no-image.png'">

                            </td>



                            <td class="text-center">

                                @php
                                    $insuranceFiles = json_decode($vehicle->insurance_image, true);
                                    if (!is_array($insuranceFiles)) {
                                        $insuranceFiles = $vehicle->insurance_image ? [$vehicle->insurance_image] : [];
                                    }
                                    $firstInsurance = $insuranceFiles[0] ?? null;
                                    $firstExt = $firstInsurance ? strtolower(pathinfo($firstInsurance, PATHINFO_EXTENSION)) : null;
                                @endphp

                                @if($firstInsurance && in_array($firstExt, ['jpg', 'jpeg', 'png', 'webp']))
                                    <img src="{{ asset('storage/'.$firstInsurance) }}"
                                        width="60"
                                        onerror="this.src='/assets/admin/images/no-image.png'">
                                @elseif($firstInsurance)
                                    <a href="{{ asset('storage/'.$firstInsurance) }}" target="_blank" class="badge bg-secondary text-decoration-none">File</a>
                                @else
                                    <span class="text-muted small">No File</span>
                                @endif

                                @if(count($insuranceFiles) > 1)
                                    <div class="small text-muted">+{{ count($insuranceFiles) - 1 }} more</div>
                                @endif

                            </td>

                            <td class="text-center">

                                <div class="form-check form-switch d-flex justify-content-center">

                                    <input class="form-check-input status-toggle"

                                        type="checkbox"

                                        data-id="{{ $vehicle->id }}"

                                        {{ $vehicle->status ? 'checked' : '' }}>

                                </div>

                            </td>



                            <!-- <td class="text-center">

                                <button class="btn btn-sm btn-info text-white"

                                    data-bs-toggle="modal"

                                    data-bs-target="#viewVehicleModal{{ $vehicle->id }}">

                                    <i class="bi bi-eye"></i>

                                </button>

                                

                                <button class="btn btn-sm btn-secondary"

                                    data-bs-toggle="modal"

                                    data-bs-target="#editVehicleModal{{ $vehicle->id }}">

                                    <i class="bi bi-pencil-square"></i>

                                </button>



                                <form action="{{ route('vehicles.destroy', $vehicle->id) }}"

                                    method="POST" class="d-inline delete-form">

                                    @csrf @method('DELETE')

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
                data-bs-target="#viewVehicleModal{{ $vehicle->id }}">
            <i class="bi bi-eye"></i>
        </button>

        <!-- Edit -->
        <button class="btn btn-sm btn-secondary"
                data-bs-toggle="modal"
                data-bs-target="#editVehicleModal{{ $vehicle->id }}">
            <i class="bi bi-pencil-square"></i>
        </button>

        <!-- Delete -->
        <form action="{{ route('vehicles.destroy', $vehicle->id) }}"
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

                            <td colspan="8" class="text-center py-4 text-muted">

                                <i class="bi bi-inbox fs-1 d-block mb-2"></i>

                                No Data Found

                            </td>

                        </tr>

                        @endforelse

                    </tbody>

                </table>



                <div class="mt-3 d-flex justify-content-between align-items-center">

                    <div class="text-muted small">

                        Showing {{ $vehicles->firstItem() ?? 0 }}–{{ $vehicles->lastItem() ?? 0 }} of {{ $vehicles->total() }} entries

                    </div>

                    <div>

                        {{ $vehicles->appends(request()->query())->links() }}

                    </div>

                </div>



            </div>

        </div>

    </div>



</div>



<!-- Add Vehicle Modal -->

<div class="modal fade" id="addVehicleModal" tabindex="-1">

    <div class="modal-dialog modal-lg modal-dialog-centered">

        <div class="modal-content border-primary rounded-4 shadow-sm">



            <form action="{{ route('vehicles.store') }}" method="POST" enctype="multipart/form-data">

                @csrf

                <input type="hidden" name="target_modal" value="addVehicleModal">



                <div class="modal-header py-2 px-3 border-bottom-0">

                    <h5 class="modal-title fw-bold">

                        <i class="bi bi-truck me-2"></i> Add Vehicle

                    </h5>

                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>

                </div>



                <div class="modal-body">

                    <div class="row g-3">



                        <div class="col-md-6">

                            <label class="form-label">Vehicle Number</label>

                            <input type="text" name="vehicle_number" class="form-control @error('vehicle_number') @if(old('target_modal') == 'addVehicleModal') is-invalid @endif @enderror vehicle-number-input"

                                value="{{ old('target_modal') == 'addVehicleModal' ? old('vehicle_number') : '' }}" 

                                placeholder="XX-00-XX-0000"

                                pattern="[A-Z]{2}-[0-9]{1,2}-[A-Z]{1,3}-[0-9]{4}"

                                maxlength="20"

                                title="Format: XX-00-XX-0000 (e.g. MH-12-AB-1234)"

                                required>

                            @error('vehicle_number')

                                @if(old('target_modal') == 'addVehicleModal')

                                    <div class="invalid-feedback">{{ $message }}</div>

                                @endif

                            @enderror

                        </div>



                        <div class="col-md-6">

                            <label class="form-label">Vendor</label>

                            <select name="vendor_id" class="form-select @error('vendor_id') @if(old('target_modal') == 'addVehicleModal') is-invalid @endif @enderror" required>

                                <option selected disabled>Select Vendor</option>

                                @foreach($vendors as $vendor)

                                    <option value="{{ $vendor->id }}"

                                        @selected((old('target_modal') == 'addVehicleModal' ? old('vendor_id') : '') == $vendor->id)>

                                        {{ $vendor->name }}

                                    </option>

                                @endforeach

                            </select>

                             @error('vendor_id')

                                @if(old('target_modal') == 'addVehicleModal')

                                    <div class="invalid-feedback">{{ $message }}</div>

                                @endif

                            @enderror

                        </div>



                        <div class="col-md-6">

                            <label class="form-label">Vehicle Type</label>

                            <select name="vehicle_type" class="form-select @error('vehicle_type') @if(old('target_modal') == 'addVehicleModal') is-invalid @endif @enderror" required>

                                <option selected disabled>Select Type</option>

                                @foreach($vehicleTypes as $type)

                                    <option value="{{ $type }}" @selected((old('target_modal') == 'addVehicleModal' ? old('vehicle_type') : '') == $type)>{{ $type }}</option>

                                @endforeach

                            </select>

                             @error('vehicle_type')

                                @if(old('target_modal') == 'addVehicleModal')

                                    <div class="invalid-feedback">{{ $message }}</div>

                                @endif

                            @enderror

                        </div>



                        <div class="col-md-6">

                            <label class="form-label">RC Image</label>

                            <input type="file" name="rc_image" class="form-control @error('rc_image') @if(old('target_modal') == 'addVehicleModal') is-invalid @endif @enderror image-input" data-preview="rcPreview" accept="image/png, image/jpeg, image/jpg, image/webp">

                            <img id="rcPreview" class="img-thumbnail mt-2 d-none" width="120">

                             @error('rc_image')

                                @if(old('target_modal') == 'addVehicleModal')

                                    <div class="invalid-feedback">{{ $message }}</div>

                                @endif

                            @enderror

                        </div>



                        <div class="col-md-6">

                            <label class="form-label">Insurance Files (Multiple)</label>

                            <input type="file" name="insurance_files[]" class="form-control insurance-files-input @error('insurance_files') @if(old('target_modal') == 'addVehicleModal') is-invalid @endif @enderror @error('insurance_files.*') @if(old('target_modal') == 'addVehicleModal') is-invalid @endif @enderror" data-preview-container="insurancePreviewAdd" multiple accept="image/png, image/jpeg, image/jpg, image/webp,application/pdf,.doc,.docx">

                            <small class="text-muted">You can upload multiple files (images, PDF, DOC, DOCX). Max 5MB each.</small>

                            <div class="mt-2 d-none" id="insurancePreviewAddWrapper">
                                <label class="small text-muted mb-1 d-block">Selected Files Preview:</label>
                                <div id="insurancePreviewAdd" class="d-flex flex-wrap gap-2"></div>
                            </div>

                             @error('insurance_files')

                                @if(old('target_modal') == 'addVehicleModal')

                                    <div class="invalid-feedback">{{ $message }}</div>

                                @endif

                            @enderror

                            @error('insurance_files.*')

                                @if(old('target_modal') == 'addVehicleModal')

                                    <div class="invalid-feedback">{{ $message }}</div>

                                @endif

                            @enderror

                        </div>





                    </div>

                </div>



                <div class="modal-footer border-top-0">

                    <button type="button" class="btn btn-outline-primary" data-bs-dismiss="modal">Cancel</button>

                    <button type="submit" class="btn btn-primary">Save Vehicle</button>

                </div>



            </form>

        </div>

    </div>

</div>



@foreach($vehicles as $vehicle)

<div class="modal fade" id="viewVehicleModal{{ $vehicle->id }}" tabindex="-1">

    <div class="modal-dialog modal-lg modal-dialog-centered">

        <div class="modal-content border-info rounded-4 shadow-sm">

            

            <div class="modal-header bg-info text-white py-2 px-3 border-bottom-0">

                <h5 class="modal-title fw-bold">

                    <i class="bi bi-eye me-2"></i> Vehicle Details

                </h5>

                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>

            </div>



            <div class="modal-body">

                <div class="row g-4">

                    <div class="col-md-6">

                        <h6 class="fw-bold text-muted mb-3 border-bottom pb-2">Basic Information</h6>

                        

                        <div class="mb-2">

                            <label class="small text-muted mb-0">Vehicle Number</label>

                            <div class="fw-bold fs-5">{{ $vehicle->vehicle_number }}</div>

                        </div>



                        <div class="mb-2">

                            <label class="small text-muted mb-0">Vendor</label>

                            <div class="fw-bold">{{ $vehicle->vendor->name ?? 'N/A' }}</div>

                            @if(isset($vehicle->vendor->phone))

                                <div class="small text-muted"><i class="bi bi-telephone me-1"></i> {{ $vehicle->vendor->phone }}</div>

                            @endif

                        </div>



                        <div class="mb-2">

                            <label class="small text-muted mb-0">Vehicle Type</label>

                            <div>

                                <span class="badge bg-secondary">{{ $vehicle->vehicle_type }}</span>

                            </div>

                        </div>



                        <div class="mb-2">

                            <label class="small text-muted mb-0">Status</label>

                            <div>

                                @if($vehicle->status)

                                    <span class="badge bg-success">Active</span>

                                @else

                                    <span class="badge bg-danger">Inactive</span>

                                @endif

                            </div>

                        </div>

                        

                        <div class="mb-2">

                            <label class="small text-muted mb-0">Created At</label>

                            <div class="text-dark">{{ $vehicle->created_at->format('d M, Y h:i A') }}</div>

                        </div>

                    </div>



                    <div class="col-md-6">

                        <h6 class="fw-bold text-muted mb-3 border-bottom pb-2">Documents</h6>



                        <div class="mb-4">

                            <label class="small text-muted mb-2 d-block">RC Image</label>

                            <div class="border rounded p-1 d-inline-block bg-light">

                                <img src="{{ asset('storage/'.$vehicle->rc_image) }}" 

                                    class="img-fluid rounded" 

                                    style="max-height: 150px; cursor: pointer;"

                                    onclick="window.open(this.src, '_blank')"

                                    onerror="this.src='/assets/admin/images/no-image.png'">

                            </div>

                            <div class="small text-muted mt-1 fst-italic">Click to enlarge</div>

                        </div>



                        <div>

                            <label class="small text-muted mb-2 d-block">Insurance Files</label>

                            @php
                                $insuranceFilesView = json_decode($vehicle->insurance_image, true);
                                if (!is_array($insuranceFilesView)) {
                                    $insuranceFilesView = $vehicle->insurance_image ? [$vehicle->insurance_image] : [];
                                }
                            @endphp

                            @if(!empty($insuranceFilesView))
                                <div class="d-flex flex-wrap gap-2">
                                    @foreach($insuranceFilesView as $insuranceFile)
                                        @php $insuranceExt = strtolower(pathinfo($insuranceFile, PATHINFO_EXTENSION)); @endphp
                                        @if(in_array($insuranceExt, ['jpg', 'jpeg', 'png', 'webp']))
                                            <a href="{{ asset('storage/'.$insuranceFile) }}" target="_blank" rel="noopener" class="border rounded p-1 bg-light d-inline-block">
                                                <img src="{{ asset('storage/'.$insuranceFile) }}"
                                                    class="img-fluid rounded"
                                                    style="max-height: 120px;"
                                                    onerror="this.src='/assets/admin/images/no-image.png'">
                                            </a>
                                        @else
                                            <a href="{{ asset('storage/'.$insuranceFile) }}" target="_blank" rel="noopener" class="btn btn-sm btn-outline-secondary">
                                                View {{ strtoupper($insuranceExt ?: 'FILE') }}
                                            </a>
                                        @endif
                                    @endforeach
                                </div>
                            @else
                                <span class="text-muted">No file uploaded</span>
                            @endif

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



<div class="modal fade" id="editVehicleModal{{ $vehicle->id }}" tabindex="-1">

    <div class="modal-dialog modal-lg modal-dialog-centered">

        <div class="modal-content border-primary rounded-4 shadow-sm">



            <form action="{{ route('vehicles.update', $vehicle->id) }}" method="POST" enctype="multipart/form-data">

                @csrf

                @method('PUT')

                <input type="hidden" name="target_modal" value="editVehicleModal{{ $vehicle->id }}">



                <div class="modal-header py-2 px-3 border-bottom-0">

                    <h5 class="modal-title fw-bold">

                        <i class="bi bi-pencil-square me-2"></i> Edit Vehicle

                    </h5>

                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>

                </div>



                <div class="modal-body">

                    <div class="row g-3">



                        <div class="col-md-6">

                            <label class="form-label">Vehicle Number</label>

                            <input type="text" name="vehicle_number" class="form-control vehicle-number-input @error('vehicle_number') is-invalid @enderror"

                                value="{{ old('target_modal') == 'editVehicleModal'.$vehicle->id ? old('vehicle_number', $vehicle->vehicle_number) : $vehicle->vehicle_number }}" 

                                pattern="[A-Z]{2}-[0-9]{1,2}-[A-Z]{1,3}-[0-9]{4}"

                                maxlength="20"

                                title="Format: XX-00-XX-0000 (e.g. MH-12-AB-1234)"

                                required>

                            @error('vehicle_number')

                                @if(old('target_modal') == 'editVehicleModal'.$vehicle->id)

                                    <div class="invalid-feedback">{{ $message }}</div>

                                @endif

                            @enderror

                        </div>



                        <div class="col-md-6">

                            <label class="form-label">Vendor</label>

                            <select name="vendor_id" class="form-select @error('vendor_id') is-invalid @enderror" required>

                                @foreach($vendors as $vendor)

                                    <option value="{{ $vendor->id }}"

                                        @selected((old('target_modal') == 'editVehicleModal'.$vehicle->id ? old('vendor_id') : $vehicle->vendor_id) == $vendor->id)>

                                        {{ $vendor->name }}

                                    </option>

                                @endforeach

                            </select>

                             @error('vendor_id')

                                @if(old('target_modal') == 'editVehicleModal'.$vehicle->id)

                                    <div class="invalid-feedback">{{ $message }}</div>

                                @endif

                            @enderror

                        </div>



                        <div class="col-md-6">

                            <label class="form-label">Vehicle Type</label>

                            <select name="vehicle_type" class="form-select @error('vehicle_type') is-invalid @enderror" required>

                                <option selected disabled>Select Type</option>

                                @foreach($vehicleTypes as $type)

                                    <option value="{{ $type }}" @selected((old('target_modal') == 'editVehicleModal'.$vehicle->id ? old('vehicle_type') : $vehicle->vehicle_type) == $type)>

                                        {{ $type }}

                                    </option>

                                @endforeach

                            </select>

                            @error('vehicle_type')

                                @if(old('target_modal') == 'editVehicleModal'.$vehicle->id)

                                    <div class="invalid-feedback">{{ $message }}</div>

                                @endif

                            @enderror

                        </div>



                        <div class="col-md-6">

                            <label class="form-label">RC Image</label>

                            <input type="file" name="rc_image" class="form-control @error('rc_image') @if(old('target_modal') == 'editVehicleModal'.$vehicle->id) is-invalid @endif @enderror image-input" data-preview="rcPreview{{ $vehicle->id }}" accept="image/png, image/jpeg, image/jpg, image/webp">

                            <div class="mt-2">

                                <label class="small text-muted mb-1 d-block">Preview:</label>

                                <img src="{{ asset('storage/'.$vehicle->rc_image) }}" 

                                    id="rcPreview{{ $vehicle->id }}" 

                                    class="img-thumbnail" width="120"

                                    onerror="this.src='/assets/admin/images/no-image.png'">

                            </div>

                            @error('rc_image')

                                @if(old('target_modal') == 'editVehicleModal'.$vehicle->id)

                                    <div class="invalid-feedback">{{ $message }}</div>

                                @endif

                            @enderror

                        </div>



                        <div class="col-md-6">

                            <label class="form-label">Insurance Files (Multiple)</label>

                            <input type="file" name="insurance_files[]" class="form-control insurance-files-input @error('insurance_files') @if(old('target_modal') == 'editVehicleModal'.$vehicle->id) is-invalid @endif @enderror @error('insurance_files.*') @if(old('target_modal') == 'editVehicleModal'.$vehicle->id) is-invalid @endif @enderror" data-preview-container="insurancePreviewEdit{{ $vehicle->id }}" multiple accept="image/png, image/jpeg, image/jpg, image/webp,application/pdf,.doc,.docx">

                            <small class="text-muted">Upload multiple files (images, PDF, DOC, DOCX). Max 5MB each.</small>

                            <div class="mt-2 d-none" id="insurancePreviewEdit{{ $vehicle->id }}Wrapper">
                                <label class="small text-muted mb-1 d-block">Selected Files Preview:</label>
                                <div id="insurancePreviewEdit{{ $vehicle->id }}" class="d-flex flex-wrap gap-2"></div>
                            </div>

                            <div class="mt-2">
                                <label class="small text-muted mb-1 d-block">Current Files:</label>
                                @php
                                    $insuranceFilesEdit = json_decode($vehicle->insurance_image, true);
                                    if (!is_array($insuranceFilesEdit)) {
                                        $insuranceFilesEdit = $vehicle->insurance_image ? [$vehicle->insurance_image] : [];
                                    }
                                @endphp
                                @if(!empty($insuranceFilesEdit))
                                    <div class="d-flex flex-wrap gap-2">
                                        @foreach($insuranceFilesEdit as $insuranceFile)
                                            @php $insuranceExt = strtolower(pathinfo($insuranceFile, PATHINFO_EXTENSION)); @endphp
                                            <a href="{{ asset('storage/'.$insuranceFile) }}" target="_blank" rel="noopener" class="btn btn-sm btn-outline-secondary">
                                                {{ strtoupper($insuranceExt ?: 'FILE') }}
                                            </a>
                                        @endforeach
                                    </div>
                                @else
                                    <span class="text-muted">No file uploaded</span>
                                @endif
                            </div>

                            @error('insurance_files')
                                @if(old('target_modal') == 'editVehicleModal'.$vehicle->id)
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @endif
                            @enderror
                            @error('insurance_files.*')
                                @if(old('target_modal') == 'editVehicleModal'.$vehicle->id)
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @endif
                            @enderror

                        </div>



                    </div>

                </div>



                <div class="modal-footer border-top-0">

                    <button type="button" class="btn btn-outline-primary" data-bs-dismiss="modal">Cancel</button>

                    <button type="submit" class="btn btn-primary">Update Vehicle</button>

                </div>



            </form>

        </div>

    </div>

</div>

@endforeach



@endsection



@push('scripts')

{{-- Scripts --}}

<script>



document.addEventListener('DOMContentLoaded', function () {

    @if ($errors->any())

        var targetModalId = "{{ old('target_modal', 'addVehicleModal') }}";

        if (targetModalId && document.getElementById(targetModalId)) {

            var modal = new bootstrap.Modal(document.getElementById(targetModalId));

            modal.show();

        }

    @endif

});





document.addEventListener('DOMContentLoaded', function () {



    // DELETE CONFIRM

    document.querySelectorAll('.delete-form').forEach(function(form) {

        form.addEventListener('submit', function(e) {

            e.preventDefault();



            Swal.fire({

                title: 'Are you sure?',

                text: "This vehicle will be deleted permanently!",

                icon: 'warning',

                showCancelButton: true,

                confirmButtonText: 'Yes, delete it!',

                cancelButtonText: 'Cancel',

                reverseButtons: true

            }).then((result) => {

                if (result.isConfirmed) form.submit();

            });

        });

    });



    // STATUS TOGGLE

    document.querySelectorAll('.status-toggle').forEach(function(toggle) {

        toggle.addEventListener('change', function() {



            const checkbox = this;



            fetch('{{ route("vehicles.updateStatus") }}', {

                method: 'POST',

                headers: {

                    'Content-Type':'application/json',

                    'X-CSRF-TOKEN':'{{ csrf_token() }}'

                },

                body: JSON.stringify({

                    id: this.dataset.id,

                    status: this.checked ? 1 : 0

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

                        timerProgressBar: true

                    });



                    Toast.fire({

                        icon: 'success',

                        title: data.success

                    });



                } else {

                    Swal.fire('Error', 'Something went wrong!', 'error');

                    checkbox.checked = !checkbox.checked;

                }

            })

            .catch(() => {

                Swal.fire('Error', 'Server error occurred.', 'error');

                checkbox.checked = !checkbox.checked;

            });



        });

    });



});

</script>



<script>

document.querySelectorAll('.image-input').forEach(input => {

    input.addEventListener('change', function() {

        const preview = document.getElementById(this.dataset.preview);

        const file = this.files[0];



        if (file) {

            preview.src = URL.createObjectURL(file);

            preview.classList.remove('d-none');

        }

    });

});

function renderInsurancePreview(input) {
    const containerId = input.dataset.previewContainer;
    if (!containerId) {
        return;
    }

    const previewContainer = document.getElementById(containerId);
    const previewWrapper = document.getElementById(containerId + 'Wrapper');

    if (!previewContainer || !previewWrapper) {
        return;
    }

    previewContainer.innerHTML = '';

    const files = Array.from(input.files || []);
    if (!files.length) {
        previewWrapper.classList.add('d-none');
        return;
    }

    files.forEach(file => {
        const ext = (file.name.split('.').pop() || 'FILE').toUpperCase();
        const isImage = file.type.startsWith('image/');

        if (isImage) {
            const img = document.createElement('img');
            img.src = URL.createObjectURL(file);
            img.alt = file.name;
            img.width = 80;
            img.className = 'img-thumbnail';
            img.title = file.name;
            previewContainer.appendChild(img);
            return;
        }

        const fileBadge = document.createElement('span');
        fileBadge.className = 'badge rounded-pill text-bg-secondary px-3 py-2';
        fileBadge.textContent = ext;
        fileBadge.title = file.name;
        previewContainer.appendChild(fileBadge);
    });

    previewWrapper.classList.remove('d-none');
}

document.querySelectorAll('.insurance-files-input').forEach(input => {
    input.addEventListener('change', function() {
        renderInsurancePreview(this);
    });
});



document.addEventListener('DOMContentLoaded', function() {

    const vehicleInputs = document.querySelectorAll('.vehicle-number-input');



    vehicleInputs.forEach(input => {

        // Force limit on length just in case

        input.setAttribute('maxlength', '20');

        

        input.addEventListener('input', function(e) {

            // Force uppercase

            let value = this.value.toUpperCase();

            

            // Remove any characters that aren't A-Z, 0-9, hyphens, or spaces

            value = value.replace(/[^A-Z0-9\-\s]/g, '');

            

            // Update value if changed

            if (this.value !== value) {

                this.value = value;

            }

        });

        

        // Block invalid keys during typing (extra safety)

        input.addEventListener('keypress', function(e) {

            // Get character from event

            const char = String.fromCharCode(e.which || e.keyCode).toUpperCase();

            

            // Allow control keys (enter, backspace etc usually don't trigger keypress, but just in case)

            // But strict validation on printable characters:

            if (!/[A-Z0-9\-\s]/.test(char) && (!e.key || e.key.length === 1)) {

                e.preventDefault();

            }

        });



        // Also clean on input (for paste, drag-drop, mobile)

        input.addEventListener('input', function(e) {

            let start = this.selectionStart;

            let end = this.selectionEnd;

            let originalValue = this.value;



            // Force uppercase and remove invalid chars

            let cleanValue = originalValue.toUpperCase().replace(/[^A-Z0-9\-\s]/g, '');

            

            if (originalValue !== cleanValue) {

                this.value = cleanValue;

                // Try to restore cursor position (approximate)

                this.setSelectionRange(start, end);

            }

        });

    });

});

</script>

@endpush

