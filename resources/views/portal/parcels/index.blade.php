@extends('layouts.portal')

@section('title', 'Shipments')

@section('page_subtitle')
View and update your assigned delivery tasks.
@endsection

@section('content')
@if(empty($canViewShipments))
    <div class="app-card ride-blocked-card text-center">
        <div class="ride-blocked-icon"><i class="bi bi-truck"></i></div>
        <h5>Shipments Locked</h5>
        <p class="text-muted mb-3">{{ $rideBlockedMessage ?? 'Start your ride to view shipments.' }}</p>
        <a href="{{ route('portal.dashboard') }}" class="app-btn app-btn-green app-btn-sm">Go to Dashboard</a>
    </div>
@elseif($parcels->isEmpty())
    <div class="app-card text-center text-muted">No pending shipments.</div>
@else
    <div class="app-card d-none d-lg-block">
        <div class="portal-table-wrap">
            <table class="portal-table">
                <thead>
                    <tr>
                        <th>Parcel ID</th>
                        <th>Date</th>
                        <th>Vendor</th>
                        <th>Vehicle</th>
                        <th>Hub</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($parcels as $parcel)
                        @php $parcelCode = $parcel->parcel_id ?? ('SID-'.str_pad($parcel->id, 7, '0', STR_PAD_LEFT)); @endphp
                        <tr>
                            <td><strong>{{ $parcelCode }}</strong></td>
                            <td>{{ optional($parcel->assignmentParcel?->assignment_date)->format('d M Y') ?? '-' }}</td>
                            <td>{{ $parcel->assignmentParcel?->vendor?->name ?? '-' }}</td>
                            <td>{{ $parcel->assignmentParcel?->vehicle?->vehicle_number ?? '-' }}</td>
                            <td>{{ $parcel->assignmentParcel?->hub?->name ?? '-' }}</td>
                            <td><span class="app-badge">{{ $statuses[$parcel->status] ?? ucwords(str_replace('_', ' ', $parcel->status)) }}</span></td>
                            <td>
                                <form method="POST" action="{{ route('portal.parcels.update-status') }}" class="d-flex gap-2">
                                    @csrf
                                    <input type="hidden" name="parcel_id" value="{{ $parcelCode }}">
                                    <select name="status" class="form-select form-select-dark form-select-sm">
                                        @foreach($statuses as $value => $label)
                                            <option value="{{ $value }}" @selected($parcel->status === $value)>{{ $label }}</option>
                                        @endforeach
                                    </select>
                                    <button class="app-btn app-btn-green app-btn-sm">Update</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="d-lg-none content-grid-2">
        @foreach($parcels as $parcel)
            @php $parcelCode = $parcel->parcel_id ?? ('SID-'.str_pad($parcel->id, 7, '0', STR_PAD_LEFT)); @endphp
            <div class="app-card">
                <div class="app-card-header">
                    <div>
                        <strong>{{ $parcelCode }}</strong>
                        <small class="d-block text-muted">{{ optional($parcel->assignmentParcel?->assignment_date)->format('d M Y') ?? 'No date' }}</small>
                    </div>
                    <span class="app-badge">{{ $statuses[$parcel->status] ?? ucwords(str_replace('_', ' ', $parcel->status)) }}</span>
                </div>
                <div class="small text-muted mb-3">
                    <div>Vendor: {{ $parcel->assignmentParcel?->vendor?->name ?? '-' }}</div>
                    <div>Vehicle: {{ $parcel->assignmentParcel?->vehicle?->vehicle_number ?? '-' }}</div>
                    <div>Hub: {{ $parcel->assignmentParcel?->hub?->name ?? '-' }}</div>
                </div>
                <form method="POST" action="{{ route('portal.parcels.update-status') }}">
                    @csrf
                    <input type="hidden" name="parcel_id" value="{{ $parcelCode }}">
                    <div class="d-flex gap-2">
                        <select name="status" class="form-select form-select-dark">
                            @foreach($statuses as $value => $label)
                                <option value="{{ $value }}" @selected($parcel->status === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                        <button class="app-btn app-btn-green app-btn-sm">Update</button>
                    </div>
                </form>
            </div>
        @endforeach
    </div>
@endif
@endsection
