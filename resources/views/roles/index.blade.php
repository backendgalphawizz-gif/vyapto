@extends('layouts.admin')

@section('title', 'Role Management')

@section('content')
<div class="main-section">

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

    @push('styles')
    
    @endpush

    <!-- Sticky Header -->
    <div class="sticky-top bg-white py-2 mb-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
        <h5 class="mb-0 fw-bold">Role Management</h5>
        <div class="d-flex gap-2 flex-wrap">
            <a href="{{ route('permissions.index') }}" class="btn btn-success btn-rounded d-flex align-items-center gap-1">
                <i class="bi bi-plus-circle"></i> Create Permission
            </a>
            <a href="{{ route('roles.create') }}" class="btn btn-primary btn-rounded d-flex align-items-center gap-1">
                <i class="bi bi-plus-circle"></i> Create New Role
            </a>
        </div>
    </div>

    <!-- Roles Table -->
    <div class="table-container">
        <table class="table table-hover table-bordered mb-0 text-center">
            <thead>
                <tr>
                    <th style="width: 5%;">ID</th>
                    <th class="text-start">Role Name</th>
                    <th style="width: 30%;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($roles as $key => $role)
                    <tr>
                        <td>{{ ++$i }}</td>
                        <td class="text-start">{{ $role->name }}</td>
                        <td>
                            <!-- <a href="{{ route('roles.show', $role->id) }}" class="btn btn-info action-btn btn-sm">
                                <i class="fa-solid fa-list"></i> Show
                            </a> -->
                            <a href="{{ route('roles.edit', $role->id) }}" class="btn btn-secondary action-btn btn-sm p-2">
                                <i class="bi bi-pencil-square"></i> 
                            </a>
                            <!-- <form action="{{ route('roles.destroy', $role->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this role?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger action-btn btn-sm">
                                    <i class="fa-solid fa-trash"></i> Delete
                                </button>
                            </form> -->
                            <form action="{{ route('roles.destroy', $role->id) }}" 
      method="POST" 
      class="d-inline delete-form">
    @csrf
    @method('DELETE')

    <button type="submit" class="btn btn-danger action-btn btn-sm p-2">
        <i class="bi bi-trash"></i> 
    </button>
</form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="mt-3 d-flex justify-content-end">
        {!! $roles->links('pagination::bootstrap-5') !!}
    </div>

</div>
@endsection



<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
document.addEventListener('submit', function(e) {
    if (e.target.classList.contains('delete-form')) {
        e.preventDefault();

        Swal.fire({
            title: 'Are you sure?',
            text: "Delete this role?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#7066e0',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                e.target.submit();
            }
        });
    }
});
</script>