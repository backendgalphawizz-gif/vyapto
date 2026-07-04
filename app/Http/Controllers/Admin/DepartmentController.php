<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Concerns\ExportsTabularData;
use Illuminate\Http\Request;
use App\Models\Department;
use Illuminate\Support\Facades\Validator;

class DepartmentController extends Controller
{
    use ExportsTabularData;

    public function __construct()
    {
        // $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $query = Department::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('name', 'like', "%{$search}%");
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Sorting Logic
        if ($request->filled('sort_by') && $request->filled('sort_order')) {
            $sortBy = $request->sort_by;
            $sortOrder = $request->sort_order;
            
            if (in_array($sortBy, ['name', 'status', 'created_at'])) {
                $query->orderBy($sortBy, $sortOrder);
            } else {
                $query->orderBy('created_at', 'desc');
            }
        } else {
            $query->orderBy('created_at', 'desc');
        }

        $departments = $query->paginate(10)->withQueryString();

        return view('admin.departments.index', compact('departments'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:departments,name',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator, 'departmentCreation')->withInput();
        }

        Department::create([
            'name' => $request->name,
            'status' => 1,
        ]);

        return redirect()->route('departments.index')->with('success', 'Department created successfully.');
    }

    public function update(Request $request, Department $department)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:departments,name,' . $department->id,
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator, 'departmentUpdate' . $department->id)->withInput();
        }

        $department->update([
            'name' => $request->name,
        ]);

        return redirect()->route('departments.index')->with('success', 'Department updated successfully.');
    }

    public function updateStatus(Request $request)
    {
        $department = Department::find($request->id);
        if ($department) {
            $department->status = $request->status;
            $department->save();
            return response()->json(['success' => 'Status updated successfully.']);
        }
        return response()->json(['error' => 'Department not found.'], 404);
    }

    public function destroy(Department $department)
    {
        // Check if department has users
        if ($department->users()->exists()) {
            return redirect()->route('departments.index')->with('error', 'Cannot delete department with assigned employees.');
        }
        
        $department->delete();
        return redirect()->route('departments.index')->with('success', 'Department deleted successfully.');
    }

    public function export(Request $request)
    {
        $query = Department::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('name', 'like', "%{$search}%");
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $departments = $query->orderBy('created_at', 'desc')->get();

        $format = $this->exportFormat($request);
        $headers = ['ID', 'Department Name', 'Status', 'Created At'];
        $rows = [];
        foreach ($departments as $department) {
            $rows[] = [
                (string) $department->id,
                (string) ($department->name ?? '-'),
                ((int) $department->status === 1) ? 'Active' : 'Inactive',
                (string) optional($department->created_at)->format('d M Y h:i A'),
            ];
        }
        if ($format === 'csv') {
            return $this->streamCsvDownload('departments_' . now()->format('Y-m-d_His'), $headers, $rows);
        }
        if ($format === 'xlsx') {
            return $this->streamExcelTableDownload('departments_' . now()->format('Y-m-d_His'), 'Department Records', $headers, $rows);
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
            . '<h2>Department Records</h2>'
            . '<p>Exported at: ' . e(now()->format('d M Y h:i A')) . '</p>'
            . '<table><thead><tr>'
            . '<th>ID</th><th>Department Name</th><th>Status</th><th>Created At</th>'
            . '</tr></thead><tbody>';

        foreach ($departments as $department) {
            $html .= '<tr>'
                . '<td>' . e((string) $department->id) . '</td>'
                . '<td>' . e((string) ($department->name ?? '-')) . '</td>'
                . '<td>' . e((int) $department->status === 1 ? 'Active' : 'Inactive') . '</td>'
                . '<td>' . e((string) optional($department->created_at)->format('d M Y h:i A')) . '</td>'
                . '</tr>';
        }

        if ($departments->isEmpty()) {
            $html .= '<tr><td colspan="4" style="text-align:center;">No Data Found</td></tr>';
        }

        $html .= '</tbody></table></body></html>';

        $dompdf = new \Dompdf\Dompdf([
            'isRemoteEnabled' => true,
            'defaultFont' => 'DejaVu Sans',
        ]);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('a4', 'landscape');
        $dompdf->render();

        $filename = 'departments_' . now()->format('Y-m-d_His') . '.pdf';
        return response($dompdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }
}
