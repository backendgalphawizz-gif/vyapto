<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Concerns\ExportsTabularData;
use Illuminate\Http\Request;
use App\Models\Vendor;
use App\CPU\ImageManager;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;

class VendorController extends Controller
{
    use ExportsTabularData;

    public function index(Request $request)
    {
        $query = Vendor::query();
                $businessPanColumn = $this->getBusinessPanColumn();

        if ($request->filled('search')) {
            $search = $request->search;
                        $query->where(function ($q) use ($search, $businessPanColumn) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                ->orWhere('business_name', 'like', "%{$search}%")
                ->orWhere('business_mobile', 'like', "%{$search}%")
                ->orWhere('pan_number', 'like', "%{$search}%")
                ->orWhere('aadhar_number', 'like', "%{$search}%")
                                                                ->orWhere($businessPanColumn, 'like', "%{$search}%")
                  ->orWhere('address', 'like', "%{$search}%")
                  ->orWhere('city', 'like', "%{$search}%")
                  ->orWhere('state', 'like', "%{$search}%")
                  ->orWhere('gst_number', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Sorting Logic
        if ($request->filled('sort_by') && $request->filled('sort_order')) {
            $sortBy = $request->sort_by;
            $sortOrder = $request->sort_order;
            
            $allowedSorts = ['name', 'email', 'phone', 'address', 'city', 'state', 'created_at', 'status'];
            if (in_array($sortBy, $allowedSorts)) {
                $query->orderBy($sortBy, $sortOrder);
            } else {
                $query->orderBy('created_at', 'desc');
            }
        } else {
            $query->orderBy('created_at', 'desc');
        }

        $vendors = $query->paginate(10)->withQueryString();

        return view('admin.vendors.index', compact('vendors'));
    }

    public function store(Request $request)
    {
        $businessPanColumn = $this->getBusinessPanColumn();
        $this->normalizeVendorInputs($request);

        $validator = Validator::make(
            $request->all(),
            $this->vendorValidationRules(),
            $this->vendorValidationMessages()
        );

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator, 'vendorCreation')->withInput();
        }

        $aadharImagePath = null;
        $aadharImageFile = $request->file('aadhar_image') ?: $request->file('gst_document');
        if ($aadharImageFile) {
            $file = $aadharImageFile;
            $ext = $file->getClientOriginalExtension();
            $imageName = ImageManager::upload('vendor/', $ext, $file);
            $aadharImagePath = 'storage/vendor/' . $imageName;
        }

        $cancelledChequeImagePath = $this->storeVendorUpload($request, 'cancelled_cheque_image');
        $bankAccountImagePath = $this->storeVendorUpload($request, 'bank_account_image');
        $bankStatementImagePath = $this->storeVendorUpload($request, 'bank_statement_image');

        $profilePath = null;
        if ($request->hasFile('profile_image')) {
            $imageName = ImageManager::upload('profile/', 'png', $request->file('profile_image'));
            $profilePath = 'storage/profile/' . $imageName;
        }

        $payload = [
            'name' => $request->name,
            'phone' => $request->phone,
            'email' => $request->email,
            'business_name' => $request->business_name,
            'business_mobile' => $request->business_mobile,
            'pan_number' => $request->pan_number,
            'aadhar_number' => $request->aadhar_number,
            'address' => $request->address,
            'city' => $request->city,
            'state' => $request->state,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'gst_number' => $request->gst_number,
            'gst_document' => $aadharImagePath,
            'profile_image' => $profilePath,
            'cancelled_cheque_details' => $request->input('cancelled_cheque_details'),
            'cancelled_cheque_image' => $cancelledChequeImagePath,
            'bank_account_number' => $request->input('bank_account_number'),
            'bank_ifsc_code' => $request->input('bank_ifsc_code'),
            'bank_account_image' => $bankAccountImagePath,
            'bank_statement_image' => $bankStatementImagePath,
            'status' => 1,
        ];

        $payload[$businessPanColumn] = $request->input($businessPanColumn, $request->input('buisness_pan', $request->input('business_pan')));

        Vendor::create($payload);

        return redirect()->route('vendors.index')->with('success', 'Vendor created successfully.');
    }

    public function update(Request $request, Vendor $vendor)
    {
        $businessPanColumn = $this->getBusinessPanColumn();
        $this->normalizeVendorInputs($request);

        $validator = Validator::make(
            $request->all(),
            $this->vendorValidationRules($vendor),
            $this->vendorValidationMessages()
        );

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator, 'vendorUpdate' . $vendor->id)->withInput();
        }

