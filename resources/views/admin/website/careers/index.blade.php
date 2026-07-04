@extends('layouts.admin')
@section('title', 'Careers & Highlights')

@section('content')
<div class="main-section">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="fw-bold mb-0">Careers & Highlights</h4>
        <a href="{{ route('admin.website.careers.create') }}" class="btn btn-primary btn-sm"><i class="bi bi-plus"></i> Add Item</a>
    </div>
    @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
    <div class="card shadow-sm border-0 rounded-3 mb-3">
        <div class="card-body">
            <form method="GET" class="row g-2">
                <div class="col-md-4">
                    <select name="category" class="form-select" onchange="this.form.submit()">
                        <option value="">All Categories</option>
                        @foreach($categories as $key => $label)
                            <option value="{{ $key }}" {{ request('category') === $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
            </form>
        </div>
    </div>
    <div class="card shadow-sm border-0 rounded-3">
        <div class="card-body table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light"><tr><th>Title</th><th>Category</th><th>Status</th><th>Actions</th></tr></thead>
                <tbody>
                    @forelse($items as $item)
                        <tr>
                            <td><strong>{{ $item->title }}</strong></td>
                            <td><span class="badge bg-info">{{ $categories[$item->category] ?? $item->category }}</span></td>
                            <td>@if($item->status)<span class="badge bg-success">Active</span>@else<span class="badge bg-secondary">Inactive</span>@endif</td>
                            <td>
                                <a href="{{ route('admin.website.careers.edit', $item) }}" class="btn btn-sm btn-secondary"><i class="bi bi-pencil"></i></a>
                                <form action="{{ route('admin.website.careers.destroy', $item) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete?')">@csrf @method('DELETE')<button class="btn btn-sm btn-danger"><i class="bi bi-trash"></i></button></form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="text-center text-muted">No items yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
            {{ $items->links() }}
        </div>
    </div>
</div>
@endsection
