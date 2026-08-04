<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Concerns\ExportsTabularData;
use App\Http\Controllers\Controller;
use App\Models\Designation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DesignationController extends Controller
{
    use ExportsTabularData;

    public function index(Request $request)
    {
        $query = Designation::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('name', 'like', "%{$search}%");
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

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

        $designations = $query->paginate(10)->withQueryString();

        return view('admin.designations.index', compact('designations'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:designations,name',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator, 'designationCreation')->withInput();
        }

        Designation::create([
            'name' => $request->name,
            'status' => 1,
        ]);

        return redirect()->route('designations.index')->with('success', 'Designation created successfully.');
    }

    public function update(Request $request, Designation $designation)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:designations,name,' . $designation->id,
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator, 'designationUpdate' . $designation->id)->withInput();
        }

        $designation->update([
            'name' => $request->name,
        ]);

        return redirect()->route('designations.index')->with('success', 'Designation updated successfully.');
    }

    public function updateStatus(Request $request)
    {
        $designation = Designation::find($request->id);
        if ($designation) {
            $designation->status = $request->status;
            $designation->save();

            return response()->json(['success' => 'Status updated successfully.']);
        }

        return response()->json(['error' => 'Designation not found.'], 404);
    }

    public function destroy(Designation $designation)
    {
        if ($designation->users()->exists()) {
            return redirect()->route('designations.index')->with('error', 'Cannot delete designation assigned to employees.');
        }

        $designation->delete();

        return redirect()->route('designations.index')->with('success', 'Designation deleted successfully.');
    }

    public function export(Request $request)
    {
        $query = Designation::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('name', 'like', "%{$search}%");
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $designations = $query->orderBy('created_at', 'desc')->get();

        $format = $this->exportFormat($request);
        $headers = ['ID', 'Designation Name', 'Status', 'Created At'];
        $rows = [];
        foreach ($designations as $designation) {
            $rows[] = [
                (string) $designation->id,
                (string) ($designation->name ?? '-'),
                ((int) $designation->status === 1) ? 'Active' : 'Inactive',
                (string) optional($designation->created_at)->format('d M Y h:i A'),
            ];
        }

        if ($format === 'csv') {
            return $this->streamCsvDownload('designations_' . now()->format('Y-m-d_His'), $headers, $rows);
        }
        if ($format === 'xlsx') {
            return $this->streamExcelTableDownload('designations_' . now()->format('Y-m-d_His'), 'Designation Records', $headers, $rows);
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
            . '<h2>Designation Records</h2>'
            . '<p>Exported at: ' . e(now()->format('d M Y h:i A')) . '</p>'
            . '<table><thead><tr>'
            . '<th>ID</th><th>Designation Name</th><th>Status</th><th>Created At</th>'
            . '</tr></thead><tbody>';

        foreach ($designations as $designation) {
            $html .= '<tr>'
                . '<td>' . e((string) $designation->id) . '</td>'
                . '<td>' . e((string) ($designation->name ?? '-')) . '</td>'
                . '<td>' . e((int) $designation->status === 1 ? 'Active' : 'Inactive') . '</td>'
                . '<td>' . e((string) optional($designation->created_at)->format('d M Y h:i A')) . '</td>'
                . '</tr>';
        }

        if ($designations->isEmpty()) {
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

        $filename = 'designations_' . now()->format('Y-m-d_His') . '.pdf';

        return response($dompdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }
}
