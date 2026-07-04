@extends('layouts.admin')
@section('title', 'Edit Hub')

@section('content')
<div class="main-section">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="fw-bold mb-0">Edit Hub</h4>
        <a href="{{ route('admin.hubs.index') }}" class="btn btn-outline-secondary btn-sm rounded-3">
            <i class="bi bi-arrow-left me-1"></i> Back to Hubs
        </a>
    </div>

    <div class="card shadow-sm rounded border mb-4">
        <div class="card-body p-4">
            <form action="{{ route('admin.hubs.update', $hub) }}" method="POST" class="row g-3">
                @csrf
                @method('PUT')

                <div class="col-12">
                    <label for="name" class="form-label fw-semibold">Hub Name <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('name') is-invalid @enderror"
                        id="name" name="name" value="{{ old('name', $hub->name) }}" required>
                    @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-12">
                    <label for="location" class="form-label fw-semibold">Address/Location</label>
                    <input type="text" class="form-control @error('location') is-invalid @enderror"
                        id="location" name="location" value="{{ old('location', $hub->location) }}">
                    @error('location')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label for="latitude" class="form-label fw-semibold">Latitude</label>
                    <input type="number" step="any" class="form-control @error('latitude') is-invalid @enderror"
                        id="latitude" name="latitude" value="{{ old('latitude', $hub->latitude) }}">
                    @error('latitude')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label for="longitude" class="form-label fw-semibold">Longitude</label>
                    <input type="number" step="any" class="form-control @error('longitude') is-invalid @enderror"
                        id="longitude" name="longitude" value="{{ old('longitude', $hub->longitude) }}">
                    @error('longitude')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label for="opening_time" class="form-label fw-semibold">Opening Time</label>
                    <input type="time" class="form-control @error('opening_time') is-invalid @enderror"
                        id="opening_time" name="opening_time" value="{{ old('opening_time', $hub->opening_time ? date('H:i', strtotime($hub->opening_time)) : '') }}">
                    @error('opening_time')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label for="closing_time" class="form-label fw-semibold">Closing Time</label>
                    <input type="time" class="form-control @error('closing_time') is-invalid @enderror"
                        id="closing_time" name="closing_time" value="{{ old('closing_time', $hub->closing_time ? date('H:i', strtotime($hub->closing_time)) : '') }}">
                    @error('closing_time')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-12 d-flex gap-2 justify-content-end mt-2">
                    <a href="{{ route('admin.hubs.index') }}" class="btn btn-light border">Cancel</a>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save me-1"></i> Update Hub
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection