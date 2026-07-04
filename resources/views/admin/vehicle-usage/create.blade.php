@extends('layouts.admin')
@section('title', 'Add Vehicle Usage')

@section('content')
<div class="main-section">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="fw-bold mb-0">Add Vehicle Usage</h4>
        <a href="{{ route('admin.vehicle-usage.index') }}" class="btn btn-outline-secondary btn-sm rounded-3">
            <i class="bi bi-arrow-left me-1"></i> Back to List
        </a>
    </div>

    <div class="card shadow-sm rounded border mb-4">
        <form action="{{ route('admin.vehicle-usage.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="card-body">
                @if($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Vehicle Number <span class="text-danger">*</span></label>
                        <input type="text" name="vehicle_number" class="form-control @error('vehicle_number') is-invalid @enderror" value="{{ old('vehicle_number') }}" required>
                        @error('vehicle_number')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Driver <span class="text-danger">*</span></label>
                        <select name="user_id" class="form-select @error('user_id') is-invalid @enderror" required>
                            <option value="">Select Driver</option>
                            @foreach($users as $user)
                            @if((int) ($user->role_id ?? 0) !== 3)
                            @continue
                            @endif
                            <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>{{ $user->name }} - {{ $user->email }}</option>
                            @endforeach
                        </select>
                        @error('user_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">KMs Driven</label>
                        <input type="number" name="kms" class="form-control @error('kms') is-invalid @enderror" value="{{ old('kms') }}" step="0.01" min="0">
                        @error('kms')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Vehicle Image</label>
                        <input type="file" name="image" id="image" class="form-control @error('image') is-invalid @enderror" accept="image/*">
                        <small class="text-muted">Allowed formats: JPG, JPEG, PNG, GIF. Max size: 2MB</small>
                        @error('image')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12">
                        <div id="imagePreview" class="d-none mt-2">
                            <img id="preview" src="#" alt="Preview" style="max-width: 220px; max-height: 220px; object-fit: cover;" class="rounded border">
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer bg-white d-flex gap-2 justify-content-end">
                <a href="{{ route('admin.vehicle-usage.index') }}" class="btn btn-outline-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-circle me-1"></i> Save Record
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.getElementById('image')?.addEventListener('change', function (e) {
        const file = e.target.files[0];
        const wrap = document.getElementById('imagePreview');
        const img = document.getElementById('preview');

        if (!file) {
            wrap.classList.add('d-none');
            img.src = '#';
            return;
        }

        const reader = new FileReader();
        reader.onload = function (ev) {
            img.src = ev.target.result;
            wrap.classList.remove('d-none');
        };
        reader.readAsDataURL(file);
    });
</script>
@endpush