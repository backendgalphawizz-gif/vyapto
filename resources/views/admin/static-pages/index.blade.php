@extends('layouts.admin')

@section('title', 'Static Pages')

@section('content')
<div class="main-section">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="fw-bold mb-0">Static Page List</h4>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <ul class="mb-0 ps-3">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <form method="GET" action="{{ route('static-pages.index') }}" class="row g-2 mb-3">
        <div class="col-md-4">
            <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="Search by key, title...">
        </div>
        <div class="col-auto">
            <button type="submit" class="btn btn-primary">Search</button>
            <a href="{{ route('static-pages.index') }}" class="btn btn-outline-secondary">Reset</a>
        </div>
    </form>

    <div class="card shadow-sm border-0 rounded-3">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover table-bordered align-middle mb-0">
                    <thead class="table-light text-center">
                        <tr>
                            <th style="width: 80px;">No</th>
                            <th>Title</th>
                            <th style="width: 120px;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pages as $index => $page)
                            <tr>
                                <td class="text-center">{{ $index + 1 }}</td>
                                <td>{{ $page->title }}</td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-secondary btn-sm rounded-2"
                                        data-bs-toggle="modal"
                                        data-bs-target="#editPageModal{{ $page->id }}"
                                        title="Edit">
                                        <i class="bi bi-pencil-square"></i>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="text-center py-4 text-muted">No static pages found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @foreach($pages as $page)
        <div class="modal fade" id="editPageModal{{ $page->id }}" tabindex="-1" aria-labelledby="editPageLabel{{ $page->id }}" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
                <div class="modal-content border-0 rounded-4 shadow">
                    <form action="{{ route('static-pages.update', $page->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="modal-header">
                            <h5 class="modal-title fw-bold" id="editPageLabel{{ $page->id }}">Edit: {{ $page->title }}</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Key</label>
                                    <input type="text" class="form-control" value="{{ $page->key }}" readonly>
                                    <small class="text-muted">Page key is fixed for app and portal links.</small>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Title <span class="text-danger">*</span></label>
                                    <input type="text" name="title" class="form-control" value="{{ old('title', $page->title) }}" required>
                                </div>
                                <div class="col-12">
                                    <label class="form-label fw-semibold">Content <span class="text-danger">*</span></label>
                                    <textarea name="content" id="editor-edit-{{ $page->id }}" class="form-control" rows="8">{!! old('content', $page->content) !!}</textarea>
                                </div>
                                <div class="col-12">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" name="status" value="1" id="status{{ $page->id }}" @checked(old('status', $page->status))>
                                        <label class="form-check-label" for="status{{ $page->id }}">Published on app & portal</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary">Update Page</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endforeach
</div>
@endsection

@push('styles')
<style>
    .ck-editor__editable_inline { min-height: 220px; }
</style>
@endpush

@push('scripts')
<script src="https://cdn.ckeditor.com/ckeditor5/39.0.1/classic/ckeditor.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    if (typeof ClassicEditor === 'undefined') {
        return;
    }

    @foreach($pages as $page)
        const editor{{ $page->id }} = document.querySelector('#editor-edit-{{ $page->id }}');
        if (editor{{ $page->id }}) {
            ClassicEditor.create(editor{{ $page->id }}).catch(function (error) {
                console.error(error);
            });
        }
    @endforeach
});
</script>
@endpush
