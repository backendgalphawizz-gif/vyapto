@extends('layouts.portal')

@section('title', 'My Profile')

@section('page_subtitle')
View and manage your personal information.
@endsection

@section('header_actions')
    <!-- <a href="{{ route('portal.profile.edit') }}" class="app-btn app-btn-green app-btn-sm">Edit Profile</a> -->
@endsection

@section('content')
@php
    $isStaffView = isset($user->role) && $user->role->name === 'Staff Employee';
@endphp

<div class="profile-layout">
    <div class="app-card text-center">
        <img src="{{ $user->profileImageUrl() }}" alt="Profile" class="profile-avatar-lg mb-3">
        <h5>{{ $user->name }}</h5>
        <p class="text-muted mb-0">{{ $user->email }}</p>
    </div>

    <div class="app-card">
        <h5 class="mb-3">Personal Details</h5>
        <div class="profile-detail-grid">
            <div><small>Full Name</small><div>{{ $user->name ?? '-' }}</div></div>
            <div><small>Email</small><div>{{ $user->email ?? '-' }}</div></div>
            <div><small>Phone</small><div>{{ $user->phone ?? '-' }}</div></div>
            <div><small>Date of Birth</small><div>{{ optional($user->date_of_birth)->format('d M, Y') ?? '-' }}</div></div>
            <div><small>Gender</small><div>{{ $user->gender ? ucfirst($user->gender) : '-' }}</div></div>
            <div><small>Marital Status</small><div>{{ $user->marital_status ? ucfirst($user->marital_status) : '-' }}</div></div>
            <div><small>Employee Role</small><div>{{ $user->role->name ?? '-' }}</div></div>
            @if($isStaffView)
                <div><small>Job Type</small><div>{{ $user->job_type ?? '-' }}</div></div>
            @else
                <div><small>Department</small><div>{{ $user->department->name ?? '-' }}</div></div>
                <div><small>Father's Name</small><div>{{ $user->father_name ?? '-' }}</div></div>
            @endif
            <div><small>Place of Birth</small><div>{{ $user->place_of_birth ?? '-' }}</div></div>
            <div><small>Status</small>
                <div>
                    @if($user->status)
                        <span class="app-badge app-badge-success">Active</span>
                    @else
                        <span class="app-badge app-badge-danger">Inactive</span>
                    @endif
                </div>
            </div>
            <div class="full-width"><small>Address</small><div>{{ $user->address ?? '-' }}</div></div>
        </div>
    </div>

    @if(!$isStaffView)
        <div class="app-card">
            <h5 class="mb-3">KYC Details</h5>
            <div class="profile-detail-grid">
                <div><small>Aadhar Number</small><div>{{ $user->aadhar_card_no ?? '-' }}</div></div>
                <div><small>PAN Number</small><div>{{ $user->pan_card_no ?? '-' }}</div></div>
                <div><small>Driving License Number</small><div>{{ $user->driving_license_no ?? '-' }}</div></div>
                <div><small>Aadhar Image</small>
                    <div>
                        @if($user->aadhar_card_image)
                            <a href="{{ asset($user->aadhar_card_image) }}" target="_blank" class="app-link">View</a>
                        @else
                            -
                        @endif
                    </div>
                </div>
                <div><small>PAN Image</small>
                    <div>
                        @if($user->pan_card_image)
                            <a href="{{ asset($user->pan_card_image) }}" target="_blank" class="app-link">View</a>
                        @else
                            -
                        @endif
                    </div>
                </div>
                <div><small>Driving License File</small>
                    <div>
                        @if($user->driving_license_image)
                            <a href="{{ asset($user->driving_license_image) }}" target="_blank" class="app-link">View</a>
                        @else
                            -
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="app-card">
            <h5 class="mb-3">Bank Details</h5>
            <div class="profile-detail-grid">
                <div><small>Bank Account Number</small><div>{{ $user->bank_account_no ?? '-' }}</div></div>
                <div><small>IFSC Code</small><div>{{ $user->ifsc_code ?? '-' }}</div></div>
                <div><small>Bank Name</small><div>{{ $user->bank_name ?? '-' }}</div></div>
                <div><small>Branch</small><div>{{ $user->bank_branch ?? '-' }}</div></div>
                <div><small>Bank Proof</small>
                    <div>
                        @if($user->bank_proof_image)
                            <a href="{{ asset($user->bank_proof_image) }}" target="_blank" class="app-link">View</a>
                        @else
                            -
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endif

    <div class="app-card">
        <h5 class="mb-3">Account</h5>
        <div class="profile-detail-grid">
            <div><small>Joined On</small><div>{{ $user->created_at->format('d M, Y h:i A') }}</div></div>
        </div>
    </div>
</div>
@endsection
