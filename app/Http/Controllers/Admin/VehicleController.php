<?php



namespace App\Http\Controllers\Admin;



use App\Http\Controllers\Controller;
use App\Http\Controllers\Concerns\ExportsTabularData;

use Illuminate\Http\Request;

use App\Models\Vehicle;

use App\Models\Vendor;

use App\CPU\ImageManager;



class VehicleController extends Controller

{
    use ExportsTabularData;

    public function index(Request $request)

    {

        $query = Vehicle::with('vendor');



        if ($request->filled('search')) {

            $search = $request->search;



            $query->where(function ($q) use ($search) {

                $q->where('vehicle_number', 'like', "%{$search}%")

                    ->orWhereHas('vendor', function ($q2) use ($search) {

                        $q2->where('name', 'like', "%{$search}%")

                            ->orWhere('phone', 'like', "%{$search}%");

                    });

            });

        }



        if ($request->filled('status')) {

            $query->where('status', $request->status);

        }



        if ($request->filled('vehicle_type')) {

            $query->where('vehicle_type', $request->vehicle_type);

        }



        // Sorting Logic

        if ($request->filled('sort_by') && $request->filled('sort_order')) {

            $sortBy = $request->sort_by;

            $sortOrder = $request->sort_order;



            if ($sortBy === 'vendor') {

                $query->join('vendors', 'vehicles.vendor_id', '=', 'vendors.id')

                      ->orderBy('vendors.name', $sortOrder)

                      ->select('vehicles.*');

            } else {

                // Ensure the column exists on the vehicles table

                $allowedSorts = ['vehicle_number', 'vehicle_type', 'status', 'created_at', 'id'];

                if (in_array($sortBy, $allowedSorts)) {

                    $query->orderBy($sortBy, $sortOrder);

                } else {

                    $query->orderBy('created_at', 'desc');

                }

            }

        } else {

            $query->orderBy('created_at', 'desc');

        }





        $vehicles = $query->paginate(10)

            ->withQueryString();



        $vendors = Vendor::where('status', 1)->get();



        $vehicleTypes = ['Bike', 'Car', 'Truck', 'Van'];

        return view('admin.vehicles.index', compact('vehicles', 'vendors', 'vehicleTypes'));

    }



    public function store(Request $request)

    {

        $request->validate([

            'vehicle_number' => ['required', 'string', 'max:20', 'unique:vehicles,vehicle_number', 'regex:/^[A-Z]{2}[- ]\d{1,2}[- ][A-Z]{1,3}[- ]\d{4}$/'],

            'vehicle_type' => 'required|string|max:50',

            'vendor_id' => 'required|exists:vendors,id',

            'rc_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',

            'insurance_files' => 'nullable|array|max:10',
            'insurance_files.*' => 'file|mimes:jpg,jpeg,png,webp,pdf,doc,docx|max:5120',

        ], [

            'vehicle_number.regex' => 'The vehicle number must be in XX-00-XX-0000 format (e.g., MH-12-AB-1234)',
            'vehicle_number.max' => 'Vehicle number cannot be longer than 20 characters.',
            'insurance_files.*.mimes' => 'Insurance files must be jpg, jpeg, png, webp, pdf, doc, or docx.',
            'insurance_files.*.max' => 'Each insurance file must be less than 5MB.',

        ]);



        $rcPath = null;

        if($request->hasFile('rc_image')){

            $rcPath = ImageManager::upload('vehicles/rc/', 'png', $request->file('rc_image'));

            $rcPath = 'vehicles/rc/' . $rcPath;

        }



        $insurancePath = null;

        $insurancePaths = [];

        if ($request->hasFile('insurance_files')) {
            foreach ($request->file('insurance_files') as $insuranceFile) {
                $insurancePaths[] = $insuranceFile->store('vehicles/insurance', 'public');
            }
        }

        if (!empty($insurancePaths)) {
            $insurancePath = json_encode($insurancePaths);
        }



        Vehicle::create([

            'vehicle_number' => $request->vehicle_number,

            'vehicle_type' => $request->vehicle_type,

            'vendor_id' => $request->vendor_id,

            'rc_image' => $rcPath,

            'insurance_image' => $insurancePath,

            'status' => 1,

        ]);





        return redirect()->route('vehicles.index')->with('success', 'Vehicle created successfully.');

    }



    public function update(Request $request, Vehicle $vehicle)

