@extends('layouts.admin')

@section('title', 'Announcement Logs')

@section('content')
<div class="container">
    <h4 class="mb-4">Announcement Logs</h4>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if($logs->count())
        <div class="table-responsive">
            <table class="table table-bordered table-sm table-hover">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Announcement Title</th>
                        <th>Learner Name</th>
                        <th>Email</th>
                        <th>Status</th>
                        <th>Sent At</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($logs as $index => $log)
                        <tr>
                            <td>{{ $logs->firstItem() + $index }}</td>
                            <td>{{ $log->announcement->title }}</td>
                            <td>{{ $log->learner->lname }}, {{ $log->learner->fname }}</td>
                            <td>{{ $log->learner->email }}</td>
                            <td>
                                @if($log->is_sent)
                                    <span class="badge bg-success">Sent</span>
                                @else
                                    <span class="badge bg-secondary">Pending</span>
                                @endif
                            </td>
                            <td>{{ $log->sent_at ? \Carbon\Carbon::parse($log->sent_at)->format('M d, Y h:i A') : '-' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-3">
            {{ $logs->links() }}
        </div>
    @else
        <p class="text-muted">No announcement logs found.</p>
    @endif
</div>
@endsection
