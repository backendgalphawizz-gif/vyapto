@extends('layouts.admin')

@section('title','All Permissions')

@section('content')
<div class="main-section">

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

      <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
        <div>
            <h4 class="fw-bold m-0"><i class="bi bi-shield-lock me-2"></i>All Permissions</h4>
            <p class="text-muted small m-0">Manage system permissions and access controls</p>
        </div>
        
        <div class="d-flex gap-2 align-items-center flex-wrap">
             <span class="badge bg-white text-primary border border-primary shadow-sm p-2 d-flex align-items-center gap-2">
                <i class="bi bi-list-check"></i> Total: <strong>{{ $totalPermissions }}</strong>
            </span>
             <!-- Back Button -->
            <a href="{{ route('roles.index') }}" class="btn btn-outline-secondary btn-sm d-flex align-items-center gap-1">
                <i class="bi bi-arrow-left"></i> Back
            </a>

            <!-- Create Permission Button -->
            <a href="{{ route('permissions.create') }}" class="btn btn-primary btn-sm d-flex align-items-center gap-1">
                <i class="bi bi-plus-circle"></i> Create Permission
            </a>
        </div>
    </div>


    <!-- Filter Form -->
    <form method="POST" action="{{ route('permissions.filter') }}" class="row g-2 mb-3">
        @csrf
        <div class="col-md-4">
            <input type="text" name="search" value="{{ session('permission_filters.search') }}" placeholder="Search by name, module or route..." class="form-control">
        </div>
            
        <div class="col-auto">
            <button type="submit" class="btn btn-primary" title="Apply Filters">
                Search
            </button>
            <a href="{{ route('permissions.index') }}" class="btn btn-outline-secondary" title="Reset Filters">
                Reset
            </a>
        </div>
    </form>


    <div class="card shadow-sm rounded border mb-4">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover table-bordered align-middle mb-0">
                    <thead class="table-light text-center">
                        <tr>
                            <th class="py-3 ps-3 border-bottom-0" style="width: 5%;">#</th>
                            <th class="py-3 border-bottom-0">
                                <a href="{{ request()->fullUrlWithQuery([
                                    'sort_by' => 'module', 
                                    'sort_order' => request('sort_by') == 'module' && request('sort_order') == 'asc' ? 'desc' : 'asc',
                                    'filter' => request('filter')
                                ]) }}" class="text-decoration-none text-secondary d-flex align-items-center justify-content-center gap-1">
                                Module 
                                @if(request('sort_by') == 'module')
                                    <i class="bi bi-sort-{{ request('sort_order') == 'asc' ? 'down' : 'up' }}"></i>
                                @else
                                    <i class="bi bi-arrow-down-up text-muted opacity-25"></i>
                                @endif
                            </a>
                        </th>
                        <th class="py-3 border-bottom-0">
                                <a href="{{ request()->fullUrlWithQuery([
                                    'sort_by' => 'name', 
                                    'sort_order' => request('sort_by') == 'name' && request('sort_order') == 'asc' ? 'desc' : 'asc',
                                    'filter' => request('filter')
                                ]) }}" class="text-decoration-none text-secondary d-flex align-items-center justify-content-center gap-1">
                                Permission Name
                                @if(request('sort_by') == 'name')
                                    <i class="bi bi-sort-{{ request('sort_order') == 'asc' ? 'down' : 'up' }}"></i>
                                @else
                                    <i class="bi bi-arrow-down-up text-muted opacity-25"></i>
                                @endif
                            </a>
                        </th>
                        <th class="py-3 border-bottom-0">
                            <a href="{{ request()->fullUrlWithQuery([
                                'sort_by' => 'route', 
                                'sort_order' => request('sort_by') == 'route' && request('sort_order') == 'asc' ? 'desc' : 'asc',
                                'filter' => request('filter')
                            ]) }}" class="text-decoration-none text-secondary d-flex align-items-center justify-content-center gap-1">
                                Route / URL
                                @if(request('sort_by') == 'route')
                                    <i class="bi bi-sort-{{ request('sort_order') == 'asc' ? 'down' : 'up' }}"></i>
                                @else
                                    <i class="bi bi-arrow-down-up text-muted opacity-25"></i>
                                @endif
                            </a>
                        </th>
                            <th class="py-3 border-bottom-0 text-center" style="width: 10%;">Actions</th>
                    </tr>
                </thead>
                <tbody class="border-top-0">
                    @forelse($permissions as $index => $perm)
                        @php
                            $firstItem = $permissions->firstItem();
                            $serial = $firstItem ? $firstItem + $index : $index + 1;
                        @endphp
                    <tr>
                        <td class="ps-3 text-muted small">{{ $serial }}</td>
                        <td><span class="badge bg-secondary bg-opacity-10 text-secondary border">{{ $perm->module }}</span></td>
                        <td class="fw-bold text-dark">{{ $perm->name }}</td>
                        <td class="small font-monospace text-muted">{{ $perm->route }}</td>
                        <td class="text-center">
                            <div class="d-flex justify-content-center gap-2">
                                <a href="{{ route('permissions.edit', $perm->id) }}" class="btn btn-sm btn-warning text-white" title="Edit">
                                    <i class="bi bi-pencil-square"></i>
                                </a>
                                <form action="{{ route('permissions.destroy', $perm->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this permission?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-danger" title="Delete">
                                        <i class="bi bi-trash3-fill"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                     <tr>
                        <td colspan="5" class="text-center py-5">
                            <div class="text-muted opacity-50 mb-3">
                                <i class="bi bi-shield-x" style="font-size: 3rem;"></i>
                            </div>
                            <h6 class="text-muted">No permissions found matching your criteria.</h6>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

           <!-- Pagination -->
        <div class="d-flex justify-content-between align-items-center p-3 bg-light border-top">
            <div class="text-muted small">
                Showing {{ $permissions->firstItem() ?? 0 }}–{{ $permissions->lastItem() ?? 0 }} of {{ $permissions->total() }} entries
            </div>
            @if($permissions->hasPages())
                <div>
                    {{-- Append 'filter' parameter to pagination if it exists in current request, to maintain filters across pages --}}
                    {{ $permissions->appends(array_merge(request()->query(), request()->has('filter') ? ['filter' => 1] : []))->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection