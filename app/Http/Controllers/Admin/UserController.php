<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Concerns\ExportsTabularData;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Department;
use App\Models\Office;
use App\CPU\ImageManager;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    use ExportsTabularData;

    public function __construct()
    {
        //$this->middleware('auth');
        //$this->middleware('role:admin'); // Sirf admin access
    }

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

    public function index(Request $request)
    {
        $query = User::with(['roles', 'office', 'department'])
            ->whereNotIn('role_id', [1, 2]);

        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('users.name', 'like', "%{$search}%")
                    ->orWhere('users.email', 'like', "%{$search}%")
                    ->orWhere('users.phone', 'like', "%{$search}%")
                    ->orWhere('users.address', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('role_id')) {
            $query->where('role_id', $request->role_id);
        }

        // Sorting Logic
        if ($request->filled('sort_by') && $request->filled('sort_order')) {
            $sortBy = $request->sort_by;
            $sortOrder = $request->sort_order;

            if ($sortBy === 'role') {
                $query->join('roles', 'users.role_id', '=', 'roles.id')
                    ->orderBy('roles.name', $sortOrder)
                    ->select('users.*');
            } else {
                $allowedSorts = ['name', 'status', 'created_at'];
                if (in_array($sortBy, $allowedSorts)) {
                    $query->orderBy($sortBy, $sortOrder);
                } else {
                    $query->orderBy('created_at', 'desc');
                }
            }
        } else {
            $query->orderBy('created_at', 'desc');
        }

        $users = $query->paginate(10)
            ->withQueryString();

        $roles = Role::whereNotIn('id', [1, 2])->get();
        $departments = Department::where('status', 1)->get();
        $offices = Office::orderBy('name')->get();

        return view('admin.users.index', compact('users', 'roles', 'departments', 'offices'));
    }


    public function create()
    {
        $roles = Role::all();
        return view('admin.users.create', compact('roles'));
    }

    // Store new user
    // public function store(Request $request)
    // {


    //     $validator = Validator::make($request->all(), [
    // 		'fullname'   => 'required|string|max:255',
    // 		'email'      => 'required|email|unique:users,email',
    // 		'phone'      => [
    // 			'required',
    // 			'regex:/^[0-9]{10}$/',
    // 			'unique:users,phone',     
    // 		],
    // 		'address'    => 'required|string|max:500',
    // 		'role_id'    => 'required|exists:roles,id', 
    //         'department_id' => 'required|exists:departments,id',
    //         'job_type'   => ['nullable', 'string', 'in:Full Time,Half Time', function ($attribute, $value, $fail) use ($request) {
    //             if ($request->role_id == $this->getStaffRoleId() && empty($value)) {
    //                 $fail('The job type field is required for staff employees.');
    //             }
    //         }],
    // 		'password'   => 'required|confirmed|min:6', 
    //         'profile_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
    // 	]);

    //     if ($validator->fails()) {
    //         return redirect()->back()->withErrors($validator, 'userCreation')->withInput();
    //     }

    //     $imagePath = null;
    //     if ($request->hasFile('profile_image')) {
    //         $imageName = ImageManager::upload('profile/', 'png', $request->file('profile_image'));
    //         $imagePath = 'storage/profile/' . $imageName;
    //     }

    //     $user = User::create([
    // 		'name' => $request->fullname,
    // 		'email' => $request->email,
    // 		'phone' => $request->phone,
    // 		'address' => $request->address,
    // 		'role_id' => $request->role_id,
    //         'department_id' => $request->department_id,
    //         'job_type' => $request->job_type,
    //         'profile_image' => $imagePath,
    // 		'password' => Hash::make($request->password),
    // 		'status' => 1,
    // 		'email_verified_at' => now(),
    // 	]);

    //     // $user->assignRole($request->role_id); 

    //     return redirect()->route('employees.index')->with('success', 'Employee created successfully.');
    // }


    public function store(Request $request)
    {
        $request->merge([
            'pan_card_no' => $request->filled('pan_card_no') ? strtoupper($request->pan_card_no) : $request->pan_card_no,
            'ifsc_code' => $request->filled('ifsc_code') ? strtoupper($request->ifsc_code) : $request->ifsc_code,
        ]);

        $validator = Validator::make($request->all(), [
            'fullname'   => ['required', 'string', 'max:255', 'regex:/^[A-Za-z\s]+$/'],
            'email'      => 'required|email|unique:users,email',
            'phone'      => ['required', 'regex:/^[0-9]{10}$/', 'unique:users,phone'],
            'address'    => 'required|string|max:500',
            'role_id'    => 'required|exists:roles,id',
            'department_id' => ['nullable', 'exists:departments,id', function ($attribute, $value, $fail) use ($request) {
                if ($request->role_id != $this->getStaffRoleId() && empty($value)) {
                    $fail('The department field is required for non-staff employees.');
                }
            }],
            'job_type'   => ['nullable', 'string', 'in:Full Time,Half Time', function ($attribute, $value, $fail) use ($request) {
                if ($request->role_id == $this->getStaffRoleId() && empty($value)) {
                    $fail('The job type field is required for staff employees.');
                }
            }],
            'office_id' => ['nullable', 'exists:offices,id', function ($attribute, $value, $fail) use ($request) {
                if ($request->role_id == $this->getStaffRoleId() && empty($value) && Office::query()->exists()) {
                    $fail('The office field is required for staff employees.');
                }
            }],
            'date_of_birth' => 'required|date|before:today',
            'gender'     => 'required|in:male,female,other',
            'marital_status' => 'required|in:single,married,divorced,widowed',
            'father_name' => ['nullable', 'string', 'max:255', 'regex:/^[A-Za-z\s]+$/', function ($attribute, $value, $fail) use ($request) {
                if ($this->isDriverRole($request->role_id) && empty($value)) {
                    $fail("Father's name is required for driver employees.");
                }
            }],
            'place_of_birth' => ['required', 'string', 'max:255', 'regex:/^[A-Za-z\s]+$/'],
            'password'   => 'required|confirmed|min:6',

            'profile_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',

            // KYC FIXED
            'aadhar_card_no' => ['nullable', 'digits:12', 'unique:users,aadhar_card_no', function ($attribute, $value, $fail) use ($request) {
                if ($this->isDriverRole($request->role_id) && empty($value)) {
                    $fail('Aadhar card number is required for driver employees.');
                }
            }],
            'aadhar_card_image' => ['nullable', 'image', 'max:2048', function ($attribute, $value, $fail) use ($request) {
                if ($this->isDriverRole($request->role_id) && !$request->hasFile('aadhar_card_image')) {
                    $fail('Aadhar card image is required for driver employees.');
                }
            }],

            'pan_card_no' => ['nullable', 'regex:/^[A-Z]{5}[0-9]{4}[A-Z]{1}$/', 'max:20', 'unique:users,pan_card_no', function ($attribute, $value, $fail) use ($request) {
                if ($this->isDriverRole($request->role_id) && empty($value)) {
                    $fail('PAN card number is required for driver employees.');
                }
            }],
            'pan_card_image' => ['nullable', 'image', 'max:2048', function ($attribute, $value, $fail) use ($request) {
                if ($this->isDriverRole($request->role_id) && !$request->hasFile('pan_card_image')) {
                    $fail('PAN card image is required for driver employees.');
                }
            }],

            'driving_license_no' => 'nullable|string|max:50',
            'driving_license_image' => 'nullable|mimes:jpeg,png,jpg,pdf|max:2048',

            // BANK FIXED
            'bank_account_no' => ['nullable', 'regex:/^[0-9]{10,16}$/', 'unique:users,bank_account_no'],
            'ifsc_code' => ['nullable', 'regex:/^[A-Z]{4}0[A-Z0-9]{6}$/', 'max:20'],
            'bank_name' => 'nullable|string|max:255',
            'bank_branch' => 'nullable|string|max:255',
            'bank_proof_image' => 'nullable|mimes:jpeg,png,jpg,pdf|max:2048',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator, 'userCreation')->withInput();
        }

        // 🔹 UPLOAD HELPER
        $upload = function ($file, $path, $ext = 'png') {
            if (!$file) return null;
            $name = ImageManager::upload($path, $ext, $file);
            return 'storage/' . $path . $name;
        };

        // FILES
        $profileImage = $upload($request->file('profile_image'), 'profile/');
        $aadharImage  = $upload($request->file('aadhar_card_image'), 'documents/aadhar/');
        $panImage     = $upload($request->file('pan_card_image'), 'documents/pan/');
        $dlImage      = $upload($request->file('driving_license_image'), 'documents/dl/');
        $bankImage    = $upload($request->file('bank_proof_image'), 'documents/bank/');

        // CREATE USER (explicit assignment avoids silent drops when fillable is incomplete)
        $user = new User();
        $user->name = $request->fullname;
        $user->email = $request->email;
        $user->phone = $request->phone;
        $user->address = $request->address;
        $user->role_id = $request->role_id;
        $user->department_id = $request->role_id == $this->getStaffRoleId() ? null : $request->department_id;
        $user->office_id = $request->role_id == $this->getStaffRoleId() ? $request->office_id : null;
        $user->job_type = $request->role_id == $this->getStaffRoleId() ? $request->job_type : null;
        $user->profile_image = $profileImage;

        // PERSONAL
        $user->date_of_birth = $request->date_of_birth;
        $user->gender = $request->gender;
        $user->father_name = $request->father_name;
        $user->place_of_birth = $request->place_of_birth;
        $user->marital_status = $request->marital_status;

        // KYC
        $user->aadhar_card_no = $request->aadhar_card_no;
        $user->aadhar_card_image = $aadharImage;
        $user->pan_card_no = $request->pan_card_no;
        $user->pan_card_image = $panImage;
        $user->driving_license_no = $request->driving_license_no;
        $user->driving_license_image = $dlImage;

        // BANK
        $user->bank_account_no = $request->bank_account_no;
        $user->ifsc_code = $request->ifsc_code;
        $user->bank_name = $request->bank_name;
        $user->bank_branch = $request->bank_branch;
        $user->bank_proof_image = $bankImage;

        $user->password = Hash::make($request->password);
        $user->status = 1;
        $user->email_verified_at = now();
        $user->save();

        return redirect()->route('employees.index')->with('success', 'Employee created successfully.');
    }

    public function edit(User $user)
    {
        $roles = Role::all();
        return view('admin.users.edit', compact('user', 'roles'));
    }

    public function update(Request $request, User $employee)
    {
        $request->merge([
            'pan_card_no' => $request->filled('pan_card_no') ? strtoupper($request->pan_card_no) : $request->pan_card_no,
            'ifsc_code' => $request->filled('ifsc_code') ? strtoupper($request->ifsc_code) : $request->ifsc_code,
        ]);

        $validator = Validator::make($request->all(), [
            'fullname' => ['required', 'string', 'max:255', 'regex:/^[A-Za-z\s]+$/'],
            'email'    => 'required|email|unique:users,email,' . $employee->id,
            'phone'    => [
                'required',
                'regex:/^[0-9]{10}$/',
                'unique:users,phone,' . $employee->id,
            ],
            'address'  => 'required|string|max:500',
            'role_id'  => 'required|exists:roles,id',
            'department_id' => ['nullable', 'exists:departments,id', function ($attribute, $value, $fail) use ($request) {
                if ($request->role_id != $this->getStaffRoleId() && empty($value)) {
                    $fail('The department field is required for non-staff employees.');
                }
            }],
            'job_type' => ['nullable', 'string', 'in:Full Time,Half Time', function ($attribute, $value, $fail) use ($request) {
                if ($request->role_id == $this->getStaffRoleId() && empty($value)) {
                    $fail('The job type field is required for staff employees.');
                }
            }],
            'office_id' => ['nullable', 'exists:offices,id', function ($attribute, $value, $fail) use ($request) {
                if ($request->role_id == $this->getStaffRoleId() && empty($value) && Office::query()->exists()) {
                    $fail('The office field is required for staff employees.');
                }
            }],
            'date_of_birth' => 'required|date|before:today',
            'gender'     => 'required|in:male,female,other',
            'marital_status' => 'required|in:single,married,divorced,widowed',
            'father_name' => ['nullable', 'string', 'max:255', 'regex:/^[A-Za-z\s]+$/', function ($attribute, $value, $fail) use ($request) {
                if ($this->isDriverRole($request->role_id) && empty($value)) {
                    $fail("Father's name is required for driver employees.");
                }
            }],
            'place_of_birth' => ['required', 'string', 'max:255', 'regex:/^[A-Za-z\s]+$/'],
            'password' => 'nullable|confirmed|min:6',
            'profile_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',

            'aadhar_card_no' => ['nullable', 'digits:12', 'unique:users,aadhar_card_no,' . $employee->id, function ($attribute, $value, $fail) use ($request) {
                if ($this->isDriverRole($request->role_id) && empty($value)) {
                    $fail('Aadhar card number is required for driver employees.');
                }
            }],
            'aadhar_card_image' => ['nullable', 'image', 'max:2048', function ($attribute, $value, $fail) use ($request, $employee) {
                if ($this->isDriverRole($request->role_id) && !$request->hasFile('aadhar_card_image') && empty($employee->aadhar_card_image)) {
                    $fail('Aadhar card image is required for driver employees.');
                }
            }],

            'pan_card_no' => ['nullable', 'regex:/^[A-Z]{5}[0-9]{4}[A-Z]{1}$/', 'max:20', 'unique:users,pan_card_no,' . $employee->id, function ($attribute, $value, $fail) use ($request) {
                if ($this->isDriverRole($request->role_id) && empty($value)) {
                    $fail('PAN card number is required for driver employees.');
                }
            }],
            'pan_card_image' => ['nullable', 'image', 'max:2048', function ($attribute, $value, $fail) use ($request, $employee) {
                if ($this->isDriverRole($request->role_id) && !$request->hasFile('pan_card_image') && empty($employee->pan_card_image)) {
                    $fail('PAN card image is required for driver employees.');
                }
            }],

            'driving_license_no' => 'nullable|string|max:50',
            'driving_license_image' => 'nullable|mimes:jpeg,png,jpg,pdf|max:2048',

            'bank_account_no' => ['nullable', 'regex:/^[0-9]{10,16}$/', 'unique:users,bank_account_no,' . $employee->id],
            'ifsc_code' => ['nullable', 'regex:/^[A-Z]{4}0[A-Z0-9]{6}$/', 'max:20'],
            'bank_name' => 'nullable|string|max:255',
            'bank_branch' => 'nullable|string|max:255',
            'bank_proof_image' => 'nullable|mimes:jpeg,png,jpg,pdf|max:2048',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator, 'userUpdate' . $employee->id)->withInput();
        }

        $imagePath = $employee->profile_image;
        if ($request->hasFile('profile_image')) {
            // Delete old image if exists
            if ($employee->profile_image && file_exists(public_path($employee->profile_image))) {
                // unlink(public_path($employee->profile_image));
            }
            $imageName = ImageManager::upload('profile/', 'png', $request->file('profile_image'));
            $imagePath = 'storage/profile/' . $imageName;
        }

        $aadharImage = $employee->aadhar_card_image;
        if ($request->hasFile('aadhar_card_image')) {
            $imageName = ImageManager::upload('documents/aadhar/', 'png', $request->file('aadhar_card_image'));
            $aadharImage = 'storage/documents/aadhar/' . $imageName;
        }

        $panImage = $employee->pan_card_image;
        if ($request->hasFile('pan_card_image')) {
            $imageName = ImageManager::upload('documents/pan/', 'png', $request->file('pan_card_image'));
            $panImage = 'storage/documents/pan/' . $imageName;
        }

        $drivingLicenseImage = $employee->driving_license_image;
        if ($request->hasFile('driving_license_image')) {
            $imageName = ImageManager::upload('documents/dl/', 'png', $request->file('driving_license_image'));
            $drivingLicenseImage = 'storage/documents/dl/' . $imageName;
        }

        $bankProofImage = $employee->bank_proof_image;
        if ($request->hasFile('bank_proof_image')) {
            $imageName = ImageManager::upload('documents/bank/', 'png', $request->file('bank_proof_image'));
            $bankProofImage = 'storage/documents/bank/' . $imageName;
        }

        // UPDATE USER (explicit assignment avoids silent drops when fillable is incomplete)
        $employee->name = $request->fullname;
        $employee->email = $request->email;
        $employee->phone = $request->phone;
        $employee->address = $request->address;
        $employee->password = $request->password ? Hash::make($request->password) : $employee->password;
        $employee->role_id = $request->role_id;
        $employee->department_id = $request->role_id == $this->getStaffRoleId() ? null : $request->department_id;
        $employee->office_id = $request->role_id == $this->getStaffRoleId() ? $request->office_id : null;
        $employee->job_type = $request->role_id == $this->getStaffRoleId() ? $request->job_type : null;
        $employee->date_of_birth = $request->date_of_birth;
        $employee->gender = $request->gender;
        $employee->father_name = $request->father_name;
        $employee->place_of_birth = $request->place_of_birth;
        $employee->marital_status = $request->marital_status;

        $employee->aadhar_card_no = $request->aadhar_card_no;
        $employee->aadhar_card_image = $aadharImage;
        $employee->pan_card_no = $request->pan_card_no;
        $employee->pan_card_image = $panImage;
        $employee->driving_license_no = $request->driving_license_no;
        $employee->driving_license_image = $drivingLicenseImage;

        $employee->bank_account_no = $request->bank_account_no;
        $employee->ifsc_code = $request->ifsc_code;
        $employee->bank_name = $request->bank_name;
        $employee->bank_branch = $request->bank_branch;
        $employee->bank_proof_image = $bankProofImage;
        $employee->profile_image = $imagePath;
        $employee->save();

        // $employee->syncRoles($request->role_id);

        return redirect()->route('employees.index')->with('success', 'Employee updated successfully.');
    }

    public function updateStatus(Request $request)
    {
        $user = User::find($request->id);
        if ($user) {
            $user->status = $request->status;
            $user->save();
            return response()->json(['success' => 'Status updated successfully.']);
        }
        return response()->json(['error' => 'User not found.'], 404);
    }

    public function destroy(User $employee)
    {
        $employee->delete();
        return redirect()->route('employees.index')->with('success', 'Employee deleted successfully.');
    }

    public function report(Request $request)
    {
        $query = User::with(['role', 'department'])
            ->whereNotIn('role_id', [1, 2]);

        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('users.name', 'like', "%{$search}%")
                    ->orWhere('users.email', 'like', "%{$search}%")
                    ->orWhere('users.phone', 'like', "%{$search}%")
                    ->orWhere('users.address', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('role_id')) {
            $query->where('role_id', $request->role_id);
        }

        $users = $query->orderBy('created_at', 'desc')->get();

        $format = $this->exportFormat($request);
        $headers = ['ID', 'Name', 'Role', 'Department', 'Email', 'Phone', 'Status'];
        $rows = [];
        foreach ($users as $user) {
            $rows[] = [
                (string) $user->id,
                (string) ($user->name ?? '-'),
                (string) ($user->role->name ?? '-'),
                (string) ($user->department->name ?? '-'),
                (string) ($user->email ?? '-'),
                (string) ($user->phone ?? '-'),
                ((int) $user->status === 1) ? 'Active' : 'Inactive',
            ];
        }
        if ($format === 'csv') {
            return $this->streamCsvDownload('employees_report_' . now()->format('Y-m-d_His'), $headers, $rows);
        }
        if ($format === 'xlsx') {
            return $this->streamExcelTableDownload('employees_report_' . now()->format('Y-m-d_His'), 'Employee Report', $headers, $rows);
        }

        if (!class_exists(\Dompdf\Dompdf::class)) {
            return back()->with('error', 'PDF library is not installed. Please install dompdf/dompdf.');
        }

        $html = '<html><head><meta charset="utf-8"><style>'
            . 'body{font-family: DejaVu Sans, Arial, sans-serif; font-size: 12px; color:#222;}'
            . 'h2{margin:0 0 8px 0; font-size:18px;}'
            . 'p{margin:0 0 10px 0; font-size:11px; color:#555;}'
            . 'table{width:100%; border-collapse:collapse;}'
            . 'th,td{border:1px solid #ddd; padding:6px; text-align:left; vertical-align:top;}'
            . 'th{background:#f3f4f6;}'
            . '</style></head><body>'
            . '<h2>Employee Report</h2>'
            . '<p>Exported at: ' . e(now()->format('d M Y h:i A')) . '</p>'
            . '<table><thead><tr>'
            . '<th>ID</th><th>Name</th><th>Role</th><th>Department</th><th>Email</th><th>Phone</th><th>Status</th>'
            . '</tr></thead><tbody>';

        foreach ($users as $user) {
            $html .= '<tr>'
                . '<td>' . e((string) $user->id) . '</td>'
                . '<td>' . e((string) ($user->name ?? '-')) . '</td>'
                . '<td>' . e((string) ($user->role->name ?? '-')) . '</td>'
                . '<td>' . e((string) ($user->department->name ?? '-')) . '</td>'
                . '<td>' . e((string) ($user->email ?? '-')) . '</td>'
                . '<td>' . e((string) ($user->phone ?? '-')) . '</td>'
                . '<td>' . e((int) $user->status === 1 ? 'Active' : 'Inactive') . '</td>'
                . '</tr>';
        }

        if ($users->isEmpty()) {
            $html .= '<tr><td colspan="7" style="text-align:center;">No Data Found</td></tr>';
        }

        $html .= '</tbody></table></body></html>';

        $dompdf = new \Dompdf\Dompdf([
            'isRemoteEnabled' => true,
            'defaultFont' => 'DejaVu Sans',
        ]);

        $dompdf->loadHtml($html);
        $dompdf->setPaper('a4', 'landscape');
        $dompdf->render();

        $filename = 'employees_report_' . now()->format('Y-m-d_His') . '.pdf';
        return response($dompdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }
}
