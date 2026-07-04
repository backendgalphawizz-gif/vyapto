@extends('layouts.admin')
@section('title', 'Website Blogs')

@section('content')
<div class="main-section">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="fw-bold mb-0">Website Blogs</h4>
        <a href="{{ route('admin.website.blogs.create') }}" class="btn btn-primary btn-sm"><i class="bi bi-plus"></i> Add Blog</a>
    </div>
    @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
    <div class="card shadow-sm border-0 rounded-3">
        <div class="card-body table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light"><tr><th>Title</th><th>Author</th><th>Published</th><th>Status</th><th>Actions</th></tr></thead>
                <tbody>
                    @forelse($blogs as $blog)
                        <tr>
                            <td><strong>{{ $blog->title }}</strong></td>
                            <td>{{ $blog->author ?? '—' }}</td>
                            <td>{{ $blog->published_at?->format('M d, Y') ?? '—' }}</td>
                            <td>@if($blog->status)<span class="badge bg-success">Active</span>@else<span class="badge bg-secondary">Inactive</span>@endif</td>
                            <td>
                                <a href="{{ route('admin.website.blogs.edit', $blog) }}" class="btn btn-sm btn-secondary"><i class="bi bi-pencil"></i></a>
                                <form action="{{ route('admin.website.blogs.destroy', $blog) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete?')">@csrf @method('DELETE')<button class="btn btn-sm btn-danger"><i class="bi bi-trash"></i></button></form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center text-muted">No blogs yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
            {{ $blogs->links() }}
        </div>
    </div>
</div>
@endsection
