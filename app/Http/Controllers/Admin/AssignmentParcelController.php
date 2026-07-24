<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Concerns\ExportsTabularData;
use App\Models\AssignmentParcel;
use App\Models\ParcelDetail;
use App\Models\Vendor;
use App\Models\Vehicle;
use App\Models\User;
use App\Models\Hub;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;
use App\Services\FcmNotificationService;
use Illuminate\Support\Facades\Log;
use Spatie\Permission\Models\Role;

class AssignmentParcelController extends Controller
{
    use ExportsTabularData;

    private $fcmNotificationService;

    /**
     * Role IDs allowed in the Staff dropdown (assignments).
     * Prefer role names so it still works if IDs differ across environments.
     */
    private function assignableStaffRoleIds(): array
    {
        $ids = Role::query()
            ->where(function ($q) {
                $q->where('name', 'Staff Employee')
                    ->orWhere('name', 'like', '%Driver%');
            })
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->all();

        // Legacy fallback used across the app
        if (empty($ids)) {
            $ids = [3];
        }

        return array_values(array_unique($ids));
    }

    private function assignableStaffQuery(bool $excludeAlreadyAssigned = false)
    {
        $query = User::query()
            ->whereIn('role_id', $this->assignableStaffRoleIds())
            ->where(function ($q) {
                $q->where('status', 1)->orWhereNull('status');
            })
            ->orderBy('name');

        if ($excludeAlreadyAssigned) {
            $query->whereNotIn('id', function ($sub) {
                $sub->select('user_id')
                    ->from('parcel_detail')
                    ->where('status', 'assigned');
            });
        }

        return $query;
    }

    public function __construct(FcmNotificationService $fcmNotificationService)
    {
        $this->fcmNotificationService = $fcmNotificationService;
    }

    /**
     * Display a listing of assignment parcels.
     */
    public function index(Request $request)
    {
        $query = AssignmentParcel::with(['vendor', 'vehicle', 'user', 'hub']);

        // Filter by status
        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }

        // Filter by hub
        if ($request->has('hub_id') && $request->hub_id != '') {
            $query->where('hub_id', $request->hub_id);
        }

        // Filter by date range
        if ($request->has('from_date') && $request->from_date != '') {
            $query->whereDate('assignment_date', '>=', $request->from_date);
        }
        if ($request->has('to_date') && $request->to_date != '') {
            $query->whereDate('assignment_date', '<=', $request->to_date);
        }

