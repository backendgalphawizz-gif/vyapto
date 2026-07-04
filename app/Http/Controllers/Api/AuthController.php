<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Api\User;
use App\CPU\ImageManager;
use Illuminate\Support\Facades\Hash;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;
use App\Models\Api\UserToken;
use Spatie\Permission\Models\Role;
use Validator;
use Carbon\Carbon;
use App\Models\Attendance;
use App\Models\VehicleUsage;

class AuthController extends Controller
{
	private function getStaffRoleId()
	{
		$role = Role::where('name', 'Staff Employee')->first();
		return $role ? $role->id : null;
	}

	private function isDriverRole($roleId)
	{
		if (empty($roleId)) {
			return false;
		}

		$role = Role::find($roleId);
		return $role && stripos($role->name, 'driver') !== false;
	}

	// LOGIN API
	public function login(Request $request)
	{

		$validator = Validator::make($request->all(), [
			'email' => 'required|email',
			'password' => 'required|min:6'
		]);

		if ($validator->fails()) {
			return response()->json([
				'status' => false,
				'message' => 'Invalid Email or Password'
			], 200);
		}

		$credentials = $request->only('email', 'password');
		if (!$token = auth('api')->attempt($credentials)) {
			return response()->json([
				'status' => false,
				'message' => 'Invalid Email or Password'
			], 200);
		}

		$user = auth('api')->user();
		$userToken = UserToken::where('user_id', $user->id)->first();
		if (!empty($userToken)) {
			$userToken->update([
				'token' => $token,
			]);
		} else {
			UserToken::where('user_id', $user->id)->delete();
			UserToken::create([
				'user_id' => $user->id,
				'token' => $token,
			]);
		}
		User::find($user->id)->update(['fcm_token' => $request->fcm_token??null]);

		return response()->json([
			'status' => true,
			'code' => 200,
			'message' => 'Login Successful',
			'token' => $token,
			'user' => auth('api')->user()
		]);
	}


	// SEND OTP
	public function sendOtp(Request $request)
	{
		$validator = Validator::make($request->all(), [
			'phone' => 'required|digits:10'
		]);

		if ($validator->fails()) {
			return response()->json([
				'status' => false,
				'errors' => $validator->errors()
			], 422);
		}

		$otp = rand(1000, 9999);
		$user = User::where('phone', $request->phone)->first();

		if (!$user) {
			return response()->json([
				'status' => false,
				'message' => 'Invaild User',
			], 200);
		}

		$user->otp = $otp;
		$user->otp_expire_at = Carbon::now()->addMinutes(5);
		$user->save();

		return response()->json([
			'status' => true,
			'code' => 200,
			'message' => 'OTP sent successfully',
			'otp' => $otp
		]);
	}


	public function verifyOtp(Request $request)
	{
		$validator = Validator::make($request->all(), [
			'phone' => 'required|digits:10',
			'otp'   => 'required|digits:4'
		]);

		if ($validator->fails()) {
			return response()->json([
				'status' => false,
				'errors' => $validator->errors()
			], 422);
		}

		$user = User::where('phone', $request->phone)
			->where('otp', $request->otp)
			->first();

		if (!$user) {
			return response()->json([
				'status' => false,
				'message' => 'Invalid OTP'
			], 200);
		}

		if (Carbon::now()->gt($user->otp_expire_at)) {
			return response()->json([
				'status' => false,
				'message' => 'OTP Expired'
			], 200);
		}

		$user->otp = null;
		$user->otp_expire_at = null;
		$user->fcm_token = $request->fcm_token??null;
		$user->save();

		$token = JWTAuth::fromUser($user);

		$userToken = UserToken::where('user_id', $user->id)->first();
		if (!empty($userToken)) {
			$userToken->update([
				'token' => $token,
			]);
		} else {

			UserToken::where('user_id', $user->id)->delete();
			UserToken::create([
				'user_id' => $user->id,
				'token' => $token,
			]);
		}

		return response()->json([
			'status' => true,
			'code' => 200,
			'message' => 'Login successful',
			'token' => $token,
			'user' => $user
		]);
	}


	public function forgotPassword(Request $request)
	{

		$validator = Validator::make($request->all(), [
			'email' => 'required',
			//'password' => 'required|min:6',
			//'password_confirmation' => 'required',
		]);

		if ($validator->fails()) {
			return response()->json([
				'status' => false,
				'errors' => $validator->errors()
			], 422);
		}

		$user = User::where('email', $request->email)->first();

		if (!$user) {
			return response()->json([
				'status' => false,
				'message' => 'Email not found'
			], 200);
		}

		/*if ($request->password !== $request->password_confirmation) {
			return response()->json([
				'status' => false,
				'message' => 'Password and Confirm Password not match'
			], 422);
		}

		$user->password = Hash::make($request->password);
		$user->save();*/

		return response()->json([
			'status' => true,
			'code' => 200,
			'message' => 'Password reset link sent to your email!'
		]);
	}

