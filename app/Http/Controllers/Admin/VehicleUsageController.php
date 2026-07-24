<?php
// app/Http/Controllers/Admin/VehicleUsageController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Concerns\ExportsTabularData;
use App\Models\VehicleUsage;
use App\Models\User;
use App\Support\StaffRoles;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class VehicleUsageController extends Controller
{
    use ExportsTabularData;

    private function staffUserRule()
    {
        return Rule::exists('users', 'id')->whereIn('role_id', StaffRoles::assignableIds());
    }

    /**
     * Display a listing of vehicle usage records.
     */
    public function index(Request $request)
    {
        $query = VehicleUsage::with('user');

        // Filter by vehicle number
        if ($request->has('vehicle_number') && $request->vehicle_number != '') {
            $query->where('vehicle_number', 'like', '%' . $request->vehicle_number . '%');
        }

        // Filter by user
        if ($request->has('user_id') && $request->user_id != '') {
            $query->where('user_id', $request->user_id);
        }

        // Filter by date range
        if ($request->has('from_date') && $request->from_date != '') {
            $query->whereDate('created_at', '>=', $request->from_date);
        }
        if ($request->has('to_date') && $request->to_date != '') {
            $query->whereDate('created_at', '<=', $request->to_date);
        }

        $vehicleUsages = $query->orderBy('created_at', 'desc')->paginate(15);

        // Get users for filter
        $users = User::orderBy('name')->get();

        $todayKmSummary = VehicleUsage::todayFirstSecondKmSummary();

        $indexView = view()->exists('admin.vehicle-usage.index')
            ? 'admin.vehicle-usage.index'
            : 'admin.vehicle-usage';

        return view($indexView, compact('vehicleUsages', 'users', 'todayKmSummary'));
    }

    /**
     * Show the form for creating a new vehicle usage record.
     */
    public function create()
    {
        $users = StaffRoles::employeesQuery()->get();
        $assignableRoleIds = StaffRoles::assignableIds();
        return view('admin.vehicle-usage.create', compact('users', 'assignableRoleIds'));
    }

    /**
     * Store a newly created vehicle usage record in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'vehicle_number' => 'required|string|max:50',
            'user_id' => ['required', $this->staffUserRule()],
            'kms' => 'nullable|numeric|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        try {
            // Handle image upload
            if ($request->hasFile('image')) {
                $imagePath = $request->file('image')->store('vehicle-usage', 'public');
                $validated['image'] = $imagePath;
            }

            VehicleUsage::create($validated);

            return redirect()->route('admin.vehicle-usage.index')
                ->with('success', 'Vehicle usage record created successfully.');
                
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to create record. ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified vehicle usage record.
     */
    public function show(VehicleUsage $vehicleUsage)
    {
        $vehicleUsage->load('user');
        return view('admin.vehicle-usage.show', compact('vehicleUsage'));
    }

    /**
     * Show the form for editing the specified vehicle usage record.
     */
    public function edit(VehicleUsage $vehicleUsage)
    {
        $users = StaffRoles::employeesQuery()->get();
        $assignableRoleIds = StaffRoles::assignableIds();
        return view('admin.vehicle-usage.edit', compact('vehicleUsage', 'users', 'assignableRoleIds'));
    }

    /**
     * Update the specified vehicle usage record in storage.
     */
    public function update(Request $request, VehicleUsage $vehicleUsage)
    {
        $validated = $request->validate([
            'vehicle_number' => 'required|string|max:50',
            'user_id' => ['required', $this->staffUserRule()],
            'kms' => 'nullable|numeric|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        try {
            // Handle image upload
            if ($request->hasFile('image')) {
                // Delete old image if exists
                if ($vehicleUsage->image) {
                    Storage::disk('public')->delete($vehicleUsage->image);
                }
                
                $imagePath = $request->file('image')->store('vehicle-usage', 'public');
                $validated['image'] = $imagePath;
            }

            $vehicleUsage->update($validated);

            return redirect()->route('admin.vehicle-usage.index')
                ->with('success', 'Vehicle usage record updated successfully.');
                
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to update record. ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified vehicle usage record from storage.
     */
    public function destroy(VehicleUsage $vehicleUsage)
    {
        try {
            // Delete image if exists
            if ($vehicleUsage->image) {
                Storage::disk('public')->delete($vehicleUsage->image);
            }
            
            $vehicleUsage->delete();

            return redirect()->route('admin.vehicle-usage.index')
                ->with('success', 'Vehicle usage record deleted successfully.');
                
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to delete record.');
        }
    }

    /**
     * Today’s KM summary (1st / 2nd entry per vehicle + user).
     * Browser: HTML page. JSON: ?format=json, or Accept: application/json, or expectsJson().
     * Optional query: date=Y-m-d (defaults to today).
     */
    public function todayKmSummary(Request $request)
    {
        $date = $request->filled('date')
            ? Carbon::parse($request->query('date'))->startOfDay()
            : Carbon::today();

        $summary = VehicleUsage::todayFirstSecondKmSummary($date);

        $vehicles = $summary->map(function ($row) {
            return [
                'vehicle_number' => $row->vehicle_number,
                'user_id' => $row->user_id,
                'user_name' => $row->user_name,
                'start_km' => $row->start_km,
                'end_km' => $row->end_km,
                'difference_km' => $row->difference_km,
                'first_entry_at' => $row->first_entry_at?->format('c'),
                'second_entry_at' => $row->second_entry_at?->format('c'),
            ];
        })->values();

        $wantsJson = $request->query('format') === 'json'
            || $request->wantsJson()
            || $request->expectsJson();

        if ($wantsJson) {
            return response()->json([
                'date' => $date->toDateString(),
                'vehicles' => $vehicles,
            ]);
        }

        $viewData = [
            'summaryDate' => $date,
            'todayKmSummary' => $summary,
        ];

        foreach ([
            'admin.vehicles.today-km-summary',
            'today-km-summary',
            'admin.vehicle-usage.today-km-summary',
            'admin.today-km-summary',
        ] as $viewName) {
            if (view()->exists($viewName)) {
                return view($viewName, $viewData);
            }
        }

        foreach ([
            resource_path('views/admin/vehicles/today-km-summary.blade.php'),
            resource_path('views/today-km-summary.blade.php'),
            resource_path('views/admin/vehicle-usage/today-km-summary.blade.php'),
            resource_path('views/admin/today-km-summary.blade.php'),
        ] as $path) {
            if (is_file($path)) {
                return view()->file($path, $viewData);
            }
        }

        return redirect()
            ->route('admin.vehicle-usage.index')
            ->with(
                'error',
                'Today’s KM summary view not found. Create resources/views/admin/vehicles/today-km-summary.blade.php (view: admin.vehicles.today-km-summary), then run: php artisan view:clear'
            );
    }

    /**
     * Export vehicle usage records to PDF.
     */
    public function export(Request $request)
    {
        $query = VehicleUsage::with('user');

        if ($request->has('vehicle_number') && $request->vehicle_number != '') {
            $query->where('vehicle_number', 'like', '%' . $request->vehicle_number . '%');
        }
        if ($request->has('user_id') && $request->user_id != '') {
            $query->where('user_id', $request->user_id);
        }
        if ($request->has('from_date') && $request->from_date != '') {
            $query->whereDate('created_at', '>=', $request->from_date);
        }
        if ($request->has('to_date') && $request->to_date != '') {
            $query->whereDate('created_at', '<=', $request->to_date);
        }

        $vehicleUsages = $query->orderBy('created_at', 'desc')->get();

        $format = $this->exportFormat($request);
        $headers = ['ID', 'Vehicle Number', 'Driver', 'KMs Driven', 'Usage Date'];
        $rows = [];
        foreach ($vehicleUsages as $usage) {
            $rows[] = [
                (string) $usage->id,
                (string) $usage->vehicle_number,
                (string) ($usage->user->name ?? 'N/A'),
                $usage->kms !== null ? number_format((float) $usage->kms, 2) . ' km' : 'N/A',
                $usage->created_at ? $usage->created_at->format('d M Y, h:i A') : 'N/A',
            ];
        }
        if ($format === 'csv') {
            return $this->streamCsvDownload('vehicle_usage_' . now()->format('Y-m-d_His'), $headers, $rows);
        }
        if ($format === 'xlsx') {
            return $this->streamExcelTableDownload('vehicle_usage_' . now()->format('Y-m-d_His'), 'Vehicle Usage Records', $headers, $rows);
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
            . '<h2>Vehicle Usage Records</h2>'
            . '<p>Exported at: ' . e(now()->format('d M Y h:i A')) . '</p>'
            . '<table><thead><tr>'
            . '<th>ID</th><th>Vehicle Number</th><th>Driver</th><th>KMs Driven</th><th>Usage Date</th>'
            . '</tr></thead><tbody>';

        foreach ($vehicleUsages as $usage) {
            $html .= '<tr>'
                . '<td>' . e((string) $usage->id) . '</td>'
                . '<td>' . e((string) $usage->vehicle_number) . '</td>'
                . '<td>' . e((string) ($usage->user->name ?? 'N/A')) . '</td>'
                . '<td>' . e($usage->kms !== null ? number_format((float) $usage->kms, 2) . ' km' : 'N/A') . '</td>'
                . '<td>' . e($usage->created_at ? $usage->created_at->format('d M Y, h:i A') : 'N/A') . '</td>'
                . '</tr>';
        }

        if ($vehicleUsages->isEmpty()) {
            $html .= '<tr><td colspan="5" style="text-align:center;">No vehicle usage records found</td></tr>';
        }

        $html .= '</tbody></table></body></html>';

        $dompdf = new \Dompdf\Dompdf([
            'isRemoteEnabled' => true,
            'defaultFont' => 'DejaVu Sans',
        ]);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('a4', 'landscape');
        $dompdf->render();

        $filename = 'vehicle_usage_' . now()->format('Y-m-d_His') . '.pdf';
        return response($dompdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }
}