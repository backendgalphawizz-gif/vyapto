<?php



namespace App\Http\Controllers\Admin;



use App\Http\Controllers\Controller;
use App\Http\Controllers\Concerns\ExportsTabularData;

use Illuminate\Http\Request;

use App\Models\SalarySlip;

use App\Models\UserSalary;

use App\Models\EmployeeSalary;

use Illuminate\Support\Facades\DB;

use App\Models\User;

use Illuminate\Support\Facades\Validator;

use Illuminate\Validation\Rule;

use Illuminate\Support\Facades\Storage;

use Barryvdh\DomPDF\Facade\Pdf;

use Carbon\Carbon;



class SalarySlipController extends Controller

{
    use ExportsTabularData;

    public function show($id)
    {
        $slip = SalarySlip::with(['employee' => function ($q) {
            $q->select(
                'id', 'name', 'email', 'phone',
                'date_of_birth', 'join_date', 'gender', 'job_type',
                'pan_card_no', 'aadhar_card_no',
                'bank_account_no', 'ifsc_code', 'bank_name', 'bank_branch'
            );
        }])->findOrFail($id);

        return view('admin.salary_slips.slip', $this->prepareSlipViewData($slip));
    }

    public function index(Request $request)

    {

        $query = SalarySlip::query()->with('employee');



        // Search by employee name

        if ($request->filled('search')) {

            $search = $request->search;

            $query->whereHas('employee', function ($q) use ($search) {

                $q->where('name', 'like', "%{$search}%")

                  ->orWhere('email', 'like', "%{$search}%");

            });

        }



        // Filter by specific employee

        if ($request->filled('employee_id')) {

            $query->where('employee_id', $request->employee_id);

        }



        // Filter by Month

        if ($request->filled('month')) {

            $query->where('month', $request->month);

        }



        // Filter by Year

        if ($request->filled('year')) {

            $query->where('year', $request->year);

        }



        // Filter by From Date (using year and month constructed date)

        if ($request->filled('from_date')) {

            $fromDate = Carbon::parse($request->from_date)->startOfMonth();

            $query->whereRaw("STR_TO_DATE(CONCAT(year, '-', month, '-01'), '%Y-%m-%d') >= ?", [$fromDate->format('Y-m-d')]);

        }



        // Filter by To Date

        if ($request->filled('to_date')) {

            $toDate = Carbon::parse($request->to_date)->endOfMonth();

            $query->whereRaw("STR_TO_DATE(CONCAT(year, '-', month, '-01'), '%Y-%m-%d') <= ?", [$toDate->format('Y-m-d')]);

        }



        // Sorting

        if ($request->filled('sort_by') && $request->filled('sort_order')) {

            $sortBy = $request->sort_by;

            $sortOrder = $request->sort_order;

            

            if ($sortBy === 'employee_name') {

                $query->join('users', 'salary_slips.employee_id', '=', 'users.id')

                      ->orderBy('users.name', $sortOrder)

                      ->select('salary_slips.*'); // Avoid column collision

            } else {

                $query->orderBy($sortBy, $sortOrder);

            }

        } else {

            $query->orderBy('year', 'desc')->orderBy('month', 'desc');

        }



        // Export: legacy ?export=csv or ?format=pdf|csv|xlsx

        if ($request->has('export') && $request->export == 'csv') {

            return $this->exportCsv($query->get());

        }

        if ($request->filled('format')) {

            $f = strtolower((string) $request->format);

            if ($f === 'excel') {

                $f = 'xlsx';

            }

            if (in_array($f, ['csv', 'pdf', 'xlsx'], true)) {

                return $this->exportSalarySlipTable($f, $query->get());

            }

        }



        $salarySlips = $query->paginate(10)->withQueryString();

        

        $employees = User::where('role_id', "!=", 1)->orderBy('name')->get();



        return view('admin.salary_slips.index', compact('salarySlips', 'employees'));

    }



    public function store(Request $request)