	// PROFILE API
	public function profile(Request $request)
	{
		// 🔐 TOKEN CHECK
		$token = str_replace('Bearer ', '', $request->header('Authorization'));
		if ($token && !UserToken::where('token', $token)->exists()) {
			return response()->json([
				'status' => false,
				'message' => 'Invalid or expired token'
			], 401);
		}

		$user = auth('api')->user();

		if (!$user) {
			return response()->json([
				'status' => false,
				'message' => 'Unauthorized'
			], 401);
		}

		$attendance = Attendance::where('employee_id', $user->id)
			->whereDate('punch_in_date', today())
			->select('punch_in_time', 'punch_out_time')
			->first();

		$timezone = 'Asia/Kolkata';

		$punchInTime = $attendance && $attendance->punch_in_time
			? \Carbon\Carbon::parse($attendance->punch_in_time)
			->timezone($timezone)
			->format('Y-m-d H:i:s')
			: null;

		$punchOutTime = $attendance && $attendance->punch_out_time
			? \Carbon\Carbon::parse($attendance->punch_out_time)
			->timezone($timezone)
			->format('Y-m-d H:i:s')
			: null;

		$user->punch_in = $punchInTime ? 1 : 0;
		$user->punch_in_time = $punchInTime;

		$user->punch_out = $punchOutTime ? 1 : 0;
		$user->punch_out_time = $punchOutTime;

		$todayVehicleUsageActualCount = VehicleUsage::where('user_id', $user->id)
			->whereDate('created_at', today())
			->count();

		$user->today_vehicle_usage_count = $todayVehicleUsageActualCount;

		return response()->json([
			'status' => true,
			'code' => 200,
			'message' => 'User Profile',
			// 'today_vehicle_usage_count' => $todayVehicleUsageActualCount,
			'user' => $user,
		]);
	}

