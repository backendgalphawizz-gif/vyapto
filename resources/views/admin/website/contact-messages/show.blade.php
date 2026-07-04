@extends('layouts.admin')
@section('title', 'Contact Message')

@section('content')
<div class="main-section">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="fw-bold mb-0">Message from {{ $message->name }}</h4>
        <a href="{{ route('admin.website.contact-messages.index') }}" class="btn btn-outline-secondary btn-sm">Back</a>
    </div>
    @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
    <div class="card shadow-sm border-0 rounded-3">
        <div class="card-body">
            <dl class="row">
                <dt class="col-sm-2">Name</dt><dd class="col-sm-10">{{ $message->name }}</dd>
                <dt class="col-sm-2">Email</dt><dd class="col-sm-10"><a href="mailto:{{ $message->email }}">{{ $message->email }}</a></dd>
                @if($message->phone)<dt class="col-sm-2">Phone</dt><dd class="col-sm-10">{{ $message->phone }}</dd>@endif
                @if($message->subject)<dt class="col-sm-2">Subject</dt><dd class="col-sm-10">{{ $message->subject }}</dd>@endif
                <dt class="col-sm-2">Message</dt><dd class="col-sm-10">{{ $message->message }}</dd>
                <dt class="col-sm-2">Received</dt><dd class="col-sm-10">{{ $message->created_at->format('M d, Y H:i') }}</dd>
            </dl>
            <form action="{{ route('admin.website.contact-messages.update-status', $message) }}" method="POST" class="mt-3">
                @csrf @method('PATCH')
                <div class="row g-2 align-items-end">
                    <div class="col-md-3">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select">
                            @foreach(\App\Models\WebsiteContactMessage::STATUSES as $key => $label)
                                <option value="{{ $key }}" {{ $message->status === $key ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2"><button type="submit" class="btn btn-primary">Update</button></div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
