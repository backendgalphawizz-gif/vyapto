@extends('layouts.admin')
@section('title', 'Edit Assignment')

@section('content')
<div class="main-section">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="fw-bold mb-0">Edit Assignment #{{ $assignmentParcel->id }}</h4>
        <a href="{{ route('admin.assignment-parcel.index') }}" class="btn btn-outline-secondary btn-sm rounded-3">
            <i class="bi bi-arrow-left me-1"></i> Back
        </a>
    </div>

    <div class="card shadow-sm rounded border mb-4">
        <div class="card-body p-4">
            <form action="{{ route('admin.assignment-parcel.update', $assignmentParcel) }}" method="POST" class="row g-3">
                @csrf
                @method('PUT')

                @if($errors->any())
                <div class="col-12">
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
                @endif

                <div class="col-md-6">
                    <label for="vendor_id" class="form-label fw-semibold">Vendor <span class="text-danger">*</span></label>
                    <select name="vendor_id" id="vendor_id" class="form-select @error('vendor_id') is-invalid @enderror" required>
                        <option value="">Select Vendor</option>
                        @foreach($vendors as $vendor)
                            <option value="{{ $vendor->id }}" {{ old('vendor_id', $assignmentParcel->vendor_id) == $vendor->id ? 'selected' : '' }}>{{ $vendor->name }}</option>
                        @endforeach
                    </select>
                    @error('vendor_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-6">
                    <label for="user_id" class="form-label fw-semibold">Staff <span class="text-danger">*</span></label>
                    <select name="user_id" id="user_id" class="form-select @error('user_id') is-invalid @enderror" required>
                        <option value="">Select Staff</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}" {{ old('user_id', $assignmentParcel->user_id) == $user->id ? 'selected' : '' }}>{{ $user->name }} - {{ $user->email }}</option>
                        @endforeach
                    </select>
                    @error('user_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-6">
                    <label for="vehicle_id" class="form-label fw-semibold">Vehicle <span class="text-danger">*</span></label>
                    <select name="vehicle_id" id="vehicle_id" class="form-select @error('vehicle_id') is-invalid @enderror" required>
                        <option value="">Select Vehicle</option>
                        @foreach($vehicles as $vehicle)
                            <option
                                value="{{ $vehicle->id }}"
                                data-owner-id="{{ $vehicleOwnerColumn ? ($vehicle->{$vehicleOwnerColumn} ?? '') : '' }}"
                                {{ old('vehicle_id', $assignmentParcel->vehicle_id) == $vehicle->id ? 'selected' : '' }}
                            >{{ $vehicle->vehicle_number }} - {{ $vehicle->type ?? 'N/A' }}</option>
                        @endforeach
                    </select>
                    <div class="form-text">Only vehicles added by selected staff are shown.</div>
                    @error('vehicle_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-6">
                    <label for="hub_id" class="form-label fw-semibold">Hub <span class="text-danger">*</span></label>
                    <select name="hub_id" id="hub_id" class="form-select @error('hub_id') is-invalid @enderror" required>
                        <option value="">Select Hub</option>
                        @foreach($hubs as $hub)
                            <option value="{{ $hub->id }}" {{ old('hub_id', $assignmentParcel->hub_id) == $hub->id ? 'selected' : '' }}>{{ $hub->name }}</option>
                        @endforeach
                    </select>
                    @error('hub_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-6">
                    <label for="parcel_quantity" class="form-label fw-semibold">Parcel Quantity <span class="text-danger">*</span></label>
                    <input type="number" name="parcel_quantity" id="parcel_quantity" class="form-control @error('parcel_quantity') is-invalid @enderror" value="{{ old('parcel_quantity', $assignmentParcel->parcel_quantity) }}" min="1" required>
                    @error('parcel_quantity')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-6">
                    <label for="assignment_date" class="form-label fw-semibold">Assignment Date <span class="text-danger">*</span></label>
                    <input type="date" name="assignment_date" id="assignment_date" class="form-control @error('assignment_date') is-invalid @enderror" value="{{ old('assignment_date', optional($assignmentParcel->assignment_date)->format('Y-m-d') ?? $assignmentParcel->assignment_date) }}" required>
                    @error('assignment_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-6">
                    <label for="status" class="form-label fw-semibold">Status <span class="text-danger">*</span></label>
                    <select name="status" id="status" class="form-select @error('status') is-invalid @enderror" required>
                        @foreach($statuses as $key => $value)
                            <option value="{{ $key }}" {{ old('status', $assignmentParcel->status) == $key ? 'selected' : '' }}>{{ $value }}</option>
                        @endforeach
                    </select>
                    @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-12">
                    <label for="notes" class="form-label fw-semibold">Notes</label>
                    <textarea name="notes" id="notes" rows="3" class="form-control @error('notes') is-invalid @enderror">{{ old('notes', $assignmentParcel->notes) }}</textarea>
                    @error('notes')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-12 d-flex gap-2 justify-content-end mt-2">
                    <a href="{{ route('admin.assignment-parcel.index') }}" class="btn btn-light border">Cancel</a>
                    <button type="submit" class="btn btn-primary"><i class="bi bi-check2-circle me-1"></i> Update Assignment</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var staffSelect = document.getElementById('user_id');
        var vehicleSelect = document.getElementById('vehicle_id');
        var canFilterByStaff = @json((bool) $vehicleOwnerColumn);

        if (!staffSelect || !vehicleSelect) {
            return;
        }

        if (!canFilterByStaff) {
            return;
        }

        var allVehicles = Array.from(vehicleSelect.options).map(function(option) {
            return {
                value: option.value,
                text: option.text,
                ownerId: option.dataset.ownerId || ''
            };
        });

        function filterVehicles() {
            var selectedStaffId = staffSelect.value;
            var selectedVehicleId = vehicleSelect.value;

            vehicleSelect.innerHTML = '<option value="">Select Vehicle</option>';

            allVehicles.forEach(function(vehicle) {
                if (!vehicle.value) {
                    return;
                }

                var belongsToStaff = vehicle.ownerId && vehicle.ownerId === selectedStaffId;
                var shouldShow = !!selectedStaffId && belongsToStaff;

                if (!shouldShow) {
                    return;
                }

                var option = document.createElement('option');
                option.value = vehicle.value;
                option.text = vehicle.text;
                option.dataset.ownerId = vehicle.ownerId;

                if (vehicle.value === selectedVehicleId) {
                    option.selected = true;
                }

                vehicleSelect.appendChild(option);
            });

            var stillSelected = Array.from(vehicleSelect.options).some(function(option) {
                return option.value === selectedVehicleId;
            });

            if (!stillSelected) {
                vehicleSelect.value = '';
            }

            vehicleSelect.disabled = !selectedStaffId;
        }

        staffSelect.addEventListener('change', filterVehicles);
        filterVehicles();
    });
</script>
@endpush
