@extends('layouts.admin')

@section('title','Edit Permission')

@section('content')
<div class="main-section">
    <h4>Edit Permission</h4>

    <form action="{{ route('permissions.update', $permission->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="mb-2">
            <label>Module Name</label>
            <input type="text" name="module" class="form-control" value="{{ $permission->module }}" required>
        </div>
        <div class="mb-2">
            <label>Permission Name</label>
            <input type="text" name="name" class="form-control" value="{{ $permission->name }}" required>
        </div>
        <div class="mb-2">
            <label>Route / URL</label>
            <input type="text" name="route" class="form-control" value="{{ $permission->route }}" required>
        </div>
        <button type="submit" class="btn btn-primary mt-2">Update Permission</button>
        <a href="{{ route('permissions.index') }}" class="btn btn-secondary mt-2">Back</a>
    </form>
</div>
@endsection