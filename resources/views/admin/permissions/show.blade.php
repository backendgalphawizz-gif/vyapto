@extends('layouts.admin')

@section('title', 'Show Role')

@section('content')
<div class="main-section">

    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0">Role Details</h4>
        <a class="btn btn-primary rounded-pill px-4" href="{{ route('roles.index') }}">
            <i class="fa-solid fa-arrow-left me-1"></i> Back
        </a>
    </div>

    <!-- Role Info Card -->
    <div class="card shadow-sm border rounded-lg">
        <div class="card-body">
            <div class="mb-3">
                <h6 class="fw-bold">Role Name</h6>
                <p class="mb-0">{{ $role->name }}</p>
            </div>

            <div class="mb-3">
                <h6 class="fw-bold">Permissions</h6>
                <div class="d-flex flex-wrap gap-2">
                    @if(!empty($rolePermissions))
                        @foreach($rolePermissions as $permission)
                            <span class="badge bg-success">{{ $permission->name }}</span>
                        @endforeach
                    @else
                        <span class="text-muted">No permissions assigned</span>
                    @endif
                </div>
            </div>
        </div>
    </div>

</div>
@endsection