@extends('layouts.admin')
@section('title', 'Contact Messages')

@section('content')
<div class="main-section">
    <h4 class="fw-bold mb-3">Contact Messages</h4>
    @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
    <div class="card shadow-sm border-0 rounded-3">
        <div class="card-body table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light"><tr><th>Name</th><th>Email</th><th>Subject</th><th>Status</th><th>Date</th><th>Actions</th></tr></thead>
                <tbody>
                    @forelse($messages as $msg)
                        <tr>
                            <td>{{ $msg->name }}</td>
                            <td>{{ $msg->email }}</td>
                            <td>{{ Str::limit($msg->subject ?? $msg->message, 40) }}</td>
                            <td><span class="badge bg-{{ $msg->status === 'new' ? 'primary' : ($msg->status === 'replied' ? 'success' : 'secondary') }}">{{ ucfirst($msg->status) }}</span></td>
                            <td>{{ $msg->created_at->format('M d, Y') }}</td>
                            <td>
                                <a href="{{ route('admin.website.contact-messages.show', $msg) }}" class="btn btn-sm btn-info text-white"><i class="bi bi-eye"></i></a>
                                <form action="{{ route('admin.website.contact-messages.destroy', $msg) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete?')">@csrf @method('DELETE')<button class="btn btn-sm btn-danger"><i class="bi bi-trash"></i></button></form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center text-muted">No messages yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
            {{ $messages->links() }}
        </div>
    </div>
</div>
@endsection
