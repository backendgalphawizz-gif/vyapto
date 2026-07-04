@extends('layouts.portal')

@section('title', 'Home')

@section('content')
@php
    $punchedIn = $attendance && $attendance->punch_in_time;
    $punchedOut = $attendance && $attendance->punch_out_time;
@endphp

<section class="welcome-banner">
    <div class="welcome-banner-content">
        <p class="welcome-kicker">Good {{ now()->hour < 12 ? 'morning' : (now()->hour < 17 ? 'afternoon' : 'evening') }},</p>
        <h2>{{ Auth::user()->name }}</h2>
        <p class="welcome-text">Here is a quick overview of your attendance, shipments, and salary for today.</p>
    </div>
    <div class="welcome-banner-meta">
        <div class="welcome-stat">
            <span>Today punch in</span>
            <strong>{{ optional($attendance?->punch_in_time)->format('h:i A') ?? 'Not yet' }}</strong>
        </div>
        <div class="welcome-stat">
            <span>Pending shipments</span>
            <strong>{{ $canViewShipments ? $pendingParcelsToday : '--' }}</strong>
        </div>
    </div>
</section>

<div class="dashboard-layout">
    <div class="dashboard-layout-primary">
        <div class="app-card app-card-highlight">
            <div class="punch-card-row">
                <div class="punch-icon-box"><i class="bi bi-fingerprint"></i></div>
                <div class="flex-grow-1">
                    <strong>{{ $punchedOut ? 'Day Completed' : ($punchedIn ? 'Punched In' : 'Not Punched In') }}</strong>
                    <small>
                        @if($punchedOut)
                            In at {{ $attendance->punch_in_time->format('h:i A') }}, out at {{ $attendance->punch_out_time->format('h:i A') }}.
                        @elseif($punchedIn)
                            Since {{ $attendance->punch_in_time->format('d-m-Y h:i A') }}.
                        @else
                            Mark attendance with selfie and GPS location.
                        @endif
                    </small>
                </div>
            </div>
            @if($punchedOut)
                <span class="app-btn app-btn-outline mt-3 punch-btn-disabled" aria-disabled="true">
                    <i class="bi bi-check-circle"></i> Attendance Completed
                </span>
            @else
                <a href="{{ route('portal.punch.index') }}" class="app-btn {{ $punchedIn ? 'app-btn-outline' : 'app-btn-green' }} mt-3">
                    <i class="bi bi-{{ $punchedIn ? 'box-arrow-right' : 'camera' }}"></i>
                    {{ $punchedIn ? 'Punch Out Now' : 'Punch In Now' }}
                </a>
            @endif
        </div>

        <div class="app-card ride-shipment-card">
            <div class="app-card-header">
                <h5><i class="bi bi-truck me-2"></i>Today's Shipments</h5>
                <span class="ride-status-badge ride-status-{{ $rideStatus }}">{{ $rideStatusLabel }}</span>
            </div>

            @if($canViewShipments)
                <div class="shipment-stat-row">
                    <div class="shipment-stat shipment-stat-pending">
                        <i class="bi bi-box-seam"></i>
                        <div>
                            <strong>{{ $pendingParcelsToday }}</strong>
                            <small>Pending</small>
                        </div>
                    </div>
                    <div class="shipment-stat shipment-stat-delivered">
                        <i class="bi bi-check-circle"></i>
                        <div>
                            <strong>{{ $deliveredParcelsToday }}</strong>
                            <small>Delivered</small>
                        </div>
                    </div>
                </div>
                <div class="app-btn-split mt-3">
                    <a href="{{ route('portal.parcels.index') }}" class="app-btn app-btn-green">View Shipments</a>
                    <a href="{{ route('portal.parcels.index') }}" class="app-btn app-btn-outline">Update Status</a>
                </div>
            @else
                <p class="card-text-muted mb-3">
                    @if(!$punchedIn)
                        Punch in first, then start your ride to unlock today's shipments.
                    @elseif($punchedOut)
                        Shipments are closed because you have already punched out.
                    @else
                        Start your ride with vehicle number and odometer reading to view shipments.
                    @endif
                </p>
                <div class="shipment-stat-row shipment-stat-row-muted">
                    <div class="shipment-stat shipment-stat-pending">
                        <i class="bi bi-box-seam"></i>
                        <div>
                            <strong>--</strong>
                            <small>Pending</small>
                        </div>
                    </div>
                    <div class="shipment-stat shipment-stat-delivered">
                        <i class="bi bi-check-circle"></i>
                        <div>
                            <strong>--</strong>
                            <small>Delivered</small>
                        </div>
                    </div>
                </div>
            @endif

            <div class="ride-action-wrap mt-3">
                @if($canStartRide)
                    <button type="button" class="app-btn app-btn-green w-100" data-bs-toggle="modal" data-bs-target="#rideModal">
                        <i class="bi bi-play-circle"></i> Start Ride
                    </button>
                @elseif($canEndRide)
                    <button type="button" class="app-btn app-btn-danger w-100" data-bs-toggle="modal" data-bs-target="#rideModal">
                        <i class="bi bi-stop-circle"></i> End Ride
                    </button>
                @endif
            </div>
        </div>
    </div>

    <div class="dashboard-layout-secondary">
        <div class="app-card">
            <div class="app-card-header">
                <h5>Attendance Summary</h5>
                <a href="{{ route('portal.attendance.index') }}" class="app-link">View all</a>
            </div>
            <div class="stat-grid">
                <div class="stat-present">
                    <div class="stat-icon"><i class="bi bi-check-lg"></i></div>
                    <div class="stat-value">{{ str_pad($presentCount, 2, '0', STR_PAD_LEFT) }}</div>
                    <small>Present</small>
                </div>
                <div class="stat-absent">
                    <div class="stat-icon"><i class="bi bi-x-lg"></i></div>
                    <div class="stat-value">{{ str_pad($absentCount, 2, '0', STR_PAD_LEFT) }}</div>
                    <small>Absent</small>
                </div>
                <div class="stat-total">
                    <div class="stat-icon"><i class="bi bi-calendar3"></i></div>
                    <div class="stat-value">{{ str_pad($workingDays, 2, '0', STR_PAD_LEFT) }}</div>
                    <small>Working Days</small>
                </div>
            </div>
        </div>

        <div class="app-card app-card-green">
            <div class="app-card-header">
                <h6><i class="bi bi-wallet2 me-2"></i>Monthly Salary</h6>
                <span>{{ now()->format('F Y') }}</span>
            </div>
            <div class="salary-amount">₹{{ number_format((float) $estimatedSalary, 0) }}</div>
            <small>Estimated based on attendance</small>
            <a href="{{ $salarySlipUrl }}" class="app-btn app-btn-white mt-3">Download Slip</a>
        </div>
    </div>
