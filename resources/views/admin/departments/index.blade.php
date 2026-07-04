@extends('layouts.admin')
@section('title', 'Manage Departments')
@section('content')

<div class="main-section">

    <!-- Success Message -->
    @if(session('success'))
        @push('scripts')
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
        @endpush
    @endif
    
    <!-- Error Message -->
    @if(session('error'))
        @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: '{{ session('error') }}',
                    confirmButtonColor: '#d33',
                    timer: 3000,
                    timerProgressBar: true,
                    confirmButtonText: 'OK'
                });
            });
        </script>
        @endpush
    @endif

    <!-- Header with Add Button -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="fw-bold">Department List</h4>
        <div>
            @include('partials.export-dropdown', [
                'exportRoute' => 'departments.export',
                'exportQuery' => request()->only(['search', 'status', 'sort_by', 'sort_order']),
            ])
            <button class="btn btn-primary  rounded-3" data-bs-toggle="modal" data-bs-target="#addDepartmentModal">
                <i class="bi bi-building me-1"></i> Add Department
            </button>
        </div>
    </div>

    <!-- Filters -->
    <form method="GET" action="{{ route('departments.index') }}" class="row g-2 mb-3">
        <div class="col-md-6">
            <input type="text" name="search" value="{{ request('search') }}"
                class="form-control"
                placeholder="Search by department name...">
        </div>

        <div class="col-md-3">
            <select name="status" class="form-select">
                <option value="">All Status</option>
                <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>Active</option>
                <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Inactive</option>
            </select>
        </div>
        <div class="col-auto">
            <input type="hidden" name="sort_by" value="{{ request('sort_by') }}">
            <input type="hidden" name="sort_order" value="{{ request('sort_order') }}">
            
            <button class="btn btn-primary">Search</button>
            <a href="{{ route('departments.index') }}" class="btn btn-outline-secondary">Reset</a>
        </div>
    </form>

    <!-- Departments Table Card -->
    <div class="card shadow-sm rounded border mb-4">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover table-bordered align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="py-3" style="width: 5%;">ID</th>
                            
                            <th class="py-3">
                                <a href="{{ request()->fullUrlWithQuery([
                                    'sort_by' => 'name', 
                                    'sort_order' => request('sort_by') == 'name' && request('sort_order') == 'asc' ? 'desc' : 'asc'
                                ]) }}" class="text-decoration-none text-dark d-flex align-items-center justify-content-between">
                                    Department Name
                                    @if(request('sort_by') == 'name')
                                        <i class="bi bi-sort-{{ request('sort_order') == 'asc' ? 'down' : 'up' }}"></i>
                                    @else
                                        <i class="bi bi-arrow-down-up text-muted opacity-25"></i>
                                    @endif
                                </a>
                            </th>

                            <th class="py-3 text-center" style="width: 15%;">
                                <a href="{{ request()->fullUrlWithQuery([
                                    'sort_by' => 'created_at', 
                                    'sort_order' => request('sort_by') == 'created_at' && request('sort_order') == 'asc' ? 'desc' : 'asc'
                                ]) }}" class="text-decoration-none text-dark d-flex align-items-center justify-content-center gap-1">
                                    Created Date
                                    @if(request('sort_by') == 'created_at')
                                        <i class="bi bi-sort-{{ request('sort_order') == 'asc' ? 'down' : 'up' }}"></i>
                                    @else
                                        <i class="bi bi-arrow-down-up text-muted opacity-25"></i>
                                    @endif
                                </a>
                            </th>

                            <th class="py-3 text-center" style="width: 15%;">Status</th>
                            <th class="py-3 text-center" style="width: 15%;">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="border-top-0">
                        @forelse($departments as $department)
                        <tr>
                            <td class="text-center">{{ $loop->iteration + ($departments->currentPage() - 1) * $departments->perPage() }}</td>
                            
                            <!-- Department Name -->
                            <td class="fw-bold text-dark">{{ $department->name }}</td>
                            
                            <!-- Created Date -->
                            <td class="text-center text-muted small">
                                {{ $department->created_at->format('d M, Y') }}<br>
                                {{ $department->created_at->format('h:i A') }}
                            </td>
                            
                            <!-- Status Toggle -->
                            <td class="text-center">
                                <div class="form-check form-switch d-inline-block">
                                    <input class="form-check-input status-toggle" type="checkbox" 
                                        data-id="{{ $department->id }}" 
                                        {{ $department->status == 1 ? 'checked' : '' }}>
                                </div>
                            </td>

                            <!-- Action Buttons -->
                            <td class="text-center">
                                <div class="d-flex justify-content-center gap-2">
                                     <!-- Edit Button -->
                                    <button class="btn btn-sm btn-secondary" data-bs-toggle="modal" data-bs-target="#editDepartmentModal{{ $department->id }}" title="Edit">
                                        <i class="bi bi-pencil-square"></i>
                                    </button>

                                    <!-- Delete Button -->
                                    <form action="{{ route('departments.destroy', $department->id) }}" method="POST" class="d-inline delete-form">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" title="Delete">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-4 bg-light text-muted">No Departments found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>

                <div class="mt-3 d-flex justify-content-between align-items-center">
                    <div class="text-muted small">
                        Showing {{ $departments->firstItem() ?? 0 }}–{{ $departments->lastItem() ?? 0 }} of {{ $departments->total() }} entries
                    </div>
                    @if($departments->hasPages())
                    <div>{{ $departments->links() }}</div>
                    @endif
                </div>

            </div>
        </div>
    </div>

    <!-- Edit Department Modals -->
    @foreach($departments as $department)
        <div class="modal fade" id="editDepartmentModal{{ $department->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-primary rounded-4 shadow-sm">
                    <form action="{{ route('departments.update', $department->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="modal-header py-2 px-3 border-bottom-0">
                            <h5 class="modal-title text-primary fw-bold">
                                <i class="bi bi-pencil-square me-2"></i> Edit Department
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="name" class="form-label fw-bold">Department Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="name" value="{{ old('name', $department->name) }}" required>
                                @if ($errors->has('departmentUpdate'.$department->id) && $errors->first('name'))
                                    <div class="text-danger small mt-1">{{ $errors->first('name') }}</div>
                                @endif
                            </div>
                        </div>
                        <div class="modal-footer border-top-0">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary px-4">Update Department</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endforeach