    {

        $validator = Validator::make($request->all(), [

            'employee_id' => 'required|exists:users,id',

            'month' => 'required|integer|between:1,12',

            'year' => 'required|integer|min:2000|max:' . (date('Y') + 1),

            'file' => 'required|file|mimes:pdf|max:10240', // 10MB max

        ]);



        if ($validator->fails()) {

            return redirect()->back()->withErrors($validator, 'creation')->withInput();

        }



        // Check duplicate

        $exists = SalarySlip::where('employee_id', $request->employee_id)

                    ->where('month', $request->month)

                    ->where('year', $request->year)

                    ->exists();



        if ($exists) {

            return redirect()->back()->with('error', 'Salary slip for this employee/month/year already exists.')->withInput();

        }



        $filePath = null;

        if ($request->hasFile('file')) {

            $file = $request->file('file');

            $fileName = 'slip_' . $request->employee_id . '_' . $request->year . '_' . $request->month . '_' . time() . '.' . $file->getClientOriginalExtension();

            $filePath = $file->storeAs('salary_slips', $fileName, 'public');

        }



        SalarySlip::create([
            'employee_id' => $request->employee_id,
            'month' => $request->month,
            'year' => $request->year,
            'file_path' => 'storage/' . $filePath,
        ]);

        // Keep portal salary list in sync (employee_salary)
        $salaryDate = sprintf('%04d-%02d-01', (int) $request->year, (int) $request->month);
        $existingEntry = DB::table('employee_salary')
            ->where('employee_id', $request->employee_id)
            ->where('date', $salaryDate)
            ->first();

        if ($existingEntry) {
            DB::table('employee_salary')
                ->where('id', $existingEntry->id)
                ->update(['updated_at' => now()]);
        } else {
            DB::table('employee_salary')->insert([
                'employee_id' => $request->employee_id,
                'date' => $salaryDate,
                'basic_salary' => 0,
                'gross_salary' => 0,
                'net_salary' => 0,
                'total_salary' => 0,
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        return redirect()->route('salary-slips.index')->with('success', 'Salary slip created successfully.');

    }



    public function update(Request $request, $id)

    {

        $slip = SalarySlip::findOrFail($id);
        $oldEmployeeId = $slip->employee_id;
        $oldSalaryDate = $slip->year . '-' . str_pad($slip->month, 2, '0', STR_PAD_LEFT) . '-01';



        $validator = Validator::make($request->all(), [

            'employee_id' => 'required|exists:users,id',

            'month' => 'required|integer|between:1,12',

            'year' => 'required|integer|min:2000|max:' . (date('Y') + 1),

            'file' => 'nullable|file|mimes:pdf|max:10240',

        ]);



        if ($validator->fails()) {

            return redirect()->back()->withErrors($validator, 'update' . $id)->withInput();

        }



        // Check duplicate if changing month/year/employee

        $exists = SalarySlip::where('employee_id', $request->employee_id)

                    ->where('month', $request->month)

                    ->where('year', $request->year)

                    ->where('slip_id', '!=', $id)

                    ->exists();



        if ($exists) {

            return redirect()->back()->with('error', 'Salary slip for this employee/month/year already exists.')->withInput();

        }



        $filePath = $slip->file_path;

        if ($request->hasFile('file')) {

            // Delete old file

            if ($slip->file_path && file_exists(public_path($slip->file_path))) {

                unlink(public_path($slip->file_path));

            }

            

            $file = $request->file('file');

            $fileName = 'slip_' . $request->employee_id . '_' . $request->year . '_' . $request->month . '_' . time() . '.' . $file->getClientOriginalExtension();

            $storedPath = $file->storeAs('salary_slips', $fileName, 'public');

            $filePath = 'storage/' . $storedPath;

        }



        $slip->update([
            'employee_id'             => $request->employee_id,
            'month'                   => $request->month,
            'year'                    => $request->year,
            'file_path'               => $filePath,
            'basic_salary'            => $request->basic_salary            ?? $slip->basic_salary,
            'pt_value'                => $request->pt_value                ?? 0,
            'pt_type'                 => $request->pt_type                 ?? $slip->pt_type,
            'hra_value'               => $request->hra_value               ?? 0,
            'hra_type'                => $request->hra_type                ?? $slip->hra_type,
            'special_allow_value'     => $request->special_allow_value     ?? 0,
            'special_allow_type'      => $request->special_allow_type      ?? $slip->special_allow_type,
            'stat_bonus_value'        => $request->stat_bonus_value        ?? 0,
            'stat_bonus_type'         => $request->stat_bonus_type         ?? $slip->stat_bonus_type,
            'perquisite_value'        => $request->perquisite_value        ?? 0,
            'perquisite_type'         => $request->perquisite_type         ?? $slip->perquisite_type,
            'exempt_reimburse_value'  => $request->exempt_reimburse_value  ?? 0,
            'exempt_reimburse_type'   => $request->exempt_reimburse_type   ?? $slip->exempt_reimburse_type,
            'deduction_10_value'      => $request->deduction_10_value      ?? 0,
            'deduction_10_type'       => $request->deduction_10_type       ?? $slip->deduction_10_type,
            'deduction_16_value'      => $request->deduction_16_value      ?? 0,
            'deduction_16_type'       => $request->deduction_16_type       ?? $slip->deduction_16_type,
            'deduction_24_value'      => $request->deduction_24_value      ?? 0,
            'deduction_24_type'       => $request->deduction_24_type       ?? $slip->deduction_24_type,
            'deduction_via_value'     => $request->deduction_via_value     ?? 0,
            'deduction_via_type'      => $request->deduction_via_type      ?? $slip->deduction_via_type,
            'net_taxable_income'      => $request->net_taxable_income      ?? $slip->net_taxable_income,
            'total_tax_payable'       => $request->total_tax_payable       ?? $slip->total_tax_payable,
            'total_tax_recovered'     => $request->total_tax_recovered     ?? $slip->total_tax_recovered,
            'balance_tax_recoverable' => $request->balance_tax_recoverable ?? $slip->balance_tax_recoverable,
        ]);

        // Keep employee_salary in sync on slip edit/update
        $resolve = function ($value, $type, $basic) {
            return $type === '%' ? (($value ?? 0) / 100) * $basic : ($value ?? 0);
        };

        $basic = (float) ($request->basic_salary ?? $slip->basic_salary ?? 0);
        $hraAmt          = $resolve($request->hra_value,           $request->hra_type,           $basic);
        $specialAllowAmt = $resolve($request->special_allow_value, $request->special_allow_type, $basic);
        $statBonusAmt    = $resolve($request->stat_bonus_value,    $request->stat_bonus_type,    $basic);
        $perquisiteAmt   = $resolve($request->perquisite_value,    $request->perquisite_type,    $basic);
        $ptAmt           = $resolve($request->pt_value,            $request->pt_type,            $basic);

        $grossSalary = $basic + $hraAmt + $specialAllowAmt + $statBonusAmt + $perquisiteAmt;
        $netSalary   = $grossSalary - $ptAmt;
        $totalSalary = $netSalary;

        $newSalaryDate = $request->year . '-' . str_pad($request->month, 2, '0', STR_PAD_LEFT) . '-01';

        $existingEntry = DB::table('employee_salary')
            ->where('employee_id', $request->employee_id)
            ->where('date', $newSalaryDate)
            ->first();

        if ($existingEntry) {
            DB::table('employee_salary')
                ->where('id', $existingEntry->id)
                ->update([
                    'basic_salary' => $basic,
                    'gross_salary' => $grossSalary,
                    'net_salary'   => $netSalary,
                    'total_salary' => $totalSalary,
                    'status'       => 1,
                    'updated_at'   => now(),
                ]);
        } else {
            DB::table('employee_salary')->insert([
                'employee_id'  => $request->employee_id,
                'date'         => $newSalaryDate,
                'basic_salary' => $basic,
                'gross_salary' => $grossSalary,
                'net_salary'   => $netSalary,
                'total_salary' => $totalSalary,
                'status'       => 1,
                'created_at'   => now(),
                'updated_at'   => now(),
            ]);
        }

        // If employee/month/year changed, remove stale previous mapping row
        if ((int) $oldEmployeeId !== (int) $request->employee_id || $oldSalaryDate !== $newSalaryDate) {
            DB::table('employee_salary')
                ->where('employee_id', $oldEmployeeId)
                ->where('date', $oldSalaryDate)
                ->delete();
        }

        return redirect()->route('salary-slips.index')->with('success', 'Salary slip updated successfully.');

    }



    public function destroy($id)

    {

        $slip = SalarySlip::findOrFail($id);

        $salaryDate = sprintf('%04d-%02d-01', (int) $slip->year, (int) $slip->month);

        if ($slip->file_path) {
            $relative = str_starts_with($slip->file_path, 'storage/')
                ? substr($slip->file_path, strlen('storage/'))
                : ltrim($slip->file_path, '/');

            if (Storage::disk('public')->exists($relative)) {
                Storage::disk('public')->delete($relative);
            } elseif (file_exists(public_path($slip->file_path))) {
                @unlink(public_path($slip->file_path));
            }
        }

        DB::table('employee_salary')
            ->where('employee_id', $slip->employee_id)
            ->where('date', $salaryDate)
            ->delete();

        $slip->delete();

        return redirect()->route('salary-slips.index')->with('success', 'Salary slip deleted successfully.');

    }



    private function exportSalarySlipTable(string $format, $data)

    {

        $headers = ['Employee', 'Month', 'Year', 'File Path', 'Created At'];

        $rows = [];

        foreach ($data as $row) {

            $rows[] = [

                $row->employee ? $row->employee->name : 'N/A',

                date('F', mktime(0, 0, 0, (int) $row->month, 10)),

                (string) $row->year,

                $row->file_path ? (string) asset($row->file_path) : '',

                $row->created_at ? $row->created_at->format('Y-m-d H:i:s') : '',

            ];

        }

        if ($format === 'csv') {

            return $this->streamCsvDownload('salary_slips_' . now()->format('Y-m-d_His'), $headers, $rows);

        }

        if ($format === 'xlsx') {

            return $this->streamExcelTableDownload('salary_slips_' . now()->format('Y-m-d_His'), 'Salary Slips', $headers, $rows);

        }

        if (! class_exists(\Dompdf\Dompdf::class)) {

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

            . '<h2>Salary Slips</h2>'

            . '<p>Exported at: ' . e(now()->format('d M Y h:i A')) . '</p>'

            . '<table><thead><tr>'

            . '<th>Employee</th><th>Month</th><th>Year</th><th>File Path</th><th>Created At</th>'

            . '</tr></thead><tbody>';

        foreach ($data as $row) {

            $html .= '<tr>'

                . '<td>' . e($row->employee ? $row->employee->name : 'N/A') . '</td>'

                . '<td>' . e(date('F', mktime(0, 0, 0, (int) $row->month, 10))) . '</td>'

                . '<td>' . e((string) $row->year) . '</td>'

                . '<td>' . e($row->file_path ? asset($row->file_path) : '') . '</td>'

                . '<td>' . e($row->created_at ? $row->created_at->format('Y-m-d H:i:s') : '') . '</td>'

                . '</tr>';

        }

        if ($data->isEmpty()) {

            $html .= '<tr><td colspan="5" style="text-align:center;">No Data Found</td></tr>';

        }

        $html .= '</tbody></table></body></html>';

        $dompdf = new \Dompdf\Dompdf([

            'isRemoteEnabled' => true,

            'defaultFont' => 'DejaVu Sans',

        ]);

        $dompdf->loadHtml($html);

        $dompdf->setPaper('a4', 'landscape');

        $dompdf->render();

        $filename = 'salary_slips_' . now()->format('Y-m-d_His') . '.pdf';

        return response($dompdf->output(), 200, [

            'Content-Type' => 'application/pdf',

            'Content-Disposition' => 'attachment; filename="' . $filename . '"',

        ]);

    }

    private function exportCsv($data)

    {

        $fileName = 'salary_slips_' . date('Y-m-d_H-i-s') . '.csv';

        $headers = [

            "Content-type"        => "text/csv",

            "Content-Disposition" => "attachment; filename=$fileName",

            "Pragma"              => "no-cache",

            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",

            "Expires"             => "0"

        ];



        $columns = array('Employee', 'Month', 'Year', 'File Path', 'Created At');



        $callback = function() use($data, $columns) {

            $file = fopen('php://output', 'w');

            fputcsv($file, $columns);



            foreach ($data as $row) {

                fputcsv($file, array(

                    $row->employee ? $row->employee->name : 'N/A',

                    date("F", mktime(0, 0, 0, $row->month, 10)),

                    $row->year,

                    asset($row->file_path),

                    $row->created_at

                ));

            }



            fclose($file);

        };



        return response()->stream($callback, 200, $headers);

    }

    public function dynamicSalaryField(Request $request){
        return view('admin.salary_slips.dynamic_salary_field');
    }

    // ─── User Salary CRUD ────────────────────────────────────────────────────

    public function salaryIndex(Request $request)
    {
        $query = UserSalary::with('user');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('salary_type')) {
            $query->where('salary_type', $request->salary_type);
        }

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        $salaries  = $query->orderBy('created_at', 'desc')->paginate(10)->withQueryString();

        // All non-admin employees (used in filter bar)
        $employees = User::where('role_id', '!=', 1)->orderBy('name')->get();

        // User IDs that already have a salary record (used to block duplicates in modals)
        $takenUserIds = UserSalary::pluck('user_id')->toArray();

        // Employees with NO salary record yet (used in create modal)
        $availableEmployees = $employees->whereNotIn('id', $takenUserIds);

        return view('admin.salary_slips.salary', compact('salaries', 'employees', 'takenUserIds', 'availableEmployees'));
    }

    public function salaryStore(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id'        => 'required|exists:users,id',
            'salary_amount'  => 'required|numeric|min:0',
            'salary_type'    => 'required|in:monthly,weekly,daily',
            'effective_from' => 'nullable|date',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator, 'salary_create')->withInput();
        }

        // Block duplicate: one salary record per user
        if (UserSalary::where('user_id', $request->user_id)->exists()) {
            return redirect()->back()
                ->withErrors(['user_id' => 'This employee already has a salary record.'], 'salary_create')
                ->withInput();
        }

        UserSalary::create([
            'user_id'        => $request->user_id,
            'salary_amount'  => $request->salary_amount,
            'salary_type'    => $request->salary_type,
            'effective_from' => $request->effective_from ?: null,
        ]);

        return redirect()->route('user-salaries.index')->with('success', 'Salary record created successfully.');
    }

    public function salaryUpdate(Request $request, $id)
    {
        $salary = UserSalary::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'user_id'        => 'required|exists:users,id',
            'salary_amount'  => 'required|numeric|min:0',
            'salary_type'    => 'required|in:monthly,weekly,daily',
            'effective_from' => 'nullable|date',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator, 'salary_update' . $id)->withInput();
        }

