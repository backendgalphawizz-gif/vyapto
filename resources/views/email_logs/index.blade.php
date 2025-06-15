@extends('layouts.admin')

@section('title', 'Email Audit Log')

@section('content')
<div class="container">
  <h5>Email Audit Log</h5>

  <table class="table table-striped table-compact table-bordered table-hover table-sm">
    <thead>
      <tr>
        <th style="width: 1%;">No.</th>
        <th>User</th>
        <th>Email</th>
        <th>Subject</th>
        <th>Sent At</th>
      </tr>
    </thead>
    <tbody>
      @forelse($logs as $log)
        <tr>
          <td>{{ $loop->iteration + ($logs->currentPage() - 1) * $logs->perPage() }}</td>
          <td>{{ $log->user->name }}</td>
          <td>{{ $log->email }}</td>
          <td>{{ $log->subject }}</td>
          <td>{{ $log->sent_at->format('M d, Y h:i A') }}</td>
        </tr>
      @empty
        <tr>
          <td colspan="5" class="text-center">No email logs found.</td>
        </tr>
      @endforelse
    </tbody>
  </table>

  <!-- Record Count Summary and Pagination -->
  <div class="d-flex justify-content-between align-items-center">
      <div class="small text-muted mb-0">
          Showing {{ $logs->firstItem() }} to {{ $logs->lastItem() }} of {{ $logs->total() }} entries
      </div>
      <div class="mb-0">
          <div class="pagination-wrapper small mb-0">
              {{ $logs->links() }}
          </div>
      </div>
  </div>
</div>
@endsection