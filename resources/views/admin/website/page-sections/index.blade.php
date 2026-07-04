@extends('layouts.admin')
@section('title', 'Website Page Sections')

@section('content')
<div class="main-section">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="fw-bold mb-0">Website Page Sections</h4>
        <a href="{{ route('website.home') }}" target="_blank" class="btn btn-outline-primary btn-sm">View Website</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card shadow-sm border-0 rounded-3 mb-3">
        <div class="card-body">
            <form method="GET" class="row g-2">
                <div class="col-md-4">
                    <select name="page" class="form-select" onchange="this.form.submit()">
                        <option value="">All Pages</option>
                        @foreach($pages as $p)
                            <option value="{{ $p }}" {{ request('page') === $p ? 'selected' : '' }}>{{ ucfirst($p) }}</option>
                        @endforeach
                    </select>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow-sm border-0 rounded-3">
        <div class="card-body table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Page</th>
                        <th>Section</th>
                        <th>Title</th>
                        <th>Image</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($sections as $section)
                        <tr>
                            <td><span class="badge bg-secondary">{{ $section->page }}</span></td>
                            <td><code>{{ $section->section_key }}</code></td>
                            <td>{{ $section->title ?? '—' }}</td>
                            <td>
                                @if($section->imageUrl())
                                    <img src="{{ $section->imageUrl() }}" alt="" width="56" height="56" class="rounded border" style="object-fit:cover;">
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td>
                                @if($section->status)
                                    <span class="badge bg-success">Active</span>
                                @else
                                    <span class="badge bg-secondary">Inactive</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('admin.website.page-sections.edit', $section) }}" class="btn btn-sm btn-secondary">
                                    <i class="bi bi-pencil"></i> Edit
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center text-muted">No sections found. Run WebsiteSeeder.</td></tr>
                    @endforelse
                </tbody>
            </table>
            {{ $sections->links() }}
        </div>
    </div>
</div>
@endsection
