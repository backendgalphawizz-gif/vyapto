@extends('layouts.admin')

@section('title','Add Permission')

@section('content')
<div class="main-section">

    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0">Add New Permission / Module</h4>
		
		 <a href="{{ route('permissions.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Back
            </a>
			
		
    </div>

    <!-- Success Message -->
     @if(session('success'))
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                Swal.fire({
                    icon: 'success',
                    title: 'Success',
                    text: '{{ session('success') }}',
                    confirmButtonColor: '#3085d6',
                    timer: 3000,
                    timerProgressBar: true,
                    confirmButtonText: 'OK'
                });
            });
        </script>
    @endif

    <!-- Permission Form Card -->
    <div class="card shadow-sm border rounded-lg">
        <div class="card-body">
            <form action="{{ route('permissions.store') }}" method="POST">
                @csrf

                <!-- Module Name -->
                <div class="mb-3">
                    <label for="module" class="form-label fw-bold">Module Name</label>
                    <input type="text" name="module" id="module" class="form-control rounded-3" placeholder="Manage Users" required>
                </div>

                <!-- Permission Name -->
                <div class="mb-3">
                    <label for="name" class="form-label fw-bold">Permission Name</label>
                    <input type="text" name="name" id="name" class="form-control rounded-3" placeholder="users.create" required>
                </div>

                <!-- Route / URL -->
                <div class="mb-3">
                    <label for="route" class="form-label fw-bold">Route / URL</label>
                    <input type="text" name="route" id="route" class="form-control rounded-3" placeholder="/users/create" required>
                </div>

                <!-- Submit Button -->
                <div class="text-center">
                    <button type="submit" class="btn btn-primary rounded-pill px-4">
                        <i class="fa-solid fa-floppy-disk me-1"></i> Save Permission
                    </button>
                </div>

            </form>
        </div>
    </div>

</div>
@endsection