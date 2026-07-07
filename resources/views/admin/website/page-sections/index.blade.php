@extends('layouts.admin')
@section('title', 'Website Page Sections')

@section('content')
<div class="main-section">
    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
        <h4 class="fw-bold mb-0">Website Page Sections</h4>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.website.page-sections.create') }}" class="btn btn-primary btn-sm">
                <i class="bi bi-plus-lg"></i> Add Section
            </a>
            <a href="{{ route('website.home') }}" target="_blank" class="btn btn-outline-primary btn-sm">View Website</a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="alert alert-info py-2 small">
        Manage all website images and text blocks here. Filter by <strong>Home</strong> for hero, stats, features, mobile app, testimonials, and CTA. Use <strong>Global</strong> for header/footer logos.
    </div>

    <div class="card shadow-sm border-0 rounded-3 mb-3">
        <div class="card-body">
            <form method="GET" class="row g-2 align-items-end">
                <div class="col-md-4">
                    <label class="form-label small text-muted mb-1">Filter by page</label>
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
                        <th>Label</th>
                        <th>Preview</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($sections as $section)
                        <tr>
                            <td><span class="badge bg-secondary">{{ $section->page }}</span></td>
                            <td><code>{{ $section->section_key }}</code></td>
                            <td>
                                <div class="fw-semibold">{{ $section->label() }}</div>
                                @if($section->title)
                                    <small class="text-muted">{{ Str::limit($section->title, 40) }}</small>
                                @endif
                            </td>
                            <td>
                                @if($section->imageUrl())
                                    <img src="{{ $section->imageUrl() }}" alt="" width="56" height="56" class="rounded border" style="object-fit:cover;">
                                    @if(!$section->image && !empty($section->extra['default_image']))
                                        <div class="small text-muted mt-1">Default</div>
                                    @endif
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
                        <tr><td colspan="6" class="text-center text-muted">No sections found. Run <code>php artisan db:seed --class=WebsiteSeeder</code></td></tr>
                    @endforelse
                </tbody>
            </table>
            {{ $sections->links() }}
        </div>
    </div>
</div>
@endsection
