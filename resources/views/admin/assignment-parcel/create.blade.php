@extends('layouts.admin')
@section('title', 'Create Assignment')

@section('content')
<div class="main-section">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="fw-bold mb-0">Create New Assignment</h4>
        <a href="{{ route('admin.assignment-parcel.index') }}" class="btn btn-outline-secondary btn-sm rounded-3">
            <i class="bi bi-arrow-left me-1"></i> Back
        </a>
    </div>

    <div class="card shadow-sm rounded border mb-4">
        <div class="card-body p-4">
        <form action="{{ route('admin.assignment-parcel.store') }}" method="POST" class="row g-3">
            @csrf
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
                <label for="user_id" class="form-label fw-semibold">Staff <span class="text-danger">*</span></label>
                <select name="user_id" id="user_id" class="form-select @error('user_id') is-invalid @enderror" required>
                    <option value="">Select Staff</option>
                    @foreach($users as $user)
                        @php
                            $roleName = (string) ($user->role->name ?? '');
                            $locType = \App\Support\StaffRoles::locationTypeForRoleId($user->role_id) ?? 'driver';
                        @endphp
                        <option
                            value="{{ $user->id }}"
                            data-role-type="{{ $locType }}"
                            data-hub-id="{{ $user->hub_id }}"
                            data-office-id="{{ $user->office_id }}"
                            {{ old('user_id') == $user->id ? 'selected' : '' }}
                        >{{ $user->name }} - {{ $user->email }} ({{ $roleName ?: 'Staff' }})</option>
                    @endforeach
                </select>
                @error('user_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-6" id="hubFieldWrap">
                <label for="hub_id" class="form-label fw-semibold">Hub <span class="text-danger">*</span></label>
                <select name="hub_id" id="hub_id" class="form-select @error('hub_id') is-invalid @enderror">
                    <option value="">Select Hub</option>
                    @foreach($hubs as $hub)
                        <option value="{{ $hub->id }}" {{ old('hub_id') == $hub->id ? 'selected' : '' }}>{{ $hub->name }}</option>
                    @endforeach
                </select>
                <div class="form-text">Drivers are assigned to a Hub location.</div>
                @error('hub_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-6 d-none" id="officeFieldWrap">
                <label for="office_id" class="form-label fw-semibold">Office <span class="text-danger">*</span></label>
                <select name="office_id" id="office_id" class="form-select @error('office_id') is-invalid @enderror">
                    <option value="">Select Office</option>
                    @foreach($offices ?? [] as $office)
                        <option value="{{ $office->id }}" {{ old('office_id') == $office->id ? 'selected' : '' }}>{{ $office->name }}</option>
                    @endforeach
                </select>
                <div class="form-text">Staff Employees are assigned to an Office location.</div>
                @error('office_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="col-12" id="driverLogisticsFields">
            <div class="row g-3">
            <div class="col-md-6">
                <label for="vendor_id" class="form-label fw-semibold">Vendor <span class="text-danger">*</span></label>
                <select name="vendor_id" id="vendor_id" class="form-select @error('vendor_id') is-invalid @enderror">
                    <option value="">Select Vendor</option>
                    @foreach($vendors as $vendor)
                        <option value="{{ $vendor->id }}" {{ old('vendor_id') == $vendor->id ? 'selected' : '' }}>{{ $vendor->name }}</option>
                    @endforeach
                </select>
                @error('vendor_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-6">
                <label for="vehicle_id" class="form-label fw-semibold">Vehicle <span class="text-danger">*</span></label>
                <select name="vehicle_id" id="vehicle_id" class="form-select @error('vehicle_id') is-invalid @enderror">
                    <option value="">Select Vehicle</option>
                    @foreach($vehicles as $vehicle)
                        <option
                            value="{{ $vehicle->id }}"
                            data-owner-id="{{ $vehicleOwnerColumn ? ($vehicle->{$vehicleOwnerColumn} ?? '') : '' }}"
                            data-vendor-id="{{ $vehicleVendorColumn ? ($vehicle->{$vehicleVendorColumn} ?? '') : '' }}"
                            {{ old('vehicle_id') == $vehicle->id ? 'selected' : '' }}
                        >{{ $vehicle->vehicle_number }} </option>
                    @endforeach
                </select>
                <div class="form-text">Only vehicles added by selected vendor are shown.</div>
                @error('vehicle_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            </div>
            </div>

            <div class="col-12" id="driverParcelFields">
            <div class="row g-3">
            <div class="col-md-6">
                <label for="parcel_quantity" class="form-label fw-semibold">Parcel Quantity <span class="text-danger">*</span></label>
                <input
                    type="text"
                    name="parcel_quantity"
                    id="parcel_quantity"
                    class="form-control @error('parcel_quantity') is-invalid @enderror"
                    value="{{ old('parcel_quantity') }}"
                    inputmode="numeric"
                    maxlength="3"
                    pattern="[1-9][0-9]{0,2}"
                    title="Enter 1–999 (max 3 digits)"
                    autocomplete="off"
                    required
                    data-parcel-qty-ajax-url="{{ $parcelQuantityAjaxUrl ?? '' }}"
                >
                <div class="form-text">Maximum 3 digits (1–999).</div>
                <div id="parcel_quantity_ajax_feedback" class="form-text small mt-1" role="status" aria-live="polite"></div>
                @error('parcel_quantity')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <!-- Parcel IDs Container -->
            <div class="col-12" id="parcelIdsContainer" style="display: none;">
                <div class="card bg-light border">
                    <div class="card-header bg-white">
                        <h6 class="mb-0 fw-semibold">Enter Unique Parcel IDs <span class="text-danger">*</span></h6>
                        <small class="text-muted">Enter a unique parcel ID for each parcel</small>
                    </div>
                    <div class="card-body">
                        <div id="parcelIdInputs" class="row g-2">
                            <!-- Dynamic inputs will be added here -->
                        </div>
                        @error('parcel_ids')<div class="alert alert-danger mt-2">{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <label for="assignment_date" class="form-label fw-semibold">Assignment Date <span class="text-danger">*</span></label>
                <input type="date" name="assignment_date" id="assignment_date" class="form-control @error('assignment_date') is-invalid @enderror" value="{{ old('assignment_date', date('Y-m-d')) }}" min="{{ date('Y-m-d') }}" required>
                @error('assignment_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="col-md-6">
                <label for="status" class="form-label fw-semibold">Status <span class="text-danger">*</span></label>
                <select name="status" id="status" class="form-select @error('status') is-invalid @enderror" required>
                    @foreach($statuses as $key => $value)
                        <option value="{{ $key }}" {{ old('status', 'pending') == $key ? 'selected' : '' }}>{{ $value }}</option>
                    @endforeach
                </select>
                @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            </div>
            </div>

            <div class="col-12 d-flex gap-2 justify-content-end mt-2">
                <a href="{{ route('admin.assignment-parcel.index') }}" class="btn btn-light border">Cancel</a>
                <button type="submit" class="btn btn-primary"><i class="bi bi-check2-circle me-1"></i> <span id="assignSubmitLabel">Create Assignment</span></button>
            </div>
        </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var vendorSelect = document.getElementById('vendor_id');
        var vehicleSelect = document.getElementById('vehicle_id');
        var parcelQuantityInput = document.getElementById('parcel_quantity');
        var parcelIdsContainer = document.getElementById('parcelIdsContainer');
        var parcelIdInputs = document.getElementById('parcelIdInputs');
        var parcelQuantityAjaxFeedback = document.getElementById('parcel_quantity_ajax_feedback');
        var parcelQtyAjaxTimer = null;
        var oldParcelIds = @json(old('parcel_ids', []));
        var canFilterByVendor = @json((bool) $vehicleVendorColumn);

        if (!vendorSelect || !vehicleSelect || !parcelQuantityInput || !parcelIdsContainer || !parcelIdInputs) {
            return;
        }

        var allVehicles = Array.from(vehicleSelect.options).map(function(option) {
            return {
                value: option.value,
                text: option.text,
                ownerId: option.dataset.ownerId || '',
                vendorId: option.dataset.vendorId || ''
            };
        });

        var initialVehicle = vehicleSelect.value;

        function filterVehiclesByVendor() {
            var selectedVendorId = vendorSelect.value;
            var selectedVehicleId = vehicleSelect.value || initialVehicle;

            vehicleSelect.innerHTML = '<option value="">Select Vehicle</option>';

            allVehicles.forEach(function(vehicle) {
                if (!vehicle.value) {
                    return;
                }

                var belongsToVendor = vehicle.vendorId && vehicle.vendorId === selectedVendorId;
                var shouldShow = canFilterByVendor ? (!!selectedVendorId && belongsToVendor) : true;

                if (!shouldShow) {
                    return;
                }

                var option = document.createElement('option');
                option.value = vehicle.value;
                option.text = vehicle.text;
                option.dataset.ownerId = vehicle.ownerId;
                option.dataset.vendorId = vehicle.vendorId;

                if (vehicle.value === selectedVehicleId) {
                    option.selected = true;
                }

                vehicleSelect.appendChild(option);
            });

            vehicleSelect.disabled = canFilterByVendor ? !selectedVendorId : false;

            var hasSelectedVehicle = Array.from(vehicleSelect.options).some(function(option) {
                return option.value === selectedVehicleId;
            });

            if (!hasSelectedVehicle) {
                vehicleSelect.value = '';
            }
        }

        function sanitizeParcelQuantityDigits() {
            var raw = parcelQuantityInput.value.replace(/\D/g, '').slice(0, 3);
            if (parcelQuantityInput.value !== raw) {
                parcelQuantityInput.value = raw;
            }
            return raw;
        }

        function runParcelQuantityAjax(digits) {
            var url = (parcelQuantityInput.dataset.parcelQtyAjaxUrl || '').trim();
            if (!url || !parcelQuantityAjaxFeedback) {
                return;
            }
            var qty = parseInt(digits, 10);
            if (!digits || isNaN(qty) || qty < 1) {
                parcelQuantityAjaxFeedback.textContent = '';
                return;
            }
            clearTimeout(parcelQtyAjaxTimer);
            parcelQtyAjaxTimer = setTimeout(function() {
                parcelQuantityAjaxFeedback.textContent = 'Checking…';
                var csrfMeta = document.querySelector('meta[name="csrf-token"]');
                var token = csrfMeta ? csrfMeta.getAttribute('content') : '';
                fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': token
                    },
                    body: JSON.stringify({ parcel_quantity: qty })
                })
                    .then(function(res) {
                        return res.json().then(function(data) {
                            return { ok: res.ok, status: res.status, data: data };
                        });
                    })
                    .then(function(result) {
                        if (result.ok && result.data && typeof result.data.message === 'string') {
                            parcelQuantityAjaxFeedback.textContent = result.data.message;
                            parcelQuantityAjaxFeedback.classList.remove('text-danger');
                            parcelQuantityAjaxFeedback.classList.add('text-success');
                        } else {
                            var msg =
                                (result.data && (result.data.message || result.data.error)) ||
                                'Could not validate quantity.';
                            parcelQuantityAjaxFeedback.textContent = msg;
                            parcelQuantityAjaxFeedback.classList.remove('text-success');
                            parcelQuantityAjaxFeedback.classList.add('text-danger');
                        }
                    })
                    .catch(function() {
                        parcelQuantityAjaxFeedback.textContent = 'Network error. Try again.';
                        parcelQuantityAjaxFeedback.classList.remove('text-success');
                        parcelQuantityAjaxFeedback.classList.add('text-danger');
                    });
            }, 350);
        }

        // Handle parcel quantity change
        function updateParcelIdInputs() {
            var quantity = parseInt(parcelQuantityInput.value, 10) || 0;
            var oldValues = {};

            // Store old values
            Array.from(parcelIdInputs.querySelectorAll('input[name^="parcel_ids"]')).forEach(function(input, index) {
                oldValues[index] = input.value;
            });

            // Clear container
            parcelIdInputs.innerHTML = '';

            if (quantity > 0) {
                parcelIdsContainer.style.display = 'block';

                for (var i = 0; i < quantity; i++) {
                    var colDiv = document.createElement('div');
                    colDiv.className = 'col-md-6 col-lg-4';

                    var label = document.createElement('label');
                    label.className = 'form-label mb-1';
                    label.textContent = 'Parcel ID No.' + (i + 1);

                    var input = document.createElement('input');
                    input.type = 'text';
                    input.name = 'parcel_ids[]';
                    input.className = 'form-control';
                    input.placeholder = 'Parcel ID ' + (i + 1);
                    input.value = oldValues[i] || oldParcelIds[i] || '';
                    input.required = true;

                    colDiv.appendChild(label);
                    colDiv.appendChild(input);
                    parcelIdInputs.appendChild(colDiv);
                }
            } else {
                parcelIdsContainer.style.display = 'none';
            }
        }

        function onParcelQuantityInput() {
            sanitizeParcelQuantityDigits();
            updateParcelIdInputs();
            runParcelQuantityAjax(parcelQuantityInput.value);
        }

        // Restore old quantity values if form had errors
        var oldQuantity = "{{ old('parcel_quantity') }}";
        if (oldQuantity) {
            parcelQuantityInput.value = oldQuantity;
        }

        parcelQuantityInput.addEventListener('input', onParcelQuantityInput);
        vendorSelect.addEventListener('change', filterVehiclesByVendor);

        filterVehiclesByVendor();

        onParcelQuantityInput();

        // Driver → Hub, Staff Employee → Office
        var staffSelect = document.getElementById('user_id');
        var hubWrap = document.getElementById('hubFieldWrap');
        var officeWrap = document.getElementById('officeFieldWrap');
        var hubSelect = document.getElementById('hub_id');
        var officeSelect = document.getElementById('office_id');

        function toggleLocationByStaff() {
            if (!staffSelect || !hubWrap || !officeWrap || !hubSelect || !officeSelect) return;
            var opt = staffSelect.options[staffSelect.selectedIndex];
            var roleType = opt ? (opt.getAttribute('data-role-type') || '') : '';
            var preferredHub = opt ? (opt.getAttribute('data-hub-id') || '') : '';
            var preferredOffice = opt ? (opt.getAttribute('data-office-id') || '') : '';
            var driverFields = document.getElementById('driverParcelFields');
            var logisticsFields = document.getElementById('driverLogisticsFields');
            var parcelQty = document.getElementById('parcel_quantity');
            var assignDate = document.getElementById('assignment_date');
            var statusSelect = document.getElementById('status');
            var submitLabel = document.getElementById('assignSubmitLabel');
            var parcelIdsContainer = document.getElementById('parcelIdsContainer');
            var vendorSelectEl = document.getElementById('vendor_id');
            var vehicleSelectEl = document.getElementById('vehicle_id');

            if (roleType === 'staff') {
                hubWrap.classList.add('d-none');
                officeWrap.classList.remove('d-none');
                hubSelect.removeAttribute('required');
                hubSelect.value = '';
                officeSelect.setAttribute('required', 'required');
                if (preferredOffice && !officeSelect.value) {
                    officeSelect.value = preferredOffice;
                }
                if (logisticsFields) logisticsFields.classList.add('d-none');
                if (driverFields) driverFields.classList.add('d-none');
                if (vendorSelectEl) { vendorSelectEl.removeAttribute('required'); vendorSelectEl.value = ''; }
                if (vehicleSelectEl) { vehicleSelectEl.removeAttribute('required'); vehicleSelectEl.value = ''; }
                if (parcelQty) { parcelQty.removeAttribute('required'); parcelQty.value = ''; }
                if (assignDate) assignDate.removeAttribute('required');
                if (statusSelect) statusSelect.removeAttribute('required');
                if (parcelIdsContainer) parcelIdsContainer.style.display = 'none';
                if (submitLabel) submitLabel.textContent = 'Assign';
            } else if (roleType === 'driver') {
                officeWrap.classList.add('d-none');
                hubWrap.classList.remove('d-none');
                officeSelect.removeAttribute('required');
                officeSelect.value = '';
                hubSelect.setAttribute('required', 'required');
                if (preferredHub && !hubSelect.value) {
                    hubSelect.value = preferredHub;
                }
                if (logisticsFields) logisticsFields.classList.remove('d-none');
                if (driverFields) driverFields.classList.remove('d-none');
                if (vendorSelectEl) vendorSelectEl.setAttribute('required', 'required');
                if (vehicleSelectEl) vehicleSelectEl.setAttribute('required', 'required');
                if (parcelQty) parcelQty.setAttribute('required', 'required');
                if (assignDate) assignDate.setAttribute('required', 'required');
                if (statusSelect) statusSelect.setAttribute('required', 'required');
                if (submitLabel) submitLabel.textContent = 'Create Assignment';
                if (typeof onParcelQuantityInput === 'function') onParcelQuantityInput();
            } else {
                hubWrap.classList.remove('d-none');
                officeWrap.classList.add('d-none');
                hubSelect.removeAttribute('required');
                officeSelect.removeAttribute('required');
                officeSelect.value = '';
                if (logisticsFields) logisticsFields.classList.remove('d-none');
                if (driverFields) driverFields.classList.remove('d-none');
                if (submitLabel) submitLabel.textContent = 'Create Assignment';
            }
        }

        if (staffSelect) {
            staffSelect.addEventListener('change', toggleLocationByStaff);
            toggleLocationByStaff();
        }
    });
</script>
@endpush