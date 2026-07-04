@extends('layouts.admin')
@section('title', 'Website Services')

@section('content')
<div class="main-section">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="fw-bold mb-0">Website Services</h4>
        <a href="{{ route('admin.website.services.create') }}" class="btn btn-primary btn-sm"><i class="bi bi-plus"></i> Add Service</a>
    </div>
    @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
    <div class="card shadow-sm border-0 rounded-3">
        <div class="card-body table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light"><tr><th>Title</th><th>Status</th><th>Sort</th><th>Actions</th></tr></thead>
                <tbody>
                    @forelse($services as $service)
                        <tr>
                            <td><strong>{{ $service->title }}</strong><br><small class="text-muted">{{ Str::limit($service->description, 60) }}</small></td>
                            <td>@if($service->status)<span class="badge bg-success">Active</span>@else<span class="badge bg-secondary">Inactive</span>@endif</td>
                            <td>{{ $service->sort_order }}</td>
                            <td>
                                <a href="{{ route('admin.website.services.edit', $service) }}" class="btn btn-sm btn-secondary"><i class="bi bi-pencil"></i></a>
                                <form action="{{ route('admin.website.services.destroy', $service) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete?')">@csrf @method('DELETE')<button class="btn btn-sm btn-danger"><i class="bi bi-trash"></i></button></form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="text-center text-muted">No services yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
            {{ $services->links() }}
        </div>
    </div>
</div>
@endsection