        // Search by vendor name or vehicle number
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->whereHas('vendor', function($vendor) use ($search) {
                    $vendor->where('name', 'like', "%{$search}%");
                })->orWhereHas('vehicle', function($vehicle) use ($search) {
                    $vehicle->where('vehicle_number', 'like', "%{$search}%");
                })->orWhereHas('user', function($user) use ($search) {
                    $user->where('name', 'like', "%{$search}%");
                });
            });
        }

        $assignments = $query->orderBy('created_at', 'desc')->paginate(15);
        
        // Get data for filters
        $hubs = Hub::orderBy('name')->get();
        $statuses = AssignmentParcel::getStatuses();

        return view('admin.assignment-parcel.index', compact('assignments', 'hubs', 'statuses'));
    }

    /**
     * Show the form for creating a new assignment parcel.
     */
    public function create()
    {
        $vendors = Vendor::orderBy('name')->get();
        $vehicleOwnerColumn = $this->getVehicleOwnerColumn();
        $vehicleVendorColumn = $this->getVehicleVendorColumn();
        $vehicles = Vehicle::orderBy('vehicle_number')->get();
        $users = $this->assignableStaffQuery(excludeAlreadyAssigned: true)->get();
       
        $hubs = Hub::orderBy('name')->get();
        $statuses = AssignmentParcel::getStatuses();

        return view('admin.assignment-parcel.create', compact('vendors', 'vehicles', 'users', 'hubs', 'statuses', 'vehicleOwnerColumn', 'vehicleVendorColumn'));
    }

    /**
     * Store a newly created assignment parcel in storage.
     */
    public function store(Request $request)
    {
        $vehicleVendorColumn = $this->getVehicleVendorColumn();

        $validated = $request->validate([
            'vendor_id' => 'required|exists:vendors,id',
            'vehicle_id' => [
                'required',
                'exists:vehicles,id',
                function ($attribute, $value, $fail) use ($request, $vehicleVendorColumn) {
                    if (!$vehicleVendorColumn) {
                        return;
                    }

                    $vehicleVendorId = DB::table('vehicles')
                        ->where('id', $value)
                        ->value($vehicleVendorColumn);

                    if ((string) $vehicleVendorId !== (string) $request->input('vendor_id')) {
                        $fail('Selected vehicle does not belong to selected vendor.');
                    }
                },
            ],
            'user_id' => ['required', Rule::exists('users', 'id')->whereIn('role_id', $this->assignableStaffRoleIds())],
            'hub_id' => 'required|exists:hubs,id',
            'parcel_quantity' => 'required|integer|min:1',
            'parcel_ids' => [
                'required',
                'array',
                'size:' . ($request->input('parcel_quantity') ?? 0),
                function ($attribute, $value, $fail) {
                    if (!is_array($value)) {
                        return;
                    }
                    
                    // Check for empty values
                    foreach ($value as $id) {
                        if (empty(trim($id))) {
                            $fail('All parcel IDs are required.');
                            return;
                        }
                    }
                    
                    // Check for duplicates within this request
                    $unique = array_unique($value);
                    if (count($unique) !== count($value)) {
                        $fail('Parcel IDs must be unique. Please ensure no duplicate IDs.');
                        return;
                    }
                    
                    // Check if any parcel ID already exists in database
                    $existingIds = ParcelDetail::whereIn('parcel_id', $value)->pluck('parcel_id')->toArray();
                    if (!empty($existingIds)) {
                        $fail('The following parcel IDs already exist: ' . implode(', ', $existingIds));
                    }
                },
            ],
            'parcel_ids.*' => 'required|string|min:1|max:100',
            'assignment_date' => 'required|date|after_or_equal:today',
            'status' => 'nullable|in:pending,assigned,in_transit,delivered,cancelled',
            'notes' => 'nullable|string|max:1000',
        ], [
            'vendor_id.required' => 'Vendor field is required.',
            'vehicle_id.required' => 'Vehicle field is required.',
            'user_id.required' => 'Staff field is required.',
            'hub_id.required' => 'Hub field is required.',
            'parcel_quantity.required' => 'Parcel quantity field is required.',
            'parcel_ids.required' => 'Parcel IDs field is required.',
            'parcel_ids.*.required' => 'Parcel ID field is required.',
            'assignment_date.required' => 'Assignment date field is required.',
            'assignment_date.after_or_equal' => 'Assignment date cannot be in the past.',
            'status.required' => 'Status field is required.',
        ]);
        

        try {
            DB::beginTransaction();
            
            $assignment = AssignmentParcel::create($validated);
            $this->createParcelDetails(
                $assignment,
                (int) $validated['user_id'],
                (int) $validated['parcel_quantity'],
                $validated['parcel_ids'],                
            );

            User::find($validated['user_id'])->update(['status_count' =>0]);
            $user_details = User::find($validated['user_id']);
            Log::debug('User details generated.', ['user_details' => $user_details]);
           
            $fcm_ids=[];
           
            $fcm_ids[]=$user_details->fcm_token;
            // print_r($fcm_ids);die;
            $title = 'Shippment Assignment';
            $msg = "Shippment Assigned to you";

            $fcmMsg = array(
                'title' => $title,
                'body' => $msg,
                'type' => 'Hub Assign',
                'android' => array(
                    // 'channelId' => 'high_importance_channel',
                    // 'importance' => 'high',
                    'priority' => 'high',
                ),
                'actionButtons' => array(
                    array(
                        'key' => 'Accept',
                        'label' => 'Accept',
                        'autoCancel' => 'true',
                        'buttonType' => 'InputField',
                    ),
                    array(
                        'key' => 'Reject',
                        'label' => 'Reject',
                        'autoCancel' => 'true',
                        'buttonType' => 'InputField',
                    ),
                )
            );

            // send_notification($fcmMsg, $fcm_ids, ['booking_id' => $booking_id,'type' => 'new_ride']);

            foreach ($fcm_ids as $token) {


                // skip invalid tokens
                if (empty($token)) {
                    continue;
                }

                $this->fcmNotificationService->sendNotification(
                    $token,
                    $title,
                    $msg,
                    [
                        'user_id' => (string)$validated['user_id'],
                        'type' => 'Hub Assign'
                    ],$fcmMsg['android'] ?? []
                );
            }

            DB::commit();

            return redirect()->route('admin.assignment-parcel.index')
                ->with('success', 'Assignment created successfully.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to create assignment. ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified assignment parcel.
     */
    public function show(AssignmentParcel $assignmentParcel)
    {
        $assignmentParcel->load([
            'vendor',
            'vehicle',
            'user',
            'hub',
            'parcelDetails' => function ($query) {
                $query->with('user')->orderBy('id');
            },
        ]);

        $assignmentParcel->syncStatusFromParcels();
        $assignmentParcel->refresh();

        return view('admin.assignment-parcel.show', compact('assignmentParcel'));
    }

    /**
     * Show the form for editing the specified assignment parcel.
     */
    public function edit(AssignmentParcel $assignmentParcel)
    {
        $vendors = Vendor::orderBy('name')->get();
        $vehicleOwnerColumn = $this->getVehicleOwnerColumn();
        $vehicleVendorColumn = $this->getVehicleVendorColumn();
        $vehicles = Vehicle::orderBy('vehicle_number')->get();
        $users = $this->assignableStaffQuery(excludeAlreadyAssigned: false)->get();
        $hubs = Hub::orderBy('name')->get();
        $statuses = AssignmentParcel::getStatuses();

        return view('admin.assignment-parcel.edit', compact('assignmentParcel', 'vendors', 'vehicles', 'users', 'hubs', 'statuses', 'vehicleOwnerColumn', 'vehicleVendorColumn'));
    }

    /**
     * Update the specified assignment parcel in storage.
     */
    public function update(Request $request, AssignmentParcel $assignmentParcel)
    {
        $vehicleVendorColumn = $this->getVehicleVendorColumn();

        $validated = $request->validate([
            'vendor_id' => 'required|exists:vendors,id',
            'vehicle_id' => [
                'required',
                'exists:vehicles,id',
                function ($attribute, $value, $fail) use ($request, $vehicleVendorColumn) {
                    if (!$vehicleVendorColumn) {
                        return;
                    }

                    $vehicleVendorId = DB::table('vehicles')
                        ->where('id', $value)
                        ->value($vehicleVendorColumn);

                    if ((string) $vehicleVendorId !== (string) $request->input('vendor_id')) {
                        $fail('Selected vehicle does not belong to selected vendor.');
                    }
                },
            ],
            'user_id' => ['required', Rule::exists('users', 'id')->whereIn('role_id', $this->assignableStaffRoleIds())],
            'hub_id' => 'required|exists:hubs,id',
            'parcel_quantity' => 'required|integer|min:1',
            'assignment_date' => 'required|date',
            'status' => 'required|in:pending,assigned,in_transit,delivered,cancelled',
            'notes' => 'nullable|string|max:1000',
        ]);

        try {
            DB::beginTransaction();

            $assignmentParcel->update($validated);

            DB::commit();

            return redirect()->route('admin.assignment-parcel.index')
                ->with('success', 'Assignment updated successfully.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to update assignment. ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified assignment parcel from storage.
     */
    public function destroy(AssignmentParcel $assignmentParcel)
    {
        try {
            DB::beginTransaction();

            $assignmentParcel->delete();

            DB::commit();

            return redirect()->route('admin.assignment-parcel.index')
                ->with('success', 'Assignment deleted successfully.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to delete assignment.');
        }
    }

    /**
     * Update assignment status.
     */
    public function updateStatus(Request $request, AssignmentParcel $assignmentParcel)
    {
        $request->validate([
            'status' => 'required|in:pending,assigned,in_transit,delivered,cancelled',
        ]);

        try {
            $assignmentParcel->update(['status' => $request->status]);

            return response()->json([
                'success' => true,
                'message' => 'Status updated successfully.',
                'status_badge' => $assignmentParcel->status_badge
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update status.'
            ], 500);
        }
    }

    /**
     * Get assignments report.
     */
    public function report(Request $request)
    {
        $query = AssignmentParcel::with(['vendor', 'vehicle', 'user', 'hub']);

        // Apply filters
        if ($request->has('from_date') && $request->from_date != '') {
            $query->whereDate('assignment_date', '>=', $request->from_date);
        }
        if ($request->has('to_date') && $request->to_date != '') {
            $query->whereDate('assignment_date', '<=', $request->to_date);
        }
        if ($request->has('hub_id') && $request->hub_id != '') {
            $query->where('hub_id', $request->hub_id);
        }

        $assignments = $query->orderBy('assignment_date', 'desc')->get();

        // Statistics
        $totalParcels = $assignments->sum('parcel_quantity');
        $totalAssignments = $assignments->count();
        $assignmentsByStatus = $assignments->groupBy('status')->map->count();
        $parcelsByHub = $assignments->groupBy('hub.name')->map->sum('parcel_quantity');

        $hubs = Hub::orderBy('name')->get();

        return view('admin.assignment-parcel.report', compact(
            'assignments', 
            'totalParcels', 
            'totalAssignments', 
            'assignmentsByStatus',
            'parcelsByHub',
            'hubs'
        ));
    }

    /**
     * Export assignments to PDF.
     */
    public function export(Request $request)
    {
        $query = AssignmentParcel::with(['vendor', 'vehicle', 'user', 'hub']);

        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }
        if ($request->has('hub_id') && $request->hub_id != '') {
            $query->where('hub_id', $request->hub_id);
        }
        if ($request->has('from_date') && $request->from_date != '') {
            $query->whereDate('assignment_date', '>=', $request->from_date);
        }
        if ($request->has('to_date') && $request->to_date != '') {
            $query->whereDate('assignment_date', '<=', $request->to_date);
        }

        $assignments = $query->orderBy('assignment_date', 'desc')->get();

        $format = $this->exportFormat($request);
        $headers = ['ID', 'Date', 'Vendor', 'Vehicle', 'Driver', 'Hub', 'Qty', 'Status', 'Notes'];
        $rows = [];
        foreach ($assignments as $assignment) {
            $rows[] = [
                (string) $assignment->id,
                $assignment->assignment_date ? date('d M Y', strtotime($assignment->assignment_date)) : 'N/A',
                (string) ($assignment->vendor->name ?? 'N/A'),
                (string) ($assignment->vehicle->vehicle_number ?? 'N/A'),
                (string) ($assignment->user->name ?? 'N/A'),
                (string) ($assignment->hub->name ?? 'N/A'),
                (string) ($assignment->parcel_quantity ?? 0),
                ucwords(str_replace('_', ' ', (string) $assignment->status)),
                (string) ($assignment->notes ?? ''),
            ];
        }
        if ($format === 'csv') {
            return $this->streamCsvDownload('assignment_parcels_' . now()->format('Y-m-d_His'), $headers, $rows);
        }
        if ($format === 'xlsx') {
            return $this->streamExcelTableDownload('assignment_parcels_' . now()->format('Y-m-d_His'), 'Assignment Parcels', $headers, $rows);
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
            . '<h2>Assignment Parcels</h2>'
            . '<p>Exported at: ' . e(now()->format('d M Y h:i A')) . '</p>'
            . '<table><thead><tr>'
            . '<th>ID</th><th>Date</th><th>Vendor</th><th>Vehicle</th><th>Driver</th><th>Hub</th><th>Qty</th><th>Status</th><th>Notes</th>'
            . '</tr></thead><tbody>';

        foreach ($assignments as $assignment) {
            $html .= '<tr>'
                . '<td>' . e((string) $assignment->id) . '</td>'
                . '<td>' . e($assignment->assignment_date ? date('d M Y', strtotime($assignment->assignment_date)) : 'N/A') . '</td>'
                . '<td>' . e($assignment->vendor->name ?? 'N/A') . '</td>'
                . '<td>' . e($assignment->vehicle->vehicle_number ?? 'N/A') . '</td>'
                . '<td>' . e($assignment->user->name ?? 'N/A') . '</td>'
                . '<td>' . e($assignment->hub->name ?? 'N/A') . '</td>'
                . '<td>' . e((string) ($assignment->parcel_quantity ?? 0)) . '</td>'
                . '<td>' . e(ucwords(str_replace('_', ' ', (string) $assignment->status))) . '</td>'
                . '<td>' . e((string) ($assignment->notes ?? '')) . '</td>'
                . '</tr>';
        }

        if ($assignments->isEmpty()) {
            $html .= '<tr><td colspan="9" style="text-align:center;">No Assignment Data Found</td></tr>';
        }

        $html .= '</tbody></table></body></html>';

        $dompdf = new \Dompdf\Dompdf([
            'isRemoteEnabled' => true,
            'defaultFont' => 'DejaVu Sans',
        ]);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('a4', 'landscape');
        $dompdf->render();

        $filename = 'assignment_parcels_' . now()->format('Y-m-d_His') . '.pdf';
        return response($dompdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    private function getVehicleOwnerColumn(): ?string
    {
        foreach (['user_id', 'seller_id', 'employee_id', 'created_by'] as $column) {
            if (Schema::hasColumn('vehicles', $column)) {
                return $column;
            }
        }

        return null;
    }

    private function getVehicleVendorColumn(): ?string
    {
        foreach (['vendor_id', 'seller_id'] as $column) {
            if (Schema::hasColumn('vehicles', $column)) {
                return $column;
            }
        }

        return null;
    }

    private function createParcelDetails(AssignmentParcel $assignment, int $staffId, int $quantity, array $parcelIds = [], string $status = 'assigned'): void
    {
        if ($quantity < 1) {
            return;
        }

        $hasParcelIdColumn = Schema::hasColumn('parcel_detail', 'parcel_id');
        $now = now();
        $rows = [];

        for ($i = 0; $i < $quantity; $i++) {
            $row = [
                'assignment_parcel_id' => $assignment->id,
                'user_id' => $staffId,
                'status' => $status??'assigned`',
                'created_at' => $now,
                'updated_at' => $now,
            ];

            // Use provided parcel ID if available and column exists
            if ($hasParcelIdColumn && isset($parcelIds[$i])) {
                $row['parcel_id'] = $parcelIds[$i];
            }

            $rows[] = $row;
        }

        ParcelDetail::insert($rows);
    }
}