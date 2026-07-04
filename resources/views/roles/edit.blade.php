@extends('layouts.admin')

@section('title', 'Edit Role')

@section('content')
<div class="main-section">

    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0">Edit Role</h4>
        <a class="btn btn-primary rounded-pill px-4 btn-sm" href="{{ route('roles.index') }}">
            <i class="fa fa-arrow-left me-1"></i> Back
        </a>
    </div>

    <!-- Validation Errors -->
    @if ($errors->any())
        <div class="alert alert-danger rounded-3">
            <strong>Whoops!</strong> There were some problems with your input.<br><br>
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Edit Role Form -->
    <div class="card shadow-sm border rounded-lg">
        <div class="card-body">
            <form method="POST" action="{{ route('roles.update', $role->id) }}">
                @csrf
                @method('PUT')

                <!-- Role Name -->
                <div class="mb-3">
                    <label class="form-label fw-bold">Role Name</label>
                    <input type="text" name="name" placeholder="Enter Role Name" class="form-control rounded-3" value="{{ $role->name }}" required>
                </div>

                <!-- Permissions -->
                <div class="mb-3">
                    <label class="form-label fw-bold">Permissions</label>
                    <div class="d-flex flex-wrap gap-2">
                        @foreach($permission as $value)
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" 
                                       name="permission[{{$value->id}}]" 
                                       value="{{$value->id}}" 
                                       id="perm{{$value->id}}"
                                       {{ in_array($value->id, $rolePermissions) ? 'checked' : '' }}>
                                <label class="form-check-label" for="perm{{$value->id}}">
                                    {{ $value->name }}
                                </label>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="text-center">
                    <button type="submit" class="btn btn-primary rounded-pill px-4">
                        <i class="fa-solid fa-floppy-disk me-1"></i> Update
                    </button>
                </div>

            </form>
        </div>
    </div>

</div>
@endsection