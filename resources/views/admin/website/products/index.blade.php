@extends('layouts.admin')
@section('title', 'Website Products')

@section('content')
<div class="main-section">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="fw-bold mb-0">Website Products</h4>
        <a href="{{ route('admin.website.products.create') }}" class="btn btn-primary btn-sm"><i class="bi bi-plus"></i> Add Product</a>
    </div>
    @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
    <div class="card shadow-sm border-0 rounded-3">
        <div class="card-body table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light"><tr><th>Title</th><th>Status</th><th>Sort</th><th>Actions</th></tr></thead>
                <tbody>
                    @forelse($products as $product)
                        <tr>
                            <td><strong>{{ $product->title }}</strong></td>
                            <td>@if($product->status)<span class="badge bg-success">Active</span>@else<span class="badge bg-secondary">Inactive</span>@endif</td>
                            <td>{{ $product->sort_order }}</td>
                            <td>
                                <a href="{{ route('admin.website.products.edit', $product) }}" class="btn btn-sm btn-secondary"><i class="bi bi-pencil"></i></a>
                                <form action="{{ route('admin.website.products.destroy', $product) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete?')">@csrf @method('DELETE')<button class="btn btn-sm btn-danger"><i class="bi bi-trash"></i></button></form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="text-center text-muted">No products yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
            {{ $products->links() }}
        </div>
    </div>
</div>
@endsection