        $aadharImagePath = $vendor->gst_document;
        $aadharImageFile = $request->file('aadhar_image') ?: $request->file('gst_document');
        if ($aadharImageFile) {
            $file = $aadharImageFile;
            $ext = $file->getClientOriginalExtension();
            $imageName = ImageManager::upload('vendor/', $ext, $file);
            $aadharImagePath = 'storage/vendor/' . $imageName;
        }

        $cancelledChequeImagePath = $this->updateVendorUpload($request, 'cancelled_cheque_image', $vendor->cancelled_cheque_image);
        $bankAccountImagePath = $this->updateVendorUpload($request, 'bank_account_image', $vendor->bank_account_image);
        $bankStatementImagePath = $this->updateVendorUpload($request, 'bank_statement_image', $vendor->bank_statement_image);

        $profilePath = $vendor->profile_image;
        if ($request->hasFile('profile_image')) {
            $imageName = ImageManager::upload('profile/', 'png', $request->file('profile_image'));
            $profilePath = 'storage/profile/' . $imageName;
        }

        $payload = [
            'name' => $request->name,
            'phone' => $request->phone,
            'email' => $request->email,
            'business_name' => $request->business_name,
            'business_mobile' => $request->business_mobile,
            'pan_number' => $request->pan_number,
            'aadhar_number' => $request->aadhar_number,
            'address' => $request->address,
            'city' => $request->city,
            'state' => $request->state,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'gst_number' => $request->gst_number,
            'gst_document' => $aadharImagePath,
            'profile_image' => $profilePath,
            'cancelled_cheque_details' => $request->input('cancelled_cheque_details'),
            'cancelled_cheque_image' => $cancelledChequeImagePath,
            'bank_account_number' => $request->input('bank_account_number'),
            'bank_ifsc_code' => $request->input('bank_ifsc_code'),
            'bank_account_image' => $bankAccountImagePath,
            'bank_statement_image' => $bankStatementImagePath,
        ];

        $payload[$businessPanColumn] = $request->input($businessPanColumn, $request->input('buisness_pan', $request->input('business_pan')));

        $vendor->update($payload);

