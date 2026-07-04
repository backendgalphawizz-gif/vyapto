@extends('layouts.portal')

@section('title', $rideAction === 'end' ? 'End Ride' : 'Start Ride')

@section('content')
<div class="app-card" style="max-width: 560px;">
    <h5 class="mb-3">{{ $rideAction === 'end' ? 'End Ride' : 'Start Ride' }}</h5>
    <p class="text-muted mb-3">Upload odometer snapshot and vehicle number.</p>

    <form method="POST" action="{{ route('portal.vehicle-usage.store') }}" enctype="multipart/form-data">
        @csrf
        <div class="app-field">
            <div class="ride-upload-box @error('image') is-invalid @enderror">
                <label for="ride_image" class="ride-upload-label">
                    <img class="ride-image-preview d-none" alt="Odometer preview">
                    <i class="bi bi-camera ride-upload-hint"></i>
                    <span class="ride-upload-hint">Tap to capture odometer reading</span>
                    <span class="ride-upload-filename d-none"></span>
                    <input type="file" id="ride_image" name="image" accept="image/*" capture="environment" required>
                </label>
            </div>
            @error('image')<div class="app-field-error">{{ $message }}</div>@enderror
        </div>
        <div class="app-field">
            <div class="app-input-wrap @error('vehicle_number') is-invalid @enderror">
                <label>Vehicle Number</label>
                <input type="text"
                       name="vehicle_number"
                       class="app-input"
                       value="{{ old('vehicle_number', $suggestedVehicleNumber) }}"
                       placeholder="MH-12-AB-1234"
                       @if($rideAction === 'end') readonly @endif
                       required>
                @if($rideAction === 'end')
                    <small class="text-muted d-block px-3 pb-2">Auto-filled from your active ride</small>
                @elseif($suggestedVehicleNumber)
                    <small class="text-muted d-block px-3 pb-2">Auto-filled from your assignment</small>
                @endif
            </div>
            @error('vehicle_number')<div class="app-field-error">{{ $message }}</div>@enderror
        </div>
        <div class="app-field">
            <div class="app-input-wrap @error('kms') is-invalid @enderror">
                <label>Odometer Kilometer</label>
                <input type="number" step="0.01" min="0" name="kms" class="app-input" value="{{ old('kms') }}" placeholder="Enter Odometer KM" required>
            </div>
            @error('kms')<div class="app-field-error">{{ $message }}</div>@enderror
        </div>
        <div class="d-flex gap-2">
            <button type="submit" class="app-btn {{ $rideAction === 'end' ? 'app-btn-danger' : 'app-btn-green' }} app-btn-sm">
                {{ $rideAction === 'end' ? 'End Ride' : 'Start Ride' }}
            </button>
            <a href="{{ route('portal.dashboard') }}" class="app-btn app-btn-outline app-btn-sm">Cancel</a>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script src="{{ asset('assets/portal/js/ride-form.js') }}"></script>
<script>
initRideForm({
    clearOnSuccess: {{ session('success') && !$errors->any() ? 'true' : 'false' }},
    restoreOnError: {{ $errors->any() ? 'true' : 'false' }},
});
</script>
@endpush
