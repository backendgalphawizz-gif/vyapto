@extends('layouts.portal')

@section('title', 'Edit Profile')

@section('page_subtitle')
Update your personal, KYC, and bank details.
@endsection

@section('content')
@php
    $isStaffView = isset($user->role) && $user->role->name === 'Staff Employee';
@endphp

<div class="app-card profile-edit-card">
    @if($errors->any())
        <div class="portal-alert portal-alert-error mb-3">
            <strong>Please fix the following:</strong>
            <ul class="mb-0 mt-2 ps-3">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('portal.profile.update') }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <h5 class="profile-form-section-title">Personal Details</h5>
        <div class="content-grid-2">
            <div class="app-input-wrap">
                <label>Full Name <span class="text-danger">*</span></label>
                <input type="text" name="fullname" class="app-input @error('fullname') is-invalid @enderror"
                    value="{{ old('fullname', $user->name) }}" pattern="[A-Za-z\s]+"
                    oninput="this.value = this.value.replace(/[^A-Za-z\s]/g, '');" maxlength="255" required>
            </div>
            <div class="app-input-wrap">
                <label>Email <span class="text-danger">*</span></label>
                <input type="email" name="email" class="app-input @error('email') is-invalid @enderror"
                    value="{{ old('email', $user->email) }}" required>
            </div>
            <div class="app-input-wrap">
                <label>Mobile <span class="text-danger">*</span></label>
                <input type="text" name="phone" class="app-input @error('phone') is-invalid @enderror"
                    value="{{ old('phone', $user->phone) }}" pattern="[0-9]{10}" maxlength="10"
                    oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 10);" required>
            </div>
            <div class="app-input-wrap">
                <label>Employee Role</label>
                <input type="text" class="app-input" value="{{ $user->role->name ?? '-' }}" readonly disabled>
            </div>

            @if($isStaffView)
                <div class="app-input-wrap">
                    <label>Job Type <span class="text-danger">*</span></label>
                    <select name="job_type" class="form-select form-select-dark @error('job_type') is-invalid @enderror" required>
                        <option value="" disabled @selected(!old('job_type', $user->job_type))>Select Job Type</option>
                        <option value="Full Time" @selected(old('job_type', $user->job_type) === 'Full Time')>Full Time</option>
                        <option value="Half Time" @selected(old('job_type', $user->job_type) === 'Half Time')>Half Time</option>
                    </select>
                </div>
            @else
                <div class="app-input-wrap">
                    <label>Department <span class="text-danger">*</span></label>
                    <select name="department_id" class="form-select form-select-dark @error('department_id') is-invalid @enderror" required>
                        <option value="" disabled @selected(!old('department_id', $user->department_id))>Select department</option>
                        @foreach($departments as $department)
                            <option value="{{ $department->id }}" @selected(old('department_id', $user->department_id) == $department->id)>{{ $department->name }}</option>
                        @endforeach
                    </select>
                </div>
            @endif

            <div class="app-input-wrap">
                <label>Date of Birth <span class="text-danger">*</span></label>
                <input type="date" name="date_of_birth" max="{{ now()->format('Y-m-d') }}"
                    class="app-input @error('date_of_birth') is-invalid @enderror"
                    value="{{ old('date_of_birth', optional($user->date_of_birth)->format('Y-m-d')) }}" required>
            </div>
            <div class="app-input-wrap">
                <label>Gender <span class="text-danger">*</span></label>
                <select name="gender" class="form-select form-select-dark @error('gender') is-invalid @enderror" required>
                    @php $editGender = strtolower((string) old('gender', $user->gender ?? '')); @endphp
                    <option value="" disabled @selected($editGender === '')>Select Gender</option>
                    @foreach(['male','female','other'] as $gender)
                        <option value="{{ $gender }}" @selected($editGender === $gender)>{{ ucfirst($gender) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="app-input-wrap">
                <label>Marital Status <span class="text-danger">*</span></label>
                <select name="marital_status" class="form-select form-select-dark @error('marital_status') is-invalid @enderror" required>
                    @php $editMarital = strtolower((string) old('marital_status', $user->marital_status ?? '')); @endphp
                    <option value="" disabled @selected($editMarital === '')>Select Marital Status</option>
                    @foreach(['single','married','divorced','widowed'] as $status)
                        <option value="{{ $status }}" @selected($editMarital === $status)>{{ ucfirst($status) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="app-input-wrap">
                <label>Place of Birth <span class="text-danger">*</span></label>
                <input type="text" name="place_of_birth" class="app-input @error('place_of_birth') is-invalid @enderror"
                    value="{{ old('place_of_birth', $user->place_of_birth) }}" pattern="[A-Za-z\s]+"
                    oninput="this.value = this.value.replace(/[^A-Za-z\s]/g, '');" maxlength="255" required>
            </div>

            @if(!$isStaffView)
                <div class="app-input-wrap">
                    <label>Father's Name</label>
                    <input type="text" name="father_name" class="app-input @error('father_name') is-invalid @enderror"
                        value="{{ old('father_name', $user->father_name) }}" pattern="[A-Za-z\s]+"
                        oninput="this.value = this.value.replace(/[^A-Za-z\s]/g, '');" maxlength="255">
                </div>
            @endif

            <div class="app-input-wrap">
                <label>Profile Image</label>
                <input type="file" name="profile_image" class="form-control form-control-dark @error('profile_image') is-invalid @enderror" accept="image/*">
                @if($user->profile_image)
                    <small class="text-muted d-block mt-1">Current: <a href="{{ $user->profileImageUrl() }}" target="_blank" class="app-link">View image</a></small>
                @endif
            </div>
        </div>

        <div class="app-input-wrap">
            <label>Address <span class="text-danger">*</span></label>
            <textarea name="address" class="app-input @error('address') is-invalid @enderror" rows="2" required>{{ old('address', $user->address) }}</textarea>
        </div>

        @if(!$isStaffView)
            <h5 class="profile-form-section-title">KYC Details</h5>
            <div class="content-grid-2">
                <div class="app-input-wrap">
                    <label>Aadhar Card Number</label>
                    <input type="text" name="aadhar_card_no" class="app-input @error('aadhar_card_no') is-invalid @enderror"
                        value="{{ old('aadhar_card_no', $user->aadhar_card_no) }}" pattern="[0-9]{12}" maxlength="12"
                        oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 12);">
                </div>
                <div class="app-input-wrap">
                    <label>Aadhar Card Image</label>
                    <input type="file" name="aadhar_card_image" class="form-control form-control-dark @error('aadhar_card_image') is-invalid @enderror" accept="image/jpeg,image/png,image/jpg,image/gif">
                    @if($user->aadhar_card_image)
                        <small class="text-muted d-block mt-1">Current: <a href="{{ asset($user->aadhar_card_image) }}" target="_blank" class="app-link">View file</a></small>
                    @endif
                </div>
                <div class="app-input-wrap">
                    <label>PAN Card Number</label>
                    <input type="text" name="pan_card_no" class="app-input @error('pan_card_no') is-invalid @enderror"
                        value="{{ old('pan_card_no', $user->pan_card_no) }}" pattern="[A-Z]{5}[0-9]{4}[A-Z]{1}" maxlength="10"
                        oninput="this.value = this.value.toUpperCase();">
                </div>
                <div class="app-input-wrap">
                    <label>PAN Card Image</label>
                    <input type="file" name="pan_card_image" class="form-control form-control-dark @error('pan_card_image') is-invalid @enderror" accept="image/jpeg,image/png,image/jpg,image/gif">
                    @if($user->pan_card_image)
                        <small class="text-muted d-block mt-1">Current: <a href="{{ asset($user->pan_card_image) }}" target="_blank" class="app-link">View file</a></small>
                    @endif
                </div>
                <div class="app-input-wrap">
                    <label>Driving License Number</label>
                    <input type="text" name="driving_license_no" class="app-input @error('driving_license_no') is-invalid @enderror"
                        value="{{ old('driving_license_no', $user->driving_license_no) }}" maxlength="50">
                </div>
                <div class="app-input-wrap">
                    <label>Driving License Image/PDF</label>
                    <input type="file" name="driving_license_image" class="form-control form-control-dark @error('driving_license_image') is-invalid @enderror" accept="image/jpeg,image/png,image/jpg,pdf">
                    @if($user->driving_license_image)
                        <small class="text-muted d-block mt-1">Current: <a href="{{ asset($user->driving_license_image) }}" target="_blank" class="app-link">View file</a></small>
                    @endif
                </div>
            </div>

            <h5 class="profile-form-section-title">Bank Details</h5>
            <div class="content-grid-2">
                <div class="app-input-wrap">
                    <label>Bank Account Number</label>
                    <input type="text" name="bank_account_no" class="app-input @error('bank_account_no') is-invalid @enderror"
                        value="{{ old('bank_account_no', $user->bank_account_no) }}" pattern="[0-9]{10,16}" minlength="10" maxlength="16"
                        oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 16);">
                </div>
                <div class="app-input-wrap">
                    <label>IFSC Code</label>
                    <input type="text" name="ifsc_code" class="app-input @error('ifsc_code') is-invalid @enderror"
                        value="{{ old('ifsc_code', $user->ifsc_code) }}" pattern="[A-Z]{4}0[A-Z0-9]{6}" maxlength="11"
                        oninput="this.value = this.value.toUpperCase();">
                </div>
                <div class="app-input-wrap">
                    <label>Bank Name</label>
                    <input type="text" name="bank_name" class="app-input @error('bank_name') is-invalid @enderror"
                        value="{{ old('bank_name', $user->bank_name) }}" maxlength="255">
                </div>
                <div class="app-input-wrap">
                    <label>Branch Name</label>
                    <input type="text" name="bank_branch" class="app-input @error('bank_branch') is-invalid @enderror"
                        value="{{ old('bank_branch', $user->bank_branch) }}" maxlength="255">
                </div>
                <div class="app-input-wrap">
                    <label>Bank Proof Image/PDF</label>
                    <input type="file" name="bank_proof_image" class="form-control form-control-dark @error('bank_proof_image') is-invalid @enderror" accept="image/jpeg,image/png,image/jpg,pdf">
                    @if($user->bank_proof_image)
                        <small class="text-muted d-block mt-1">Current: <a href="{{ asset($user->bank_proof_image) }}" target="_blank" class="app-link">View file</a></small>
                    @endif
                </div>
            </div>
        @endif

        <h5 class="profile-form-section-title">Account Security</h5>
        <div class="content-grid-2">
            <div class="app-field">
                <div class="app-input-wrap @error('password') is-invalid @enderror">
                    <label for="profile_password">New Password <small class="text-muted">(leave blank to keep current)</small></label>
                    <div class="password-input-row">
                        <input type="password" name="password" id="profile_password" class="app-input" autocomplete="new-password">
                        <button type="button" class="password-toggle-btn" aria-label="Show password" data-password-toggle>
                            <i class="bi bi-eye"></i>
                        </button>
                    </div>
                </div>
                @error('password')<div class="app-field-error">{{ $message }}</div>@enderror
            </div>
            <div class="app-field">
                <div class="app-input-wrap">
                    <label for="profile_password_confirmation">Confirm Password</label>
                    <div class="password-input-row">
                        <input type="password" name="password_confirmation" id="profile_password_confirmation" class="app-input" autocomplete="new-password">
                        <button type="button" class="password-toggle-btn" aria-label="Show password" data-password-toggle>
                            <i class="bi bi-eye"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="d-flex gap-2 mt-4">
            <button type="submit" class="app-btn app-btn-green app-btn-sm">Save Changes</button>
            <a href="{{ route('portal.profile.show') }}" class="app-btn app-btn-outline app-btn-sm">Cancel</a>
        </div>
    </form>
</div>
@endsection