        return redirect()->route('vendors.index')->with('success', 'Vendor updated successfully.');
    }

    public function updateStatus(Request $request)
    {
        $vendor = Vendor::find($request->id);
        if ($vendor) {
            $vendor->status = $request->status;
            $vendor->save();
            return response()->json(['success' => 'Status updated successfully.']);
        }
        return response()->json(['error' => 'Vendor not found.'], 404);
    }

    public function destroy(Vendor $vendor)
    {
        // cleanup files
        if ($vendor->profile_image && file_exists(public_path($vendor->profile_image))) {
            unlink(public_path($vendor->profile_image));
        }
        if ($vendor->gst_document && file_exists(public_path($vendor->gst_document))) {
            unlink(public_path($vendor->gst_document));
        }
        foreach (['cancelled_cheque_image', 'bank_account_image', 'bank_statement_image'] as $fileField) {
            $path = $vendor->{$fileField} ?? null;
            if ($path && file_exists(public_path($path))) {
                unlink(public_path($path));
            }
        }
        $vendor->delete();
        return redirect()->route('vendors.index')->with('success', 'Vendor deleted successfully.');
    }

    public function export(Request $request)
    {
        $query = Vendor::query();
        $businessPanColumn = $this->getBusinessPanColumn();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search, $businessPanColumn) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('business_name', 'like', "%{$search}%")
                    ->orWhere('business_mobile', 'like', "%{$search}%")
                    ->orWhere('pan_number', 'like', "%{$search}%")
                    ->orWhere('aadhar_number', 'like', "%{$search}%")
                    ->orWhere($businessPanColumn, 'like', "%{$search}%")
                    ->orWhere('address', 'like', "%{$search}%")
                    ->orWhere('city', 'like', "%{$search}%")
                    ->orWhere('state', 'like', "%{$search}%")
                    ->orWhere('gst_number', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $vendors = $query->orderBy('created_at', 'desc')->get();

        $format = $this->exportFormat($request);

        $headers = ['ID', 'Name', 'Mobile', 'Email', 'PAN', 'Aadhar', 'Business Name', 'Business Mobile', 'Business PAN', 'GST', 'Status'];
        $rows = [];
        foreach ($vendors as $vendor) {
            $businessPanValue = $vendor->{$businessPanColumn} ?? '-';
            $rows[] = [
                (string) $vendor->id,
                (string) ($vendor->name ?? '-'),
                (string) ($vendor->phone ?? '-'),
                (string) ($vendor->email ?? '-'),
                (string) ($vendor->pan_number ?? '-'),
                (string) ($vendor->aadhar_number ?? '-'),
                (string) ($vendor->business_name ?? '-'),
                (string) ($vendor->business_mobile ?? '-'),
                (string) $businessPanValue,
                (string) ($vendor->gst_number ?? '-'),
                ((int) $vendor->status === 1) ? 'Active' : 'Inactive',
            ];
        }

        if ($format === 'csv') {
            return $this->streamCsvDownload('vendors_' . now()->format('Y-m-d_His'), $headers, $rows);
        }
        if ($format === 'xlsx') {
            return $this->streamExcelTableDownload('vendors_' . now()->format('Y-m-d_His'), 'Vendor Records', $headers, $rows);
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
            . '<h2>Vendor Records</h2>'
            . '<p>Exported at: ' . e(now()->format('d M Y h:i A')) . '</p>'
            . '<table><thead><tr>'
            . '<th>ID</th><th>Name</th><th>Mobile</th><th>Email</th><th>PAN</th><th>Aadhar</th><th>Business Name</th><th>Business Mobile</th><th>Business PAN</th><th>GST</th><th>Status</th>'
            . '</tr></thead><tbody>';

        foreach ($vendors as $vendor) {
            $businessPanValue = $vendor->{$businessPanColumn} ?? '-';

            $html .= '<tr>'
                . '<td>' . e((string) $vendor->id) . '</td>'
                . '<td>' . e((string) ($vendor->name ?? '-')) . '</td>'
                . '<td>' . e((string) ($vendor->phone ?? '-')) . '</td>'
                . '<td>' . e((string) ($vendor->email ?? '-')) . '</td>'
                . '<td>' . e((string) ($vendor->pan_number ?? '-')) . '</td>'
                . '<td>' . e((string) ($vendor->aadhar_number ?? '-')) . '</td>'
                . '<td>' . e((string) ($vendor->business_name ?? '-')) . '</td>'
                . '<td>' . e((string) ($vendor->business_mobile ?? '-')) . '</td>'
                . '<td>' . e((string) ($businessPanValue ?? '-')) . '</td>'
                . '<td>' . e((string) ($vendor->gst_number ?? '-')) . '</td>'
                . '<td>' . e((int) $vendor->status === 1 ? 'Active' : 'Inactive') . '</td>'
                . '</tr>';
        }

        if ($vendors->isEmpty()) {
            $html .= '<tr><td colspan="11" style="text-align:center;">No Data Found</td></tr>';
        }

        $html .= '</tbody></table></body></html>';

        $dompdf = new \Dompdf\Dompdf([
            'isRemoteEnabled' => true,
            'defaultFont' => 'DejaVu Sans',
        ]);

        $dompdf->loadHtml($html);
        $dompdf->setPaper('a4', 'landscape');
        $dompdf->render();

        $filename = 'vendors_' . now()->format('Y-m-d_His') . '.pdf';

        return response($dompdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    private function getBusinessPanColumn(): string
    {
        if (Schema::hasColumn('vendors', 'buisness_pan')) {
            return 'buisness_pan';
        }

        if (Schema::hasColumn('vendors', 'business_pan')) {
            return 'business_pan';
        }

        return 'buisness_pan';
    }

    private function normalizeVendorInputs(Request $request): void
    {
        $request->merge([
            'name' => $this->normalizeSpaces($request->input('name')),
            'business_name' => $this->normalizeSpaces($request->input('business_name')),
            'city' => $this->normalizeSpaces($request->input('city')),
            'state' => $this->normalizeSpaces($request->input('state')),
            'phone' => preg_replace('/\D+/', '', (string) $request->input('phone')),
            'business_mobile' => preg_replace('/\D+/', '', (string) $request->input('business_mobile')),
            'aadhar_number' => preg_replace('/\D+/', '', (string) $request->input('aadhar_number')),
            'pan_number' => strtoupper(trim((string) $request->input('pan_number'))),
            'buisness_pan' => strtoupper(trim((string) $request->input('buisness_pan'))),
            'business_pan' => strtoupper(trim((string) $request->input('business_pan'))),
            'gst_number' => strtoupper(trim((string) $request->input('gst_number'))),
            'cancelled_cheque_details' => $this->normalizeSpaces($request->input('cancelled_cheque_details')),
            'bank_ifsc_code' => ($ifsc = strtoupper(preg_replace('/\s+/', '', (string) $request->input('bank_ifsc_code', '')))) !== ''
                ? $ifsc
                : null,
            'bank_account_number' => ($acct = preg_replace('/\D+/', '', (string) $request->input('bank_account_number', ''))) !== ''
                ? $acct
                : null,
        ]);
    }

    private function normalizeSpaces($value): ?string
    {
        if ($value === null) {
            return null;
        }

        $clean = preg_replace('/\s+/', ' ', trim((string) $value));
        return $clean === '' ? null : $clean;
    }

    private function vendorValidationRules(?Vendor $vendor = null): array
    {
        $emailRule = $vendor
            ? ['required', 'email:rfc', 'max:255', Rule::unique('vendors', 'email')->ignore($vendor->id)]
            : ['required', 'email:rfc', 'max:255', 'unique:vendors,email'];

        $phoneRule = $vendor
            ? ['required', 'digits:10', Rule::unique('vendors', 'phone')->ignore($vendor->id)]
            : ['required', 'digits:10', 'unique:vendors,phone'];

        $gstRule = $vendor
            ? ['nullable', 'size:15', 'regex:/^\d{2}[A-Z]{5}\d{4}[A-Z][1-9A-Z]Z[0-9A-Z]$/', Rule::unique('vendors', 'gst_number')->ignore($vendor->id)]
            : ['nullable', 'size:15', 'regex:/^\d{2}[A-Z]{5}\d{4}[A-Z][1-9A-Z]Z[0-9A-Z]$/', 'unique:vendors,gst_number'];

        return [
            'name' => ['required', 'string', 'min:2', 'max:255', 'regex:/^[A-Za-z][A-Za-z\s\.\'-]*$/'],
            'phone' => $phoneRule,
            'email' => $emailRule,
            'business_name' => ['nullable', 'string', 'min:2', 'max:255', 'regex:/^[A-Za-z0-9][A-Za-z0-9\s\.\'&\-\/\(\)]*$/'],
            'business_mobile' => ['nullable', 'digits:10'],
            'pan_number' => ['required', 'size:10', 'regex:/^[A-Z]{5}[0-9]{4}[A-Z]$/'],
            'aadhar_number' => ['required', 'digits:12'],
            'buisness_pan' => ['required', 'size:10', 'regex:/^[A-Z]{5}[0-9]{4}[A-Z]$/'],
            'business_pan' => ['nullable', 'size:10', 'regex:/^[A-Z]{5}[0-9]{4}[A-Z]$/'],
            'address' => ['nullable', 'string', 'max:500'],
            'city' => ['nullable', 'string', 'max:100', 'regex:/^[A-Za-z][A-Za-z\s\.\'-]*$/'],
            'state' => ['nullable', 'string', 'max:100', 'regex:/^[A-Za-z][A-Za-z\s\.\'-]*$/'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'gst_number' => $gstRule,
            'aadhar_image' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp,pdf', 'max:5120'],
            'gst_document' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp,pdf', 'max:5120'],
            'profile_image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
            'cancelled_cheque_details' => ['nullable', 'string', 'max:2000'],
            'cancelled_cheque_image' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp,pdf', 'max:5120'],
            'bank_account_number' => ['nullable', 'string', 'max:20', 'regex:/^[0-9]{6,20}$/'],
            'bank_ifsc_code' => ['nullable', 'string', 'size:11', 'regex:/^[A-Z]{4}0[A-Z0-9]{6}$/'],
            'bank_account_image' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp,pdf', 'max:5120'],
            'bank_statement_image' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp,pdf', 'max:5120'],
        ];
    }

    private function vendorValidationMessages(): array
    {
        return [
            'name.regex' => 'Name can only contain letters, spaces, apostrophe, dot, and hyphen.',
            'phone.digits' => 'Mobile number must be exactly 10 digits.',
            'business_mobile.digits' => 'Business mobile must be exactly 10 digits.',
            'email.email' => 'Please enter a valid email address.',
            'pan_number.size' => 'PAN must be exactly 10 characters.',
            'pan_number.regex' => 'PAN format is invalid. Example: ABCDE1234F.',
            'aadhar_number.digits' => 'Aadhar number must be exactly 12 digits.',
            'buisness_pan.required' => 'Business PAN is required.',
            'buisness_pan.size' => 'Business PAN must be exactly 10 characters.',
            'buisness_pan.regex' => 'Business PAN format is invalid. Example: ABCDE1234F.',
            'business_pan.size' => 'Business PAN must be exactly 10 characters.',
            'business_pan.regex' => 'Business PAN format is invalid. Example: ABCDE1234F.',
            'business_name.regex' => 'Business name contains invalid characters.',
            'city.regex' => 'City can only contain letters, spaces, apostrophe, dot, and hyphen.',
            'state.regex' => 'State can only contain letters, spaces, apostrophe, dot, and hyphen.',
            'gst_number.size' => 'GST number must be exactly 15 characters.',
            'gst_number.regex' => 'GST format is invalid. Example: 22ABCDE1234F1Z5.',
            'aadhar_image.mimes' => 'Aadhar image must be PDF, JPG, JPEG, PNG, or WEBP.',
            'aadhar_image.max' => 'Aadhar image size must be less than 5MB.',
            'gst_document.mimes' => 'Aadhar image must be PDF, JPG, JPEG, PNG, or WEBP.',
            'gst_document.max' => 'Aadhar image size must be less than 5MB.',
            'profile_image.mimes' => 'Profile image must be JPG, JPEG, PNG, or WEBP.',
            'profile_image.max' => 'Profile image size must be less than 2MB.',
            'bank_account_number.regex' => 'Bank account number must be 6–20 digits.',
            'bank_ifsc_code.size' => 'IFSC code must be exactly 11 characters.',
            'bank_ifsc_code.regex' => 'IFSC format is invalid. Example: HDFC0001234.',
            'cancelled_cheque_image.mimes' => 'Cancelled cheque image must be PDF, JPG, JPEG, PNG, or WEBP.',
            'bank_account_image.mimes' => 'Bank account image must be PDF, JPG, JPEG, PNG, or WEBP.',
            'bank_statement_image.mimes' => 'Bank statement image must be PDF, JPG, JPEG, PNG, or WEBP.',
        ];
    }

    private function storeVendorUpload(Request $request, string $field): ?string
    {
        if (!$request->hasFile($field)) {
            return null;
        }
        $file = $request->file($field);
        $ext = $file->getClientOriginalExtension();
        $imageName = ImageManager::upload('vendor/', $ext, $file);

        return 'storage/vendor/' . $imageName;
    }

    private function updateVendorUpload(Request $request, string $field, ?string $existing): ?string
    {
        if (!$request->hasFile($field)) {
            return $existing;
        }
        if ($existing && file_exists(public_path($existing))) {
            unlink(public_path($existing));
        }

        return $this->storeVendorUpload($request, $field);
    }
}