        // Block duplicate: if changing to a user who already has a different salary record
        if ((int) $request->user_id !== (int) $salary->user_id &&
            UserSalary::where('user_id', $request->user_id)->exists()) {
            return redirect()->back()
                ->withErrors(['user_id' => 'This employee already has a salary record.'], 'salary_update' . $id)
                ->withInput();
        }

        $salary->update([
            'user_id'        => $request->user_id,
            'salary_amount'  => $request->salary_amount,
            'salary_type'    => $request->salary_type,
            'effective_from' => $request->effective_from ?: null,
        ]);

        // Align only current/future month slips — do not rewrite historical payslips.
        $now = now();
        SalarySlip::where('employee_id', $request->user_id)
            ->where(function ($q) use ($now) {
                $q->where('year', '>', $now->year)
                    ->orWhere(function ($q2) use ($now) {
                        $q2->where('year', $now->year)->where('month', '>=', $now->month);
                    });
            })
            ->update([
                'basic_salary' => (float) $request->salary_amount,
            ]);

        return redirect()->route('user-salaries.index')->with('success', 'Salary record updated successfully.');
    }

    public function salaryDestroy($id)
    {
        $salary = UserSalary::findOrFail($id);
        $salary->delete();

        return redirect()->route('user-salaries.index')->with('success', 'Salary record deleted successfully.');
    }

    public function generateSlip(Request $request, $userSalaryId)
    {
        $userSalary = UserSalary::with('user')->findOrFail($userSalaryId);

        $validator = Validator::make($request->all(), [
            'month'                   => 'required|integer|between:1,12',
            'year'                    => 'required|integer|min:2000|max:' . (date('Y') + 1),
            'pt_value'                => 'nullable|numeric|min:0',
            'pt_type'                 => 'required|in:%,fixed',
            'hra_value'               => 'nullable|numeric|min:0',
            'hra_type'                => 'required|in:%,fixed',
            'special_allow_value'     => 'nullable|numeric|min:0',
            'special_allow_type'      => 'required|in:%,fixed',
            'stat_bonus_value'        => 'nullable|numeric|min:0',
            'stat_bonus_type'         => 'required|in:%,fixed',
            'perquisite_value'        => 'nullable|numeric|min:0',
            'perquisite_type'         => 'required|in:%,fixed',
            'exempt_reimburse_value'  => 'nullable|numeric|min:0',
            'exempt_reimburse_type'   => 'required|in:%,fixed',
            'deduction_10_value'      => 'nullable|numeric|min:0',
            'deduction_10_type'       => 'required|in:%,fixed',
            'deduction_16_value'      => 'nullable|numeric|min:0',
            'deduction_16_type'       => 'required|in:%,fixed',
            'deduction_24_value'      => 'nullable|numeric|min:0',
            'deduction_24_type'       => 'required|in:%,fixed',
            'deduction_via_value'     => 'nullable|numeric|min:0',
            'deduction_via_type'      => 'required|in:%,fixed',
            'net_taxable_income'      => 'nullable|numeric|min:0',
            'total_tax_payable'       => 'nullable|numeric|min:0',
            'total_tax_recovered'     => 'nullable|numeric|min:0',
            'balance_tax_recoverable' => 'nullable|numeric',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator, 'generate_slip_' . $userSalaryId)
                ->withInput();
        }

        // Prevent duplicate slip for same employee/month/year
        $exists = SalarySlip::where('employee_id', $userSalary->user_id)
            ->where('month', $request->month)
            ->where('year', $request->year)
            ->exists();

        if ($exists) {
            return redirect()->back()
                ->with('error', 'A salary slip for this employee and month/year already exists.')
                ->withInput();
        }

        // ── Helper: resolve actual amount ──────────────────────────────────
        $resolve = function ($value, $type, $basic) {
            return $type === '%' ? (($value ?? 0) / 100) * $basic : ($value ?? 0);
        };

        $basic = (float) $userSalary->salary_amount;

        $hraAmt           = $resolve($request->hra_value,              $request->hra_type,              $basic);
        $specialAllowAmt  = $resolve($request->special_allow_value,    $request->special_allow_type,    $basic);
        $statBonusAmt     = $resolve($request->stat_bonus_value,       $request->stat_bonus_type,       $basic);
        $perquisiteAmt    = $resolve($request->perquisite_value,       $request->perquisite_type,       $basic);
        $ptAmt            = $resolve($request->pt_value,               $request->pt_type,               $basic);

        $grossSalary = $basic + $hraAmt + $specialAllowAmt + $statBonusAmt + $perquisiteAmt;
        $netSalary   = $grossSalary - $ptAmt;
        $totalSalary = $netSalary;

        DB::beginTransaction();

        try {
            $slip = SalarySlip::create([
                'employee_id'             => $userSalary->user_id,
                'month'                   => $request->month,
                'year'                    => $request->year,
                'basic_salary'            => $basic,
                'file_path'               => '',
                'pt_value'                => $request->pt_value ?? 0,
                'pt_type'                 => $request->pt_type,
                'hra_value'               => $request->hra_value ?? 0,
                'hra_type'                => $request->hra_type,
                'special_allow_value'     => $request->special_allow_value ?? 0,
                'special_allow_type'      => $request->special_allow_type,
                'stat_bonus_value'        => $request->stat_bonus_value ?? 0,
                'stat_bonus_type'         => $request->stat_bonus_type,
                'perquisite_value'        => $request->perquisite_value ?? 0,
                'perquisite_type'         => $request->perquisite_type,
                'exempt_reimburse_value'  => $request->exempt_reimburse_value ?? 0,
                'exempt_reimburse_type'   => $request->exempt_reimburse_type,
                'deduction_10_value'      => $request->deduction_10_value ?? 0,
                'deduction_10_type'       => $request->deduction_10_type,
                'deduction_16_value'      => $request->deduction_16_value ?? 0,
                'deduction_16_type'       => $request->deduction_16_type,
                'deduction_24_value'      => $request->deduction_24_value ?? 0,
                'deduction_24_type'       => $request->deduction_24_type,
                'deduction_via_value'     => $request->deduction_via_value ?? 0,
                'deduction_via_type'      => $request->deduction_via_type,
                'net_taxable_income'      => $request->net_taxable_income ?? 0,
                'total_tax_payable'       => $request->total_tax_payable ?? 0,
                'total_tax_recovered'     => $request->total_tax_recovered ?? 0,
                'balance_tax_recoverable' => $request->balance_tax_recoverable ?? 0,
            ]);

            $slip->load(['employee' => function ($q) {
                $q->select(
                    'id', 'name', 'email', 'phone',
                    'date_of_birth', 'join_date', 'gender', 'job_type',
                    'pan_card_no', 'aadhar_card_no',
                    'bank_account_no', 'ifsc_code', 'bank_name', 'bank_branch'
                );
            }]);

            $slip->update([
                'file_path' => $this->storeSlipPdf($slip),
            ]);

        // ── Insert / update employee_salary record ──────────────────────────
        $salaryDate = $request->year . '-' . str_pad($request->month, 2, '0', STR_PAD_LEFT) . '-01';

        $existingEntry = DB::table('employee_salary')
            ->where('employee_id', $userSalary->user_id)
            ->where('date', $salaryDate)
            ->first();

        if ($existingEntry) {
            DB::table('employee_salary')
                ->where('id', $existingEntry->id)
                ->update([
                    'basic_salary' => $basic,
                    'gross_salary' => $grossSalary,
                    'net_salary'   => $netSalary,
                    'total_salary' => $totalSalary,
                    'status'       => 1,
                    'updated_at'   => now(),
                ]);
        } else {
            DB::table('employee_salary')->insert([
                'employee_id'  => $userSalary->user_id,
                'date'         => $salaryDate,
                'basic_salary' => $basic,
                'gross_salary' => $grossSalary,
                'net_salary'   => $netSalary,
                'total_salary' => $totalSalary,
                'status'       => 1,
                'created_at'   => now(),
                'updated_at'   => now(),
            ]);
        }

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();

            return redirect()->back()
                ->with('error', 'Failed to generate salary slip: '.$e->getMessage())
                ->withInput();
        }

        return redirect()->route('user-salaries.index')
            ->with('success', 'Salary slip generated successfully for ' . $userSalary->user->name . '.');
    }

    private function prepareSlipViewData(SalarySlip $slip): array
    {
        if ($slip->employee) {
            $slip->employee->makeVisible([
                'bank_account_no', 'ifsc_code', 'bank_name', 'bank_branch',
                'pan_card_no', 'aadhar_card_no', 'phone', 'date_of_birth',
                'join_date', 'gender', 'job_type',
            ]);
        }

        $resolve = function ($value, $type, $basic) {
            return $type === '%' ? (($value ?? 0) / 100) * $basic : ($value ?? 0);
        };

        $userSalary = UserSalary::where('user_id', $slip->employee_id)->first();
        $basic = (float) ($userSalary->salary_amount ?? $slip->basic_salary);
        $salaryType = $userSalary->salary_type ?? 'monthly';

        $salaryLabels = match ($salaryType) {
            'weekly' => [
                'title_period' => 'WEEK',
                'col_base' => 'WEEKLY<br>SALARY',
                'col_current' => 'CURRENT<br>WEEK',
                'tax_curr' => 'CURR WEEK',
            ],
            'daily' => [
                'title_period' => 'DAY',
                'col_base' => 'DAILY<br>SALARY',
                'col_current' => 'CURRENT<br>DAY',
                'tax_curr' => 'CURR DAY',
            ],
            default => [
                'title_period' => 'MONTH',
                'col_base' => 'MONTHLY<br>SALARY',
                'col_current' => 'CURRENT<br>MONTH',
                'tax_curr' => 'CURR MONTH',
            ],
        };

        $earnings = [
            'basic'          => $basic,
            'hra'            => $resolve($slip->hra_value, $slip->hra_type, $basic),
            'special_allow'  => $resolve($slip->special_allow_value, $slip->special_allow_type, $basic),
            'stat_bonus'     => $resolve($slip->stat_bonus_value, $slip->stat_bonus_type, $basic),
            'perquisite'     => $resolve($slip->perquisite_value, $slip->perquisite_type, $basic),
        ];

        $earnings['gross'] = $earnings['basic'] + $earnings['hra']
            + $earnings['special_allow'] + $earnings['stat_bonus'] + $earnings['perquisite'];

        $deductions = [
            'pt' => $resolve($slip->pt_value, $slip->pt_type, $basic),
        ];
        $deductions['total'] = array_sum($deductions);

        $netPay = $earnings['gross'] - $deductions['total'];

        $taxLines = [
            'exempt_reimburse' => $resolve($slip->exempt_reimburse_value, $slip->exempt_reimburse_type, $basic),
            'deduction_10'     => $resolve($slip->deduction_10_value, $slip->deduction_10_type, $basic),
            'deduction_16'     => $resolve($slip->deduction_16_value, $slip->deduction_16_type, $basic),
            'deduction_24'     => $resolve($slip->deduction_24_value, $slip->deduction_24_type, $basic),
            'deduction_via'    => $resolve($slip->deduction_via_value, $slip->deduction_via_type, $basic),
        ];

        return compact(
            'slip',
            'earnings',
            'deductions',
            'netPay',
            'taxLines',
            'salaryLabels',
            'salaryType'
        );
    }

    private function storeSlipPdf(SalarySlip $slip): string
    {
        $viewData = $this->prepareSlipViewData($slip);
        $viewData['forPdf'] = true;

        $fileName = sprintf(
            'slip_%d_%d_%02d_%s.pdf',
            $slip->employee_id,
            $slip->year,
            $slip->month,
            time()
        );

        $relativePath = 'salary_slips/'.$fileName;

        if (! Storage::disk('public')->exists('salary_slips')) {
            Storage::disk('public')->makeDirectory('salary_slips');
        }

        $pdf = Pdf::loadView('admin.salary_slips.slip', $viewData)->setPaper('a4', 'portrait');
        Storage::disk('public')->put($relativePath, $pdf->output());

        return 'storage/'.$relativePath;
    }

}