</div>

<!-- Add Department Modal -->
<div class="modal fade" id="addDepartmentModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-primary rounded-4 shadow-sm">
            <form id="addDepartmentForm" action="{{ route('departments.store') }}" method="POST" novalidate>
                @csrf
                <div class="modal-header py-2 px-3 border-bottom-0">
                    <h5 class="modal-title text-primary fw-bold">
                        <i class="bi bi-plus-circle me-2"></i> Add New Department
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div class="mb-3">
                        <label for="name" class="form-label fw-bold">Department Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="name" value="{{ old('name') }}" placeholder="Enter department name" required>
                         @if ($errors->departmentCreation->has('name'))
                            <div class="text-danger small mt-1">{{ $errors->departmentCreation->first('name') }}</div>
                        @endif
                    </div>
                </div>

                <div class="modal-footer border-top-0">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary px-4">Create Department</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        @if ($errors->departmentCreation->any())
            var addModal = new bootstrap.Modal(document.getElementById('addDepartmentModal'));
            addModal.show();
        @endif

        @foreach($departments as $department)
            @if ($errors->has('departmentUpdate'.$department->id))
                var editModal = new bootstrap.Modal(document.getElementById('editDepartmentModal{{ $department->id }}'));
                editModal.show();
            @endif
        @endforeach
    });
    
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.delete-form').forEach(function(form) {
            form.addEventListener('submit', function(e) {
                e.preventDefault();

                Swal.fire({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#7066e0',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Yes, delete it!',
                    cancelButtonText: 'Cancel',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });

        // Status Toggle Script
        document.querySelectorAll('.status-toggle').forEach(function(toggle) {
            toggle.addEventListener('change', function() {
                var id = this.getAttribute('data-id');
                var status = this.checked ? 1 : 0;
                var url = '{{ route("departments.updateStatus") }}'; // Ensure route exists

                fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ id: id, status: status })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const toast = Swal.mixin({
                            toast: true,
                            position: 'top-end',
                            showConfirmButton: false,
                            timer: 3000,
                            timerProgressBar: true,
                        });
                        toast.fire({
                            icon: 'success',
                            title: 'Status Updated Successfully'
                        });
                    } else {
                         Swal.fire('Error', 'Something went wrong!', 'error');
                         this.checked = !status; // Revert
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire('Error', 'Something went wrong!', 'error');
                    this.checked = !status;
                });
            });
        });
        
        // Form Validation
        const addForm = document.getElementById('addDepartmentForm');
        if (addForm) {
            addForm.addEventListener('submit', function (event) {
                if (!addForm.checkValidity()) {
                    event.preventDefault();
                    event.stopPropagation();
                }
                addForm.classList.add('was-validated');
            }, false);
        }
    });
</script>
@endpush
