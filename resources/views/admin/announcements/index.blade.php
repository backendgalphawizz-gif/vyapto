@extends('layouts.admin')

@section('title', 'Announcements')

@section('content')
<div class="container">
    <h4 class="mb-4">Create Announcement</h4>

    <form method="POST" action="{{ route('admin.announcements.store') }}">
        @csrf
        <div class="mb-3">
            <label class="form-label">Title</label>
            <input type="text" name="title" class="form-control border border-1 border-primary rounded" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Content</label>
            <textarea name="content" class="form-control border border-1 border-primary rounded" rows="4" required></textarea>
        </div>
        <input type="hidden" name="sent_by" value="{{ Auth::user()->name }}">
        <button type="submit" class="btn btn-primary">Submit</button>
    </form>

    <hr class="my-4 border border-1 border-secondary">

    <h5 class="mb-3">Previous Announcements</h5>
    <table class="table table-bordered table-sm">
        <thead class="bg-light">
            <tr>
                <th>#</th>
                <th>Title</th>
                <th>Sent By</th>
                <th>Sent At</th>
            </tr>
        </thead>
        <tbody>
            @foreach($announcements as $i => $announcement)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>{{ $announcement->title }}</td>
                    <td>{{ $announcement->sent_by }}</td>
                    <td>{{ $announcement->created_at->format('M d, Y h:i A') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection

{{-- SweetAlert Toast --}}
@if (session('success'))
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'success',
                title: @json(session('success')),
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                customClass: {
                    popup: 'rounded'
                }
            });
        });
    </script>
@endif