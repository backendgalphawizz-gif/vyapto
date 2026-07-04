@extends('layouts.admin')

@section('title', 'Show Role')

@section('content')
<div class="main-section">

    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <h4 class="mb-0 fw-bold">Role Details</h4>
        <a class="btn btn-primary rounded-pill px-4 d-flex align-items-center gap-1" href="{{ route('roles.index') }}">
            <i class="fa-solid fa-arrow-left"></i> Back
        </a>
    </div>

    @push('styles')
    
    @endpush

    <!-- Role Info Card -->
    <div class="card role-card mb-4">
        <div class="card-body">
            <div class="mb-4">
                <h6 class="fw-bold text-primary">Role Name</h6>
                <p class="fs-5">{{ $role->name }}</p>
            </div>

            <div>
                <h6 class="fw-bold text-success mb-2">Permissions</h6>
                <div class="d-flex flex-wrap gap-2">
                    @if(!empty($rolePermissions))
                        @foreach($rolePermissions as $permission)
                            <span class="badge badge-gradient">{{ $permission->name }}</span>
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