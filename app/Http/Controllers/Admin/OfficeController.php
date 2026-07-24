<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Concerns\ExportsTabularData;
use App\Models\Office;
use Illuminate\Http\Request;

class OfficeController extends Controller
{
    use ExportsTabularData;

    public function index()
    {
        $offices = Office::orderBy('name')->paginate(10);
        return view('offices.index', compact('offices'));
    }

    public function create()
    {
        return view('offices.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'location' => 'nullable|string|max:255',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'opening_time' => 'nullable|date_format:H:i',
            'closing_time' => 'nullable|date_format:H:i|after:opening_time',
        ]);

        Office::create($validated);

        return redirect()->route('admin.offices.index')
            ->with('success', 'Office created successfully.');
    }

    public function show(Office $office)
    {
        return view('offices.show', compact('office'));
    }

    public function edit(Office $office)
    {
        return view('offices.edit', compact('office'));
    }

    public function update(Request $request, Office $office)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'location' => 'nullable|string|max:255',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'opening_time' => 'nullable|date_format:H:i',
            'closing_time' => 'nullable|date_format:H:i|after:opening_time',
        ]);

        $office->update($validated);

        return redirect()->route('admin.offices.index')
            ->with('success', 'Office updated successfully.');
    }

    public function destroy(Office $office)
    {
        $office->delete();

        return redirect()->route('admin.offices.index')
            ->with('success', 'Office deleted successfully.');
    }

    public function map()
    {
        $offices = Office::all();
        return view('offices.map', compact('offices'));
    }

    public function export(Request $request)
    {
        $offices = Office::orderBy('name')->get();

        $format = $this->exportFormat($request);
        $headers = ['ID', 'Name', 'Location', 'Latitude', 'Longitude', 'Open Time', 'Close Time', 'Status'];
        $rows = [];
        foreach ($offices as $office) {
            $rows[] = [
                (string) $office->id,
                (string) $office->name,
                (string) ($office->location ?? 'N/A'),
                $office->latitude !== null ? (string) $office->latitude : 'N/A',
                $office->longitude !== null ? (string) $office->longitude : 'N/A',
                $office->opening_time ? date('h:i A', strtotime($office->opening_time)) : 'N/A',
                $office->closing_time ? date('h:i A', strtotime($office->closing_time)) : 'N/A',
                $office->is_open ? 'Open' : 'Closed',
            ];
        }
        if ($format === 'csv') {
            return $this->streamCsvDownload('offices_' . now()->format('Y-m-d_His'), $headers, $rows);
        }
        if ($format === 'xlsx') {
            return $this->streamExcelTableDownload('offices_' . now()->format('Y-m-d_His'), 'Office Records', $headers, $rows);
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
            . '<h2>Office Records</h2>'
            . '<p>Exported at: ' . e(now()->format('d M Y h:i A')) . '</p>'
            . '<table><thead><tr>'
            . '<th>ID</th><th>Name</th><th>Location</th><th>Latitude</th><th>Longitude</th><th>Open Time</th><th>Close Time</th><th>Status</th>'
            . '</tr></thead><tbody>';

        foreach ($offices as $office) {
            $html .= '<tr>'
                . '<td>' . e((string) $office->id) . '</td>'
                . '<td>' . e((string) $office->name) . '</td>'
                . '<td>' . e((string) ($office->location ?? 'N/A')) . '</td>'
                . '<td>' . e($office->latitude !== null ? (string) $office->latitude : 'N/A') . '</td>'
                . '<td>' . e($office->longitude !== null ? (string) $office->longitude : 'N/A') . '</td>'
                . '<td>' . e($office->opening_time ? date('h:i A', strtotime($office->opening_time)) : 'N/A') . '</td>'
                . '<td>' . e($office->closing_time ? date('h:i A', strtotime($office->closing_time)) : 'N/A') . '</td>'
                . '<td>' . e($office->is_open ? 'Open' : 'Closed') . '</td>'
                . '</tr>';
        }

        if ($offices->isEmpty()) {
            $html .= '<tr><td colspan="8" style="text-align:center;">No Office Data Found</td></tr>';
        }

        $html .= '</tbody></table></body></html>';

        $dompdf = new \Dompdf\Dompdf([
            'isRemoteEnabled' => true,
            'defaultFont' => 'DejaVu Sans',
        ]);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('a4', 'landscape');
        $dompdf->render();

        $filename = 'offices_' . now()->format('Y-m-d_His') . '.pdf';
        return response($dompdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }
}
