<?php
// app/Http/Controllers/Admin/HubController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Concerns\ExportsTabularData;
use App\Models\Hub;
use Illuminate\Http\Request;

class HubController extends Controller
{
    use ExportsTabularData;

    /**
     * Display a listing of hubs.
     */
    public function index()
    {
        $hubs = Hub::orderBy('name')->paginate(10);
        return view('hubs.index', compact('hubs'));
    }

    /**
     * Show the form for creating a new hub.
     */
    public function create()
    {
        return view('hubs.create');
    }

    /**
     * Store a newly created hub in storage.
     */
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

        Hub::create($validated);

        // FIXED: Changed from hubs.index to admin.hubs.index
        return redirect()->route('admin.hubs.index')
            ->with('success', 'Hub created successfully.');
    }

    /**
     * Display the specified hub.
     */
    public function show(Hub $hub)
    {
        return view('hubs.show', compact('hub'));
    }

    /**
     * Show the form for editing the specified hub.
     */
    public function edit(Hub $hub)
    {
        return view('hubs.edit', compact('hub'));
    }

    /**
     * Update the specified hub in storage.
     */
    public function update(Request $request, Hub $hub)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'location' => 'nullable|string|max:255',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'opening_time' => 'nullable|date_format:H:i',
            'closing_time' => 'nullable|date_format:H:i|after:opening_time',
        ]);

        $hub->update($validated);

        // FIXED: Changed from hubs.index to admin.hubs.index
        return redirect()->route('admin.hubs.index')
            ->with('success', 'Hub updated successfully.');
    }

    /**
     * Remove the specified hub from storage.
     */
    public function destroy(Hub $hub)
    {
        $hub->delete();

        // FIXED: Changed from hubs.index to admin.hubs.index
        return redirect()->route('admin.hubs.index')
            ->with('success', 'Hub deleted successfully.');
    }

    /**
     * Get hubs for map view.
     */
    public function map()
    {
        $hubs = Hub::all();
        return view('hubs.map', compact('hubs'));
    }

    /**
     * API endpoint for getting hubs.
     */
    public function api()
    {
        $hubs = Hub::all();
        return response()->json($hubs);
    }

    /**
     * Export hubs to PDF.
     */
    public function export(Request $request)
    {
        $hubs = Hub::orderBy('name')->get();

        $format = $this->exportFormat($request);
        $headers = ['ID', 'Name', 'Location', 'Latitude', 'Longitude', 'Open Time', 'Close Time', 'Status'];
        $rows = [];
        foreach ($hubs as $hub) {
            $rows[] = [
                (string) $hub->id,
                (string) $hub->name,
                (string) ($hub->location ?? 'N/A'),
                $hub->latitude !== null ? (string) $hub->latitude : 'N/A',
                $hub->longitude !== null ? (string) $hub->longitude : 'N/A',
                $hub->opening_time ? date('h:i A', strtotime($hub->opening_time)) : 'N/A',
                $hub->closing_time ? date('h:i A', strtotime($hub->closing_time)) : 'N/A',
                $hub->is_open ? 'Open' : 'Closed',
            ];
        }
        if ($format === 'csv') {
            return $this->streamCsvDownload('hubs_' . now()->format('Y-m-d_His'), $headers, $rows);
        }
        if ($format === 'xlsx') {
            return $this->streamExcelTableDownload('hubs_' . now()->format('Y-m-d_His'), 'Hub Records', $headers, $rows);
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
            . '<h2>Hub Records</h2>'
            . '<p>Exported at: ' . e(now()->format('d M Y h:i A')) . '</p>'
            . '<table><thead><tr>'
            . '<th>ID</th><th>Name</th><th>Location</th><th>Latitude</th><th>Longitude</th><th>Open Time</th><th>Close Time</th><th>Status</th>'
            . '</tr></thead><tbody>';

        foreach ($hubs as $hub) {
            $html .= '<tr>'
                . '<td>' . e((string) $hub->id) . '</td>'
                . '<td>' . e((string) $hub->name) . '</td>'
                . '<td>' . e((string) ($hub->location ?? 'N/A')) . '</td>'
                . '<td>' . e($hub->latitude !== null ? (string) $hub->latitude : 'N/A') . '</td>'
                . '<td>' . e($hub->longitude !== null ? (string) $hub->longitude : 'N/A') . '</td>'
                . '<td>' . e($hub->opening_time ? date('h:i A', strtotime($hub->opening_time)) : 'N/A') . '</td>'
                . '<td>' . e($hub->closing_time ? date('h:i A', strtotime($hub->closing_time)) : 'N/A') . '</td>'
                . '<td>' . e($hub->is_open ? 'Open' : 'Closed') . '</td>'
                . '</tr>';
        }

        if ($hubs->isEmpty()) {
            $html .= '<tr><td colspan="8" style="text-align:center;">No Hub Data Found</td></tr>';
        }

        $html .= '</tbody></table></body></html>';

        $dompdf = new \Dompdf\Dompdf([
            'isRemoteEnabled' => true,
            'defaultFont' => 'DejaVu Sans',
        ]);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('a4', 'landscape');
        $dompdf->render();

        $filename = 'hubs_' . now()->format('Y-m-d_His') . '.pdf';
        return response($dompdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }
}