@extends('layouts.portal')

@section('title', 'Vehicle Usage')

@section('header_actions')
    <a href="{{ route('portal.vehicle-usage.create') }}" class="app-btn app-btn-green app-btn-sm"><i class="bi bi-plus-lg"></i> Log Usage</a>
@endsection

@section('content')
@if($entries->isEmpty())
    <div class="app-card text-center text-muted">No vehicle usage records yet.</div>
@else
    <div class="app-card d-none d-md-block">
        <div class="portal-table-wrap">
            <table class="portal-table">
                <thead>
                    <tr>
                        <th>Vehicle</th>
                        <th>Date</th>
                        <th>KM</th>
                        <th>Photo</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($entries as $entry)
                        <tr>
                            <td><strong>{{ $entry->vehicle_number }}</strong></td>
                            <td>{{ $entry->created_at->format('d M Y h:i A') }}</td>
                            <td>{{ number_format((float) $entry->kms, 2) }} KM</td>
                            <td>
                                @if($entry->image)
                                    <a href="{{ asset('storage/'.$entry->image) }}" target="_blank" class="app-link">View</a>
                                @else
                                    -
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="mt-3">{{ $entries->links() }}</div>
    </div>

    <div class="content-grid-2 d-md-none">
        @foreach($entries as $entry)
            <div class="app-card">
                <div class="app-card-header">
                    <div>
                        <strong>{{ $entry->vehicle_number }}</strong>
                        <small class="d-block text-muted">{{ $entry->created_at->format('d M Y h:i A') }}</small>
                    </div>
                    <strong>{{ number_format((float) $entry->kms, 2) }} KM</strong>
                </div>
                @if($entry->image)
                    <a href="{{ asset('storage/'.$entry->image) }}" target="_blank" class="app-link">View photo</a>
                @endif
            </div>
        @endforeach
        <div class="mt-2">{{ $entries->links() }}</div>
    </div>
@endif
@endsection