</div>

@if($canStartRide || $canEndRide)
    <div class="modal fade ride-modal" id="rideModal" tabindex="-1" aria-labelledby="rideModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <div>
                        <h5 class="modal-title" id="rideModalLabel">
                            <i class="bi bi-truck me-2"></i>{{ $canEndRide ? 'End Ride' : 'Start Ride' }}
                        </h5>
                        <small class="text-muted">Upload odometer snapshot and vehicle number</small>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" action="{{ route('portal.vehicle-usage.store') }}" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
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
                                       @if($canEndRide) readonly @endif
                                       required>
                                @if($canEndRide)
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
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="app-btn app-btn-outline" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="app-btn {{ $canEndRide ? 'app-btn-danger' : 'app-btn-green' }}">
                            {{ $canEndRide ? 'End Ride' : 'Start Ride' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endif
@endsection

@push('scripts')
<script src="{{ asset('assets/portal/js/ride-form.js') }}"></script>
<script>
(function () {
    initRideForm({
        clearOnSuccess: {{ session('success') && !$errors->any() ? 'true' : 'false' }},
        restoreOnError: {{ $errors->any() ? 'true' : 'false' }},
    });

    @if(($errors->has('kms') || $errors->has('vehicle_number') || $errors->has('image')) && ($canStartRide || $canEndRide))
        const rideModal = document.getElementById('rideModal');
        if (rideModal && window.bootstrap) {
            bootstrap.Modal.getOrCreateInstance(rideModal).show();
        }
    @endif
})();
</script>
@endpush
