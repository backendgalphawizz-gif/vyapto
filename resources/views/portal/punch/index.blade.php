@extends('layouts.portal')

@section('title', 'Punch In / Out')

@section('page_subtitle')
Mark your attendance with location verification.
@endsection

@section('content')
@php
    $punchedIn = $attendance && $attendance->punch_in_time;
    $punchedOut = $attendance && $attendance->punch_out_time;
@endphp

<div class="punch-page-layout">
    <div>
        <div class="app-card">
            <div class="punch-card-row mb-3">
                <div class="punch-icon-box"><i class="bi bi-geo-alt"></i></div>
                <div>
                    <strong>Location Required</strong>
                    <small>Allow GPS and stay within 100 meters of your assigned hub or office.</small>
                </div>
            </div>
            <div id="locationStatus" class="portal-alert portal-alert-error mb-0">Fetching your location...</div>
        </div>

        <div class="app-card">
            <div class="punch-columns">
                <div class="punch-col">
                    <div class="punch-arrow-box"><i class="bi bi-box-arrow-in-right"></i></div>
                    <div>
                        <small>Punch In</small>
                        <strong>{{ optional($attendance?->punch_in_time)->format('h:i a') ?? '--:--' }}</strong>
                    </div>
                </div>
                <div class="punch-col">
                    <div class="punch-arrow-box"><i class="bi bi-box-arrow-left"></i></div>
                    <div>
                        <small>Punch Out</small>
                        <strong>{{ optional($attendance?->punch_out_time)->format('h:i a') ?? '--:--' }}</strong>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div>
        <div class="app-card {{ $punchedIn ? 'punch-form-disabled' : '' }}">
            <h5 class="mb-3">Punch In</h5>
            @if($punchedIn)
                <div class="portal-alert portal-alert-success mb-3">You have already punched in for today.</div>
            @endif
            <form id="punchInForm" method="POST" action="{{ route('portal.punch.in') }}" enctype="multipart/form-data" @if($punchedIn) data-disabled="1" @endif>
                @csrf
                <input type="hidden" name="latitude" id="in_latitude">
                <input type="hidden" name="longitude" id="in_longitude">
                <input type="hidden" name="location" id="in_location">
                <div class="app-input-wrap mb-3">
                    <label>Selfie (optional)</label>
                    <input type="file" name="image" accept="image/*" capture="user" class="form-control form-control-dark" @disabled($punchedIn)>
                </div>
                <button type="submit" class="app-btn app-btn-green" id="punchInBtn" @disabled($punchedIn)>
                    <i class="bi bi-camera"></i> Punch In
                </button>
            </form>
        </div>

        <div class="app-card {{ $punchedOut ? 'punch-form-disabled' : '' }}">
            <h5 class="mb-3">Punch Out</h5>
            @if($punchedOut)
                <div class="portal-alert portal-alert-success mb-3">You have already punched out for today.</div>
            @elseif(!$punchedIn)
                <div class="portal-alert portal-alert-error mb-3">Punch in first to enable punch out.</div>
            @endif
            <form id="punchOutForm" method="POST" action="{{ route('portal.punch.out') }}" enctype="multipart/form-data" @if($punchedOut || !$punchedIn) data-disabled="1" @endif>
                @csrf
                <input type="hidden" name="latitude" id="out_latitude">
                <input type="hidden" name="longitude" id="out_longitude">
                <input type="hidden" name="location" id="out_location">
                <div class="app-input-wrap mb-3">
                    <label>Selfie (optional)</label>
                    <input type="file" name="image" accept="image/*" capture="user" class="form-control form-control-dark" @disabled(!$punchedIn || $punchedOut)>
                </div>
                <button type="submit" class="app-btn app-btn-outline" id="punchOutBtn" @disabled(!$punchedIn || $punchedOut)>
                    <i class="bi bi-box-arrow-right"></i> Punch Out
                </button>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
(function () {
    const statusEl = document.getElementById('locationStatus');
    let coords = null;

    function setCoords(lat, lng) {
        coords = { lat, lng };
        ['in', 'out'].forEach(function (prefix) {
            document.getElementById(prefix + '_latitude').value = lat;
            document.getElementById(prefix + '_longitude').value = lng;
            document.getElementById(prefix + '_location').value = 'Browser GPS location';
        });
        statusEl.className = 'portal-alert portal-alert-success mb-0';
        statusEl.textContent = 'Location ready';
    }

    if (!navigator.geolocation) {
        statusEl.textContent = 'Geolocation is not supported.';
        return;
    }

    navigator.geolocation.getCurrentPosition(function (position) {
        setCoords(position.coords.latitude, position.coords.longitude);
    }, function () {
        statusEl.textContent = 'Unable to fetch location. Please allow GPS access.';
    }, { enableHighAccuracy: true, timeout: 15000 });

    function guardSubmit(event) {
        if (event.currentTarget.dataset.disabled === '1') {
            event.preventDefault();
            return;
        }
        if (!coords) {
            event.preventDefault();
            alert('Location is required before punching.');
        }
    }

    document.getElementById('punchInForm').addEventListener('submit', guardSubmit);
    document.getElementById('punchOutForm').addEventListener('submit', guardSubmit);
})();
</script>
@endpush
