<?php

namespace App\Http\Controllers\Portal;

use App\CPU\ImageManager;
use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Role;

class ProfileController extends Controller
{
    public function show()
    {
        $user = Auth::user()->load(['department', 'role']);
        $departments = Department::where('status', 1)->orderBy('name')->get();

        return view('portal.profile.show', compact('user', 'departments'));
    }

    public function edit()
    {
        $user = Auth::user()->load(['department', 'role']);
        $departments = Department::where('status', 1)->orderBy('name')->get();

        return view('portal.profile.edit', compact('user', 'departments'));
    }

    public function update(Request $request)
    {
        /** @var User $user */
        $user = Auth::user()->load('role');

        $request->merge([
            'pan_card_no' => $request->filled('pan_card_no') ? strtoupper($request->pan_card_no) : $request->pan_card_no,
            'ifsc_code' => $request->filled('ifsc_code') ? strtoupper($request->ifsc_code) : $request->ifsc_code,
        ]);

        $roleId = $user->role_id;
        $isStaff = $this->isStaffRole($roleId);
        $isDriver = $this->isDriverRole($roleId);

        $validator = Validator::make($request->all(), [
            'fullname' => ['required', 'string', 'max:255', 'regex:/^[A-Za-z\s]+$/'],
            'email' => 'required|email|unique:users,email,'.$user->id,
            'phone' => ['required', 'regex:/^[0-9]{10}$/', 'unique:users,phone,'.$user->id],
            'address' => 'required|string|max:500',
            'department_id' => ['nullable', 'exists:departments,id', function ($attribute, $value, $fail) use ($isStaff) {
                if (! $isStaff && empty($value)) {
                    $fail('The department field is required.');
                }
            }],
            'job_type' => ['nullable', 'string', 'in:Full Time,Half Time', function ($attribute, $value, $fail) use ($isStaff) {
                if ($isStaff && empty($value)) {
                    $fail('The job type field is required.');
                }
            }],
            'date_of_birth' => 'required|date|before:today',
            'gender' => 'required|in:male,female,other',
            'marital_status' => 'required|in:single,married,divorced,widowed',
            'father_name' => ['nullable', 'string', 'max:255', 'regex:/^[A-Za-z\s]+$/', function ($attribute, $value, $fail) use ($isDriver, $isStaff) {
                if ($isDriver && ! $isStaff && empty($value)) {
                    $fail("Father's name is required.");
                }
            }],
            'place_of_birth' => ['required', 'string', 'max:255', 'regex:/^[A-Za-z\s]+$/'],
            'password' => 'nullable|confirmed|min:6',
            'profile_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'aadhar_card_no' => ['nullable', 'digits:12', 'unique:users,aadhar_card_no,'.$user->id, function ($attribute, $value, $fail) use ($isDriver, $isStaff) {
                if ($isDriver && ! $isStaff && empty($value)) {
                    $fail('Aadhar card number is required.');
                }
            }],
            'aadhar_card_image' => ['nullable', 'image', 'max:2048', function ($attribute, $value, $fail) use ($request, $user, $isDriver, $isStaff) {
                if ($isDriver && ! $isStaff && ! $request->hasFile('aadhar_card_image') && empty($user->aadhar_card_image)) {
                    $fail('Aadhar card image is required.');
                }
            }],
            'pan_card_no' => ['nullable', 'regex:/^[A-Z]{5}[0-9]{4}[A-Z]{1}$/', 'max:20', 'unique:users,pan_card_no,'.$user->id, function ($attribute, $value, $fail) use ($isDriver, $isStaff) {
                if ($isDriver && ! $isStaff && empty($value)) {
                    $fail('PAN card number is required.');
                }
            }],
            'pan_card_image' => ['nullable', 'image', 'max:2048', function ($attribute, $value, $fail) use ($request, $user, $isDriver, $isStaff) {
                if ($isDriver && ! $isStaff && ! $request->hasFile('pan_card_image') && empty($user->pan_card_image)) {
                    $fail('PAN card image is required.');
                }
            }],
            'driving_license_no' => 'nullable|string|max:50',
            'driving_license_image' => 'nullable|mimes:jpeg,png,jpg,pdf|max:2048',
            'bank_account_no' => ['nullable', 'regex:/^[0-9]{10,16}$/', 'unique:users,bank_account_no,'.$user->id],
            'ifsc_code' => ['nullable', 'regex:/^[A-Z]{4}0[A-Z0-9]{6}$/', 'max:20'],
            'bank_name' => 'nullable|string|max:255',
            'bank_branch' => 'nullable|string|max:255',
            'bank_proof_image' => 'nullable|mimes:jpeg,png,jpg,pdf|max:2048',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $validated = $validator->validated();

        if ($request->hasFile('profile_image')) {
            $oldImage = $user->profile_image ? basename($user->profile_image) : null;
            $name = ImageManager::update('profile/', $oldImage, 'png', $request->file('profile_image'));
            $user->profile_image = 'storage/profile/'.$name;
        }

        $aadharImage = $user->aadhar_card_image;
        if ($request->hasFile('aadhar_card_image')) {
            $imageName = ImageManager::upload('documents/aadhar/', 'png', $request->file('aadhar_card_image'));
            $aadharImage = 'storage/documents/aadhar/'.$imageName;
        }

        $panImage = $user->pan_card_image;
        if ($request->hasFile('pan_card_image')) {
            $imageName = ImageManager::upload('documents/pan/', 'png', $request->file('pan_card_image'));
            $panImage = 'storage/documents/pan/'.$imageName;
        }

        $drivingLicenseImage = $user->driving_license_image;
        if ($request->hasFile('driving_license_image')) {
            $imageName = ImageManager::upload('documents/dl/', 'png', $request->file('driving_license_image'));
            $drivingLicenseImage = 'storage/documents/dl/'.$imageName;
        }

        $bankProofImage = $user->bank_proof_image;
        if ($request->hasFile('bank_proof_image')) {
            $imageName = ImageManager::upload('documents/bank/', 'png', $request->file('bank_proof_image'));
            $bankProofImage = 'storage/documents/bank/'.$imageName;
        }

        $user->name = $validated['fullname'];
        $user->email = $validated['email'];
        $user->phone = $validated['phone'];
        $user->address = $validated['address'];
        $user->department_id = $isStaff ? $user->department_id : ($validated['department_id'] ?? null);
        $user->job_type = $isStaff ? ($validated['job_type'] ?? null) : $user->job_type;
        $user->date_of_birth = $validated['date_of_birth'];
        $user->gender = $validated['gender'];
        $user->marital_status = $validated['marital_status'];
        $user->place_of_birth = $validated['place_of_birth'];

        if (! $isStaff) {
            $user->father_name = $validated['father_name'] ?? null;
            $user->aadhar_card_no = $validated['aadhar_card_no'] ?? null;
            $user->aadhar_card_image = $aadharImage;
            $user->pan_card_no = $validated['pan_card_no'] ?? null;
            $user->pan_card_image = $panImage;
            $user->driving_license_no = $validated['driving_license_no'] ?? null;
            $user->driving_license_image = $drivingLicenseImage;
            $user->bank_account_no = $validated['bank_account_no'] ?? null;
            $user->ifsc_code = $validated['ifsc_code'] ?? null;
            $user->bank_name = $validated['bank_name'] ?? null;
            $user->bank_branch = $validated['bank_branch'] ?? null;
            $user->bank_proof_image = $bankProofImage;
        }

        if ($request->filled('password')) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        return redirect()->route('portal.profile.show')->with('success', 'Profile updated successfully.');
    }

    private function getStaffRoleId(): ?int
    {
        $role = Role::where('name', 'Staff Employee')->first();

        return $role?->id;
    }

    private function isStaffRole(?int $roleId): bool
    {
        return $roleId && $roleId === $this->getStaffRoleId();
    }

    private function isDriverRole(?int $roleId): bool
    {
        if (empty($roleId)) {
            return false;
        }

        $role = Role::find($roleId);

        return $role && stripos($role->name, 'driver') !== false;
    }
}
