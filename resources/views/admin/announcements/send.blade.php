@extends('layouts.admin')

@section('title', 'Send Announcement')

@section('content')
<div class="container">
    <h4 class="mb-4">Send Announcement to Specific Learners</h4>

    <form action="{{ route('admin.announcements.processSend') }}" method="POST">
        @csrf

        <div class="mb-3">
            <label class="form-label">Select Announcement</label>
            <select name="announcement_id" class="form-select border border-1 border-primary rounded" required>
                <option value="">-- Select --</option>
                @foreach($announcements as $announcement)
                    <option value="{{ $announcement->id }}">{{ $announcement->title }}</option>
                @endforeach
            </select>
        </div>

        <div class="row mb-3">
            <div class="col">
                <label class="form-label">Grade Level</label>
                <select name="grade_level" class="form-select border border-1 border-primary rounded">
                    <option value="">All</option>
                    @foreach($gradeLevels as $level)
                        <option value="{{ $level }}">{{ $level }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col">
                <label class="form-label">Section</label>
                <select name="section" class="form-select border border-1 border-primary rounded">
                    <option value="">All</option>
                    @foreach($sections as $section)
                        <option value="{{ $section }}">{{ $section }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <button type="submit" class="btn btn-primary">Send Announcement</button>
    </form>
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
                    popup: 'rounded' // optional: makes it rounded
                }
            });
        });
    </script>
@endif