	public function updateProfile(Request $request)
	{
		$user = auth('api')->user();

		if (!$user) {
			return response()->json([
				'status' => false,
				'message' => 'Unauthorized'
			], 401);
		}

		// Return JSON for oversized multipart payloads when request reaches PHP.
		$contentLength = (int) $request->server('CONTENT_LENGTH', 0);
		$maxPostBytes = $this->iniSizeToBytes(ini_get('post_max_size'));
		if ($maxPostBytes > 0 && $contentLength > $maxPostBytes) {
			return response()->json([
				'status' => false,
				'message' => 'Uploaded payload is too large. Please upload a smaller image.'
			], 413);
		}

		$roleId = $user->role_id;

		$request->merge([
			'pan_card_no' => $request->filled('pan_card_no') ? strtoupper($request->pan_card_no) : $request->pan_card_no,
			'ifsc_code' => $request->filled('ifsc_code') ? strtoupper($request->ifsc_code) : $request->ifsc_code,
		]);

		$validator = Validator::make($request->all(), [
			'fullname'   => ['required', 'string', 'max:255', 'regex:/^[A-Za-z\s]+$/'],
			'email'      => 'required|email|unique:users,email,' . $user->id,
			'phone'      => [
				'required',
				'regex:/^[0-9]{10}$/',
				'unique:users,phone,' . $user->id,
			],
			'address'    => 'required|string|max:500',
			'department_id' => ['nullable', 'exists:departments,id', function ($attribute, $value, $fail) use ($roleId) {
				if ($roleId != $this->getStaffRoleId() && empty($value)) {
					$fail('The department field is required for non-staff employees.');
				}
			}],
			'job_type'   => ['nullable', 'string', 'in:Full Time,Half Time', function ($attribute, $value, $fail) use ($roleId) {
				if ($roleId == $this->getStaffRoleId() && empty($value)) {
					$fail('The job type field is required for staff employees.');
				}
			}],
			'date_of_birth' => 'required|date|before:today',
			'gender'     => 'required|in:male,female,other',
			'marital_status' => 'required|in:single,married,divorced,widowed',
			'father_name' => ['nullable', 'string', 'max:255', 'regex:/^[A-Za-z\s]+$/', function ($attribute, $value, $fail) use ($roleId) {
				if ($this->isDriverRole($roleId) && empty($value)) {
					$fail("Father's name is required for driver employees.");
				}
			}],
			'place_of_birth' => ['required', 'string', 'max:255', 'regex:/^[A-Za-z\s]+$/'],
			'password'   => 'nullable|confirmed|min:6',
			'profile_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:10240',

			'aadhar_card_no' => ['nullable', 'digits:12', 'unique:users,aadhar_card_no,' . $user->id, function ($attribute, $value, $fail) use ($roleId) {
				if ($this->isDriverRole($roleId) && empty($value)) {
					$fail('Aadhar card number is required for driver employees.');
				}
			}],
			'aadhar_card_image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:10240', function ($attribute, $value, $fail) use ($request, $user, $roleId) {
				if ($this->isDriverRole($roleId) && !$request->hasFile('aadhar_card_image') && empty($user->aadhar_card_image)) {
					$fail('Aadhar card image is required for driver employees.');
				}
			}],

			'pan_card_no' => ['nullable', 'regex:/^[A-Z]{5}[0-9]{4}[A-Z]{1}$/', 'max:20', 'unique:users,pan_card_no,' . $user->id, function ($attribute, $value, $fail) use ($roleId) {
				if ($this->isDriverRole($roleId) && empty($value)) {
					$fail('PAN card number is required for driver employees.');
				}
			}],
			'pan_card_image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:10240', function ($attribute, $value, $fail) use ($request, $user, $roleId) {
				if ($this->isDriverRole($roleId) && !$request->hasFile('pan_card_image') && empty($user->pan_card_image)) {
					$fail('PAN card image is required for driver employees.');
				}
			}],

			'driving_license_no' => 'nullable|string|max:50',
			'driving_license_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:10240',

			'bank_account_no' => ['nullable', 'regex:/^[0-9]{10,16}$/', 'unique:users,bank_account_no,' . $user->id],
			'ifsc_code' => ['nullable', 'regex:/^[A-Z]{4}0[A-Z0-9]{6}$/', 'max:20'],
			'bank_name' => 'nullable|string|max:255',
			'bank_branch' => 'nullable|string|max:255',
			'bank_proof_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:10240',
		]);

		// if ($validator->fails()) {
		// 	return response()->json([
		// 		'status' => false,
		// 		'errors' => $validator->errors()
		// 	], 422);
		// }

		if ($validator->fails()) {
			return response()->json([
				'status' => false,
				'errors' => $validator->errors()->first()
			], 422);
		}

		$upload = function ($file, $path, $ext = 'png', $oldPath = null) {
			if (!$file) return $oldPath;
			$name = ImageManager::upload($path, $ext, $file);
			return 'storage/' . $path . $name;
		};

		$user->profile_image = $upload($request->file('profile_image'), 'profile/', 'png', $user->profile_image);
		$user->aadhar_card_image = $upload($request->file('aadhar_card_image'), 'documents/aadhar/', 'png', $user->aadhar_card_image);
		$user->pan_card_image = $upload($request->file('pan_card_image'), 'documents/pan/', 'png', $user->pan_card_image);
		$user->driving_license_image = $upload($request->file('driving_license_image'), 'documents/dl/', 'png', $user->driving_license_image);
		$user->bank_proof_image = $upload($request->file('bank_proof_image'), 'documents/bank/', 'png', $user->bank_proof_image);

		$user->name = $request->fullname;
		$user->email = $request->email;
		$user->phone = $request->phone;
		$user->address = $request->address;
		$user->role_id = $roleId;
		$user->department_id = $request->department_id;
		$user->job_type = $request->job_type;
		$user->date_of_birth = $request->date_of_birth;
		$user->gender = $request->gender;
		$user->father_name = $request->father_name;
		$user->place_of_birth = $request->place_of_birth;
		$user->marital_status = $request->marital_status;

		$user->aadhar_card_no = $request->aadhar_card_no;
		$user->pan_card_no = $request->pan_card_no;
		$user->driving_license_no = $request->driving_license_no;

		$user->bank_account_no = $request->bank_account_no;
		$user->ifsc_code = $request->ifsc_code;
		$user->bank_name = $request->bank_name;
		$user->bank_branch = $request->bank_branch;

		if ($request->filled('password')) {
			$user->password = Hash::make($request->password);
		}

		$user->save();

		return response()->json([
			'status' => true,
			'message' => 'Profile updated successfully',
			'user' => $user
		]);
	}

	private function iniSizeToBytes($value)
	{
		if ($value === null || $value === '') {
			return 0;
		}

		$value = trim($value);
		$unit = strtolower(substr($value, -1));
		$number = (float) $value;

		switch ($unit) {
			case 'g':
				$number *= 1024;
				// no break
			case 'm':
				$number *= 1024;
				// no break
			case 'k':
				$number *= 1024;
		}

		return (int) $number;
	}


	public function logout(Request $request)
	{
		$user = auth('api')->user();
		if ($user) {
			UserToken::where('user_id', $user->id)->delete();
			auth('api')->logout();
			return response()->json([
				'status' => true,
				'message' => 'Logout Successfully'
			]);
		}
	}
}
