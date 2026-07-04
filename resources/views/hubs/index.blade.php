@extends('layouts.admin')
@section('title', 'Manage Hubs')

@section('content')
<div class="main-section">
    @if(session('error'))
    <div id="hubErrorMessage" data-message="{{ session('error') }}"></div>
    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var errorHolder = document.getElementById('hubErrorMessage');
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: errorHolder ? errorHolder.dataset.message : '',
                confirmButtonColor: '#d33'
            });
        });
    </script>
    @endpush
    @endif

    @if(session('success'))
    <div id="hubSuccessMessage" data-message="{{ session('success') }}"></div>
    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var successHolder = document.getElementById('hubSuccessMessage');
            Swal.fire({
                icon: 'success',
                title: 'Success',
                text: successHolder ? successHolder.dataset.message : '',
                confirmButtonColor: '#3085d6',
                timer: 2500,
                timerProgressBar: true
            });
        });
    </script>
    @endpush
    @endif

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="fw-bold mb-0">Hub Management</h4>
        <div>
            <!-- <a href="{{ route('admin.hubs.map') }}" class="btn btn-outline-secondary btn-sm rounded-3 me-2">
                <i class="bi bi-geo-alt-fill me-1"></i> View Map
            </a> -->
            @include('partials.export-dropdown', [
                'exportRoute' => 'admin.hubs.export',
                'exportQuery' => [],
            ])
            <a href="{{ route('admin.hubs.create') }}" class="btn btn-primary  rounded-3">
                <i class="bi bi-plus-circle me-1"></i> Add Hub
            </a>
        </div>
    </div>

    <div class="card shadow-sm rounded border mb-4">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover table-bordered align-middle mb-0">
                    <thead class="table-light text-center">
                        <tr>
                            <th style="width: 6%;">ID</th>
                            <th>Name</th>
                            <th>Location</th>
                            <th>Coordinates</th>
                            <th>Timing</th>
                            <th>Status</th>
                            <th style="width: 14%;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($hubs as $hub)
                        <tr>
                            <td class="text-center">{{ $hubs->firstItem() + $loop->index }}</td>
                            <td>
                                <div class="fw-bold">{{ $hub->name }}</div>
                                <div class="small text-muted">Hub ID: {{ $hub->id }}</div>
                            </td>
                            <td>{{ $hub->location ?? 'N/A' }}</td>
                            <td>
                                @if($hub->latitude && $hub->longitude)
                                <span class="small">{{ $hub->latitude }}, {{ $hub->longitude }}</span>
                                @else
                                <span class="text-muted">N/A</span>
                                @endif
                            </td>
                            <td>
                                <div class="small"><strong>Open:</strong> {{ $hub->opening_time ? date('h:i A', strtotime($hub->opening_time)) : 'N/A' }}</div>
                                <div class="small"><strong>Close:</strong> {{ $hub->closing_time ? date('h:i A', strtotime($hub->closing_time)) : 'N/A' }}</div>
                            </td>
                            <td class="text-center">
                                @if($hub->is_open)
                                <span class="badge bg-success">Open</span>
                                @else
                                <span class="badge bg-danger">Closed</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <a href="{{ route('admin.hubs.show', $hub) }}" class="btn btn-sm btn-info text-white" title="View">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('admin.hubs.edit', $hub) }}" class="btn btn-sm btn-secondary" title="Edit">
                                    <i class="bi bi-pencil-square"></i>
                                </a>
                                <form action="{{ route('admin.hubs.destroy', $hub) }}" method="POST" class="d-inline delete-form">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" title="Delete">
                                        <i class="bi bi-trash3-fill"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">
                                <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                No Hub Data Found
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3 d-flex justify-content-between align-items-center">
                <div class="text-muted small">
                    Showing {{ $hubs->firstItem() ?? 0 }}-{{ $hubs->lastItem() ?? 0 }} of {{ $hubs->total() }} entries
                </div>
                <div>
                    {{ $hubs->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.delete-form').forEach(function(form) {
            form.addEventListener('submit', function(e) {
                e.preventDefault();

                Swal.fire({
                    title: 'Delete hub?',
                    text: 'This action cannot be undone.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#7066e0',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Yes, delete',
                    cancelButtonText: 'Cancel'
                }).then(function(result) {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });
    });
</script>
@endpush