    {

        $request->validate([

            'vehicle_number' => ['required', 'string', 'max:4', 'unique:vehicles,vehicle_number,' . $vehicle->id, 'regex:/^[A-Z]{2}[- ]\d{1,2}[- ][A-Z]{1,3}[- ]\d{4}$/'],

            'vehicle_type' => 'required|string|max:50',

            'vendor_id' => 'required|exists:vendors,id',

            'rc_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',

            'insurance_files' => 'nullable|array|max:10',
            'insurance_files.*' => 'file|mimes:jpg,jpeg,png,webp,pdf,doc,docx|max:5120',

        ], [

             'vehicle_number.regex' => 'The vehicle number must be in proper format (e.g., MH-12-AB-1234)',
             'vehicle_number.max' => 'Vehicle number cannot be longer than 4 characters.',
             'insurance_files.*.mimes' => 'Insurance files must be jpg, jpeg, png, webp, pdf, doc, or docx.',
             'insurance_files.*.max' => 'Each insurance file must be less than 5MB.',

        ]);



        $data = [

            'vehicle_number' => $request->vehicle_number,

            'vehicle_type' => $request->vehicle_type,

            'vendor_id' => $request->vendor_id,

        ];



        if ($request->hasFile('rc_image')) {

            if ($vehicle->rc_image && file_exists(public_path('storage/' . $vehicle->rc_image))) {

                unlink(public_path('storage/' . $vehicle->rc_image));

            }

            $data['rc_image'] = ImageManager::upload('vehicles/rc/', 'png', $request->file('rc_image'));

            $data['rc_image'] = 'vehicles/rc/' . $data['rc_image'];

        }



        if ($request->hasFile('insurance_files')) {

            $existingFiles = $this->parseInsuranceFiles($vehicle->insurance_image);
            foreach ($existingFiles as $existingFile) {
                if ($existingFile && file_exists(public_path('storage/' . $existingFile))) {
                    unlink(public_path('storage/' . $existingFile));
                }
            }

            $newInsuranceFiles = [];
            foreach ($request->file('insurance_files') as $insuranceFile) {
                $newInsuranceFiles[] = $insuranceFile->store('vehicles/insurance', 'public');
            }

            $data['insurance_image'] = json_encode($newInsuranceFiles);
        }



        $vehicle->update($data);





        return redirect()->route('vehicles.index')->with('success', 'Vehicle updated successfully.');

    }



    public function updateStatus(Request $request)

    {

        $request->validate([

            'id' => 'required|exists:vehicles,id',

            'status' => 'required|boolean'

        ]);



        $vehicle = Vehicle::find($request->id);

        $vehicle->status = $request->status;

        $vehicle->save();



        return response()->json(['success' => 'Status updated successfully.']);

    }



    public function destroy(Vehicle $vehicle)

    {

        $vehicle->delete();

        return redirect()->route('vehicles.index')->with('success', 'Vehicle deleted successfully.');

    }

    public function export(Request $request)
    {
        $query = Vehicle::with('vendor');

        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('vehicle_number', 'like', "%{$search}%")
                    ->orWhereHas('vendor', function ($q2) use ($search) {
                        $q2->where('name', 'like', "%{$search}%")
                            ->orWhere('phone', 'like', "%{$search}%");
                    });
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('vehicle_type')) {
            $query->where('vehicle_type', $request->vehicle_type);
        }

        $vehicles = $query->orderBy('created_at', 'desc')->get();

        $format = $this->exportFormat($request);
        $headers = ['ID', 'Vehicle Number', 'Vendor', 'Type', 'Status'];
        $rows = [];
        foreach ($vehicles as $vehicle) {
            $rows[] = [
                (string) $vehicle->id,
                (string) $vehicle->vehicle_number,
                (string) ($vehicle->vendor->name ?? '-'),
                (string) $vehicle->vehicle_type,
                ((int) $vehicle->status === 1) ? 'Active' : 'Inactive',
            ];
        }
        if ($format === 'csv') {
            return $this->streamCsvDownload('vehicles_' . now()->format('Y-m-d_His'), $headers, $rows);
        }
        if ($format === 'xlsx') {
            return $this->streamExcelTableDownload('vehicles_' . now()->format('Y-m-d_His'), 'Vehicle Records', $headers, $rows);
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
            . '<h2>Vehicle Records</h2>'
            . '<p>Exported at: ' . e(now()->format('d M Y h:i A')) . '</p>'
            . '<table><thead><tr>'
            . '<th>ID</th><th>Vehicle Number</th><th>Vendor</th><th>Type</th><th>Status</th>'
            . '</tr></thead><tbody>';

        foreach ($vehicles as $vehicle) {
            $html .= '<tr>'
                . '<td>' . e((string) $vehicle->id) . '</td>'
                . '<td>' . e((string) $vehicle->vehicle_number) . '</td>'
                . '<td>' . e((string) ($vehicle->vendor->name ?? '-')) . '</td>'
                . '<td>' . e((string) $vehicle->vehicle_type) . '</td>'
                . '<td>' . e((int) $vehicle->status === 1 ? 'Active' : 'Inactive') . '</td>'
                . '</tr>';
        }

        if ($vehicles->isEmpty()) {
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

        $filename = 'vehicles_' . now()->format('Y-m-d_His') . '.pdf';
        return response($dompdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    private function parseInsuranceFiles($value): array
    {
        if (empty($value)) {
            return [];
        }

        $decoded = json_decode($value, true);
        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
            return array_values(array_filter($decoded));
        }

        return [$value];
    }

}

